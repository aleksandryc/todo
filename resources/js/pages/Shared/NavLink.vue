<template>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url === '/' }" href="/">Home</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/users') }" href="/users">Users</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/settings') }" href="/settings">Settings</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/tasks') }" href="/tasks">Tasks</Link>
    <Link class="hover:text-red-600" :class="{ 'text-red-400': page.url.startsWith('/name') }" href="/name/dashboard">Name</Link>
    <Link class="hover:text-red-600" href="/logout" method="post" as="button">Log out</Link>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';


const page = usePage();
defineProps({ active: Boolean });
</script>

<style lang="scss" scoped></style>
