<input type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}"
    class="w-full border rounded px-3 py-2 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}"
    placeholder="{{ $field['placeholder'] ?? '' }}" {{ !empty($field['required']) ? 'required' : '' }}
    @if ($depends) data-depends-on="{{ $depends['field'] }}" data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
