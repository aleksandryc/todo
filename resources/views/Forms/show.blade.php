<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-[#cfcdcc] print:bg-white print:pb-0">
    <div class="container bg-white rounded shadow mx-auto mt-2">
        @if($flash = session('message'))
        <div id="flash-message" class="text-3xl text-center text-green-700" role="alert">
            {{$flash}}
        </div>
        @endif
        <h1 class="text-2xl text-center">{{ $formConfig['title'] ?? 'Untiteled form'}}</h1>
        <p class="text-md text-center max-w-xl mx-auto">{{ $formConfig['description'] ?? '' }}</p>

        <form class="card mx-auto max-w-full px-4 py-2 rounded-md" action="{{ route('forms.submit', $formKey) }}" method="post" enctype="multipart/form-data">
            @csrf
            @foreach ($formComponents as $name => $field)
            <div class="mb-4">
                <label for="{{ $name }}" class="block font-semibold mb-1">
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                    <span class="text-red-500">*</span>
                    @endif
                </label>

                @if (in_array($field['type'], ['text', 'email', 'date', 'tel']))
                <input
                    type="{{ $field['type'] }}"
                    name="{{ $name }}"
                    id="{{ $name }}"
                    value="{{ old($name) }}"
                    class="w-full border rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}"
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                    {{ !empty($field['required']) ? 'required' : '' }}>
                @endif

                @if ($field['type'] === 'textarea')
                <textarea name="{{ $name }}" id="{{ $name }}" class="w-full border rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}" {{ !empty($field['required']) ? 'required' : '' }}>
                {{ old($name) }}
                </textarea>
                @endif

                @if ($field['type'] === 'select')
                <select name="{{ $name }}" id="{{ $name }}" class="w-full border rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}" {{ !empty($field['required']) ? 'required' : '' }}>
                    <option value="">--- Select ---</option>
                    @foreach ($field['options'] as $option)
                    <option value="{{ $option }}" {{ old($name) === $option ? 'selected' : '' }}>
                        {{ $option }}
                    </option>
                    @endforeach
                </select>
                @endif

                @if ($field['type'] === 'radio')
                <div class="flex gap-4 mt-1 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}">
                    @foreach ($field['options'] as $option)
                    <label>
                        <input type="{{ $field['type'] }}" name="{{ $name }}" value="{{ $option }}" {{ !empty($field['required']) ? 'required' : '' }}>
                        {{ $option }}
                    </label>
                    @endforeach
                </div>
                @endif
                @if ($field['type'] === 'checkbox')
                <div class="flex gap-4 mt-1 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}">
                    <label>
                        <input type="checkbox" name="{{ $name }}" value="1" {{ !empty($field['required']) ? 'required' : '' }}>
                        {{ $field['options'] }}
                    </label>

                </div>
                @endif

                @if ($field['type'] === 'checkbox-group')
                <div class="flex gap-4 mt-1 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}">
                    @foreach ($field['options'] as $option)
                    <label>
                        <input type="checkbox" name="{{ $name }}[]" value="{{ $option }}">
                        {{ $option }}
                    </label>
                    @endforeach
                </div>
                @endif

                @if ($field['type'] === 'file' || $field['type'] === 'url')
                <input type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}" class="w-full border rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ !empty($field['required']) ? 'required' : '' }}>
                @endif
                @error($name)
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach
            <div class="mb-4">
                <label
                    for="mailRecipient"
                    class="block font-semibold mb-1"
                    >
                    Send to:
                    <span class="text-red-500">*</span>
                </label>
                <input type="email"
                name="mailRecipient"
                id="mailRecipient"
                value="{{ old('mailRecipient') }}"
                class="w-full border rounded px-3 py-2 bg-[#fecaca]/25 border-[#f87171] }}"
                placeholder="example@mail.com"
                required>
                 @error($name)
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label
                    for="ccRecipient"
                    class="block font-semibold mb-1"
                    >
                    Copy to:
                </label>
                <input type="email" multiple
                name="ccRecipient"
                id="ccRecipient"
                value="{{ old('ccRecipient') }}"
                class="w-full border rounded px-3 py-2"
                placeholder="example@mail.com, another@mailaddres.com"
                >
                 @error($name)
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
</body>
</html>
