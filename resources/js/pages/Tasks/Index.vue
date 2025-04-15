<template>
    <div class="container mx-auto flex justify-center">
        <h1 class="mb-8 text-3xl tracking-wide text-shadow-lg">This is tasks page!</h1>
    </div>
    <div class="container flex justify-between">
        <Link
            href="/tasks/create"
            class="mb-4 inline-flex items-center rounded-lg bg-blue-700 px-5 py-2.5 text-center text-sm font-medium text-white hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
        >
            Create Task</Link
        >

        <input v-model="search" type="text" placeholder="search..." class="rounded-md border-2 border-gray-200 p-2 text-black" />
    </div>
    <div  class="container mx-auto justify-center flex flex-wrap">
        <div v-for="task in tasks" :key="task.id" class="relative flex w-[20%] m-6 flex-col rounded-lg border border-gray-800 shadow-lg shadow-slate-200  max-h-44 overflow-auto" >
            <Link :href="'/tasks/'+ task.id + '/update'">
                <div class="mx-3 flex justify-between border-b border-slate-200 px-1 pb-2 pt-3">
                    <span class="text-sm font-medium text-white"> {{ task.name }} </span>
                    <span
                        class="text-sm font-medium p-1"
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
                    <p v-for="user in task.users" :key="user.name" class="font-light leading-normal text-white">User: {{ user.name }}</p>
                </div>
        </Link>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';

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
