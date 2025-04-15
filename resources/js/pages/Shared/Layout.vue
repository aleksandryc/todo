<template>
    <AppHead :title="appname">

    </AppHead>
    <div class="mb-8 flex min-h-8 justify-between border-b border-white bg-slate-900 sticky top-0 z-50">
        <Header />

        <Nav />
    </div>
    <div v-if="flashMessage" class="bg-slate-900 p-4 text-center text-2xl font-bold text-red-600">
        <p class="text-sm font-bold">{{ flashMessage }}</p>
    </div>
    <div class="container mx-auto">
        <slot />
    </div>
</template>

<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import AppHead from './AppHead.vue';
import Header from './Header.vue';
import Nav from './Nav.vue';
import NProgress from 'nprogress';

const page = usePage();

const flashMessage = computed(() => {
    const flash = page.props.flash as { message?: string };
    return flash.message || '';
});

const appname = computed(() => page.component);

router.on('start', () => NProgress.start())
router.on('finish', () => NProgress.done())
</script>

<style scoped></style>
