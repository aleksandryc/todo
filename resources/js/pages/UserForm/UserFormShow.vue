<template>
  <div class="card">
    <form @submit.prevent="onSubmit">
      <h2 class="text-center text-3xl">{{ formComponent.title }}</h2>
      <input type="hidden" name="formKey" :value="form.formKey" />
      <div
        v-for="(field, key) in formComponent.fields"
        :key="key as string"
        class="m-1">
        <FormInput
          v-if="field.type === 'select'"
          v-bind="getFieldAttrs(field, key as string)"
          @change="form.validate(key as keyof MyFormDataType)">
          <BetterSelectBasic
            v-model:selected="form[key as keyof typeof props.formComponent.fields]"
            v-bind="getFieldAttrs(field, key as string)"
            :values="Array.isArray(field.value) ? field.value : []"
            :require-search="false" />
        </FormInput>
        <component
          :is="componentMap[field.type] || 'div'"
          v-model="form[key as keyof typeof props.formComponent.fields]"
          v-bind="getFieldAttrs(field, key as string)"
          @change="form.validate(key as keyof MyFormDataType)" />
        <div v-if="field.type === 'notes'" class="my-3">
          <div v-if="field.label" class="text-sm">{{ field.label }}</div>
          <div
            v-if="field.value"
            class="text-wrap rounded-md bg-brand p-2 pl-3 text-white">
            {{ field.value }}
          </div>
        </div>
      </div>
      <div class="m-1">
        <FormInput
          v-model="form.mailRecipients"
          label="Send to"
          type="email"
          placeholder="example@mail.com"
          :required="true"
          :error="form.errors.mailRecipients"
          @change="form.validate('mailRecipients')" />

        <FormInput
          v-model="form.ccRecipients"
          label="Copy to"
          type="email"
          placeholder="example@mail.com"
          :error="form.errors.ccRecipients"
          @change="form.validate('ccRecipients')" />
      </div>

      <div class="mt-2 flex flex-row justify-end space-x-2">
        <BasicButton is="InertiaLink" theme="gray" href="/forms">
          Back
        </BasicButton>
        <FormResetButton :form="form" />

        <BasicButton
          is="button"
          theme="green"
          type="submit"
          :disabled="form.processing"
          >Send
        </BasicButton>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useForm } from "laravel-precognition-vue-inertia";
import type {
  FormComponent,
  FormDataType,
  FormField,
  KnownFieldType,
  Dependency,
} from "./types";
import FormInput from "$/Components/FormInput.vue";
import CheckBoxInput from "$/Components/Pages/UserForm/CheckBoxInput.vue";
import CheckBoxGroupInput from "$/Components/Pages/UserForm/CheckBoxGroupInput.vue";
import UserFormRadio from "$/Components/Pages/UserForm/UserFormRadio.vue";
import BasicButton from "$/Components/BasicButton.vue";
import FormResetButton from "$/Components/FormResetButton.vue";
import BetterSelectBasic from "$/Components/BetterSelectBasic.vue";

const props = defineProps<{
  formComponent: FormComponent;
}>();

const componentMap: Record<KnownFieldType, unknown> = {
  "text": FormInput,
  "number": FormInput,
  "date": FormInput,
  "textarea": FormInput,
  "radio": UserFormRadio,
  "checkbox": CheckBoxInput,
  "checkbox-group": CheckBoxGroupInput,
  "file": FormInput,
  "url": FormInput,
  "email": FormInput,
  "tel": FormInput,
  "select": FormInput, // Added: Assuming FormInput can handle select via type or it's a placeholder
  "notes": "div", // Added: Special handling for notes
};

// Define the type for our form data using the generic FormDataType
type MyFormDataType = FormDataType<typeof props.formComponent.fields>;

// Type for the part of MyFormDataType that corresponds to formComponent.fields
type FormFieldsDataType = Pick<
  MyFormDataType,
  keyof typeof props.formComponent.fields
>;

const initialFieldValues = Object.fromEntries(
  Object.entries(props.formComponent.fields).map(([key, field]) => {
    let value: string | number | boolean | string[] | null;
    if (field.value !== undefined) {
      switch (field.type) {
        case "checkbox-group":
          value = Array.isArray(field.value) ? (field.value as string[]) : [];
          break;
        case "checkbox":
          value = typeof field.value === "boolean" ? field.value : false;
          break;
        case "number":
          value = typeof field.value === "number" ? field.value : null;
          break;
        default: // Includes string, date, select, etc.
          value = String(field.value ?? ""); // Ensure it's a string
      }
    } else {
      // Default initial values based on field type defined in MyFormDataType
      switch (field.type) {
        case "checkbox-group":
          value = [];
          break;
        case "checkbox":
          value = false;
          break;
        case "number":
          value = null;
          break;
        default:
          value = "";
      }
    }
    return [key, value];
  }),
) as FormFieldsDataType;

