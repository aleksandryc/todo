<!doctype html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite(['postcss/app.css'])
    <title>{{ $formComponent['title'] ?? 'User Form' }}</title>
</head>
<body>
    <div class="container mx-auto space-y-6">
        <form action="{{ route('forms.store') }}" method="POST" class="card space-y-2" enctype="multipart/form-data">
            <h2 class="text-center text-2xl">{{ $formComponent['title'] ?? "Untitled Form" }}</h2>
            <p class="text-center">{{ $formComponent['description'] ?? "" }}</p>
            @csrf
            @foreach ($formComponent['fields'] as $name => $field)
            @if (!isset($field['related-to']) || empty($field['related-to']))
            <div {{ !empty($field['_hidden_fields']) ? 'hidden' : '' }}>
                <label for="{{ $name }}" class="wrapper">
                    <div class="form-label mb-2">
                        @if (!empty($field['required']))
                        <span class="font-bold text-red-600">*</span>
                        @endif
                        {{ $field['label'] ?? '' }}
                    </div>
                    <x-dynamic-component
                        :component="'userforms.' . $field['type'] . '-input'"
                        :name="$name"
                        :field="$field"
                        :formComponent="$formComponent['fields']"

                        />
                    @error($name)
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </label>
            </div>
            @endif
            @endforeach
            <div>
                <label for="mailRecipients" class="wrapper">
                    <div class="form-label mb-2">
                        <span class="text-red-500">*</span>
                        Send to: </div>
                </label>
                <input type="email" name="mailRecipients" id="mailRecipients" value="{{ old('mailRecipients') }}" class="form-input focus:bg-green-100 {{ !empty('required') ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}" placeholder="example@mail.com" required>
                @error("mailRecipients")
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="ccRecipients" class="wrapper">
                    <div class="form-label mb-2">Copy to: </div>
                </label>
                <input type="email" multiple name="ccRecipients" id="ccRecipients" value="{{ old('ccRecipients') }}" class="form-input focus:bg-green-100" placeholder="example@mail.com, another@mailaddres.com">
                @error('ccRecipients')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-row justify-end space-x-2">
                <a href="{{ url()->previous() }}" class="btn btn-gray">Back</a>
                <button type="reset" class="btn btn-coral">Reset</button>
                <button type="submit" class=" btn btn-green">Send</button>
            </div>
        </form>
    </div>
<script>



