<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="{{ mix('postcss/app.css') }}">
    <title>{{ $formConfig['title'] ?? 'User Form' }}</title>
</head>
<body class="min-h-screen bg-[#cfcdcc] print:bg-white print:pb-0">
    <div>
        @if($flash = session('message'))
        <div id="flash-message" class="text-3xl text-center text-green-700" role="alert">
            {{$flash}}
        </div>
        @endif
        <form class="card mx-auto sm:w-11/12 px-4 py-2 mt-2 rounded-md" action="{{ route('forms.submit', $formKey) }}" method="post" enctype="multipart/form-data">
        <h1 class="text-2xl text-center p-1 m-1">{{ $formConfig['title'] ?? 'Untiteled form'}}</h1>
        <p class="text-xs text-center max-w-lg md:text-sm p-2 m-1 mx-auto">{{ $formConfig['description'] ?? '' }}</p>
            @csrf
            @foreach ($formComponents as $name => $field)
            @php
            $depends = $field['depends_on'] ?? null;
            // Skipping fields that are related (related-to)
            if (!empty($field['related-to'])) {
            continue;
            }
            @endphp
            <div class="mb-4" {{ !empty($field['hidden']) ? 'hidden' : '' }}>

                <label for="{{ $name }}" class="form-label">
                    @if (!empty($field['required']))
                    <span class="text-red-500">*</span>
                    @endif
                    {{ $field['label'] ?? '' }}
                </label>

                @if (in_array($field['type'], ['text', 'email', 'date', 'tel']))
                <input type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}" value="{{ !empty($field['value']) ? $field['value'] : old($name) }}" class="form-input focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>

                @endif
                @if($field['type'] === 'number')
                <input
                    type="{{ $field['type'] }}"
                    name="{{ $name }}"
                    id="{{ $name }}"
                    step="{{ $field['step'] ?? '' }}"
                    min="{{ $field["min"] ?? '' }}"
                    max="{{ $field["max"] ?? '' }}"
                    minlength="{{ $field["minlength"] ?? '' }}"
                    maxlength="{{ $field["maxlength"] ?? '' }}"
                    value="{{ !empty($field['value']) ? $field['value'] : old($name) }}"
                    class="form-input focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}"
                    placeholder="{{ $field['placeholder'] ?? '' }}"
                    {{ !empty($field['required']) ? 'required' : '' }}
                @if($depends)
                    data-depends-on="{{ $depends['field'] }}"
                    data-disable-when="{{ json_encode($depends['disable_when']) }}"
                @endif>
                @endif

                @if ($field['type'] === 'textarea')
                <textarea name="{{ $name }}" id="{{ $name }}" class="form-input focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>{{ old($name) }}</textarea>
                @endif

                @if ($field['type'] === 'select')
                <select name="{{ $name }}" id="{{ $name }}" class="form-input focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif {{ !empty($field['required']) ? 'required' : '' }}>
                    <option value="">--- Select ---</option>
                    @foreach ($field['options'] as $option)
                    <option value="{{ $option }}" {{ old($name) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @endif

                @if ($field['type'] === 'radio')
                <div class="flex gap-4 mt-1 w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}">
                    @foreach ($field['options'] as $option)
                    <label class="form-label text-center">
                        <input type="{{ $field['type'] }}" class="form-input" name="{{ $name }}" value="{{ $option }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                        {{ $option }}
                    </label>
                    @endforeach
                </div>
                @endif
                @if ($field['type'] === 'checkbox')
                <div class="flex gap-4 mt-1
                    {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}">
                    <label class="form-label text-center">
                        <input type="checkbox" name="{{ $name }}" value="{{ $field['value'] ?? '1' }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                        {{ $field['options'] }}
                    </label>

                </div>
                @endif

                @if ($field['type'] === 'checkbox-group')
                <div class="flex flex-col lg:flex-row gap-4 mt-1 rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}">
                    @foreach ($field['options'] as $option)
                    <div class="flex flex-col w-full sm:w-auto">
                        <label class="form-label text-center">
                            <input type="checkbox" name="{{ $name }}[]" value="{{ $option }}" {{ !empty($field['required']) ? 'data-required' : '' }} class="form-input" @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                            <span class="ml-2 text-sm sm:text-base">{{ $option }}</span>
                        </label>

                        @if(!empty($formComponents))
                        @foreach($formComponents as $relatedName => $relatedField)
                        @if(!empty($relatedField['related-to']) && $relatedField['related-to'] === $option)
                        <div class="mt-2 ml-4 sm:ml-8 w-full sm:w-64">
                            <label for="{{ $relatedName }}" class="form-label">
                                @if (!empty($relatedField['required']))
                                <span class="text-red-500">*</span>
                                @endif
                                {{ $relatedField['label'] }}
                            </label>
                            @if (in_array($relatedField['type'], ['text', 'email', 'date', 'tel']))
                            <input type="{{ $relatedField['type'] }}" name="{{ $relatedName }}" id="{{ $relatedName }}" value="{{ old($relatedName) }}" class="form-input focus:bg-green-100 {{ !empty($relatedField['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : ' border' }}" placeholder="{{ $relatedField['placeholder'] ?? '' }}" {{ !empty($relatedField['required']) ? 'required' : '' }} @if(!empty($relatedField['depends_on'])) data-depends-on="{{ $relatedField['depends_on']['field'] }}" data-disable-when="{{ json_encode($relatedField['depends_on']['disable_when']) }}" @endif>
                            @endif
                            @error($relatedName)
                            <p class="text-red-500 text-xs sm:text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif
                        @endforeach
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

                @if ($field['type'] === 'file' || $field['type'] === 'url')
                <input type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}" class="form-input {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}" placeholder="{{ $field['placeholder'] ?? '' }}" accept={{ $field['accept'] ?? '.jpg, .jpeg, .png, .pdf, .doc, .docs, .xls, .xlsx' }} {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                @endif

                @if(!empty($field['type'] === 'notes'))
                <div class="{{ !empty($field['class']) ? $field['class'] : '' }}">{!! nl2br($field['value']) !!}</div>
                @endif

                @error($name)
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach
            <div class="mb-4">
                <label for="mailRecipients" class="form-label">
                    <span class="text-red-500">*</span>
                    Send to:
                </label>
                <input type="email" name="mailRecipients" id="mailRecipients" value="{{ old('mailRecipients') }}"
                class="form-input focus:bg-green-100 {{ !empty('required') ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" placeholder="example@mail.com" required>
                @error("mailRecipients")
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="mb-4">
                <label for="ccRecipients" class="form-label">
                    Copy to:
                </label>
                <input type="email" multiple name="ccRecipients" id="ccRecipients" value="{{ old('ccRecipients') }}" class="form-input focus:bg-green-100" placeholder="example@mail.com, another@mailaddres.com">
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
    @vite(['js/Composables/ValidationAndFormationForUserForms.js'])
</body>
</html>
