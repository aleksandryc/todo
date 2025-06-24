// src/types.ts

/**
 * Определяет возможные типы полей формы.
 * Расширьте этот тип по мере необходимости для поддержки большего количества элементов формы.
 */
export type FormFieldType =
  | 'text'
  | 'email'
  | 'password'
  | 'number'
  | 'textarea'
  | 'select'
  | 'checkbox' // Может быть одиночным или группой, если есть options
  | 'radio'    // Всегда группа с options
  | 'date'
  | 'url'
  | 'group'    // Для вложенных форм или групп полей
  | 'notes'    // Для отображения текстовых заметок (не поле ввода)
  | 'hidden';  // Для скрытых полей

/**
 * Опции для полей типа select, radio group.
 */
export interface FormFieldOption {
  value: string | number;
  label: string;
}

/**
 * Правила валидации для поля формы.
 * Можно расширить для поддержки различных правил.
 */
export interface ValidationRule {
  type: 'required' | 'minLength' | 'maxLength' | 'pattern' | 'email' | 'custom';
  value?: any; // Значение для правила (например, минимальная длина, регулярное выражение)
  message: string; // Сообщение об ошибке
  // Для правил типа 'custom', можно добавить функцию валидации
  validator?: (value: any, formData: FormData) => boolean | string;
}

/**
 * Определяет условие для зависимости одного поля от другого.
 * Используется для динамического включения/отключения полей.
 */
export interface DependencyRule {
  field: string; // Имя поля, от которого зависит текущее поле
  // conditionType: 'disable_when' | 'active_when'; // Тип условия не нужен, если мы всегда отключаем
  value?: any; // Значение поля `field`, при котором условие сработает (для disable_when/active_when)
                 // Если не указано, может означать "когда поле `field` заполнено (filled)"
  condition: 'filled' | 'equals' | 'not_equals' | 'contains'; // Тип условия
  initialDisabled?: boolean; // Начальное состояние disabled, если не указано, то false
                               // 'disabled' в PHP примере = initialDisabled: true, disable_when = value
                               // 'active_when' = initialDisabled: true, enable_when = value (противоположно disable_when)
                               // Мы будем реализовывать логику "поле disabled если условие ИСТИННО"
}


/**
 * Базовый интерфейс для всех полей формы.
 */
export interface BaseFormField {
  name: string; // Уникальное имя поля, используется для v-model и идентификации
  label?: string; // Метка поля (может отсутствовать для hidden, notes)
  type: FormFieldType;
  defaultValue?: any;
  placeholder?: string;
  options?: FormFieldOption[]; // Для select, radio, checkbox (если группа)
  validation?: ValidationRule[];
  attrs?: Record<string, any>; // Дополнительные HTML атрибуты

  // Свойства для числовых полей
  min?: number;
  max?: number;
  step?: number;

  // Для условного отображения/активности
  dependsOn?: DependencyRule[]; // Массив зависимостей, поле будет disabled если ХОТЯ БЫ ОДНО правило истинно

  // Для типа 'notes'
  value?: string; // Используется для отображения контента в поле 'notes' (не путать с defaultValue)

  // Для типа 'hidden' (хотя value может быть в defaultValue)
  // Если isHidden: true, то поле не рендерится видимо, но его значение есть в formData
  // Либо просто используем type: 'hidden' и FormElement его специальным образом рендерит
}

/**
 * Интерфейс для поля группы, которое может содержать вложенные поля.
 */
export interface FormGroupField extends BaseFormField {
  type: 'group';
  fields: FormField[]; // Вложенные поля
}

/**
 * Объединенный тип для всех видов полей формы.
 */
export type FormField = BaseFormField | FormGroupField;

/**
 * Интерфейс для конфигурации всей формы.
 */
export interface FormConfiguration {
  key: string; // Уникальный ключ формы, соответствует formKey
  title?: string; // Заголовок формы
  description?: string; // Описание формы
  fields: FormField[];
  hiddenFields?: Record<string, { label?: string, value: any }>; // Для _hidden_fields из PHP
  // Можно добавить другие метаданные формы, например, эндпоинт для отправки
  submitEndpoint?: string;
}

/**
 * Интерфейс для объекта, представляющего состояние формы (значения полей).
 * Ключи - это `name` поля.
 */
export interface FormData {
  [fieldName: string]: any;
}

/**
 * Интерфейс для ошибок валидации формы.
 * Ключи - это `name` поля.
 */
export interface FormErrors {
  [fieldName: string]: string | undefined;
}
