<template>
  <form v-if="formConfig" @submit.prevent="handleSubmit">
    <h2 v-if="formConfig.title">{{ formConfig.title }}</h2>
    <p v-if="formConfig.description" class="form-description">{{ formConfig.description }}</p>

    <template v-for="field in formConfig.fields" :key="field.name">
      <div v-if="field.type !== 'hidden'" class="form-field-container">
        <FormElement
          :field="field"
          v-model="formData[field.name]"
          :error="formErrors[field.name]"
          @update:modelValue="updateFieldData(field.name, $event)"
          :is-disabled="isFieldDisabled(field)"
        />
      </div>
      <FormElement
        v-else
        :field="field"
        v-model="formData[field.name]"
        :is-disabled="false"
      />
    </template>

    <button type="submit" :disabled="loading">Отправить</button>
    <pre v-if="debugMode">Form Data: {{ formData }}</pre>
    <pre v-if="debugMode">Form Errors: {{ formErrors }}</pre>
  </form>
  <div v-else-if="loading">
    Загрузка конфигурации формы...
  </div>
  <div v-else-if="error">
    Ошибка загрузки формы: {{ error }}
  </div>
  <div v-else>
    Конфигурация для формы с ключом '{{ formKey }}' не найдена.
  </div>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, computed, provide, reactive } from 'vue';
import type { FormConfiguration, FormData, FormErrors, FormField, BaseFormField, FormGroupField, DependencyRule } from '../types';
import FormElement from './FormElement.vue';
import { fetchFormConfiguration } from '../services/formService';

const props = defineProps<{
  formKey: string;
  initialData?: Partial<FormData>; // Для предварительного заполнения формы
  debugMode?: boolean;
}>();

const emit = defineEmits<{
  (e: 'submit', data: FormData): void;
  (e: 'formLoaded', config: FormConfiguration): void;
  (e: 'formError', error: string): void;
}>();

const formConfig = ref<FormConfiguration | null>(null);
const formData = reactive<FormData>({}); // Используем reactive для formData для лучшей работы с вложенными объектами и computed свойствами
const formErrors = ref<FormErrors>({});
const loading = ref<boolean>(true);
const error = ref<string | null>(null);

provide('formData', formData); // Предоставляем reactive formData
provide('formErrors', formErrors);

// Инициализация данных формы, включая скрытые поля
const initializeFormData = (config: FormConfiguration, baseInitialData?: Partial<FormData>): FormData => {
  const data: FormData = {};

  function processFields(fields: FormField[], currentData: FormData, pathPrefix: string = '') {
    for (const field of fields) {
      const fieldName = pathPrefix ? `${pathPrefix}.${field.name}` : field.name;
      if (field.type === 'group') {
        // Для групп создаем вложенный объект, если его нет
        if (!currentData[fieldName] && baseInitialData?.[fieldName] === undefined) {
          currentData[fieldName] = {};
        }
        // Рекурсивно инициализируем данные для вложенных полей
        // Передаем соответствующую часть initialData если она есть
        const nestedInitialData = baseInitialData?.[fieldName] as Partial<FormData> ?? {};
        processFields((field as FormGroupField).fields, currentData[fieldName] as FormData, ''); // pathPrefix не нужен дальше, т.к. fieldName уже полный
      } else {
        // Устанавливаем значение по умолчанию или из initialData
        if (baseInitialData && baseInitialData[fieldName] !== undefined) {
          currentData[fieldName] = baseInitialData[fieldName];
        } else if ((field as BaseFormField).defaultValue !== undefined) {
          currentData[fieldName] = (field as BaseFormField).defaultValue;
        } else {
          // Устанавливаем null или другое значение по умолчанию в зависимости от типа поля
           currentData[fieldName] = field.type === 'checkbox' && (field as BaseFormField).options && (field as BaseFormField).options!.length > 0 ? [] : null;
        }
      }
    }
  }

  // Инициализация из props.initialData (плоская структура)
  const initialFlatData = { ...props.initialData };

  // Распаковка initialData для вложенных полей (если они переданы как group.field)
  const structuredInitialData: FormData = {};
  for (const key in initialFlatData) {
    if (key.includes('.')) {
      const parts = key.split('.');
      let currentLevel = structuredInitialData;
      parts.forEach((part, index) => {
        if (index === parts.length - 1) {
          currentLevel[part] = initialFlatData[key];
        } else {
          currentLevel[part] = currentLevel[part] || {};
          currentLevel = currentLevel[part] as FormData;
        }
      });
    } else {
      structuredInitialData[key] = initialFlatData[key];
    }
  }

  processFields(config.fields, data, '');

  // Добавляем hiddenFields из конфигурации формы
  if (config.hiddenFields) {
    for (const key in config.hiddenFields) {
      if (data[key] === undefined) { // Не перезаписываем, если уже есть из initialData или defaultValue поля
         data[key] = config.hiddenFields[key].value;
      }
    }
  }
  return data;
};


