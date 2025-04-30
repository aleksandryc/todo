<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ htmlspecialchars($title ?? 'Document') }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        img {
            max-width: 300px;
            height: auto;
        }
    </style>
</head>

<body>
    <h1>{{ htmlspecialchars($title ?? 'Document') }}</h1>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        @if(!empty($fields) && is_array($fields))
        @foreach($fields as $key => $value)
        <tr>
            <td><strong>{{ htmlspecialchars(ucwords(str_replace('_', ' ', $key))) }}</strong></td>
            <td>
                @php
                    $embeddedKey = $key . '_embedded';
                    $embeddedData = $fields[$embeddedKey] ?? null;
                @endphp
                    @if(is_string($embeddedData) && preg_match('/^data:image\/(png|jpg|jpeg|gif):baase64./', $embeddedData))
                        <img src="{{ $embeddedData }}" alt="Embedded image">
                    @endif
                @if(is_string($value) && filter_var($value, FILTER_VALIDATE_URL))
                    <a href="{{ $value }}">{{ $value }}</a>
                @elseif(is_string($value) && str_starts_with($value, 'attachments/'))
                    Attached file: {{ $value}}
                @else
                    {{ htmlspecialchars($value) }}
                @endif
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="2">No data available</td>
        </tr>
        @endif
    </table>
    <p>Submitted at: {{ now()->toDateTimeString() }}</p>
</body>

</html>
