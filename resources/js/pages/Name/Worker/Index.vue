<template>
    <AppHead title="Wrokers page" />
    <div class="container mx-auto" v-if="props.workshops">
        <div class="mx-auto flex justify-between px-2 text-xs">
            <div v-for="shop in props.workshops" :key="shop.id">
                <p class="mb-2 text-center font-bold text-xl tracking-widest border-2 border-amber-200 p-2 items-center shadow-amber-100 shadow-sm">{{ shop.workshop_name }}</p>
                <div v-for="table in shop.processes" :key="table.id" class="mr-2 flex flex-wrap justify-between p-1">
                    {{ table.tables.name }} | {{ table.tables.color }}

                    <button @click.prevent="submit(table.tables.id)" class="ml-3 rounded-2xl bg-amber-600 p-2 text-xs font-light">Next step</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import AppHead from '../../Shared/AppHead.vue';

const props = defineProps({
    workshops: Object,
});

const submit = (processId) => {
    router.put(route('worker.process.complete', processId));
};
</script>

<style scoped></style>
