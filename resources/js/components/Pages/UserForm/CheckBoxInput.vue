<template>
  <div
    class="mt-1 flex w-full gap-4 rounded px-3 py-2"
    :class="
      required
        ? 'border-red-400 bg-red-200 bg-opacity-25 text-red-900'
        : 'rounded-md border border-gray-light bg-zinc-300 bg-opacity-25'
    ">
    <label class="form-label text-center">
      <span v-if="required" class="text-red-700">*</span>{{ label }}
    </label>
    <input
      v-model="checked"
      type="checkbox"
      class="accent-brand"
      :name="name"
      :value="options"
      :required="required"
      :disabled="disabled" />
    <span>{{ options }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";

interface Props {
  readonly name: string;
  readonly label?: string;
  readonly modelValue?: string;
  readonly required?: boolean;
  readonly disabled?: boolean;
  readonly options: string;
}

const props = withDefaults(defineProps<Props>(), {
  label: "",
  modelValue: "",
  required: false,
});

const emit = defineEmits<{
  (e: "update:modelValue", value: string): void;
  (e: "change", value: string): void;
}>();

const checked = computed<boolean>({
  get: () => props.modelValue === props.options,
  set: (val: boolean) => {
    emit("update:modelValue", val ? props.options : "");
  },
});
</script>
