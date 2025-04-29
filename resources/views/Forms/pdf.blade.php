<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ htmlspecialchars($title ?? 'Document') }}</title>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>

<body>
    <h1>{{ htmlspecialchars($title ?? 'Document') }}</h1>

    <table border="1" cellpadding="8" cellspacing="0" width="100%">
        @if(!empty($fields) && is_array($fields))
        @foreach($fields as $key => $value)
        <tr>
            <td><strong>{{ htmlspecialchars(ucwords(str_replace('_', ' ', $key))) }}</strong></td>
            <td>{{ htmlspecialchars($value) }}</td>
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
