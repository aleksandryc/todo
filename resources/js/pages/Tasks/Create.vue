<template>
    <AppHead :title="'Create User'" />
    <div>
        <h1 class="text-3xl">Create New Task</h1>
    </div>
    <section class="mt-8">
        <form @submit.prevent="submit" class="mx-auto mt-8 max-w-sm">
            <div class="mb-5">
                <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Task Name</label>
                <input
                    autocomplete="off"
                    v-model="form.name"
                    type="text"
                    id="name"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                    placeholder="Task name"
                    required
                />
                <div v-if="page.props.errors" v-text="page.props.errors.name" class="mt-4 text-center text-xs text-red-500"></div>
            </div>
            <div class="mb-5">
                <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Your description</label>
                <textarea
                    autocomplete="off"
                    rows="2"
                    cols="50"
                    v-model="form.description"
                    placeholder="Description here"
                    type="text"
                    id="description"
                    class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                />
                <div v-if="page.props.errors" v-text="page.props.errors.email" class="mt-4 text-center text-xs text-red-500"></div>
            </div>
            <div class="mb-5">
                <label for="users" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Choose an users:</label>
                <select multiple class="flex min-w-full bg-transparent text-gray-300" name="users" id="users" size="6" v-model="form.users">
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
            </div>
            <div class="mb-5">
                <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Choose a status:</label>
                <select class="flex min-w-full bg-transparent text-gray-100" name="status" id="status" size="1" v-model="form.status">
                    <option
                        v-for="status in ['pending', 'in_progress', 'completed']"
                        :key="status"
                        :value="status"
                        :class="{
                            'text-green-500': status === 'completed',
                            'text-yellow-500': status === 'in_progress',
                            'text-red-500': status === 'pending',
                        }"
                    >
                        {{ status }}
                    </option>
                </select>
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
import { router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppHead from '../Shared/AppHead.vue';

defineProps({
    users: Object,
    tasks: Object,
});

const page = usePage();

const form = reactive({
    name: '',
    description: '',
    users: [],
    status: '',
});

const submit = () => {
    router.post('/tasks', form);
};
</script>

<style scoped></style>
