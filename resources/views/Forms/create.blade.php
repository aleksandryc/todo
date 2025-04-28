<h1>{{ $formTitle }}</h1>

@if(isset($formFields) && is_array($formFields) && count($formFields) > 0)

<form action="{{ route('forms.submit', $formKey) }}" method="post"
    enctype="multipart/form-data">
    @csrf
    @foreach($formFields as $field)
    <div>
        <label for="{{ $field['name'] }}">{{ $field['label']}}</label>
        @if($field['type'] === 'texarea')
        <textarea
            name="{{ $field['name'] }}"
            id="{{ $field['name'] }}"
            placeholder="{{ $field['placeholder'] ?? '' }}">{{ old($field['name']) }}</textarea>
        @elseif( $field['type'] === 'file')
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

    <button type="submit">Send</button>
</form>

@else
    <h2>Form does not created!</h2>
@endif
