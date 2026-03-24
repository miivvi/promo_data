<?php

declare(strict_types=1);

namespace App\DTOs;

readonly class MinMaxPriceReportRowDto
{
    public function __construct(
        public string $manufacturerName,
        public string $productName,
        public float $priceMin,
        public string $priceMinDate,
        public float $priceMax,
        public string $priceMaxDate,
    ) {
    }

    public static function fromRow(object|array $row): self
    {
        $r = (array) $row;

        return new self(
            manufacturerName: (string) ($r['manufacturer_name'] ?? ''),
            productName: (string) ($r['product_name'] ?? ''),
            priceMin: round((float) ($r['price_min'] ?? 0), 2),
            priceMinDate: isset($r['price_min_date']) ? (string) $r['price_min_date'] : '',
            priceMax: round((float) ($r['price_max'] ?? 0), 2),
            priceMaxDate: isset($r['price_max_date']) ? (string) $r['price_max_date'] : '',
        );
    }

    public function toCsvMinRow(): array
    {
        return [
            $this->manufacturerName,
            $this->productName,
            $this->priceMin,
            $this->priceMinDate,
        ];
    }

    public function toCsvMaxRow(): array
    {
        return [
            $this->manufacturerName,
            $this->productName,
            $this->priceMax,
            $this->priceMaxDate,
        ];
    }
}
