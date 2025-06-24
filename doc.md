# User Forms Module

The module enables dynamic form rendering, validation, PDF generation, email notifications, and flexible configuration through PHP config files.

## How to Add a New Form

1. Open `config/userforms.php`
2. Add a new key with form meta and fields definition

## Form Structure - Required Fields

### Core Components

1. **Form Key** (Required)
   - Used to generate the URL
   - Contains the following elements:
     - **Form Name**
     - **Form Description**
     - **Fields**

### Field Configuration

Each field requires:

- **Field Key** - Unique identifier
- **Label** - Display name
- **Type** - Input field type

#### Basic Example

```php
"basic-form" => [
     "title" => 'Example',
     "description" => "Description example",
     "fields" => [
          'first' => [
                'label' => 'Label for entry',
                "type" => 'url',
                "required" => true,
          ],
          'second' => [
                'label' => 'Label for entry',
                "type" => 'date',
                "required" => true,
          ],
     ],
],
```

## For specific field like radio, select, checkbox, chexckbox-group

Use these required fields:

- **Options** - Required for checkbox, radio, select (Array format)
- **Value** - Required for custom type Notes
- **Required** - Set `true` if field is mandatory

#### Basic Example

```php
"advance-form" => [
     "title" => 'Example Two',
     "description" => "Description example two",
     "fields" => [
          'first' => [
                'label' => 'Label for entry',
                "type" => 'radio',
                'options' => ["Full Time", "Part Time"],
          ],
          'second' => [
                'label' => 'Label for entry',
                "type" => "checkbox-group",
                "options" => [
                    "External Email Access",
                    "External VPN Access",
                ],
          ],
     ],
],
```

## Form Structure - Optional Fields

### Additional Field Properties

```php
"placeholder" => "Enter text for placeholder",
"related-to" => "External Email Access",

// Use standard Laravel rules like required_if or required_without.
"rules" => ["required_if:access_type, External Email Access", "max:255"],

// Numeric Input Controls
"number" => [
     "step" => "1",      // Optional
     "min" => "1000",    // Optional
     "max" => "9999",    // Optional
     "minlength" => "1000", // Optional
     "maxlength" => "9999", // Optional
]
```

### Dependency Configuration

Use `depends_on` with `checkbox-group` for conditional field formatting:

```php
'depends_on' => [
     'field' => 'access_type[]',
     'disable_when' => 'External Email Access',
],
```

### Hidden Fields

Use hidden fields to include additional PDF information:

```php
'_hidden_fields' => [
     "application_received" => [
          "label" => 'application_received', // Required
          "value" => ' ', // Required
     ],
]
```
