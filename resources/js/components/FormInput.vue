<template>
  <div>
    <label
      class="wrapper"
      :data-type="type"
      :style="{ '--suffix': JSON.stringify(suffix) }"
      :title="title">
      <div class="form-label mb-2">
        <span v-if="required" class="font-bold text-red-600">*</span>
        {{ label }}:
        <input
          v-if="type === 'checkbox' && !$slots.default"
          ref="inputElement"
          :checked="modelValue as boolean"
          type="checkbox"
          @change="update" />
      </div>
      <transition-expand>
        <div v-if="error" class="flex transition-all">
          <div
            class="card my-2 mb-4 overflow-hidden border-amber-700 bg-amber-200 text-amber-700">
            <font-awesome-icon icon="exclamation-triangle" />
            {{ error }}
          </div>
        </div>
      </transition-expand>
      <slot>
        <input
          v-if="type !== 'checkbox'"
          ref="inputElement"
          class="form-input"
          :class="{
            'h-10': type?.toLowerCase() === 'color',
            'sentry-mask': type === 'password',
          }"
          :type="type"
          :step="step"
          :disabled="disabled"
          :[attrMin]="min"
          :[attrMax]="max"
          :[destValue]="modelValue"
          :minlength="min"
          :maxlength="max"
          :required="required"
          :placeholder="placeholder ?? label"
          :pattern="pattern"
          @input="update" />
      </slot>
    </label>
  </div>
</template>

<script lang="ts" setup>
import { computed, onMounted, ref, watch } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import TransitionExpand from "$/Components/Transitions/Expand.vue";
import { library } from "@fortawesome/fontawesome-svg-core";
import { faExclamationTriangle } from "@fortawesome/free-solid-svg-icons";
import { useDebounceFn } from "@vueuse/shared";

export type Value = string | number | boolean | null | undefined;

library.add(faExclamationTriangle);

const $props = withDefaults(
  defineProps<{
    disabled?: boolean;
    modelValue?: Value;
    label?: string;
    placeholder?: string;
    error?: string;
    min?: number | string;
    max?: number | string;
    type?: string;
    step?: number | string;
    required?: boolean;
    pattern?: string;
    formatter?: (event: Event) => Value;
    suffix?: string;
    emptyStringEmits?: "" | undefined | null;
    title?: string;
  }>(),
  {
    disabled: false,
    modelValue: null,
    label: undefined,
    placeholder: undefined,
    error: undefined,
    min: undefined,
    max: undefined,
    type: undefined,
    step: undefined,
    required: false,
    pattern: undefined,
    formatter: undefined,
    suffix: undefined,
    emptyStringEmits: null,
  },
);

const emits = defineEmits<{
  (e: "update:modelValue", value: Value): void;
  (e: "change"): void;
}>();

const minMaxPostfix = computed(() =>
  ["text", "password", "email", undefined].includes($props.type?.toLowerCase())
    ? "length"
    : "",
);

const destValue = computed(() =>
  $props.type !== "checkbox" ? "value" : "checked",
);
const attrMin = computed(() => "min" + minMaxPostfix.value);
const attrMax = computed(() => "max" + minMaxPostfix.value);

const inputElement = ref<HTMLInputElement>();
onMounted(() =>
  watch(
    () => $props.error,
    () => inputElement.value?.setCustomValidity($props.error ?? ""),
    { immediate: true },
  ),
);

const update = (event: Event) => {
  const target = event.target as HTMLInputElement;
  let value = target.value as Value;
  const { formatter, type } = $props;
  if (formatter) value = formatter(event) ?? value;
  if (type === "checkbox") value = target.checked;
  if (value === "") value = $props.emptyStringEmits;
  emits("update:modelValue", value);
  change();
};

const change = useDebounceFn(() => emits("change"), 350, { maxWait: 3000 });
</script>

<style scoped>
.wrapper {
  display: inline-block;
  width: 100%;
  position: relative;
}

@media (hover: hover) and (pointer: fine) {
  .wrapper[data-type="number"]:hover::after,
  .wrapper[data-type="number"]:focus-within::after {
    right: 1.75em;
  }
}

.wrapper:after {
  position: absolute;
  bottom: 8px;
  right: 0.5em;
  transition: all 0.05s ease-in-out;
  content: var(--suffix);
}

/* handle Firefox (arrows always shown) */
@supports (-moz-appearance: none) {
  .wrapper[data-type="number"] input {
    -moz-appearance: textfield;
  }
  .wrapper[data-type="number"]:focus-within input,
  .wrapper[data-type="number"]:hover input {
    -moz-appearance: auto;
  }
}

/* handle Safari (arrows always shown)*/
@supports (-webkit-appearance: none) {
  .wrapper[data-type="number"] input::-webkit-outer-spin-button,
  .wrapper[data-type="number"] input::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }
  .wrapper[data-type="number"]:focus-within input::-webkit-outer-spin-button,
  .wrapper[data-type="number"]:focus-within input::-webkit-inner-spin-button,
  .wrapper[data-type="number"]:hover input::-webkit-outer-spin-button,
  .wrapper[data-type="number"]:hover input::-webkit-inner-spin-button {
    -webkit-appearance: inner-spin-button;
  }
}
</style>
