<template>
    <div class="min-h-screen bg-gray-800 p-6">
        <h1 class="mb-6 text-3xl font-bold">Dashboard</h1>

        <!--Statistics-->
        <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-3">
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-950">Total orders</h2>
                <p class="text-3xl font-bold text-blue-500">{{ props.stats?.total_orders }}</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-950">Total tables</h2>
                <p class="text-3xl font-bold text-blue-500">{{ props.stats?.total_tables }}</p>
            </div>
            <div class="rounded-lg bg-white p-6 shadow">
                <h2 class="text-xl font-semibold text-gray-950">Active workshops</h2>
                <p class="text-3xl font-bold text-blue-500">{{ props.stats?.active_workshops }}</p>
            </div>
        </div>

        <!--Queue in workshops-->
        <div class="mb-6 rounded-lg bg-white shadow pl-6">
            <h2 class="mb-4 text-2xl font-semibold text-gray-950">Queue in workshops</h2>
            <div v-for="workshop in props.workshops" :key="workshop.id" class="mb-6">
                <h3 class="text-xl font-medium text-gray-800">
                    {{
                        workshop.name.charAt(0).toUpperCase() + workshop.name.slice(1)
                    }}
                    ({{ workshop.processes.length }} / 3)
                </h3>
                <div v-if="workshop.processes.length">
                    <div v-for="process in workshop.processes" :key="process.id" class="border-t py-2 text-gray-900">
                        <p class="text-gray-900">
                            Table: {{ process.table.name }} (Order #{{ process.table.orders_id }})
                            <span class="text-sm text-gray-500">Status: {{ process.status }}</span>
                        </p>
                    </div>
                </div>
                <p v-else class="text-red-800">No tables in work</p>
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
