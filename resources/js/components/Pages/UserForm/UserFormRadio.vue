<template>
  <label v-if="label" class="form-label"
    ><span v-if="required" class="text-red-700">*</span>{{ label }}</label
  >
  <div class="form-input flex gap-4" :class="containerClass">
    <div v-for="option in options" :key="option" class="form-label text-center">
      <input
        v-model="modelValueProxy"
        type="radio"
        class="mr-1.5 accent-brand"
        :name="name"
        :value="option"
        :required="required"
        :disabled="disabled" />
      <span>{{ option }}</span>
    </div>
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
  readonly options: string[];
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

const containerClass = computed(() =>
  props.required
    ? "border rounded-md border-red-400 bg-red-200 bg-opacity-25 text-red-900"
    : "bg-zinc-300 bg-opacity-25 rounded-md border border-gray-light",
);

const modelValueProxy = computed<string>({
  get: () => props.modelValue ?? "",
  set: (val: string) => emit("update:modelValue", val),
});
</script>
