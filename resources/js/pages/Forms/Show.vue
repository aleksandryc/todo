<template>
    <div>
        <h1>{{ props.formConfig?.title }}</h1>
    </div>
    <form
        @submit.prevent="customForm.post('/forms/' + props.formKey + '/submit')"
        class="flex w-sm gap-1.5 flex-col mx-auto p-2.5 border-8 border-red-900/45 rounded-4xl silver-glossy"
        >
        <progress v-if="customForm.progress" :value="customForm.progress.percentage" max="100">{{ customForm.progress.percentage }}%</progress>
        <div v-for="(field, name) in formComponents" :key="name" class="flex justify-center mx-auto w-11/12">
            <div class="">
                <div class="flex-col w-fit mx-auto">
                    <label for="{{ name }}" class="p-1.5 text-[min(10vw, 12px)] font-bold mx-auto"
                        ><span class="p-1 align-text-top text-sm text-red-500">*</span>{{ field.label }}
                        <input :type="field.type" :name="name" class="" />
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-evenly space-x-2">
            <button class="m-2 rounded border-2 border-red-800 p-2 shadow-2xl shadow-amber-800 hover:bg-green-800 animate-pulse" @click="customForm.reset">Clear</button>
            <button
                class="m-2 rounded border border-red-800 p-2 shadow shadow-amber-800 hover:bg-green-900"
                type="submit"
                :disabled="customForm.processing"
            >
                Submit
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    formKey: String,
    formConfig: Object,
    formComponents: Object,
});
const customForm = useForm({});
</script>

<style scoped>
.silver-glossy {
    background: linear-gradient(135deg, #f5c3c3, #E0E0E0, #A0A0A0);
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
}
</style>