const form = useForm<MyFormDataType>("post", "/forms", {
  ...initialFieldValues,
  mailRecipients: "",
  ccRecipients: "",
  formKey: props.formComponent.formKey,
});

const disabledStates = computed(() => {
  const result: Record<string, boolean> = {};

  for (const [fieldKey, field] of Object.entries(props.formComponent.fields)) {
    let isDisabled = false;

    const dependencies = Array.isArray(field.depends_on)
      ? field.depends_on
      : field.depends_on
        ? [field.depends_on]
        : [];

    if (dependencies.length === 0) {
      result[fieldKey] = false;
      continue;
    }

    isDisabled = !!dependencies[0]?.disabled; // Initial assumption based on first dependency

    dependencies.forEach((dep: Dependency) => {
      const depFieldKey = dep.field.replace(
        /\[\]$/,
        "",
      ) as keyof FormFieldsDataType;
      const depValue = form[depFieldKey];

      if (
        "active_when" in dep &&
        dep.active_when !== undefined &&
        checkDependencyCondition(depValue, dep.active_when)
      ) {
        isDisabled = false;
      }

      if (
        "disable_when" in dep &&
        dep.disable_when !== undefined &&
        checkDependencyCondition(depValue, dep.disable_when)
      ) {
        isDisabled = true;
      }
    });
    result[fieldKey] = isDisabled;
  }
  return result;
});

function checkDependencyCondition(
  depValue: string | number | boolean | string[] | null, // Value from form data
  when: boolean | string | string[] | undefined, // Condition from dependency config
): boolean {
  if (when === undefined) return false;

  if (when === "filled") {
    if (Array.isArray(depValue)) return depValue.length > 0;
    return depValue !== undefined && depValue !== null && depValue !== "";
  }

  if (Array.isArray(when)) {
    // Condition is an array of possible values
    if (Array.isArray(depValue)) {
      // Field value is an array (e.g., checkbox-group)
      return when.some((val) => depValue.includes(val as never)); // Cast needed if types don't overlap perfectly
    }
    // Field value is a single value, check if it's in the 'when' array
    return when.includes(depValue as never); // Cast needed
  }

  // 'when' is a single specific value (string or boolean)
  if (Array.isArray(depValue)) {
    // Field value is an array, check if 'when' is one of its elements
    return depValue.includes(when as never); // Cast needed
  }

  if (typeof when === "boolean") {
    return Boolean(depValue) === when;
  }
  // General comparison for string/number
  return depValue == when;
}

const dynamicRequired = computed(() => {
  const result: Record<string, boolean> = {};
  const fieldKeys = Object.keys(
    props.formComponent.fields,
  ) as (keyof FormFieldsDataType)[];

  for (const key of fieldKeys) {
    const field = props.formComponent.fields[key];
    if (!field?.required) {
      result[key as string] = false; // Not required by config
      continue;
    }

    // If required by config, then check its actual state for dynamic requirement
    const value = form[key];
    if (field.type === "checkbox-group") {
      result[key as string] = !(Array.isArray(value) && value.length > 0);
    } else if (field.type === "radio") {
      // For radio, it's required if value is empty (initial state)
      result[key as string] = value === undefined || value === null || value === "";
    } else {
      // For other types, if marked required, it's generally required to be non-empty
      // FormInput component usually handles this with its own validation display
      result[key as string] = true; // Let FormInput handle empty check if it's a simple text input etc.
                                  // Or more specific: result[key as string] = value === "";
    }
  }
  return result;
});

function getFieldAttrs(
  field: FormField, // field is now more strongly typed
  key: string, // key is a string, but corresponds to a key in formComponent.fields
) {
  const typedKey = key as keyof FormFieldsDataType;
  const raw = {
    name: key,
    label: field.label,
    type: field.type, // KnownFieldType
    placeholder: field.placeholder,
    required: dynamicRequired.value[key], // Assuming key is string here
    options: field.options,
    // field.value is the initial config value, form[typedKey] is the current reactive value
    // For 'values' prop, it depends on what child components expect.
    // If 'values' is for initial/default, use field.value.
    // If it's for current bound value, it's already handled by v-model.
    // Let's assume 'values' is for options or initial setup if not v-model related.
    values: field.value, // This was original, might be for specific components like radio/checkbox group options if not in 'options'
    max: field.max,
    min: field.min,
    step: field.step,
    disabled: disabledStates.value[key], // Assuming key is string here
    error: form.errors[typedKey],
  };

  return Object.fromEntries(
    Object.entries(raw).filter(
      ([_, val])
        val !== undefined &&
        val !== null &&
        // Allow empty string for some attributes like placeholder
        // val !== "" && // Re-evaluating this: empty placeholder is valid
        !(Array.isArray(val) && val.length === 0), // Empty arrays for options might be filtered
    ),
  );
}
const onSubmit = () => form.submit();
</script>
