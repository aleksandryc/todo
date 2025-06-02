<div
    class="flex flex-col md:flex-row gap-4 mt-1 w-full rounded p-2
    {{ !empty($field['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : 'bg-gray-50 border border-gray-200' }}">
    @foreach ($field['options'] as $option)
        <div class="flex flex-col w-fit sm:w-auto">
            <label class="flex items-start sm:items-center">
                <input type="checkbox" name="{{ $name }}[]" value="{{ $option }}"
                    {{ !empty($field['required']) ? 'data-required' : '' }} class="form-checkbox mt-1 sm:mt-0"
                    @if ($depends) data-depends-on="{{ $depends['field'] }}"
                                    data-disable-when="{{ json_encode($depends['disable_when']) }}" @endif>
                <span class="ml-2 text-sm sm:text-base">{{ $option }}</span>
            </label>

            @if (!empty($formComponents))
                @foreach ($formComponents as $relatedName => $relatedField)
                    @if (!empty($relatedField['related-to']) && $relatedField['related-to'] === $option)
                        <div class="mt-2 p-1 w-fit sm:w-64">
                            <label for="{{ $relatedName }}" class="block text-sm mb-1">
                                {{ $relatedField['label'] }}
                                @if (!empty($relatedField['required']))
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            @if (in_array($relatedField['type'], ['text', 'email', 'date', 'tel']))
                                <input type="{{ $relatedField['type'] }}" name="{{ $relatedName }}"
                                    id="{{ $relatedName }}" value="{{ old($relatedName) }}"
                                    class="w-fit rounded p-2 text-sm focus:bg-green-100 {{ !empty($relatedField['required']) ? 'bg-[#fecaca]/25 border border-[#f87171]' : ' border' }}"
                                    placeholder="{{ $relatedField['placeholder'] ?? '' }}"
                                    {{ !empty($relatedField['required']) ? 'required' : '' }}
                                    @if (!empty($relatedField['depends_on'])) data-depends-on="{{ $relatedField['depends_on']['field'] }}"
                                                    data-disable-when="{{ json_encode($relatedField['depends_on']['disable_when']) }}" @endif>
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
