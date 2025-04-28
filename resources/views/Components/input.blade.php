@foreach ($formFields as $field)
    <div class="mb-4">
        <label for="{{ $field['name'] }}">{{ $field['name'] }}</label>

        @if($field['type'] === 'file')
        <input
            type="file"
            name="{{ $field['name'] }}"
            id="{{ $field['name'] }}">
        @else
        <input
            type="{{ $field['type'] }}"
            name="{{ $field['name'] }}"
            id="{{ $field['name'] }}"
            value="{{ old($field['name']) }}"
            placeholder="{{ $field['placeholder'] ?? '' }}">
        @endif
    </div>
    @endforeach