document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    if (!form) return;

    document.querySelectorAll('.text-red-500').forEach((errorElement) => {
        const field = errorElement.previousElementSibling;
        if (field && field.matches('input, textarea, select')) {
            validateField(field);
        }
    });

    const fields = form.querySelectorAll('input, textarea, select, [data-required]');
    fields.forEach((field) => {
        const events = ['input', 'blur'];
        if (['radio', 'checkbox'].includes(field.type)) events.push('change');
        events.forEach((event) => field.addEventListener(event, () => validateField(field)));
    });

    form.addEventListener('submit', function (e) {
        let isValid = true;
        fields.forEach((field) => {
            if (!validateField(field)) isValid = false;
        });
        if (!isValid) {
            e.preventDefault();
            const firstInvalid = form.querySelector('.invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                });
            }
        }
    });

    function validateField(field) {
        let isValid = checkValidity(field);
        updateStyles(field, isValid);
        return isValid;
    }

    function checkValidity(field) {
        if (field.type === 'checkbox' && field.name.endsWith('[]')) {
            const checkboxes = document.querySelectorAll(`input[name="${field.name}"]`);
            return checkboxes[0].hasAttribute('data-required') ? Array.from(checkboxes).some((cb) => cb.checked) : true;
        } else if (field.type === 'radio') {
            const radios = document.querySelectorAll(`input[name="${field.name}"]`);
            return field.required ? Array.from(radios).some((r) => r.checked) : true;
        }
        return field.checkValidity();
    }

    function updateStyles(field, isValid) {
        const container = field.closest('div');
        const errorMessage = field.parentNode.querySelector('.error-message');
        const isCheckbox = field.type === 'checkbox';

        if (isValid) {
            field.classList.remove('invalid');
            if (!isCheckbox) {
                field.classList.remove('bg-[#fecaca]/25', 'border-[#f87171]');
                field.classList.add('bg-gray-50', 'border-gray-200');
            }
            container?.classList.remove('bg-[#fecaca]/25', 'border-[#f87171]');
            errorMessage?.remove();
        } else {
            field.classList.add('invalid');
            if (!isCheckbox) {
                field.classList.add('bg-[#fecaca]/25', 'border-[#f87171]');
                field.classList.remove('bg-gray-50', 'border-gray-200');
            }
            container?.classList.add('bg-[#fecaca]/25', 'border-[#f87171]');
            showErrorMessage(field, errorMessage);
        }

        if (isCheckbox) {
            const checkboxContainer = field.closest('.flex.gap-4');
            if (checkboxContainer) {
                if (isValid) {
                    checkboxContainer.classList.remove('bg-[#fecaca]/25', 'border-[#f87171]');
                    checkboxContainer.classList.add('bg-gray-50', 'border-gray-200');
                } else {
                    checkboxContainer.classList.add('bg-[#fecaca]/25', 'border-[#f87171]');
                    checkboxContainer.classList.remove('bg-gray-50', 'border-gray-200');
                }
            }
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
        document.querySelectorAll('[data-depends-on]').forEach(function (depField) {
            const dependsOn = depField.getAttribute('data-depends-on');
            let disableWhen = depField.getAttribute('data-disable-when');
            try {
                disableWhen = JSON.parse(disableWhen);
            } catch {}
            const disableWhenBool = disableWhen === true || disableWhen === 'true' || disableWhen === 1 || disableWhen === '1';
            const controllerInputs = document.querySelectorAll(`[name="${dependsOn}"]`);
            let shouldDisable = false;

            if (controllerInputs.length > 0) {
                const controller = controllerInputs[0];

                // Checkbox-group
                if (controller.type === 'checkbox' && dependsOn.endsWith('[]')) {
                    let valuesToCheck = Array.isArray(disableWhen) ? disableWhen : [disableWhen];
                    shouldDisable = !Array.from(controllerInputs).some((cb) => cb.checked && valuesToCheck.includes(cb.value));
                }
                // Checkbox
                else if (controller.type === 'checkbox') {
                    shouldDisable = controller.checked === disableWhenBool;
                }
                // Radio
                else if (controller.type === 'radio') {
                    let valuesToCheck = Array.isArray(disableWhen) ? disableWhen : [disableWhen];
                    const checked = Array.from(controllerInputs).find((r) => r.checked);
                    shouldDisable = !(checked && valuesToCheck.includes(checked.value));
                }
                // Select
                else if (controller.tagName === 'SELECT') {
                    shouldDisable = controller.value == disableWhen;
                }

                else {
                    if (disableWhen === 'filled') {
                        shouldDisable = !!controller.value;
                    } else {
                        shouldDisable = controller.value == disableWhen;
                    }
                }
            }

            if (depField.tagName === 'INPUT' || depField.tagName === 'SELECT' || depField.tagName === 'TEXTAREA') {
                depField.disabled = shouldDisable;
                depField.classList.toggle('opacity-50', shouldDisable);
            } else {
                depField.querySelectorAll('input, select, textarea').forEach((el) => {
                    el.disabled = shouldDisable;
                    el.classList.toggle('opacity-50', shouldDisable);
                });
            }
        });
    }

    document.querySelectorAll('[data-depends-on]').forEach(function (depField) {
        const dependsOn = depField.getAttribute('data-depends-on');
        document.querySelectorAll(`[name="${dependsOn}"]`).forEach((controller) => {
            ['input', 'change', 'blur'].forEach((eventName) => {
                controller.addEventListener(eventName, updateAllDependencies);
            });
        });
    });
    form.addEventListener('reset', function () {
        setTimeout(updateAllDependencies, 0);
    });
    updateAllDependencies();
});


</script>
</body>
</html>
