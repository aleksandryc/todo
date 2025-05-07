<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Document' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12pt;
            margin: 0;
            padding: 0;
            text-align: justify;
        }
        @page {
            margin: 10mm 20mm 30mm 20mm;
        }
        table {
            border-collapse: collapse;
            border-spacing: 5px;
            margin: 2px;
            border: 1px solid black;
            }
        td {
            border: 1px solid grey;
            width: 10px;
            height: 10px;
            padding: 8px;
            }
        img {
            height: 100px;
            display: inline;
            vertical-align: bottom;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div>
        @if ($logo)
        <img style="float: right" src="{{ $logo }}">
        @endif
        <h1>{{ $title ?? 'Document' }}</h1>
        <p>{{ $description ?? '' }}</p>
    </div>

    <table border="1" cellpadding="8" cellspacing="0" width="95%">
        @forelse($fields as $key => $value)
        <tr>
            <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
            <td>
                @php
                $embeddedData = $embeddedImages[$key] ?? null;
                @endphp

                @if($embeddedData)
                <a href="{{ asset('storage/' . (is_string($value) ? $value : '')) }}">
                    <img src="{{ $embeddedData }}" alt="Embedded image">
                </a>
                @elseif(is_string($value) && str_starts_with($value, 'attachments/'))
                <a href="{{ asset('storage/' . $value) }}">{{ asset('storage/' . $value) }}</a>
                @elseif(is_array($value))
                {{ implode(', ', array_map('strval', $value)) }}
                @elseif(is_scalar($value))
                {{ $value }}
                @else
                N/A
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="2">No data available</td>
        </tr>
        @endforelse
    </table>

    <p style="font-size: 10px">Submitted at: {{ now()->toDateTimeString() }}</p>
</body>
</html>
