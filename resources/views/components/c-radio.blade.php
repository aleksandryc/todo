<div
    class="flex gap-4 mt-1 w-full rounded px-3 py-2 focus:bg-green-100
    {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}">
    @foreach ($field['options'] as $option)
        <label class="p-1">
            <input
                type="{{ $field['type'] }}"
                name="{{ $name }}"
                value="{{ $option }}"
                class="mr-2"
                {{ !empty($field['required']) ? 'required' : '' }}
                @if($depends)
                    data-depends-on="{{ $depends['field'] }}"
                    data-disable-when="{{ json_encode($depends['disable_when']) }}"
                @endif
                >{{ $option }}
        </label>
    @endforeach
</div>
