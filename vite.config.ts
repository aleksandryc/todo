import vue from '@vitejs/plugin-vue';
//import autoprefixer from 'autoprefixer';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import tailwindcss from '@tailwindcss/vite'
import { resolve } from 'node:path';
import { defineConfig } from 'vite';
import DefineOptions from 'unplugin-vue-define-options/vite'

export default defineConfig({
    plugins: [
        tailwindcss(),
        DefineOptions(),
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
            'ziggy-js': resolve(__dirname, 'vendor/tightenco/ziggy'),
        },
    },
    /* css: {
        postcss: {
            plugins: [autoprefixer, tailwindcss],
        },
    }, */
});
