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
            @endphp
            <div class="mb-4">
                <label for="{{ $name }}" class="block font-semibold mb-1">
                    {{ $field['label'] }}
                    @if (!empty($field['required']))
                    <span class="text-red-500">*</span>
                    @endif
                </label>

                @if (in_array($field['type'], ['text', 'email', 'date', 'tel']))
                <input type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}" value="{{ old($name) }}" class="w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                @endif

                @if ($field['type'] === 'textarea')
                <textarea name="{{ $name }}" id="{{ $name }}" class="w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>{{ old($name) ? preg_replace('/[\p{C}]/u', '', old(text_field)) : '' }}</textarea>
                @endif

                @if ($field['type'] === 'select')
                <select name="{{ $name }}" id="{{ $name }}" class="w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif {{ !empty($field['required']) ? 'required' : '' }}>
                    <option value="">--- Select ---</option>
                    @foreach ($field['options'] as $option)
                    <option value="{{ $option }}" {{ old($name) === $option ? 'selected' : '' }}>{{ $option }}</option>
                    @endforeach
                </select>
                @endif

                @if ($field['type'] === 'radio')
                <div class="flex gap-4 mt-1 w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}">
                    @foreach ($field['options'] as $option)
                    <label>
                        <input type="{{ $field['type'] }}" name="{{ $name }}" value="{{ $option }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                        {{ $option }}
                    </label>
                    @endforeach
                </div>
                @endif
                @if ($field['type'] === 'checkbox')
                <div class="flex gap-4 mt-1
                    {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}">
                    <label>
                        <input type="checkbox" name="{{ $name }}" value="1" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                        {{ $field['options'] }}
                    </label>

                </div>
                @endif

                @if ($field['type'] === 'checkbox-group')
                <div class="flex gap-4 mt-1 w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}">
                    @foreach ($field['options'] as $option)
                    <label>
                        <input type="checkbox" name="{{ $name }}[]" value="{{ $option }}" {{ !empty($field['required']) ? 'data-required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                        {{ $option }}
                    </label>
                    @endforeach
                </div>
                @endif

                @if ($field['type'] === 'file' || $field['type'] === 'url')
                <input type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}" class="w-full border rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}" placeholder="{{ $field['placeholder'] ?? '' }}" {{ !empty($field['required']) ? 'required' : '' }} @if($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                @endif
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (!form) return;

            // Server Error Initialization
            document.querySelectorAll('.text-red-500').forEach(errorElement => {
                const field = errorElement.previousElementSibling;
                if (field && field.matches('input, textarea, select')) {
                    validateField(field);
                }
            });

            // Event Handlers
            const fields = form.querySelectorAll('input, textarea, select, [data-required]');
            fields.forEach(field => {
                const events = ['input', 'blur'];
                if (['radio', 'checkbox'].includes(field.type)) events.push('change');
                events.forEach(event => field.addEventListener(event, () => validateField(field)));
            });

            // Validation on Submission
            form.addEventListener('submit', function(e) {
                let isValid = true;
                fields.forEach(field => {
                    if (!validateField(field)) isValid = false;
                });
                if (!isValid) {
                    e.preventDefault();
                    form.querySelector('.invalid') ?? scrollIntoView({
                        behavior: 'smooth'
                        , block: 'center'
                    , });
                }
            });

            // Validation Functions
            function validateField(field) {
                let isValid = checkValidity(field);
                updateStyles(field, isValid);
                return isValid;
            }

            function checkValidity(field) {
                if (field.type === 'checkbox' && field.name.endsWith('[]')) {
                    const checkboxes = document.querySelectorAll(`input[name="${field.name}"]`);
                    return checkboxes[0].hasAttribute('data-required') ?
                        Array.from(checkboxes).some(cb => cb.checked) : true;
                } else if (field.type === 'radio') {
                    const radios = document.querySelectorAll(`input[name="${field.name}"]`);
                    return field.required ? Array.from(radios).some(r => r.checked) : true;
                }
                return field.checkValidity();
            }

            function updateStyles(field, isValid) {
                const container = field.closest('div');
                const errorMessage = field.parentNode.querySelector('.error-message');

                if (isValid) {
                    field.classList.remove('invalid');
                    field.classList.remove('bg-[#fecaca]/25', 'border', 'border-[#f87171]');
                    field.classList.add('bg-gray-50', 'border', 'border-gray-200');
                    container?.classList.remove('bg-[#fecaca]/25', 'border-[#f87171]');
                    errorMessage?.remove();
                } else {
                    field.classList.add('invalid');
                    field.classList.add('bg-[#fecaca]/25', 'border', 'border-[#f87171]');
                    field.classList.remove('bg-gray-50', 'border-gray-200');
                    container?.classList.add('bg-[#fecaca]/25', 'border-[#f87171]');
                    showErrorMessage(field, errorMessage);
                }
            }

            function showErrorMessage(field, existingError) {
                const message = getErrorMessage(field);
                if (!existingError) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                    errorDiv.textContent = message;
                    field.parentNode.appendChild(errorDiv);
                } else {
                    existingError.textContent = message;
                }
            }

            function getErrorMessage(field) {
                if (field.validity.valueMissing) return 'This field is required.';
                if (field.validity.typeMismatch) {
                    if (field.type === 'email') return 'Invalid email format.';
                    if (field.type === 'url') return 'Invalid URL format.';
                }
                return 'Invalid input.';
            }

            function updateAllDependencies() {
                document.querySelectorAll('[data-depends-on]').forEach(function(depField) {
                    const dependsOn = depField.getAttribute('data-depends-on');
                    let disableWhen = depField.getAttribute('data-disable-when');
                    try {
                        disableWhen = JSON.parse(disableWhen);
                    } catch {}
                    const disableWhenBool = (disableWhen === true || disableWhen === 'true' || disableWhen === 1 || disableWhen === '1');
                    const controllerInputs = document.querySelectorAll(`[name="${dependsOn}"]`);
                    let shouldDisable = false;

                    if (controllerInputs.length > 0) {
                        const controller = controllerInputs[0];

                        // Checkbox-group
                        if (controller.type === 'checkbox' && dependsOn.endsWith('[]')) {
                            let valuesToCheck = Array.isArray(disableWhen) ? disableWhen : [disableWhen];
                            shouldDisable = !Array.from(controllerInputs).some(cb => cb.checked && valuesToCheck.includes(cb.value));
                        }
                        // Checkbox
                        else if (controller.type === 'checkbox') {
                            shouldDisable = controller.checked === disableWhenBool;
                        }
                        // Radio
                        else if (controller.type === 'radio') {
                            let valuesToCheck = Array.isArray(disableWhen) ? disableWhen : [disableWhen];
                            const checked = Array.from(controllerInputs).find(r => r.checked);
                            shouldDisable = !(checked && valuesToCheck.includes(checked.value));
                        }
                        // Select
                        else if (controller.tagName === 'SELECT') {
                            shouldDisable = controller.value == disableWhen;
                        }
                        // Other field (date, text, email)
                        else {
                            if (disableWhen === 'filled') {
                                shouldDisable = !!controller.value;
                            } else {
                                shouldDisable = controller.value == disableWhen;
                            }
                        }
                    }

                    // On/off field
                    if (depField.tagName === 'INPUT' || depField.tagName === 'SELECT' || depField.tagName === 'TEXTAREA') {
                        depField.disabled = shouldDisable;
                        depField.classList.toggle('opacity-50', shouldDisable);
                    } else {
                        depField.querySelectorAll('input, select, textarea').forEach(el => {
                            el.disabled = shouldDisable;
                            el.classList.toggle('opacity-50', shouldDisable);
                        });
                    }
                });
            }

            // Навешиваем обработчики на все контролирующие поля для любых событий
            document.querySelectorAll('[data-depends-on]').forEach(function(depField) {
                const dependsOn = depField.getAttribute('data-depends-on');
                document.querySelectorAll(`[name="${dependsOn}"]`).forEach(controller => {
                    ['input', 'change', 'blur'].forEach(eventName => {
                        controller.addEventListener(eventName, updateAllDependencies);
                    });
                });
            });
            form.addEventListener('reset', function() {
                // Даем браузеру сбросить значения, затем обновляем зависимости
                setTimeout(updateAllDependencies, 0);
            });
            // Инициализация при загрузке
            updateAllDependencies();
        });

    </script>
</body>
</html>
