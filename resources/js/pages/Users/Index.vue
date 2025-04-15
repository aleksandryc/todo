<!-- eslint-disable vue/no-v-text-v-html-on-component -->
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import Paginator from '../Shared/Paginator.vue';

const props = defineProps({
    users: Object,
    filters: Object,
    can: Object,
});

const search = ref(props.filters?.search);

watch(
    search,
    debounce(function (value) {
        router.get('/users', { search: value }, { preserveState: true, replace: true });
    }, 500),
);
</script>

<template>
    <div class="mb-6 flex justify-between border-b border-green-100 p-2">
        <div class="flex items-center">
            <p class="text-3xl">This is users page!</p>
            <Link v-if="can?.create" href="/users/create" class="text-shadow-lg/30 mt-2 rounded-md px-4 text-green-100">Create new user</Link>
        </div>
        <input v-model="search" type="text" placeholder="search..." class="rounded-md border-2 border-gray-200 p-2 text-black" />
    </div>
    <div class="container mx-auto max-w-screen-md">
        <div class="relative overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400 rtl:text-right">
                <thead class="bg-gray-50 text-xs uppercase text-gray-700 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="w-1/4 px-6 py-3 text-center">User avatar</th>
                        <th scope="col" class="px-6 py-3">Unser name</th>
                        <th scope="col" class="px-6 py-3 text-right">Rules</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user, index in users?.data" :key="user.id" class="border-b border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800" :class="{'dark:bg-slate-700': index % 2 === 1}" >
                        <td scope="row" class="justify-items-center whitespace-nowrap px-6 py-4 font-medium text-gray-900 dark:text-white">
                            <img class="size-12 flex-none rounded-full bg-gray-50" :src="'https://robohash.org/' + user.name + '.png'" alt="" />
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-lg font-semibold text-white">{{ user.name }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <div v-if="user.can?.update" class="hidden shrink-0 space-x-2 sm:flex sm:flex-col sm:items-end">
                                <Link :href="'/users/' + user.id + '/edit'" class="space-x-2 text-sm/6 text-white" as="button" method="get"
                                    >Edit</Link
                                >
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">
                            <div class="container mx-auto flex justify-center">
                                <Paginator :users="users" />
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</template>
