<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body>
    <div class="container mx-auto mt-2">
        <h1 class="text-3xl text-center font-bold mb-6">Available Forms</h1>
        @if (count($forms) > 0)
            <ul class="space-y-4 max-w-md">
                @foreach ($forms as $form)
                <li class="border-2 border-green-800 p-4 rounded-md">
                    <a class="font-bold underline text-blue-800" href="{{ route('forms.show', $form['key']) }}">{{ $form['title'] }}</a>
                    @if ($form['description'])
                        <p class="text-xs p-2 text-justify">{{ $form['description'] }}</p>
                    @endif
                </li>
                @endforeach
            </ul>
        @else
        <p>No forms available.</p>
        @endif
    </div>
</body>
</html>
