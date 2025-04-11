<template>
    <div class="ml-4 mt-2 flex items-center p-4">
        <h1 class="font-bold">My App {{ $page.props.name }}</h1>
        <p class="ml-5 mt-1 text-sm font-bold">
            Welcome back <span class="ml-1 text-orange-400 underline">{{ user.username }}</span>
        </p>
    </div>
</template>

<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth.user);
</script>

<style scoped></style>
