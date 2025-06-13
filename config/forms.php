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

------------------------------------------------------------------------------

For providing additional information you can use "type" => "notes"
It creates new <div> </div> in form, you can pass tailwind classes throgh "class"

'notes' => [
    'label' => '',
    'type' => 'notes',
    'value' => "Your consideration in this matter is appreciated",
    'class' => 'p-2 mt-1',
    ],

------------------------------------------------------------------------------

To provide additional information for office use only in the PDF you can use this example

// Hidden fields PDF

'_hidden_fields' => [
    "approved_supervison_signature" => [
        "label" => 'approved_supervison_signature',
        "value" => ' ',
    ],
    "approved_human_resources" => [
        "label" => 'approved_human_resources',
        "value" => ' ',
    ],
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
            "access_type" => [
                "name" => "access_type",
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
                "Device_used_email" => [
                    "label" =>
                    "Devices being used (MFA requires mobile phone): ",
                    "type" => "text",
                    "related-to" => "External Email Access",
                    'depends_on' => [
                        'field' => 'access_type[]',
                        'disable_when' => 'External Email Access',
                    ],
                    "placeholder" => "MFA requires mobile phone",
                    "conditional-rules" => [
                        "when" => [
                            "field" => "access_type",
                            "value" => "External Email Access",
                            "rules" => ["required", "max:255"],
                        ],
                    ],
                ],
                "device_used_vpn" => [
                    "label" => "Devices being used: ",
                    "type" => "text",
                    "related-to" => "External VPN Access",
                    'depends_on' => [
                        'field' => 'access_type[]',
                        'disable_when' => 'External VPN Access',
                    ],
                    "placeholder" => "MFA requires mobile phone",
                    "conditional-rules" => [
                        "when" => [
                            "field" => "access_type",
                            "value" => "External VPN Access",
                            "rules" => ["required", "max:255"],
                        ],
                    ],
                ],
            ],
            "date_range_start" => [
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
            "date_range_end" => [
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
                    "after_or_equal:date_range_start",
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
                    'field' => 'date_range_end',
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

    "employee-referal" => [
        "title" => "New Employee Referal Form",
        "description" => "Employee Referral Form is used by employees to recommend potential candidates for open positions within a company.",
        "fields" => [
            "name" => [
                "name" => 'name',
                "label" => "Name",
                "type" => "text",
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter Full name",
                "required" => true,
            ],
            "referred_by" => [
                "name" => 'refferd',
                "label" => "Reffered By",
                "type" => "text",
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter Full name",
                "required" => true,
            ],
            "employee_number" => [
                "name" => 'employee_number',
                "label" => "Employee #",
                "type" => "number",
                "step" => "1",
                "min" => "1000",
                "max" => "9999",
                "minlength" => "1000",
                "maxlength" => "9999",
                "required" => true,
                "rules" => ["required"],
                "placeholder" => "Enter Employee number ",
            ],
            "department" => [
                "name" => 'department',
                "label" => "Department",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter your Department",
            ],
            "supervisor" => [
                "name" => 'supervisor',
                "label" => "Supervisor",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter Full name of Supervisor",
            ],
        ],
    ],
    "job-transfer-request" => [
        "title" => "Job Transfer Request Form",
        "description" => "To submit a request to transfer to a different department or position within the company.",
        "fields" => [
            "date_of_completion" => [
                "label" => "Date",
                "type" => "date",
                "rules" => [
                    "required",
                    "date",
                ],
                "required" => true,
            ],
            "name" => [
                "name" => 'name',
                "label" => "Name",
                "type" => "text",
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter Full name",
                "required" => true,
            ],
            "employee_number" => [
                "name" => 'employee_number',
                "label" => "Employee #",
                "type" => "number",
                "step" => "1",
                "min" => "1000",
                "max" => "9999",
                "minlength" => "1000",
                "maxlength" => "9999",
                "required" => true,
                "rules" => ["required"],
                "placeholder" => "Enter Employee number ",
            ],
            "department" => [
                "name" => 'department',
                "label" => "Department",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter your Department",
            ],
            "plant" => [
                'name' => 'plant',
                "label" => "Plant",
                "type" => "select",
                "options" => [
                    "Morden",
                    "Winkler"
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            "shift" => [
                'name' => 'shift',
                "label" => "Shift",
                "type" => "select",
                "options" => [
                    "Morden Day",
                    "Morden Evening",
                    "Office",
                    "Winkler Day",
                    "Winkler Evening",
                    "Driver",
                    "Cleaning",
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            "seniority" => [
                "name" => 'seniority',
                "label" => "Seniority",
                "type" => "text",
                "required" => false,
                "rules" => ["nullable", "max:255"],
                "placeholder" => "",
            ],
            "position_requested" => [
                "name" => 'position_requested',
                "label" => "Position requested",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter your position requested",
            ],
            "requested_department" => [
                "name" => 'requested_department',
                "label" => "Department",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter departmnet name to transfer",
            ],
            "requested_plant" => [
                'name' => 'requested_plant',
                "label" => "Plant",
                "type" => "select",
                "options" => [
                    "Morden",
                    "Winkler"
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            "requested_shift" => [
                'name' => 'requested_shift',
                "label" => "Shift",
                "type" => "select",
                "options" => [
                    "Morden Day",
                    "Morden Evening",
                    "Office",
                    "Winkler Day",
                    "Winkler Evening",
                    "Driver",
                    "Cleaning",
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            'schedule' => [
                'label' => 'Choose type of time',
                'name' => 'schedule',
                'type' => 'radio',
                'required' => false,
                "rules" => ["required"],
                'options' => ["Full Time", "Part Time"],
                "required" => true,
            ],
            "requested_date" => [
                "name" => "requested_date",
                "label" => "Requested Date of Transfer",
                "type" => "date",
                "rules" => [
                    "required",
                    "date",
                ],
                "required" => true,
            ],
            "reason_transfering" => [
                "name" => 'reason_transfering',
                "label" => "Reason for transfering",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter reason for transfer",
            ],
            "experience" => [
                "name" => 'experience',
                "label" => "Describe your work experience and how it relates to this position",
                "type" => "textarea",
                "required" => false,
                "rules" => ["nullable", "max:22255"],
            ],
            '_hidden_fields' => [
                "application_recieved" => [
                    "label" => 'application_recieved',
                    "value" => ' ',
                ],
                "interview_date" => [
                    "label" => 'interview_date',
                    "value" => ' ',
                ],
                "to_be_int-viewed_by" => [
                    "label" => 'to_be_int-viewed_by',
                    "value" => ' ',

                ],
                "comments" => [
                    "label" => 'comments',
                    "value" => ' ',

                ],
                "action" => [
                    "label" => 'action',
                    "value" => ' ',

                ],
                "HR_manager" => [
                    "label" => 'HR_manager',
                    "value" => ' ',

                ],
                "date" => [
                    "label" => 'date',
                    "value" => ' ',

                ],
            ],
        ],
    ],
    'bereavement-leave' => [
        'title' => 'Bereavement Leave Form',
        'description' => 'A Bereavement Leave Form is a document used by employees to formally request time off from work due to the death of a family member or close friend.',
        'fields' => [
            "date" => [
                "name" => "date",
                "label" => "Date",
                "type" => "date",
                "rules" => [
                    "required",
                    "date",
                ],
                "required" => true,
            ],
            "employee_number" => [
                "name" => 'employee_number',
                "label" => "Employee #",
                "type" => "number",
                "step" => "1",
                "min" => "1000",
                "max" => "9999",
                "minlength" => "1000",
                "maxlength" => "9999",
                "required" => true,
                "rules" => ["required"],
                "placeholder" => "Enter Employee number ",
            ],
            "name" => [
                "name" => 'name',
                "label" => "Name",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter your full name",
            ],
            "leave_type" => [
                "name" => "leave_type",
                "label" => "Subject: Bereavement Leave",
                "type" => "checkbox-group",
                "options" => [
                    "Paid",
                    "Unpaid up to three days",
                ],
                "rules" => [
                    "required",
                    "array",
                    Rule::in([
                        "Paid",
                        "Unpaid up to three days",
                    ]),
                ],
                "required" => true,
            ],
            "qty_days" => [
                "name" => 'qty_days',
                "label" => "I am requsting bereavement leave for ",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "365",
                "minlength" => "1",
                "maxlength" => "365",
                "required" => true,
                "rules" => ["required"],
                "placeholder" => "Enter the number of days ",
            ],
            "date_range_start" => [
                "label" =>
                "From ",
                "type" => "date",
                "required" => true,
                "rules" => ["required", "date"],
            ],
            "date_range_end" => [
                "label" =>
                "To ",
                "type" => "date",
                "rules" => [
                    "required",
                    "date",
                    "after_or_equal:date_range_start",
                ],
                "required" => true,
            ],
            "name_of_decased" => [
                "name" => 'name_of_decasedme',
                "label" => "Due to the death of ",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Name of decased",
            ],
            "is_the_decased_a" => [
                "name" => "is_the_decased_a",
                "label" => "Please choose one of the following",
                "type" => "checkbox-group",
                "options" => [
                    "Spouse",
                    "Child",
                    "Parent",
                    "Parent in law",
                    "Sibling or Grandchild",
                    "Sibling in law",
                    "Niece",
                    "Nephew or Grandparent",
                    "Uncle",
                    "Aunt or 1st Cousin",
                ],
                "rules" => [
                    "required",
                    "array",
                    Rule::in([
                        "Spouse",
                        "Child",
                        "Parent",
                        "Parent in law",
                        "Sibling or Grandchild",
                        "Sibling in law",
                        "Niece",
                        "Nephew or Grandparent",
                        "Uncle",
                        "Aunt or 1st Cousin",
                    ]),
                ],
                "required" => true,
            ],
            'notes-family' => [
                'label' => 'Your consideration in this matter is appreciated',
                'type' => 'notes',
                'value' => "Unpaid leave 3 day's for any family member",
                'class' => 'p-1 w-fit border border-seagreen-700',
            ],
            'notes-guide' => [
                'label' => 'Paid Leave guidelines',
                'type' => 'notes',
                'value' => "3 days - Spouse, child, Parent/in law, Sibling, or Grandchild
                1 day - Sibling in law, Niece, Nephew and Grandparent,
                1/2 day - Uncle, Aunt and 1st Cousin",
                'class' => 'p-2 w-fit border border-seagreen-700',
            ],
            'notes-ref' => [
                'label' => '',
                'type' => 'notes',
                'value' => "For mor information please look up in the employee handbook pg. 60 or contact Human Resource Manager",
                'class' => 'p-1 text-xs',
            ],
            'employee_initials' => [
                'label' => 'Employee Initials',
                'type' => 'text',
                'required' => true,
                'placeholder' => 'Enter your initials',
                'rules' => ["required", "max:15"],
            ],
            // Hidden fields PDF
            '_hidden_fields' => [
                "approved_supervison_signature" => [
                    "label" => 'approved_supervison_signature',
                    "value" => ' ',
                ],
                "approved_human_resources" => [
                    "label" => 'approved_human_resources',
                    "value" => ' ',
                ],
            ],
        ],
    ],
    'staff-request' => [
        'title' => 'Staffing Request Form',
        'description' => 'A Staff Request Form is a form template designed to serve as a formalized process for department managers to communicate their staffing needs to human resources, recruitment teams, or relevant decision-makers within the organization.',
        'fields' => [
            'section_one' => [
                'label' => '',
                'value' => 'Position Information',
                'type' => 'notes',
                'class' => 'py-1 px-2 bg-slate-700 text-white text-lg font-bold'
            ],
            'type_of_position' => [
                'label' => 'Type of position',
                'name' => 'type_of_position',
                'type' => 'radio',
                'required' => true,
                "rules" => ["required"],
                'options' => ["New Position", "Replacement"],
            ],
            "date_of_request" => [
                "name" => "date_of_request",
                "label" => "Date of Request",
                "type" => "date",
                "rules" => [
                    "required",
                    "date",
                ],
                "required" => true,
            ],
            "previous_employee" => [
                "name" => 'previous_employeeme',
                "label" => "Name of Previous Employee",
                "type" => "text",
                "required" => false,
                "rules" => ["nullable", "max:255"],
                "placeholder" => "Fill this field when choose replacement with a full name",
                'depends_on' => [
                    'field' => 'position',
                    'disable_when' => "Replacement",
                ],
                "conditional-rules" => [
                    "when" => [
                        "field" => "position",
                        "value" => "Replacement",
                        "rules" => ["required", "max:255"],
                    ],
                ],
            ],
            "department" => [
                "name" => 'department',
                "label" => "Department",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter departmnet",
            ],
            "shift" => [
                'name' => 'shift',
                "label" => "Shift",
                "type" => "select",
                "options" => [
                    "Day-shift",
                    "Evening-shift",
                    "Night-shift",
                ],
                "rules" => ["required"],
                "required" => true,
            ],
            "supervisor" => [
                "name" => 'supervisor',
                "label" => "Supervisor",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter Supervisor Name",
            ],
            "position_title" => [
                "name" => 'position_title',
                "label" => "Position title of new Candidate",
                "type" => "text",
                "required" => true,
                "rules" => ["required", "max:255"],
                "placeholder" => "Enter position title for a new candidate",
            ],
            "required_start_date" => [
                "name" => "required_start_date",
                "label" => "Required Start Date",
                "type" => "date",
                "rules" => [
                    "required",
                    "date",
                ],
                "required" => true,
            ],
            "required_end_date" => [
                "name" => "required_end_date",
                "label" => "End Date(if Applicable)",
                "type" => "date",
                "rules" => [
                    "nullable",
                    "date",
                    "after_or_equal:req_start_date",
                ],
                "required" => false,
            ],
            "pay_range_from" => [
                "name" => 'pay_range_from',
                "label" => "Pay range from ",
                "type" => "number",
                "step" => "0.01",
                "min" => "15.80",
                "minlength" => "15.80",
                "required" => true,
                "rules" => ["required"],
                "placeholder" => "Minimum wage is $15.80 per hour effective October 1, 2024. ",
            ],
            "pay_range_to" => [
                "name" => 'pay_range_to',
                "label" => "Pay range to ",
                "type" => "number",
                "step" => "0.01",
                "min" => "15.80",
                "minlength" => "15.80",
                "required" => true,
                "rules" => ["required"],
                "placeholder" => "Minimum wage is $15.80 per hour effective October 1, 2024. ",
            ],
            'work_type' => [
                'label' => 'Choose type of work',
                'name' => 'work_type',
                'type' => 'radio',
                'required' => false,
                "rules" => ["required"],
                'options' => ["Permanent", "Temporary"],
                "required" => true,
            ],
            'schedule_type' => [
                'label' => 'Choose type of time',
                'name' => 'schedule_type',
                'type' => 'radio',
                'required' => false,
                "rules" => ["required"],
                'options' => ["Full Time", "Part Time"],
                "required" => true,
            ],
            "training_required_on_a_different_shift" => [
                "name" => "training_required_on_a_different_shift",
                "label" => "Training Required on a different shift",
                "type" => "radio",
                "options" => [
                    "Yes",
                    "No",
                ],
                "rules" => [
                    "required",
                    Rule::in([
                        "Yes",
                        "No",
                    ]),
                ],
                "required" => true,
            ],
            "training_shift" => [
                "name" => 'training_shift',
                "label" => "Choose shift for training",
                "type" => "radio",
                "required" => false,
                "options" => [
                    "Day-shift",
                    "Evening-shift",
                    "Night-shift",
                    "Other – Please specify",
                ],
                "rules" => [
                    "nullable",
                    Rule::in([
                        "Day-shift",
                        "Evening-shift",
                        "Night-shift",
                        "Other – Please specify",
                    ]),
                ],
                'depends_on' => [
                    'field' => 'training_required_on_a_different_shift',
                    'disable_when' => "Yes",
                ],
                "conditional-rules" => [
                    "when" => [
                        "field" => "training_required_on_a_different_shift",
                        "value" => "Yes",
                        "rules" => ["required"],
                    ],
                ],
            ],
            "other_shift_specify" => [
                "name" => 'other_shift_specify',
                "label" => "Other shift specify",
                "type" => "text",
                "required" => false,
                "rules" => ["nullable", "max:255"],
                "placeholder" => "Specify shift",
                'depends_on' => [
                    'field' => 'training_shift',
                    'disable_when' => "Other – Please specify",
                ],
            ],
            "rationale_for_staffing_request" => [
                "name" => 'rationale_for_staffing-request',
                "label" => "Rationale for Staffing Request",
                "type" => "textarea",
                "required" => false,
                "rules" => ["nullable", "max:16350"],
            ],
            'section_competency' => [
                'label' => '',
                'value' => 'Competency\'s needed for the job 0-10 ( please stay objective and as realistic as possible ) 0-not 5-needed10 -critical',
                'type' => 'notes',
                'class' => 'py-1 px-2 bg-slate-700 text-white text-lg font-bold'
            ],
            'core_head' => [
                'label' => '',
                'value' => 'Core',
                'type' => 'notes',
                'class' => 'p-2 text-2xl text-center font-bold'
            ],
            "woodworking_mech_skill" => [
                "name" => 'woodworking_Mech_skill',
                "label" => "Woodworking/Mech. skill",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "knowledge_of_product_or_machine" => [
                "name" => 'knowledge_of_product_or_machine',
                "label" => "Knowledge of Product/Machine",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "reliability_transportation" => [
                "name" => 'reliability_transportation',
                "label" => "Reliability/Transportation",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "drivers_license" => [
                "name" => 'drivers_license',
                "label" => "Drivers License",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "Demonstrated_ability_to_learn" => [
                "name" => 'Demonstrated_ability_to_learn',
                "label" => "Demonstrated ability to learn",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "understanding_of_good_leadership" => [
                "name" => 'understanding_of_good_leadership',
                "label" => "Understanding of good leadership",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "Reason_for_starting_here" => [
                "name" => 'Reason_for_starting_here',
                "label" => "Reason for Starting here",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "Communication_and_interaction_with_others" => [
                "name" => 'Communication_and_interaction_with_others',
                "label" => "Communication and Interaction with Others",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "Member_1_Leader_10" => [
                "name" => 'Member_1_Leader_10',
                "label" => "Member – 1 Leader - 10",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "goal_driven" => [
                "name" => 'goal_driven',
                "label" => "Goal Driven",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "motivation_for_the_job" => [
                "name" => 'motivation_for_the_job',
                "label" => "Motivation for the Job",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "career_oriented" => [
                "name" => 'career_oriented',
                "label" => "Career Oriented",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "good_self_assessment_honest" => [
                "name" => 'good_self_assessment_honest',
                "label" => "Good self assessment, Honest",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            'functional_head' => [
                'label' => '',
                'value' => 'Functional/Job',
                'type' => 'notes',
                'class' => 'p-2 text-2xl text-center font-bold'
            ],
            "read_write_undstd" => [
                "name" => 'read_write_undstd',
                "label" => "Read – Write - Undstd.",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "neatness_of_writing" => [
                "name" => 'neatness_of_writing',
                "label" => "Neatness of writing",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "differentiating_of_colors" => [
                "name" => 'differentiating_of_colors',
                "label" => "Differentiating of Colors",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "math_skills" => [
                "name" => 'math_skills',
                "label" => "Math Skills",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "problem_solving" => [
                "name" => 'problem_solving',
                "label" => "Problem Solving",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "exposure_to_dust_chemicals" => [
                "name" => 'exposure_to_dust_chemicals',
                "label" => "Exposure to Dust/Chemicals",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "measurements_mm_inch" => [
                "name" => 'measurements_mm_inch',
                "label" => "Measurements mm, inch",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            'additional_head' => [
                'label' => '',
                'value' => 'Additional',
                'type' => 'notes',
                'class' => 'p-2 text-2xl text-center font-bold'
            ],
            "physical_abilities" => [
                "name" => 'physical_abilities',
                "label" => "Physical abilities",
                "type" => "number",
                "step" => "1",
                "min" => "1",
                "max" => "10",
                "minlength" => "1",
                "maxlength" => "10",
                "placeholder" => " 0-not 5-needed 10 -critical",
            ],
            "additional_abilities" => [
                "name" => 'additional_abilities',
                "label" => "Please, specify any additional Competency you may need",
                "type" => "textarea",
                "required" => false,
                "rules" => ["nullable", "max:1255"],
            ],
            "additional_KSAO" => [
                "name" => 'additional_KSAO',
                "label" => "Any additional KSAO (Knowledge, Skill, Ability and Other attribute)",
                "type" => "textarea",
                "required" => false,
                "rules" => ["nullable", "max:1255"],
            ],
            "power_tool_training_required_during" => [
                "name" => "power_tool_training_required_during",
                "label" => "Power Tool Training required during",
                "type" => "checkbox-group",
                "options" => [
                    "First week",
                    "Second week",
                    "Within 3 month",
                ],
                "rules" => [
                    "array",
                    Rule::in([
                        "First week",
                        "Second week",
                        "Within 3 month",
                    ]),
                ],
            ],
            "tools_first_week" => [
                "label" => "",
                "type" => "text",
                "related-to" => "First week",
                'depends_on' => [
                    'field' => 'power_tool_training_required_during[]',
                    'disable_when' => 'First week',
                ],
                "placeholder" => "Enter text",
            ],
            "tools_second_week" => [
                "label" => "",
                "type" => "text",
                "related-to" => "Second week",
                'depends_on' => [
                    'field' => 'power_tool_training_required_during[]',
                    'disable_when' => 'Second week',
                ],
                "placeholder" => "Enter text",
            ],
            "tools_3_month" => [
                "label" => "",
                "type" => "text",
                "related-to" => "Within 3 month",
                'depends_on' => [
                    'field' => 'power_tool_training_required_during[]',
                    'disable_when' => 'Within 3 month',
                ],
                "placeholder" => "Enter text",
            ],
            '_hidden_fields' => [
                "supervisor_signature" => [
                    "label" => 'supervisor_signature',
                    "value" => ' ',
                ],
                "supervisor_signature_date" => [
                    "label" => 'date',
                    "value" => ' ',
                ],
                "production_manager_or_office_manager_signature" => [
                    "label" => 'production_manager_or_office_manager_signature',
                    "value" => ' ',
                ],
                "production_manager_or_office_manager_signature_date" => [
                    "label" => 'date',
                    "value" => ' ',
                ],
                "Executive_vice_president_human_resources" => [
                    "label" => 'Executive_vice_president_human_resources',
                    "value" => ' ',
                ],
                "Executive_vice_president_human_resources_date" => [
                    "label" => 'date',
                    "value" => ' ',
                ],
                "position_description_reviewed" => [
                    "label" => 'position_description_reviewed',
                    "value" => ' ',
                ],
                "Previous_job_posting_reviewed" => [
                    "label" => 'Previous_job_posting_reviewed',
                    "value" => ' ',
                ],
                "media_advertising_confirmed" => [
                    "label" => 'media_advertising_confirmed',
                    "value" => ' ',
                ],
                "web_page_updated" => [
                    "label" => 'web_page_updated',
                    "value" => ' ',
                ],
                "in_house_posting_updated" => [
                    "label" => 'in_house_posting_updated',
                    "value" => ' ',
                ],
                "date" => [
                    "label" => 'date',
                    "value" => ' ',
                ],
            ],
        ],
    ],
];
