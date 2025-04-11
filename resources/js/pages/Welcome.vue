<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps({ time: String });
</script>

<template>
    <div class="mb-8">
        <h1 class="text-3xl">This is Home page</h1>
    </div>

    <div class="gate-components">
        <div class="gate left-gate">
            <span class="gate-text">Produ</span>
        </div>
        <div class="gate right-gate">
            <span class="gate-text">ction</span>
        </div>
        <div class="hidden-text flex flex-col">
            <Link class="p-2 hover:text-red-600" href="/tasks">Tasks</Link>
            <Link class="p-2 hover:text-red-600" href="/users/create">Create User</Link>
            <Link class="p-2 hover:text-red-600" href="/users">All Users</Link>
        </div>
    </div>
    <div style="margin-top: 800px">
        <p>The current time is {{ time }}</p>
        <Link class="hover:text-red-600" href="/" as="button" preserve-scroll>Refresh time</Link>
    </div>
</template>

<style scoped>
</style>
