<template>
    <AppHead title="Wrokers page" />
    <div class="container mx-auto" v-if="props.workshops">
        <div class="flex justify-between mx-auto px-2">
            <div v-for="shop in props.workshops" :key="shop.id">
                <p class="text-center mb-2">{{ shop.workshop_name }}</p>
                <div
                v-for="table in shop.processes"
                :key="table.id"
                class="flex justify-between p-1 flex-wrap mr-2"
                >{{ table.tables.name }} | {{ table.process_status}}

                    <button
                    @click.prevent="submit"
                    class="ml-3 p-2 bg-amber-600 rounded-2xl text-xs font-light">Next step</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import AppHead from '../../Shared/AppHead.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    workshops: Object,
});

const submit = () => {
    router.post('/name/worker');
};
</script>

<style scoped></style>