const loadForm = async (key: string) => {
  loading.value = true;
  error.value = null;
  formConfig.value = null;
  // Очистка formData перед загрузкой новой конфигурации
  for (const k in formData) delete formData[k];
  formErrors.value = {};

  try {
    const config = await fetchFormConfiguration(key);
    if (config) {
      formConfig.value = config;
      // Инициализируем formData с использованием reactive объекта
      const newFormData = initializeFormData(config, props.initialData);
      for (const k in newFormData) {
        formData[k] = newFormData[k];
      }
      emit('formLoaded', config);
    } else {
      error.value = `Конфигурация для ключа '${key}' не найдена.`;
      emit('formError', error.value);
    }
  } catch (err) {
    console.error('Ошибка при загрузке конфигурации формы:', err);
    error.value = err instanceof Error ? err.message : 'Неизвестная ошибка загрузки формы.';
    emit('formError', error.value);
  } finally {
    loading.value = false;
  }
};

// Функция для определения, должно ли поле быть отключено
const isFieldDisabled = (field: FormField | BaseFormField): boolean => {
  if (!field.dependsOn || field.dependsOn.length === 0) {
    return false; // Нет зависимостей, поле не отключается по этому правилу
  }

  for (const rule of field.dependsOn) {
    const dependentFieldValue = formData[rule.field]; // Получаем значение поля, от которого зависим

    let conditionMet = false;
    switch (rule.condition) {
      case 'filled':
        conditionMet = dependentFieldValue !== null && dependentFieldValue !== undefined && dependentFieldValue !== '';
        break;
      case 'equals':
        conditionMet = dependentFieldValue === rule.value;
        break;
      case 'not_equals':
        conditionMet = dependentFieldValue !== rule.value;
        break;
      case 'contains': // Для массивов (например, checkbox group) или строк
        if (Array.isArray(dependentFieldValue)) {
          conditionMet = dependentFieldValue.includes(rule.value);
        } else if (typeof dependentFieldValue === 'string') {
          conditionMet = dependentFieldValue.includes(rule.value as string);
        }
        break;
    }

    if (conditionMet) {
      // Если правило `dependsOn` означает "отключить когда условие истинно" (как в `disable_when`)
      // и `initialDisabled` не установлено или `false`, то поле отключается.
      // Если `initialDisabled` было `true`, то поле уже было отключено, и это условие может его включить (если логика инвертирована).
      // В AGENTS.md: 'disabled' => true, 'disable_when' => 'External Email Access'
      // Это значит: initialDisabled = true. Если rule.field === 'External Email Access', то поле НЕ будет disabled.
      // Наша current logic: поле disabled, если rule.condition истинно.
      // initialDisabled: true в dependsOn означает, что поле изначально отключено, и правило его включает.
      // initialDisabled: false (или не указано) означает, что поле изначально включено, и правило его отключает.

      // Если initialDisabled = true, то условие должно быть НЕ выполнено, чтобы поле осталось disabled.
      // Или, если условие ВЫПОЛНЕНО, поле становится enabled (противоположность isDisabled).
      // Если initialDisabled = false, то если условие ВЫПОЛНЕНО, поле становится disabled.

      // Переформулируем: поле isDisabled если:
      // (initialDisabled И НЕ УсловиеДляВключения) ИЛИ (НЕ initialDisabled И УсловиеДляОтключения)
      // Для 'disable_when': initialDisabled=false. Поле disabled, если conditionMet.
      // Для 'active_when' (enable_when): initialDisabled=true. Поле disabled, если НЕ conditionMet.

      // Пример из AGENTS.md: 'disabled' => true, 'disable_when' => 'External Email Access'
      // Это означает, что поле должно быть АКТИВНО, когда значение 'External Email Access'.
      // В нашей структуре это `initialDisabled: true`, `condition: 'equals'`, `value: 'External Email Access'`.
      // Поле будет disabled, если `initialDisabled` И `НЕ conditionMet`.
      if (rule.initialDisabled) { // Поле изначально отключено, правило должно его включить
        if (!conditionMet) return true; // Условие для включения не выполнено, оставляем отключенным
      } else { // Поле изначально включено, правило должно его отключить
        if (conditionMet) return true; // Условие для отключения выполнено
      }
    }
  }
  // Если ни одно из правил не привело к отключению (с учетом initialDisabled)
  return false;
};


