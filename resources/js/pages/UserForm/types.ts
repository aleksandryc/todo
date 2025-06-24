export interface FormField {
  "label": string;
  "type": string;
  "depends_on"?: Dependency | Dependency[];
  "max"?: number | string;
  "min"?: number | string;
  "maxlength"?: number;
  "minlength"?: number;
  "name"?: string;
  "options"?: string[] | Array<{ value: string; label: string }>;
  "placeholder"?: string;
  "required"?: boolean;
  "rules"?: string[];
  "step"?: number;
  "value"?:
    | string
    | number
    | boolean
    | Array<{ value: string; display: string }>;
  "related-to"?: string;
  "_hidden_fields"?: {
    label: string;
    value: string;
  };
  "mailRecipients": string;
  "ccRecipients": string;
}

export interface FormComponent {
  title: string;
  description: string;
  fields: Record<string, FormField>;
  formKey: string;
}

export interface Dependency {
  field: string;
  active_when?: boolean | string;
  disable_when?: boolean | string;
  disabled?: boolean;
}

export interface FormValues {
  [key: string]: string | number | boolean | string[];
}
