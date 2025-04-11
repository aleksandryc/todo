<template>
    <AppHead title="Edit User" />
    <div>
        <h1 class="text-3xl">Edit User</h1>
    </div>
    <form @submit.prevent="submit" class="mx-auto mt-8 max-w-sm">
        <div class="mb-5">
            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">User Name</label>
            <input
                v-model="form.name"
                type="text"
                id="name"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                placeholder="user name"
            />
            <div v-if="page.props.errors" v-text="page.props.errors.name" class="mt-4 text-center text-xs text-red-500"></div>
        </div>
        <div class="mb-5">
            <label for="email" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Your email</label>
            <input
                v-model="form.email"
                placeholder="my@email.here"
                type="email"
                id="email"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
            />
            <div v-if="page.props.errors" v-text="page.props.errors.email" class="mt-4 text-center text-xs text-red-500"></div>
        </div>
        <div class="flex justify-between">
            <button
                :disabled="form.processing"
                type="submit"
                value="delete"
                name="action"
                class="w-full rounded-lg bg-red-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 sm:w-auto"
            >
                Delete
            </button>
            <button
                :disabled="form.processing"
                type="submit"
                value="update"
                name="action"
                class="w-full rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 sm:w-auto"
            >
                Update
            </button>
        </div>
    </form>
</template>

<script setup lang="ts">
import { router, useForm, usePage } from '@inertiajs/vue3';
import AppHead from '../Shared/AppHead.vue';

const page = usePage();

const props = defineProps({
    user: Object,
    id: Number,
    name: String,
    email: String,
});

const form = useForm({
    name: props.user?.name || '',
    email: props.user?.email || '',
    id: props.user?.id || '',
});
const uid = props.user?.id;
const submit = (event) => {
    const action = event.submitter.value;
    if (action === 'delete') {
        router.post('/users/' + form.id + '/delete', uid.value);
    } else if (action === 'update') {
        router.post('/users/' + uid.value + '/update', form);
    }
};
</script>

<style scoped></style>
