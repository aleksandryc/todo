<template>
    <form
        class="container mx-auto mt-4 max-w-md rounded-md border border-s-green-800 p-4"
        :action="actionUrl"
        method="post"
        enctype="multipart/form-data"
        @submit.prevent="handleSubmit"
    >
        <input type="hidden" name="_token" :value="csrfToken" />
        <div v-for="(field, name) in formComponents" :key="name" class="mb-4">
            <label :for="name" class="mb-1 block font-semibold">
                {{ field.label }}
                <span v-if="field.required" class="text-red-500">*</span>
            </label>

            <!-- Text fields email, date -->
            <input
                v-if="['text', 'email', 'date'].includes(field.type)"
                :type="field.type"
                :name="name"
                :id="name"
                v-model="formData[name]"
                class="w-full rounded border px-3 py-2"
                :placeholder="field.placeholder || ''"
                :required="field.required"
            />
            <!-- Phone number with input mask -->
            <input
                v-if="field.type === 'tel'"
                :name="name"
                :id="name"
                v-model="formData[name]"
                class="w-full rounded border px-3 py-2"
                :placeholder="field.placeholder || ''"
                :required="field.required"
            />

            <!-- Textarea -->
            <textarea
                v-if="field.type === 'textarea'"
                :name="name"
                :id="name"
                v-model="formData[name]"
                class="w-full rounded border px-3 py-2"
                :required="field.required"
            ></textarea>

            <!-- Textarea -->
            <select
                v-if="field.type === 'select'"
                :name="name"
                :id="name"
                v-model="formData[name]"
                class="w-full rounded border px-3 py-2"
                :required="field.required"
            >
                <option value="">---Select---</option>
                <option v-for="option in field.options" :key="option" :value="option" :selected="formData[name] === option">{{ option }}</option>
            </select>

            <!-- Radio button-->
            <div v-if="field.type === 'radio'" class="mt-1 flex gap-4">
                <label v-for="option in field.options" :key="option">
                    <input type="radio" :name="name" :value="option" v-model="formData[name]" :required="field.required" class="mr-2" />{{ option }}
                </label>
            </div>

            <!-- Checkbox -->
            <div v-if="field.type === 'checkbox'" class="mt-1 flex gap-4">
                <label>
                    <input type="checkbox" :name="name" :value="1" v-model="formData[name]" :required="field.required" />{{ field.options }}
                </label>
            </div>

            <!-- Checkbox group -->
            <div v-if="field.type === 'checkbox-group'" class="mt-1 flex gap-4">
                <label v-for="option in field.options" :key="option" class="flex items-center">
                    <input
                        type="checkbox"
                        :name="`${name}[]`"
                        :value="option"
                        v-model="formData[name]"
                        :checked="formData[name]?.includes(option)"
                        @change="updateCheckbox(name, option, ($event.target as HTMLInputElement)?.checked)"
                        :required="field.required && !formData[name]?.length"
                        class="mr-2"
                        />
                        {{ option }}
                </label>
            </div>

            <!-- File and URL -->
            <input
                v-if="['file', 'url'].includes(field.type)"
                :type="field.type"
                :name="name"
                :id="name"
                class="w-full rounded border px-3 py-2"
                :placeholder="field.placeholder || ''"
                :required="field.required"
            />

            <!-- Errors -->

            <p v-if="errors[name]" class="mt-1 text-sm text-red-500">{{ errors[name][0] }}</p>
        </div>
        <div class="flex justify-between">
            <button type="submit" class="bg-green-700 px-4 py-2 text-white">Send</button>
            <button type="reset" class="bg-red-800 px-4 py-2 text-white">Clear form</button>
        </div>
    </form>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import axios from 'axios';

export default defineComponent({
    props: {
        formKey: {
            type: String,
            required: true,
        },
        formConfig: {
            type: Object,
            required: true,
        },
        formComponents: {
            type: Object,
            required: true,
        },
        oldInput: {
            type: Object,
            default: () => ({}),
        },
        csrfToken: {
            type: String,
            required: true,
        },
        actionUrl: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        //Form data
        const formData = ref(
            Object.keys(props.formComponents).reduce((acc, name) => {
                // For checkbox group init empty array if not old input
                if (props.formComponents[name].type === 'checkbox-group'){
                    acc[name] = Array.isArray(props.oldInput[name])
                    ? [...props.oldInput[name]]
                    : [];
                } else {
                    acc[name] = props.oldInput[name] ?? null;
                }
                return acc;
            }, {} as Record<string, any>),
        );
        const errors = ref<Record<string, string[]>>({});
        //Hadle checkbox-group
        const updateCheckbox = (name: string, option: string, checked: boolean) => {
            // Check that formData[name] always is Array
            if (!Array.isArray(formData.value[name])) {
                formData.value[name] = [];
            }
            if (checked) {
                if (!formData.value[name].includes(option)) {
                    formData.value[name].push(option);
                }
            } else {
                formData.value[name] = formData.value[name].filter(
                    (item: string) => item !== option,
                );
            }
            console.log(`Checkbox ${name} updated:`, formData.value[name]);
        };
        //Send form data
        const handleSubmit = async () => {
            const form = new FormData();
            for (const [key, value] of Object.entries(formData.value)) {
                if (Array.isArray(value)) {
                    value.forEach((val) => form.append(`${key}[]`, val));
                } else if (value !== null && value !== undefined) {
                    form.append(key, value);
                }
            }
            try {
                await axios.post(props.actionUrl, form, {
                    headers: {
                        'X-CSRF-TOKEN': props.csrfToken,
                        'Content-Type': 'multipart/form-data',
                    },
                });
                window.location.reload();
            } catch (error) {
                if (axios.isAxiosError(error) && error.response && error.response.status === 422) {
                    errors.value = error.response.data.errors;
                } else {
                    console.error('Form submission error:', error);
                }
            }
        };
        return { formData, errors, handleSubmit, updateCheckbox };
    },
});
</script>

<style scoped></style>
