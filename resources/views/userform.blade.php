<x-test>
    @foreach ($formFields as $field)
    <div>
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
        @error($field['name'])
    <div style="color: red;">{{ $message }}</div>
        @enderror
    </div>
    @endforeach
</x-test>
