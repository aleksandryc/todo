import { createApp } from 'vue';
import FormApp from './components/FormApp.vue';

const el = document.getElementById('form-app');
if (el) {
    const app = createApp(FormApp, {
        formKey: el.dataset.formKey,
        formConfig: el.dataset.formConfig ? JSON.parse(el.dataset.formConfig) : {},
        formComponents: JSON.parse(el.dataset.formComponents ?? '{}'),
        oldInput: JSON.parse(el.dataset.oldInput ?? '{}'),
        csrfToken: el.dataset.csrfToken,
        actionUrl: el.dataset.actionUrl,
    });
    // Don't foorget about sentry if needed

    app.mount('#form-app');
}
