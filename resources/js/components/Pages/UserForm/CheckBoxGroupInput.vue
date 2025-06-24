<template>
  <div>
    <label v-if="label" class="form-label">
      <span v-if="required" class="mr-1 text-red-700">*</span>
      {{ label }}
    </label>
    <div :class="containerClass" class="flex p-1">
      <div
        v-for="(option, index) in options"
        :key="generateKey(option, index)"
        class="flex w-full flex-col sm:w-auto">
        <label :for="getOptionId(option)" class="form-label text-center">
          <input
            :id="getOptionId(option)"
            v-model="modelValueProxy"
            type="checkbox"
            class="m-1 justify-center accent-brand"
            :name="name"
            :value="option"
            :required="required"
            :disabled="disabled" />
          <span class="mx-2 text-sm sm:text-base">{{ option }}</span>
        </label>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

interface Props {
  readonly modelValue?: string[];
  readonly name: string;
  readonly options: string[];
  readonly required?: boolean;
  readonly label?: string;
  readonly disabled?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
  modelValue: () => [],
  required: false,
  label: "",
});

const emit = defineEmits<{
  "update:modelValue": [value: string[]];
  "change": [value: string[]];
}>();

const modelValueProxy = computed<string[]>({
  get: () => props.modelValue ?? [],
  set: (val) => emit("update:modelValue", val),
});

const containerClass = computed(() =>
  props.required
    ? "border rounded-md border-red-400 bg-red-200 bg-opacity-25 text-red-900"
    : "bg-zinc-300 bg-opacity-25 rounded-md border border-gray-light",
);

function getOptionId(option: string): string {
  return `${props.name}-${option.replace(/\s+/g, "-").toLowerCase()}`;
}

function generateKey(option: string, index: number): string {
  return `${option}-${index}`;
}
</script>
