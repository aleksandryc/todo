<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>
<div class="container mx-auto mt-2 w-2/3">
    <h1 class="text-3xl text-center">{{ $formConfig['title'] }}</h1>
    <form action="{{ route('forms.submit', $formKey) }}" method="post" enctype="multipart/form-data">
        @csrf
        @foreach($formConfig['fields'] as $name => $field)
            <div class="mb-4">
                <label
                    for="{{ $name }}"
                    class="block font-semibold mb-1">
                        {{ $field['label']}}
                        @if(!empty($field['required']))
                        <span class="text-red-500">*</span>
                        @endif
                </label>

                @if(in_array($field['type'], ['text', 'email', 'date', 'tel']))
                    <input
                        type="{{ $field['type'] }}"
                        name="{{ $name }}"
                        id="{{ $name }}"
                        value="{{ old($name) }}"
                        class="w-full border rounded px-3 py-2"
                        placeholder="{{ $field['placeholder'] ?? '' }}"
                        {{ !empty($field['required']) ? 'required' : '' }}>
                @endif

                @if($field['type'] === 'textarea')
                    <textarea
                        name="{{ $name }}"
                        id="{{ $name }}"
                        placeholder="{{ $field['placeholder'] ?? '' }}"
                        class="w-full border rounded px-3 py-2"
                        {{ !empty($field['reuired']) ? 'required' : '' }}>
                        {{ old($name) }}
                        </textarea>
                @endif

                @if($field['type'] === 'select')
                    <select
                        name="{{ $name }}"
                        id="{{ $name }}"
                        class="w-full border rounded px-3 py-2"
                        {{ !empty($field['required']) ? 'required' : '' }}>
                            <option value="">--- Select ---</option>
                            @foreach($field['options'] as $option)
                                <option value="{{ $option }}" {{ old($name) === $option ? 'selected' : '' }}>
                                    {{ $option }}
                                </option>
                        @endforeach
                    </select>
                @endif

                @if($field['type'] === 'radio')
                    <div class="flex gap-4 mt-1">
                        @foreach($field['options'] as $option)
                            <label>
                                <input
                                    type="radio"
                                    name="{{ $name }}"
                                    value="{{ $option }}"
                                    {{ old($name) === $option ? 'checked' : ''}}
                                    {{ !empty($field['required']) ? 'required' : '' }}>
                                    {{ $option }}
                            </label>
                        @endforeach
                    </div>
                @endif

                @if($field['type'] === 'file')
                    <input
                        type="file"
                        name="{{ $name }}"
                        id="{{ $name }}"
                        class="w-full border rounded px-3 py-2"
                        {{ !empty($field['required']) ? 'required' : '' }}>
                @endif
                @error($name)
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        @endforeach
        <button type="submit"  class="bg-blue-600 text-white px-4 py-2">Send</button>
    </form>
</div>

