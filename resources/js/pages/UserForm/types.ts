// Define a union of known field types
export type KnownFieldType =
  | "text"
  | "number"
  | "date"
  | "textarea"
  | "radio"
  | "checkbox"
  | "checkbox-group"
  | "file"
  | "url"
  | "email"
  | "tel"
  | "select"
  | "notes"; // Added select and notes

export interface FormField {
  label: string;
  type: KnownFieldType; // Use the union type
  depends_on?: Dependency | Dependency[];
  max?: number | string;
  min?: number | string;
  maxlength?: number;
  minlength?: number;
  name?: string;
  options?: string[] | Array<{ value: string; label: string }>;
  placeholder?: string;
  required?: boolean;
  rules?: string[];
  step?: number;
  value?: // Adjusted to better reflect possible initial values, especially for multi-value fields
    | string
    | number
    | boolean
    | string[] // For checkbox-group, multi-select
    | Array<{ value: string; display: string }>;
  "related-to"?: string;
  _hidden_fields?: {
    label: string;
    value: string;
  };
  // REMOVED mailRecipients and ccRecipients from here
}

export interface FormComponent {
  title: string;
  description: string;
  fields: Record<string, FormField>; // fields will use FormField with KnownFieldType
  formKey: string;
}

export interface Dependency {
  field: string; // This refers to a key in FormComponent.fields
  active_when?: boolean | string | string[]; // Added string[] for "value is one of..."
  disable_when?: boolean | string | string[]; // Added string[] for "value is one of..."
  disabled?: boolean;
}

// This type will represent the structure of the form data passed to useForm
// It maps over the keys of a specific FormComponent.fields structure.
export type FormDataType<TFields extends Record<string, FormField>> = {
  // Mapped type for dynamic fields based on their definition
  [K in keyof TFields]: TFields[K]["type"] extends "checkbox-group"
    ? string[] // Assuming checkbox-group values are strings
    : TFields[K]["type"] extends "checkbox"
      ? boolean
      : TFields[K]["type"] extends "number"
        ? number | null // Allow null for empty number fields
        : string; // Default to string for other types (text, date, select, etc.)
                    // File inputs might need special handling (File object or string path),
                    // for now, string might represent a path or be empty.
} & {
  // Static fields
  mailRecipients: string;
  ccRecipients: string;
  formKey: string;
};

// This is the existing general type, might still be useful for generic processing
export interface FormValues {
  [key: string]: string | number | boolean | string[];
}
