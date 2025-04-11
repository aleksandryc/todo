<!-- eslint-disable vue/no-v-text-v-html-on-component -->
<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { ref, watch } from 'vue';
import Paginator from '../Shared/Paginator.vue';

const props = defineProps({
    users: Object,
    filters: Object,
    can: Object
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

    <section>
        <ul v-for="user in users?.data" :key="user.id" role="list" class="divide-y divide-gray-100">
            <li class="flex justify-between space-x-6 py-4">
                <div class="flex min-w-0 gap-x-4">
                    <img class="size-12 flex-none rounded-full bg-gray-50" :src="'https://robohash.org/' + user.name + '.png'" alt="" />
                    <div class="min-w-0 flex-auto p-3">
                        <p class="text-sm/6 font-semibold text-white">{{ user.name }}</p>
                    </div>
                </div>
                <div v-if="user.can?.update" class="hidden space-x-2 shrink-0 sm:flex sm:flex-col sm:items-end">
                    <Link :href="'/users/' + user.id + '/edit'" class="text-sm/6 text-white space-x-2" as="button" method="get">Edit</Link>
                </div>

            </li>
        </ul>
    </section>
    <Paginator :users="users" />
</template>
