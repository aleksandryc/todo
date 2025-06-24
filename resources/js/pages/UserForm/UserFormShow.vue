<template>
  <div class="card">
    <form @submit.prevent="onSubmit">
      <h2 class="text-center text-3xl">{{ formComponent.title }}</h2>
      <input type="hidden" name="formKey" :value="form.formKey" />
      <div v-for="(field, key) in formComponent.fields" :key="key" class="m-1">
        <FormInput
          v-if="field.type === 'select'"
          v-bind="getFieldAttrs(field, key)"
          @change="form.validate(key)">
          <BetterSelectBasic
            v-model:selected="(form as any)[key]"
            v-bind="getFieldAttrs(field, key)"
            :values="Array.isArray(field.value) ? field.value : []"
            :require-search="false" />
        </FormInput>
        <component
          :is="componentMap[field.type] || 'div'"
          v-model="(form as any)[key]"
          v-bind="getFieldAttrs(field, key)"
          @change="form.validate(key)" />
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
import type { FormComponent } from "./types";
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

const componentMap: Record<string, unknown> = {
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
} as const;

const initialValues = Object.fromEntries(
  Object.entries(props.formComponent.fields).map(([key, field]) => [
    key,
    field.value !== undefined
      ? field.value
      : field.type === "checkbox-group"
        ? []
        : "",
  ]),
);

const form = useForm("post", "/forms", {
  ...initialValues,
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

    isDisabled = !!dependencies[0]?.disabled;

    dependencies.forEach((dep) => {
      const depField = dep.field.replace(/\[\]$/, "");
      const depValue = form[depField as keyof typeof form];

      if (
        "active_when" in dep &&
        checkDependencyCondition(depValue, dep.active_when)
      ) {
        isDisabled = false;
      }

      if (
        "disable_when" in dep &&
        checkDependencyCondition(depValue, dep.disable_when)
      ) {
        isDisabled = true;
      }
    });

    result[fieldKey] = isDisabled;
  }

  return result;
});

function checkDependencyCondition(depValue: unknown, when: unknown): boolean {
  if (when === "filled") {
    if (Array.isArray(depValue)) return depValue.length > 0;
    return depValue !== undefined && depValue !== null && depValue !== "";
  }

  if (Array.isArray(when)) {
    if (Array.isArray(depValue)) {
      return when.some((val) => depValue.includes(val));
    }
    return when.includes(depValue);
  }

  if (Array.isArray(depValue)) {
    return depValue.includes(when);
  }

  if (typeof when === "boolean") {
    return Boolean(depValue) === when;
  }

  return depValue == when;
}

const dynamicRequired = computed(() => {
  const result: Record<string, boolean> = {};

  for (const [key, field] of Object.entries(props.formComponent.fields)) {
    if (!field?.required) continue;
    result[key] = false;

    if (field.type === "checkbox-group") {
      const value = form[key as keyof typeof form];
      result[key] =
        field.type === "checkbox-group"
          ? !(Array.isArray(value) && value.length > 0)
          : true;
    } else if (field.type === "radio") {
      const value = form[key as keyof typeof form];
      result[key] = value === undefined || value === null || value === "";
    } else {
      result[key] = true;
    }
  }
  return result;
});

function getFieldAttrs(
  field: (typeof props.formComponent.fields)[string],
  key: string,
) {
  const raw = {
    name: key,
    label: field.label,
    type: field.type,
    placeholder: field.placeholder,
    required: dynamicRequired.value[key],
    options: field.options,
    values: field.value,
    max: field.max,
    min: field.min,
    step: field.step,
    disabled: disabledStates.value[key],
    error: (form.errors as Record<string, string | undefined>)[key],
  };

  return Object.fromEntries(
    Object.entries(raw).filter(
      ([_, val]) =>
        val !== undefined &&
        val !== null &&
        val !== "" &&
        !(Array.isArray(val) && val.length === 0),
    ),
  );
}
const onSubmit = () => form.submit();
</script>
