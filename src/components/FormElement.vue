<template>
  <div class="form-element" :class="`field-type-${field.type}`">
    <label v-if="field.label && field.type !== 'hidden'" :for="fieldId">{{ field.label }}</label>

    <!-- Простые текстовые инпуты, URL, Number, Date -->
    <input
      v-if="['text', 'email', 'password', 'number', 'date', 'url'].includes(field.type)"
      :type="field.type"
      :id="fieldId"
      :name="field.name"
      :value="modelValue"
      @input="updateValue(($event.target as HTMLInputElement).value)"
      :placeholder="field.placeholder"
      :required="isRequired"
      :disabled="isDisabled"
      :min="field.type === 'number' ? field.min : undefined"
      :max="field.type === 'number' ? field.max : undefined"
      :step="field.type === 'number' ? field.step : undefined"
      v-bind="field.attrs"
      :aria-invalid="!!error"
      :aria-describedby="error ? errorId : undefined"
    />

    <!-- Textarea -->
    <textarea
      v-else-if="field.type === 'textarea'"
      :id="fieldId"
      :name="field.name"
      :value="modelValue"
      @input="updateValue(($event.target as HTMLTextAreaElement).value)"
      :placeholder="field.placeholder"
      :required="isRequired"
      :disabled="isDisabled"
      v-bind="field.attrs"
      :aria-invalid="!!error"
      :aria-describedby="error ? errorId : undefined"
    ></textarea>

    <!-- Select -->
    <select
      v-else-if="field.type === 'select'"
      :id="fieldId"
      :name="field.name"
      :value="modelValue"
      @change="updateValue(($event.target as HTMLSelectElement).value)"
      :required="isRequired"
      :disabled="isDisabled"
      v-bind="field.attrs"
      :aria-invalid="!!error"
      :aria-describedby="error ? errorId : undefined"
    >
      <option v-if="field.placeholder" value="" disabled :selected="!modelValue">{{ field.placeholder }}</option>
      <option v-for="option in field.options" :key="option.value" :value="option.value">
        {{ option.label }}
      </option>
    </select>

    <!-- Checkbox -->
    <div v-else-if="field.type === 'checkbox'" class="checkbox-group">
      <!-- Если чекбокс один (булево значение) -->
      <template v-if="!field.options || field.options.length === 0">
        <input
          type="checkbox"
          :id="fieldId"
          :name="field.name"
          :checked="!!modelValue"
          @change="updateValue(($event.target as HTMLInputElement).checked)"
          :disabled="isDisabled"
          v-bind="field.attrs"
          :aria-invalid="!!error"
          :aria-describedby="error ? errorId : undefined"
        />
        <label :for="fieldId" class="checkbox-label-inline" v-if="field.label && !isGroupField">{{ field.label }}</label>
      </template>
      <!-- Если чекбоксов несколько (массив значений) -->
      <template v-else>
        <div v-for="option in field.options" :key="option.value" class="checkbox-item">
          <input
            type="checkbox"
            :id="`${fieldId}-${option.value}`"
            :name="field.name"
            :value="option.value"
            :checked="Array.isArray(modelValue) && modelValue.includes(option.value)"
            @change="updateCheckboxGroup(($event.target as HTMLInputElement).value, ($event.target as HTMLInputElement).checked)"
            :disabled="isDisabled"
            v-bind="field.attrs"
          />
          <label :for="`${fieldId}-${option.value}`">{{ option.label }}</label>
        </div>
      </template>
    </div>

    <!-- Radio Group -->
    <div v-else-if="field.type === 'radio'" class="radio-group" role="radiogroup" :aria-labelledby="field.label ? fieldId + '-label' : undefined">
      <div v-for="option in field.options" :key="option.value" class="radio-item">
        <input
          type="radio"
          :id="`${fieldId}-${option.value}`"
          :name="field.name"
          :value="option.value"
          :checked="modelValue === option.value"
          @change="updateValue(option.value)"
          :disabled="isDisabled"
          v-bind="field.attrs"
        />
        <label :for="`${fieldId}-${option.value}`">{{ option.label }}</label>
      </div>
    </div>

    <!-- Group of fields (recursion) -->
    <fieldset v-else-if="field.type === 'group'" class="form-group" :disabled="isDisabled">
        <legend v-if="field.label">{{ field.label }}</legend>
        <FormElement
            v-for="subField in (field as FormGroupField).fields"
            :key="subField.name"
            :field="subField"
            :modelValue="getNestedValue(subField.name)"
            @update:modelValue="updateNestedValue(subField.name, $event)"
            :error="getNestedError(subField.name)"
            :isGroupField="true"
            :isDisabled="props.isDisabled || getSubFieldDisabledState(subField)"
        />
    </fieldset>

    <!-- Notes Field -->
    <div v-else-if="field.type === 'notes'" class="notes-field">
      <p v-if="field.value" v-html="field.value"></p>
    </div>

    <!-- Hidden Field -->
    <input
      v-else-if="field.type === 'hidden'"
      type="hidden"
      :id="fieldId"
      :name="field.name"
      :value="modelValue"
    />

    <div v-else class="unknown-field-type">
      Неизвестный тип поля: {{ field.type }}
    </div>

    <div v-if="error && field.type !== 'hidden'" :id="errorId" class="error-message" role="alert">
      {{ error }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed, inject } from 'vue';
