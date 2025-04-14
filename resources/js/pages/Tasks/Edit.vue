<template>
    <AppHead :title="'Edit Task ' + props.task?.name" />
    <div>
        <h1 class="text-3xl">Edit Task</h1>
    </div>
    <form @submit.prevent="submit" class="mx-auto mt-8 max-w-sm">
        <div class="mb-5">
            <label for="name" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Task Name</label>
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
            <label for="description" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Your description</label>
            <input
                v-model="form.description"
                placeholder="description"
                type="description"
                id="description"
                class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
            />
            <div v-if="page.props.errors" v-text="page.props.errors.email" class="mt-4 text-center text-xs text-red-500"></div>
        </div>
        <div class="mb-5">
            <label for="status" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Choose a status:</label>
            <select class="flex min-w-full bg-slate-200 text-gray-900" name="status" id="status" size="1" v-model="form.status">
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
        <div class="mb-5">
            <label for="users" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Choose a users:</label>
            <select v-model="form.seluser" multiple size="6" class="flex min-w-full bg-slate-200 text-gray-900" name="users" id="users">
                <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
                </option>
            </select>
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
import { router, usePage } from '@inertiajs/vue3';
import { reactive } from 'vue';
import AppHead from '../Shared/AppHead.vue';

const page = usePage();
const props = defineProps({
    task: Object,
    users: Object,
    asainedUsers: Object,
});
const form = reactive({
    name: props.task?.name || '',
    description: props.task?.description || '',
    seluser: props.asainedUsers || [],
    status: '',
    id: props.task?.id || '',
    processing: false, // Added processing property
});
const uid = props.task?.id;

/* const submit = () => {
    router.post('/tasks/' + uid + '/update', form);
}; */

const submit = (event) => {
    const action = event.submitter.value;
    if (action === 'delete') {
        router.post('/tasks/' + form.id + '/delete', form.id);
    } else if (action === 'update') {
        router.post('/tasks/' + uid.value + '/update', form);
    }
};
</script>

<style scoped></style>
