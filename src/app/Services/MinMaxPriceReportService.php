<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\MinMaxPriceReportRowDto;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class MinMaxPriceReportService
{
    private const CSV_HEADERS = ['manufacturer_name', 'product_name', 'price', 'price_date'];

    private const CSV_SEPARATOR = ';';

    public function buildBaseQuery(int $categoryId): Builder
    {
        $from = Carbon::now()->subDays(7)->format('Y-m-d H:i:s');
        $to = Carbon::now()->format('Y-m-d H:i:s');

        return DB::query()
            ->from('price as p')
            ->join('product as pr', 'pr.product_id', '=', 'p.product_id')
            ->join('manufacturer as m', 'm.manufacturer_id', '=', 'pr.manufacturer_id')
            ->where('pr.category_id', $categoryId)
            ->whereBetween('p.price_date', [$from, $to])
            ->select([
                'm.manufacturer_name',
                'pr.product_name',
                'pr.product_id',
                'p.price',
                'p.price_date',
            ]);
    }

    public function buildMinMaxSql(Builder $baseQuery): string
    {
        $baseSql = $baseQuery->toSql();

        return "
            WITH base AS ({$baseSql}),
            min_row AS (
                SELECT DISTINCT ON (product_id)
                    product_id,
                    price       AS price_min,
                    price_date  AS price_min_date
                FROM base
                ORDER BY product_id, price ASC, price_date DESC
            ),
            max_row AS (
                SELECT DISTINCT ON (product_id)
                    product_id,
                    price       AS price_max,
                    price_date  AS price_max_date
                FROM base
                ORDER BY product_id, price DESC, price_date DESC
            ),
            meta AS (
                SELECT DISTINCT product_id, manufacturer_name, product_name
                FROM base
            )
            SELECT
                meta.manufacturer_name,
                meta.product_name,
                min_row.price_min,
                min_row.price_min_date,
                max_row.price_max,
                max_row.price_max_date
            FROM meta
            INNER JOIN min_row USING (product_id)
            INNER JOIN max_row USING (product_id)
            ORDER BY meta.product_name
        ";
    }

    /**
     * @return \Traversable<int, MinMaxPriceReportRowDto>
     */
    public function buildReportData(int $categoryId): \Traversable
    {
        $builder = $this->buildBaseQuery($categoryId);
        $sql = $this->buildMinMaxSql($builder);
        $bindings = $builder->getBindings();

        foreach (DB::connection()->cursor($sql, $bindings) as $row) {
            yield MinMaxPriceReportRowDto::fromRow($row);
        }
    }

    public function makeReportPath(int $categoryId): string
    {
        $fileName = sprintf(
            'report_manufacturer_id_%s_%s.csv',
            $categoryId,
            Carbon::parse(now())->format('Y-m-d_H-i-s')
        );

        $path = storage_path(sprintf('app/exports/%s', $fileName));

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        return $path;
    }

    public function exportToCsv(int $categoryId): string
    {
        $filePath = $this->makeReportPath($categoryId);
        $handle = fopen($filePath, 'wb');

        fputcsv($handle, self::CSV_HEADERS, self::CSV_SEPARATOR);

        foreach ($this->buildReportData($categoryId) as $row) {
            fputcsv($handle, $row->toCsvMinRow(), self::CSV_SEPARATOR);
            fputcsv($handle, $row->toCsvMaxRow(), self::CSV_SEPARATOR);
        }

        fclose($handle);

        return $filePath;
    }
}
