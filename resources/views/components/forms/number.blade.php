@props([
    "step" => "any",
    "min" => "",
    "max" => "",
    "minlength" => "",
    "maxlength" => "",
    "value" => "",
    "placeholder" => "For entering numbers...",
])


{{-- <input
    type="{{ $field['type'] }}" name="{{ $name }}" id="{{ $name }}" step="{{ $field['step'] ?? '' }}"
    min="{{ $field['min'] ?? '' }}" max="{{ $field['max'] ?? '' }}" minlength="{{ $field['minlength'] ?? '' }}"
    maxlength="{{ $field['maxlength'] ?? '' }}" value="{{ !empty($field['value']) ? $field['value'] : old($name) }}"
    class="w-full rounded px-3 py-2 focus:bg-green-100 {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}"
    placeholder="{{ $field['placeholder'] ?? '' }}" {{ !empty($field['required']) ? 'required' : '' }}
    @if ($depends) data-depends-on="{{ $depends['field'] }}"
                    data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
 --}}
<input type="number" {{ $attributes->merge([
    "class" => "w-full rounded px-3 py-2 focus:bg-green-100", ]) }}
    step = "{{ $step }}"
    min = "{{ $min }}"
    max = "{{ $max }}"
    minlength = "{{ $minlength }}"
    maxlength = "{{ $maxlength }}"
    value = "{{ $value }}"
    placeholder = "{{ $placeholder }}"
  />
