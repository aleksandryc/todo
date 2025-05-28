<div
class="flex gap-4 mt-1
{{ !empty($field['required']) ? 'bg-[#fecaca]/25 border-[#f87171]' : '' }}"
>
    <label>
        <input
            type="checkbox"
            class="mr-2"
            name="{{ $name }}"
            value="{{ $field['value'] ?? '1' }}"
            {{ !empty($field['required']) ? 'required' : '' }}
            @if($depends)
                data-depends-on="{{ $depends['field'] }}"
                data-disable-when="{{ json_encode($depends['disable_when']) }}"
            @endif
            >{{ $field['options'] }}
    </label>
</div>
