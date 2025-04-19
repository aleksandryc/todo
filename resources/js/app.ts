import '../css/app.css';

import Layout from './pages/Shared/Layout.vue'
import { createInertiaApp, Link } from '@inertiajs/vue3';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { initializeTheme } from './composables/useAppearance';

createInertiaApp({
    title: (title) => `${title} - My App`,
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: true });
        const page = pages[`./pages/${name}.vue`] as { default: any };
        if (page.default.layout === undefined) {
            page.default.layout = Layout;
        }
        return { default: page.default } as { default: DefineComponent };
      },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        // The delay after which the progress bar will appear, in milliseconds...
        delay: 210,

        // The color of the progress bar...
        color: '#red',

        // Whether to include the default NProgress styles...
        includeCSS: true,

        // Whether the NProgress spinner will be shown...
        showSpinner: true,
    },
});

// This will set light / dark mode on page load...
initializeTheme();