const validateField = (field: FormField, value: any): string | undefined => {
  if (field.type === 'group' || field.type === 'notes' || field.type === 'hidden') return undefined;

  const baseField = field as BaseFormField;
  if (!baseField.validation) return undefined;

  for (const rule of baseField.validation) {
    // Проверяем, не отключено ли поле, если да, то валидация может быть пропущена (зависит от требований)
    // if (isFieldDisabled(baseField)) continue; // Пока не пропускаем, валидируем все равно

    if (rule.type === 'required') {
      let isEmpty = false;
      if (value === null || value === undefined || value === '') {
        isEmpty = true;
      } else if (Array.isArray(value) && value.length === 0) { // для checkbox group
        isEmpty = true;
      } else if (baseField.type === 'checkbox' && !value && rule.value === true) { // для одиночного checkbox, который должен быть true
         return rule.message || 'Это поле обязательно для отметки.';
      }
      if (isEmpty && !(baseField.type === 'checkbox' && rule.value === true) ) { // не применяем isEmpty для required: true чекбокса
         return rule.message || 'Это поле обязательно для заполнения.';
      }
    }
    if (value === null || value === undefined || value === '') continue; // Остальные правила не применяются к пустым значениям (кроме required)

    if (rule.type === 'minLength' && typeof value === 'string' && value.length < rule.value) {
      return rule.message || `Минимальная длина ${rule.value} символов.`;
    }
    if (rule.type === 'maxLength' && typeof value === 'string' && value.length > rule.value) {
      return rule.message || `Максимальная длина ${rule.value} символов.`;
    }
    if (rule.type === 'email') {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(value)) {
        return rule.message || 'Введите корректный email.';
      }
    }
    if (rule.type === 'pattern' && typeof rule.value === 'string') {
      const regex = new RegExp(rule.value);
      if (!regex.test(value)) {
        return rule.message || 'Значение не соответствует шаблону.';
      }
    }
    if (rule.type === 'custom' && rule.validator) {
        const customValidationResult = rule.validator(value, formData);
        if (typeof customValidationResult === 'string') {
            return customValidationResult; // Сообщение об ошибке от кастомного валидатора
        }
        if (customValidationResult === false) {
            return rule.message || 'Поле не прошло кастомную валидацию.';
        }
    }
  }
  return undefined;
};

const validateForm = (): boolean => {
  formErrors.value = {};
  let isValid = true;

  const validateFieldsRecursive = (fields: FormField[], currentData: FormData, pathPrefix: string = '') => {
    for (const field of fields) {
      const fieldName = pathPrefix ? `${pathPrefix}.${field.name}` : field.name;
      // Пропускаем валидацию для отключенных полей
      if (isFieldDisabled(field)) {
        if (formErrors.value[fieldName]) delete formErrors.value[fieldName]; // Удаляем старую ошибку если поле стало disabled
        continue;
      }

      if (field.type === 'group') {
        // Для групп, валидируем вложенные поля. currentData[field.name] должно быть объектом.
        if (currentData[field.name] && typeof currentData[field.name] === 'object') {
           validateFieldsRecursive((field as FormGroupField).fields, currentData[field.name] as FormData, ''); // pathPrefix уже не нужен, т.к. field.name у подполей простое
        }
      } else if (field.type !== 'notes' && field.type !== 'hidden') {
        const valueToValidate = pathPrefix ? (currentData as any)[field.name] : currentData[fieldName];
        const errorMessage = validateField(field, valueToValidate);
        if (errorMessage) {
          formErrors.value[fieldName] = errorMessage;
          isValid = false;
        } else if (formErrors.value[fieldName]) {
           delete formErrors.value[fieldName]; // Очищаем ошибку, если поле стало валидным
        }
      }
    }
  };

  if (formConfig.value) {
    // Передаем копию formData для избежания проблем с реактивностью внутри validateFieldsRecursive при доступе к currentData
    validateFieldsRecursive(formConfig.value.fields, JSON.parse(JSON.stringify(formData)));
  }
  return isValid;
};

