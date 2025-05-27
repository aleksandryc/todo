<?php

use Illuminate\Validation\Rule;

/*
Regular expression for phone number
'regex:/^\\+?\\d{1,4}?[-.\\s]?\\(?\\d{1,3}?\\)?[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,4}[-.\\s]?\\d{1,9}$/',

------------------------------------------------------------------------------
In the configuration, you can specify rules both as an array and as a string:
// Array
"rules" => ["required", "string", "max:255"]

// String
"rules" => "required|string|max:255"

------------------------------------------------------------------------------

When the form configuration the checkbox is defined as:

"field-name" => [
    "label" => "Your Lable Here",
    "type" => "checkbox",
    'options' => 'Your option',
    'value' => "permanent", // this value will be sent to PDF and email
    .....
],

------------------------------------------------------------------------------

if there is a group of checkboxes in the configuration,
you can set "related-to" for the fields associated with them,
and then when rendering the form they will be grouped in one block

In the form configuration it should look like this:

"type-of-access" => [
    "type" => "checkbox-group",
    "label" => "Type of access",
    "options" => ["External Email Access", "External VPN Access"],
],
"email-device" => [
    "type" => "text",
    "label" => "Email Device",
    "related-to" => "External Email Access",
    "placeholder" => "Enter device for email access"
],
"vpn-device" => [
    "type" => "text",
    "label" => "VPN Device",
    "related-to" => "External VPN Access",
    "placeholder" => "Enter device for VPN access"
]

------------------------------------------------------------------------------

If you set a dependency "depends_on" in the form configuration,
the dependent field will be blocked or become active depending on the specified condition.

Important:
In the config for depends_on, specify the field name with [] for a group of checkboxes, for example:

'depends_on' => [
    'field' => 'access-type[]',
    'disable_when' => 'External Email Access', // or Array of values
],

*/


