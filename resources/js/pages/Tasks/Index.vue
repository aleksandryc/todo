<template>
    <AppHead title="List of users tasks" />
    <div class="container mx-auto flex justify-center">
        <h1 class="mb-2 text-3xl text-shadow-md text-shadow-blue-500">This is tasks page!</h1>
    </div>
    <div class="container flex justify-between">
        <Link
            href="/tasks/create"
            class="mb-4 inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 hover:shadow-md hover:shadow-blue-400 focus:ring-4 focus:ring-blue-300 focus:outline-none dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
            Create Task</Link
        >

        <input v-model="search" type="text" placeholder="search..." class="rounded-md border-2 border-gray-200 bg-black p-2 text-amber-50" />
    </div>
    <div class="container mx-auto flex flex-wrap justify-center">
        <div
            v-for="task in tasks"
            :key="task.id"
            class="relative m-6 flex max-h-44 w-[20%] flex-col overflow-auto rounded-lg border border-gray-800 shadow-lg shadow-slate-200"
        >
            <Link :href="'/tasks/' + task.id + '/update'">
                <div class="mx-3 flex justify-between border-b border-slate-200 px-1 pt-3 pb-2">
                    <span class="text-sm font-medium text-white"> {{ task.name }} </span>
                    <span
                        class="p-1 text-sm font-medium"
                        :class="{
                            'text-green-500': task.status === 'completed',
                            'text-yellow-500': task.status === 'in_progress',
                            'text-red-500': task.status === 'pending',
                        }"
                        >{{ task.status }}</span
                    >
                </div>
                <div class="p-4">
                    <h5 class="mb-2 text-xl font-semibold text-white">{{ task.description }}</h5>
                    <p v-for="user in task.users" :key="user.name" class="leading-normal font-light text-white">User: {{ user.name }}</p>
                </div>
            </Link>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import AppHead from '../Shared/AppHead.vue';

const props = defineProps({
    tasks: Object,
    filters: Object,
});

const search = ref(props.filters?.search);

watch(
    search,
    debounce(function (value) {
        router.get('/tasks', { search: value }, { preserveState: true, replace: true });
    }, 500),
);
</script>

<style scoped></style>
