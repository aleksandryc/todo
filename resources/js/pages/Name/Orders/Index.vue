<template>
    <div class="container min-h-screen p-6">
        <h1 class="mb-6 text-3xl font-bold text-amber-100">Orders</h1>

        <!-- Filters -->
        <div class="mb-6 rounded-lg bg-slate-700 p-4 shadow">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-medium text-gray-100">Status</label>
                    <select v-model="filters.status" @change="applyFilters" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option class="text-black" value="">All</option>
                        <option class="text-black" value="pending">Pending</option>
                        <option class="text-black" value="in_progress">In progress</option>
                        <option class="text-black" value="in_delivery">In Delivery</option>
                        <option class="text-black" value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100">Client</label>
                    <input
                        v-model="filters.client"
                        @input="applyFilters"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        placeholder="Search by name or email"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100">Order ID</label>
                    <input
                        v-model="filters.id"
                        @change="applyFilters"
                        type="number"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        placeholder="Enter order ID"
                    />
                </div>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="overflow-x-auto rounded-lg bg-zinc-700 shadow">
            <table class="min-w-full divide-y divide-yellow-100">
                <thead class="bg-sky-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider uppercase">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider uppercase">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider uppercase">Tables</th>
                        <th class="px-6 py-3 text-left text-xs font-medium tracking-wider uppercase">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-yellow-100">
                    <tr v-for="order in orders?.data" :key="order.id">
                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ order.id }}</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ order.client.name }} ({{ order.client.email }})</td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ order.status }}</td>
                        <td>
                            <tr v-for="table in order.tables" :key="table.id" class="px-6 py-4 text-sm whitespace-nowrap">
                                {{
                                    table.name
                                }}
                            </tr>
                        </td>
                        <td class="px-6 py-4 text-sm whitespace-nowrap">{{ new Date(order.created_at).toDateString() }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginator -->
        <div class="mt-4 flex justify-between">
            <button :disabled="!orders?.prev_page_url" @click="loadPage(orders?.prev_page_url)" class="rounded bg-sky-500 px-4 disabled:bg-gray-500">
                Previous
            </button>
            <button :disabled="!orders?.next_page_url" @click="loadPage(orders?.next_page_url)" class="rounded bg-sky-500 px-4 disabled:bg-gray-500">
                Next
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { reactive } from 'vue';

const props = defineProps({
    orders: Object,
    filters: Object,
});

const filters = reactive({
    status: props.filters?.status || '',
    client: props.filters?.client || '',
    id: props.filters?.id || '',
});

const applyFilters = () => {
    router.get('./orders', filters, { preserveState: true, preserveScroll: true });
};

const loadPage = (url: string) => {
    router.get(url, {}, { preserveState: true, preserveScroll: true });
};
</script>

<style scoped></style>
