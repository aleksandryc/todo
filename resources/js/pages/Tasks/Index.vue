<template>
    <div>
        <h1 class="mb-8 text-3xl">This is tasks page!</h1>
    </div>
    <div>
        <Link href="/tasks/create"
        class="mb-4 inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5
        text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none
        focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Create Task</Link>
    </div>
    <div class="flex flex-wrap space-x-2 ml-1">
        <div v-for="task in tasks" :key="task.id" class="relative my-6 flex w-72 flex-col rounded-lg border border-slate-200  shadow-sm">
            <div class="flex justify-between mx-3 mb-0 border-b border-slate-200 px-1 pb-2 pt-3">
                <span class="text-sm font-medium text-white"> {{ task.name }} </span>
                <span class="text-sm font-medium"
                    :class="{
                        'text-green-500' : task.status === 'completed',
                        'text-yellow-500' : task.status === 'in_progress',
                        'text-red-500' : task.status === 'pending',
                        }">{{ task.status }}</span>
            </div>
            <div class="p-4">
                <h5 class="mb-2 text-xl font-semibold text-white">{{ task.description }}</h5>
                <p v-for="user in task.users" :key="user.name" class="font-light leading-normal text-white">
                    User: {{ user.name }}
                </p>
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps({
    tasks: Object,
});
</script>

<style scoped></style>
