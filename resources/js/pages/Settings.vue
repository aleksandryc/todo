<script setup lang="ts">
import AppHead from './Shared/AppHead.vue';
import FormApp from '../components/FormApp.vue';
import { ref } from 'vue';

// Props
const props = defineProps({
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
    },
});
</script>

<template>
    <AppHead/>
    <FormApp 
        :formKey="props.formKey" 
        :formConfig="props.formConfig" 
        :formComponents="props.formComponents" 
        :csrfToken="'yourCsrfTokenHere'" 
        :actionUrl="'yourActionUrlHere'" 
    />
</template>