const handleSubmit = () => {
  if (validateForm()) {
    // Собираем только те данные, которые не отключены (опционально, зависит от требований)
    const dataToSubmit: FormData = {};
    const processSubmitData = (fields: FormField[], currentFormData: FormData, currentSubmitData: FormData, pathPrefix: string = '') => {
        for (const field of fields) {
            const fieldName = pathPrefix ? `${pathPrefix}.${field.name}` : field.name;
            if (isFieldDisabled(field)) continue;

            if (field.type === 'group') {
                currentSubmitData[field.name] = {};
                processSubmitData((field as FormGroupField).fields, currentFormData[field.name] as FormData, currentSubmitData[field.name] as FormData);
            } else {
                currentSubmitData[field.name] = currentFormData[field.name];
            }
        }
    };
    // processSubmitData(formConfig.value!.fields, formData, dataToSubmit);
    // Пока отправляем все данные formData, включая disabled. Бэкенд должен решить, что с ними делать.
    emit('submit', JSON.parse(JSON.stringify(formData)));
  } else {
    console.warn('Форма содержит ошибки:', formErrors.value);
  }
};

const updateFieldData = (fieldName: string, value: any) => {
  // Для вложенных полей fieldName может быть 'groupName.subFieldName'
  // Нужно правильно обновить formData, если он реактивный
  const keys = fieldName.split('.');
  let currentLevel = formData;
  keys.forEach((key, index) => {
    if (index === keys.length - 1) {
      currentLevel[key] = value;
    } else {
      if (!currentLevel[key] || typeof currentLevel[key] !== 'object') {
        currentLevel[key] = {}; // Создаем вложенный объект, если его нет
      }
      currentLevel = currentLevel[key] as FormData;
    }
  });

  if (formConfig.value) {
    const fieldDefinition = findField(formConfig.value.fields, fieldName);
    if (fieldDefinition && fieldDefinition.type !== 'group' && fieldDefinition.type !== 'notes' && fieldDefinition.type !== 'hidden') {
      if (!isFieldDisabled(fieldDefinition)) { // Валидируем только если поле не отключено
        formErrors.value[fieldName] = validateField(fieldDefinition, value);
      } else {
        if (formErrors.value[fieldName]) delete formErrors.value[fieldName]; // Очищаем ошибку, если поле стало disabled
      }
    }
  }
};

const findField = (fields: FormField[], fieldName: string): FormField | undefined => {
  const nameParts = fieldName.split('.');
  let currentFields = fields;
  let found: FormField | undefined = undefined;

  for (let i = 0; i < nameParts.length; i++) {
    const part = nameParts[i];
    found = currentFields.find(f => f.name === part);
    if (!found) return undefined;
    if (i < nameParts.length - 1) {
      if (found.type === 'group') {
        currentFields = (found as FormGroupField).fields;
      } else {
        return undefined; // Путь ведет не в группу
      }
    }
  }
  return found;
};

watch(() => props.formKey, (newKey) => {
  if (newKey) loadForm(newKey);
}, { immediate: true });


watch(() => props.initialData, (newData, oldData) => {
  // Переинициализируем форму только если initialData действительно изменились и есть конфигурация
  if (formConfig.value && newData && JSON.stringify(newData) !== JSON.stringify(oldData)) {
    const newFormData = initializeFormData(formConfig.value, newData);
    for (const k in formData) delete formData[k]; // Очищаем старые ключи
    for (const k in newFormData) { // Устанавливаем новые
      formData[k] = newFormData[k];
    }
    // После переинициализации данных, нужно заново провести валидацию, если это необходимо
    // или сбросить ошибки. Пока сбрасываем.
    formErrors.value = {};
  }
}, { deep: true });


onMounted(() => {
  // loadForm уже вызывается через watch immediate: true
});

</script>

<style scoped>
.form-field-container {
  margin-bottom: 1rem;
}
.form-description {
  margin-bottom: 1.5rem;
  font-style: italic;
  color: #555;
}
/* Добавьте стили для формы и ее элементов по мере необходимости */
</style>
