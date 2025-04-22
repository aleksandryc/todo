<template>
    <AppHead title="Wrokers page" />
    <div class="container mx-auto" v-if="props.workshops">
        <div class="flex justify-between mx-auto px-2">
            <div v-for="shop in props.workshops" :key="shop.id">
                <p>{{ shop.name }}</p>
                <p>{{ shop.processes.id }}</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppHead from '../../Shared/AppHead.vue';

const props = defineProps({
    workshops: Object,
});
</script>

<style scoped></style>
