<template>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url === '/' }" href="/">Home</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/users') }" href="/users">Users</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/settings') }" href="/settings">Settings</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/tasks') }" href="/tasks">Tasks</Link>
    <div class="relative inline-block text-left">
    <div class="group">
        <button type="button"
            class="inline-flex justify-center items-center w-full ">
            <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/name')}" href="/name/dashboard" >Name</Link>
            <!-- Dropdown arrow -->
            <svg class="w-4 h-4 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 12l-5-5h10l-5 5z" />
            </svg>
        </button>

        <!-- Dropdown menu -->
        <div v-if="role"
                class="absolute left-0.5 w-40 origin-top-left bg-white divide-y divide-gray-100 rounded-md shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition duration-200">
                <div class="py-1">
                    <!-- Admin Options -->
                    <template v-if="role === 'admin'">
                        <Link href="/name/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Dashboard</Link>
                        <Link href="/name/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Orders</Link>
                        <Link href="/name/tables" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tables</Link>
                    </template>

                    <!-- Worker Options -->
                    <template v-else-if="role === 'worker'">
                        <a href="/worker/tasks" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Tasks</a>
                        <a href="/worker/reports" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Reports</a>
                    </template>

                    <!-- Client Options -->
                    <template v-else-if="role === 'client'">
                        <a href="/client/orders" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                        <a href="/client/support" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Support</a>
                    </template>
                </div>
            </div>
    </div>
</div>

    <Link class="hover:text-red-600" href="/logout" method="post" as="button">Log out</Link>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';


const page = usePage();
defineProps({ active: Boolean, });

const role = computed(() => page.props.auth.role.roles);
</script>

<style lang="scss" scoped></style>
