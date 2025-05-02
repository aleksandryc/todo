<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ htmlspecialchars($title ?? 'Document') }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style>
        img {
            max-width: 500px;
            height: auto;
        }
    </style>
</head>

<body>
    <h1>{{ htmlspecialchars($title ?? 'Document') }}</h1>

    <table border="1" cellpadding="8" cellspacing="0" width="90%">

        @forelse($fields as $key => $value)
        <tr>
            <td>
                <strong>{{ htmlspecialchars(ucwords(str_replace('_', ' ', $key))) }}</strong>
            </td>
            <td>
                @php
                $embeddedData = $embeddedImages[$key] ?? null;
                @endphp

                @if($embeddedData)
                <a href="{{ asset('storage/'.$value) }}">
                    <img src="{{ $embeddedData }}" alt="Embedded image">
                </a>

                @elseif(is_string($value) && str_starts_with($value, 'attachments/'))
                <a href="{{ asset('storage/'.$value) }}">
                    {{ asset('storage/'.$value) }}
                </a>
                @elseif (is_array($value))
                {{ implode(',', $value)}}

                @else
                {{ htmlspecialchars($value) }}

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


/*
TypeError
htmlspecialchars(): Argument #1 ($string) must be of type string, array given
vendor\laravel\framework\src\Illuminate\Support\helpers.php :141
return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
*/
