<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import AppHead from '../Shared/AppHead.vue';

const page = usePage();

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('login');
};
</script>

<template>
    <AppHead :title="'Login page'" />
    <div class="grid place-items-center pt-10">
        <h1 class="mx-auto flex items-center text-center text-3xl">Login</h1>
    </div>
    <section class="mt-8 min-h-screen">
        <form @submit.prevent="submit" class="mx-auto mt-8 max-w-sm">
            <div class="mb-5">
                <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Your email</label>
                <input
                    v-model="form.email"
                    placeholder="my@email.here"
                    type="email"
                    id="email"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    required
                />
                <div v-if="page.props.errors" v-text="page.props.errors.email" class="mt-4 text-center text-xs text-red-500"></div>
            </div>
            <div class="mb-5">
                <label for="password" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Your password</label>
                <input
                    v-model="form.password"
                    placeholder="••••••••"
                    type="password"
                    id="password"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    required
                />
                <div v-if="page.props.errors" v-text="page.props.errors.password" class="mt-4 text-center text-xs text-red-500"></div>
            </div>

            <div class="grid place-items-center">
                <button
                    type="submit"
                    class="w-full rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 sm:w-auto"
                >
                    Submit
                </button>
            </div>
        </form>
    </section>
</template>
<script lang="ts">
export default {
    layout: null,
};
</script>