import type { FormField, FormGroupField, FormData, FormErrors, BaseFormField } from '../types';

const props = defineProps<{
  field: FormField;
  modelValue: any;
  error?: string;
  isGroupField?: boolean;
  isDisabled?: boolean;
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: any): void;
}>();

const parentFormData = inject<FormData | undefined>('formData', undefined);
const parentFormErrors = inject<FormErrors | undefined>('formErrors', undefined);

const fieldId = computed(() => `field-${props.field.name.replace(/\./g, '-')}`);
const errorId = computed(() => `${fieldId.value}-error`);

const isRequired = computed(() =>
  (props.field as BaseFormField).validation?.some((rule: any) => rule.type === 'required')
);

const updateValue = (value: any) => {
  emit('update:modelValue', value);
};

const updateCheckboxGroup = (optionValue: string | number, checked: boolean) => {
  let currentValue = Array.isArray(props.modelValue) ? [...props.modelValue] : [];
  if (checked) {
    if (!currentValue.includes(optionValue)) {
      currentValue.push(optionValue);
    }
  } else {
    currentValue = currentValue.filter(v => v !== optionValue);
  }
  emit('update:modelValue', currentValue);
};

const getNestedValue = (subFieldName: string) => {
    return props.modelValue && typeof props.modelValue === 'object' ? props.modelValue[subFieldName] : undefined;
};

const updateNestedValue = (subFieldName: string, value: any) => {
    const updatedGroupValue = {
        ...(props.modelValue && typeof props.modelValue === 'object' ? props.modelValue : {}),
        [subFieldName]: value
    };
    emit('update:modelValue', updatedGroupValue);
};

const getNestedError = (subFieldName: string): string | undefined => {
  if (parentFormErrors && parentFormErrors.value) {
    const fullPathSubFieldName = props.isGroupField ? `${props.field.name}.${subFieldName}` : subFieldName;
    return parentFormErrors.value[fullPathSubFieldName];
  }
  return undefined;
};

const getSubFieldDisabledState = (subField: BaseFormField): boolean => {
  if (props.isDisabled) return true;
  // Более сложная логика для dependsOn на уровне подполей должна обрабатываться в DynamicForm
  // и передаваться через props.isDisabled для каждого FormElement рекурсивно.
  // Здесь мы просто наследуем isDisabled от родительской группы.
  return false;
};

</script>

<style scoped>
.form-element {
  display: flex;
  flex-direction: column;
  margin-bottom: 0.75rem;
}

.form-element label {
  margin-bottom: 0.25rem;
  font-weight: bold;
}

.form-element input[type="text"],
.form-element input[type="email"],
.form-element input[type="password"],
.form-element input[type="number"],
.form-element input[type="date"],
.form-element input[type="url"],
.form-element textarea,
.form-element select {
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
  width: 100%;
  box-sizing: border-box;
}

.form-element input:disabled,
.form-element textarea:disabled,
.form-element select:disabled,
.form-element input[type="checkbox"]:disabled + label,
.form-element input[type="radio"]:disabled + label {
  color: #aaa;
  cursor: not-allowed;
}
.form-element input[type="checkbox"]:disabled,
.form-element input[type="radio"]:disabled {
   cursor: not-allowed;
}

.form-element *:disabled {
  background-color: #f0f0f0;
  /* cursor: not-allowed; */ /* Уже покрыто выше для специфичных элементов */
  opacity: 0.7;
}


.form-element input[aria-invalid="true"],
.form-element textarea[aria-invalid="true"],
.form-element select[aria-invalid="true"] {
  border-color: red;
}

.error-message {
  color: red;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.checkbox-group, .radio-group {
  display: flex;
  flex-direction: column;
}

.checkbox-item, .radio-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.25rem;
}

.checkbox-item input, .radio-item input {
  margin-right: 0.5rem;
}

.checkbox-label-inline {
 font-weight: normal;
 margin-left: 0.25rem;
}

.form-group {
  border: 1px solid #eee;
  padding: 1rem;
  border-radius: 4px;
  margin-top: 0.5rem;
}
.form-group[disabled] {
  background-color: #f9f9f9;
  opacity: 0.8;
}


.form-group legend {
  font-weight: bold;
  padding: 0 0.5rem;
}

.notes-field {
  padding: 0.5rem;
  background-color: #f9f9f9;
  border: 1px solid #eee;
  border-radius: 4px;
  font-size: 0.9rem;
  color: #333;
  margin-top: 0.25rem;
}
.notes-field p {
  margin: 0;
}


.unknown-field-type {
  color: orange;
  padding: 0.5rem;
  border: 1px dashed orange;
}
</style>
