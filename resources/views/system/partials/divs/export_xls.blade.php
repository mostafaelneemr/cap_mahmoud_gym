<html xmlns:x="urn:schemas-microsoft-com:office:excel">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <!--[if gte mso 9]>
        <xml>
            <x:ExcelWorkbook>
                <x:ExcelWorksheets>
                    <x:ExcelWorksheet>
                        <x:Name>Sheet 1</x:Name>
                        <x:WorksheetOptions>
                            <x:Print>
                                <x:ValidPrinterInfo/>
                            </x:Print>
                        </x:WorksheetOptions>
                    </x:ExcelWorksheet>
                </x:ExcelWorksheets>
            </x:ExcelWorkbook>
        </xml>
        <![endif]-->
</head>

<table>
    <thead>
        <tr>
            <th colspan="{{ count($heads) }}">{{ $title }}</th>
        </tr>
        <tr>

            @foreach ($heads as $key => $value)
                <th>{{ $value }}</th>

    </thead>
    <tbody>
        @foreach ($exData as $key => $value)
            <tr>
                @foreach ($callback as $k => $v)
                    @if (is_string($v))
                        <td> {{ $value[$v] }} </td>
                    @else
                        <td>{{ $v($value) }}</td>

            </tr>

    </tbody>
</table>

@php
    header('Content-Disposition: attachment; filename=' . $title . '-' . date('Y-m-d H i') . '.xls');
@endphp
