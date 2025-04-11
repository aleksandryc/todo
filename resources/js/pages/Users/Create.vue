<template>
    <AppHead :title="'Create User'" />
    <div>
        <h1 class="text-3xl">Create New User</h1>
    </div>
    <section class="mt-8">
        <form @submit.prevent="submit" class="mx-auto max-w-sm mt-8">
            <div class="mb-5">
                <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">User Name</label>
                <input
                    v-model="form.name"
                    type="text"
                    id="name"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    placeholder="user name"
                    required
                />
                <div v-if="page.props.errors" v-text="page.props.errors.name" class="mt-4 text-red-500 text-center text-xs" ></div>
            </div>
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
                <div v-if="page.props.errors" v-text="page.props.errors.email" class="mt-4 text-red-500 text-center text-xs" ></div>
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
                <div v-if="page.props.errors" v-text="page.props.errors.password" class="mt-4 text-red-500 text-center text-xs" ></div>
            </div>

            <div class="flex justify-end">
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

<script setup lang="ts">
import { reactive } from 'vue';
import AppHead from '../Shared/AppHead.vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = reactive({
    name: '',
    email: '',
    password: '',
});

const submit = () => {
    router.post('/users', form);
};
</script>

<style scoped></style>
