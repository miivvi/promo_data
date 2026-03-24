<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
    <h2 class="mb-4 text-xl font-semibold">{{ $title }}</h2>

    @if($data->isEmpty())
        <p>Записей пока нет.</p>
    @else
        <div>
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">Дата процесса</th>
                        <th scope="col">Время запуска</th>
                        <th scope="col">Время выполнения</th>
                        <th scope="col">Идентификатор процесса</th>
                        <th scope="col">Статус процесса</th>
                        <th scope="col">Имя файла</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $p)
                        <tr @if ($p->isFailProcess) class="table-danger" @endif>
                            <td>{{ $p->date }}</td>
                            <td>{{ $p->startTime }}</td>
                            <td>{{ $p->execTime }}</td>
                            <td>{{ $p->pid }}</td>
                            <td>{{ $p->name }}</td>
                            <td><a href="{{ route('download_file', ['id' => $p->id]) }}">{{ $p->file }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
