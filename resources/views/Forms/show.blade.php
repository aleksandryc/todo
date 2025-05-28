<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $formConfig['title'] ?? 'User Form' }}</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

</head>
<body class="min-h-screen bg-[#cfcdcc] print:bg-white print:pb-0">
    <div class="container bg-white rounded shadow mx-auto mt-2">
        @if($flash = session('message'))
        <div id="flash-message" class="text-3xl text-center text-green-700" role="alert">
            {{$flash}}
        </div>
        @endif
        <h1 class="text-2xl text-center p-1 m-1">{{ $formConfig['title'] ?? 'Untiteled form'}}</h1>
        <p class="text-md text-center max-w-xl p-1 m-1 mx-auto">{{ $formConfig['description'] ?? '' }}</p>
        <form class="card mx-auto max-w-full px-4 py-2 mt-2 rounded-md" action="{{ route('forms.submit', $formKey) }}" method="post" enctype="multipart/form-data">
            @csrf
            @foreach ($formComponents as $name => $field)
            @php
            $depends = $field['depends_on'] ?? null;
            // Skipping fields that are related (related-to)
            if (!empty($field['related-to'])) {
            continue;
            }
            @endphp
            <div class="mb-4">
                <label for="{{ $name }}" class="block font-semibold mb-1">
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                    <span class="text-red-500">*</span>
                    @endif
                </label>
                @switch($field['type'])
                    @case('text')
                    @case('email')
                    @case('date')
                    @case('tel')
                        @include('components.c-input', [$name, $field, $depends])
                    @break
                    @case('textarea')
                        @include('components.c-textarea', [$name, $field, $depends])
                    @break
                    @case('select')
                        @include('components.c-select', [$name, $field, $depends])
                    @break
                    @case('radio')
                        @include('components.c-radio', [$name, $field, $depends])
                    @break
                    @case('checkbox')
                        @include('components.c-checkbox', [$name, $field, $depends])
                    @break
                    @case('checkbox-group')
                        @include('components.c-checkboxgroup', [$name, $field, $depends, $formComponents])
                    @break
                    @case('file')
                    @case('url')
                        @include('components.c-file-url', [$name, $field, $depends])
                    @break
                @endswitch

                @error($name)
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach
            <div class="mb-4">
                <label for="mailRecipients" class="block font-semibold mb-1">
                    Send to:
                    <span class="text-red-500">*</span>
                </label>
                <input type="email" name="mailRecipients" id="mailRecipients" value="{{ old('mailRecipients') }}" class="w-full rounded px-3 py-2 focus:bg-green-100
                    {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" placeholder="example@mail.com" required>
                @error("mailRecipients")
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="ccRecipients" class="block font-semibold mb-1">
                    Copy to:
                </label>
                <input type="email" multiple name="ccRecipients" id="ccRecipients" value="{{ old('ccRecipients') }}" class="w-full border rounded px-3 py-2 focus:bg-green-100" placeholder="example@mail.com, another@mailaddres.com">
                @error('ccRecipients')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ url()->previous() }}" class=" bg-[#645b54] px-4 py-2 rounded-md text-white hover:bg-[#333333]">Back</a>
                <button type="reset" class="rounded-md px-4 py-2 border-[#9c382c] bg-[#ed9b82] hover:text-black hover:bg-[#e76f4f] hover:border-[#522322]">Clear form</button>
                <button type="submit" class=" bg-[#39a57a] px-4 py-2 rounded-md text-white hover:bg-[#288257]">Send</button>
            </div>
        </form>
    </div>
    @vite(['resources/js/userform.js'])

</body>
</html>
