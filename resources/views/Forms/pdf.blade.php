<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Document' }}</title>

    <style>
        img {
            max-width: 500px;
            height: auto;
        }
    </style>
</head>
<body>
    <h1>{{ $title ?? 'Document' }}</h1>

    <table border="1" cellpadding="8" cellspacing="0" width="90%">
        @forelse($fields as $key => $value)
            <tr>
                <td><strong>{{ ucwords(str_replace('_', ' ', $key)) }}</strong></td>
                <td>
                    @php
                        $embeddedData = $embeddedImages[$key] ?? null;
                    @endphp

                    @if($embeddedData)
                        <a style="{max-width:240px}" href="{{ asset('storage/' . (is_string($value) ? $value : '')) }}">
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

    <p>Submitted at: {{ now()->toDateTimeString() }}</p>
</body>
</html>