return [
    "external-access-request" => [
        "title" => "External Access Request",
        "description" =>
        "To request an employee be granted external access to company information when outside of Elias Woodwork’s facilities please fill in the following and submit to HR for approval. If a mobile phone or laptop is required, please note that in the devices section. ",
        "fields" => [
            "names-group" => [
                "supervisor" => [
                    "name" => "supervisor",
                    "label" => "Supervisor",
                    "type" => "text",
                    "rules" => ["required", "max:255"],
                    "placeholder" => "Enter Full name",
                    "required" => true,
                ],
                "employee" => [
                    "name" => "employee",
                    "label" => "Employee",
                    "type" => "text",
                    "rules" => ["required", "max:255"],
                    "placeholder" => "Enter Full name",
                    "required" => true,
                ],
            ],
            "access-type" => [
                "name" => "access-type",
                "label" => "Type of Access (Check all that apply) ",
                "type" => "checkbox-group",
                "options" => [
                    "External Email Access",
                    "External VPN Access",
                ],
                "rules" => [
                    "required",
                    "array",
                    Rule::in([
                        "External Email Access",
                        "External VPN Access",
                    ]),
                ],
                "required" => true,
            ],
            [
                "Device-used-email" => [
                    "label" =>
                    "Devices being used (MFA requires mobile phone): ",
                    "type" => "text",
                    "related-to" => "External Email Access",
                    'depends_on' => [
                        'field' => 'access-type[]',
                        'disable_when' => 'External Email Access',
                    ],
                    "placeholder" => "MFA requires mobile phone",
                    "conditional-rules" => [
                        "when" => [
                            "field" => "access-type",
                            "value" => "External Email Access",
                            "rules" => ["required", "max:255"],
                        ],
                    ],
                ],
                "device-used-vpn" => [
                    "label" => "Devices being used: ",
                    "type" => "text",
                    "related-to" => "External VPN Access",
                    'depends_on' => [
                        'field' => 'access-type[]',
                        'disable_when' => 'External VPN Access',
                    ],
                    "placeholder" => "MFA requires mobile phone",
                    "conditional-rules" => [
                        "when" => [
                            "field" => "access-type",
                            "value" => "External VPN Access",
                            "rules" => ["required", "max:255"],
                        ],
                    ],
                ],
            ],
            "date-range-start" => [
                "label" =>
                "Enter start date of approval ",
                "type" => "date",
                "rules" => ["nullable", "date"],
                "conditional-rules" => [
                    "when" => [
                        "field" => "permanent",
                        "value" => false,
                        "rules" => ["required", "date"],
                    ],
                ],
            ],
            "date-range-end" => [
                "label" =>
                "Enter end date of approval ",
                "type" => "date",
                'depends_on' => [
                    'field' => 'permanent',
                    'disable_when' => true,
                ],
                "rules" => [
                    "nullable",
                    "date",
                    "after_or_equal:date-range-start",
                ],
                "required" => false,
                "conditional-rules" => [
                    "when" => [
                        "field" => "permanent",
                        "value" => false,
                        "rules" => ["required", "date"],
                    ],
                ],
            ],
            "permanent" => [
                "label" =>
                "",
                "type" => "checkbox",
                'options' => 'Permanent',
                'value' => "permanent",
                "rules" => ["nullable"],
                "required" => false,
                'depends_on' => [
                    'field' => 'date-range-end',
                    'disable_when' => 'filled',
                ],
            ],
            "reason" => [
                "label" =>
                "Reason (Provide a brief description of why this is needed)",
                "type" => "textarea",
                "required" => true,
            ],
        ],
    ],

    "maintenance-request-form" => [
        "title" => "DEMO Maintenance Request Form",
        "description" => "Demo: \"Request form for repair of equipment from the worker.\"",
        "fields" => [
            "date" => [
                "name" => 'date',
                "label" => "Date",
                "type" => "date",
                "required" => true,
            ],
            "employee-name" => [
                "name" => 'employee-name',
                "label" => "Employee name",
                "type" => "text",
                "rules" => ["required", "max:255"],
                "required" => true,
            ],
            "department" => [
                'name' => 'department-name',
                "label" => "Department name",
                "type" => "select",
                "options" => [
                    "Shiping", "Recieving", "Manufacturing"
                ],
                "rules" => ["required", "max:255"],
                "required" => true,
            ],
            "machine-name" => [
                'name' => 'machine-name',
                "label" => "Machine/Equipment name",
                "type" => "text",
                "rules" => ["required", "max:255"],
                'depends_on' => [
                    'field' => 'supervisor-notify',
                    'disable_when' =>  "Yes",
                ],
            ],
            "machine-id" => [
                'name' => 'machine-id',
                "label" => "Machine/Equipment ID ('optional')",
                "type" => "text",
                "rules" => ["nullable", "max:255"],
            ],
            "problem-description" => [
                "name" => 'problem-desccription',
                "label" => "Problem Description",
                "type" => "textarea",
                "rules" => ["required", "max:2048"],
                "required" => true,
            ],
            "urgency-level" => [
                "name" => 'urgency-level',
                "label" => "Urgency level",
                "type" => "radio",
                "options" => [
                    "Low (Minor issue, no impact on workflow)",
                    "Medium (slows work but still operational)",
                    "High (Machine not operational)",
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            "supervisor-notify" => [
                "label" => "Has Supervisor Been Notified?",
                "type" => "radio",
                "options" => ["Yes", "No"],
                "rules" => ["required"],
                "required" => true,
            ],
            "submitted-by" => [
                "name" => 'submitted-by',
                "label" => "Submitted by ",
                "type" => "text",
                "rules" => ["required", "max:255"],
                "required" => true,
            ],
        ],
    ],
    "new-course" => [
        "title" => "DEOM Add New Course Form",
        "description" => "Demo Form",
        "fields" => [
            "email" => [
                "label" => "Email",
                "type" => "email",
                "rules" => [
                    "required",
                    "email",
                    'regex: /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/',
                ],
                "placeholder" => "Enter email",
                "required" => true,
            ],
            "name" => [
                "label" => "Course name",
                "type" => "textarea",
                "rules" => ["required", "max:255"],
                "required" => true,
            ],

            "date" => [
                "label" => "Hard Expiry Date",
                "type" => "date",
                "rules" => ["required"],
                "required" => true,
            ],

            "valid" => [
                "label" => "Default Valid Period",
                "type" => "date", //TODO Four fields need to be added for chosing period (Years, Month, Weeks, Days)
                "rules" => ["nullable"],
            ],
            "logo" => [
                "label" => "Attach a file",
                "type" => "file",
                "rules" => ["nullable", "file", "max:2048"],
                "required" => false,
            ],
            "type" => [
                "label" => "Type of course",
                "type" => "select",
                "options" => [
                    "Policies",
                    "Toolbox Talks",
                    "Manitoba SAFE Work Courses",
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            "website" => [
                "label" => "Link to website",
                "type" => "url",
                "rules" => ["nullable", "url"],
                "required" => false,
                "placeholder" => "Example: https://example.com",
            ],
            "attachment" => [
                "label" => "Attach a file",
                "type" => "file",
                "rules" => ["nullable", "file", "max:2048"],
                "required" => false,
            ],
        ],
    ],
    'Customer Feedback' => [
        'title' => 'DEMO Customer Feedback',
        'description' => 'We value your feedback!',
        'fields' => [
            'name' => [
                'label' => 'name',
                'type' => 'text',
                'required' => false,
            ],
            'email' => [
                'label' => 'email',
                'type' => 'email',
                'required' => false,
            ],
            'rating' => [
                'label' => 'rating',
                'type' => 'radio',
                "rules" => ["required"],
                'required' => true,
                'options' => ['1', '2', '3', '4', '5'],
            ],
            'comments' => [
                'label' => 'comments',
                'type' => 'textarea',
                'required' => false,
                'placeholder' => 'Share your thoughts...',
            ],
        ],
    ],
    'event-registration' => [
        'title' => 'DEMO Event Registration',
        'description' => 'Sign up for our upcoming event.',
        'fields' => [
            'name' => [
                'label' => 'name',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Enter your name',
            ],
            'email' => [
                'label' => 'email',
                'type' => 'email',
                'required' => true,
            ],
            'number_of_tickets' => [
                'label' => 'number_of_tickets',
                'type' => 'select',
                'required' => true,
                'options' => ['1', '2', '3', '4', '5'],
            ],
            'meal_preference' => [
                'label' => 'meal_preference',
                'name' => 'meal',
                'type' => 'radio',
                'required' => false,
                "rules" => ["nullable"],
                'options' => ["Vegetarian", "Non-Vegetarian", "Vegan"],
            ],
            'special_requests' => [
                'label' => 'special_requests',
                'type' => 'textarea',
                'required' => false,
            ],
        ],
    ],
];
