<template>
    <div class="container min-h-screen p-6">
        <h1 class="text-3xl font-bold mb-6 text-amber-100">Orders</h1>

        <!-- Filters -->
         <div class="bg-slate-700 p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-100">Status</label>
                    <select v-model="filters.status" @change="applyFilters" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option class="text-black" value="">All</option>
                        <option class="text-black" value="pending">Pending</option>
                        <option class="text-black" value="in_progress">In progress</option>
                        <option class="text-black" value="completed">Completed</option>
                        <option class="text-black" value="delivered">Delivered</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100">Client</label>
                    <input v-model="filters.client" @input="applyFilters" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Search by name or email">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100">Order ID</label>
                    <input v-model="filters.id" @change="applyFilters" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter order ID">
                </div>
            </div>
         </div>

         <!-- Orders Table -->
          <div class="rounded-lg shadow overflow-x-auto bg-zinc-700">
            <table class="min-w-full divide-y divide-yellow-100">
                <thead class="bg-sky-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Client</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Tables</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-yellow-100">
                    <tr v-for="order in orders?.data" :key="order.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ order.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ order.client.name }} ({{ order.client.email }})</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ order.status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ order.tables.length }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ new Date(order.create_at).toLocaleDateString() }}</td>
                    </tr>
                </tbody>
            </table>
          </div>

          <!-- Paginator -->
           <div class="mt-4 flex justify-between">
                <button :disabled="!orders?.prev_page_url" @click="loadPage(orders?.prev_page_url)" class="px-4 bg-sky-500 rounded disabled:bg-gray-500">Previous</button>
                <button :disabled="!orders?.next_page_url" @click="loadPage(orders?.next_page_url)" class="px-4 bg-sky-500 rounded disabled:bg-gray-500">Next</button>
            </div>
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    orders: Object,
    filters: Object
});

const filters = reactive({
    status: props.filters?.status || '',
    client: props.filters?.client || '',
    id: props.filters?.id || '',
});

const applyFilters = () => {
    router.get('./orders', filters, { preserveState: true, preserveScroll:true} );
};

const loadPage = (url: string) => {
    router.get(url, {}, { preserveState: true, preserveScroll:true})
}
</script>

<style scoped>

</style>
