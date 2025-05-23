<template>
    <div>
        <h1>{{ props.formConfig?.title }}</h1>
    </div>
    <form @submit.prevent="customForm.post('/forms/' + props.formKey + '/submit')">
        <progress v-if="customForm.progress" :value="customForm.progress.percentage" max="100">{{ customForm.progress.percentage }}%</progress>
        <div>{{ formComponents }}</div>
        <div class="flex justify-end space-x-2">
            <button class="m-2 rounded border border-red-800 p-2 shadow shadow-amber-800 hover:bg-amber-800" @click="customForm.reset">Clear</button>
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

<style scoped></style>
