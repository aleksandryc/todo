<template>
    <div class="min-h-screen bg-gray-800 p-6">
        <h1 class="mb-6 text-3xl font-bold">Dashboard</h1>

        <!--Statistics-->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg bg-slate-700 p-6 shadow">
                <h2 class="text-xl font-semibold">Total orders</h2>
                <p class="text-3xl font-bold text-blue-500">{{ props.stats?.total_orders }}</p>
            </div>
            <div class="rounded-lg bg-slate-700 p-6 shadow">
                <h2 class="text-xl font-semibold">Total tables</h2>
                <p class="text-3xl font-bold text-blue-500">{{ props.stats?.total_tables }}</p>
            </div>
            <div class="rounded-lg bg-slate-700 p-6 shadow">
                <h2 class="text-xl font-semibold">Active workshops</h2>
                <p class="text-3xl font-bold text-blue-500">{{ props.stats?.active_workshops }}</p>
            </div>
        </div>

        <!--Queue in workshops-->
        <div class="mb-6 rounded-lg bg-slate-700 p-2 pl-6 shadow">
            <h2 class="mb-4 text-2xl font-semibold">Queue in workshops</h2>
            <div v-for="workshop in props.workshops" :key="workshop.id" class="mb-6">
                <h3 class="text-xl font-medium">
                    {{ workshop.name.charAt(0).toUpperCase() + workshop.name.slice(1) }}
                    <span v-if="workshop.max_tables">({{ workshop.processes.length }} / 3)</span>
                </h3>
                <div v-if="workshop.processes.length">
                    <div v-for="process in workshop.processes" :key="process.id" class="border-t py-2">
                        <div class="flex justify-between p-2" :class="{'text-gray-500': process.status === 'completed'}">
                            <span> {{ process.tables.name }} (Order #{{ process.tables.orders_id }}) </span>
                            <div>
                                <span class="ml-3 text-sm">Status: </span>
                                <span
                                    class="text-sm"
                                    :class="{
                                        'text-green-500': process.status === 'in_progress',
                                        'text-red-500': process.status === 'pending',
                                    }"
                                    >{{ process.status }}</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="text-red-500">No tables in work</p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
const props = defineProps({
    stats: Object,
    workshops: Object,
});
</script>

<style scoped></style>
