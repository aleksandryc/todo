<template>
    <div class="container min-h-screen p-6">
        <h1 class="text-3xl font-bold mb-6 text-amber-100">Tables</h1>

        <!-- Filters -->
         <div class="bg-slate-700 p-4 rounded-lg shadow mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-100">Status</label>
                    <select v-model="filters.status" @change="applyFilters" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option class="text-black" value="">All</option>
                        <option class="text-black" value="pending">Pending</option>
                        <option class="text-black" value="in_acceptance">Acceptance</option>
                        <option class="text-black" value="in_painting">In Painting</option>
                        <option class="text-black" value="in_assembly">In Assembly</option>
                        <option class="text-black" value="in_delivery">In Delivery</option>
                        <option class="text-black" value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100">Status</label>
                    <select v-model="filters.material" @change="applyFilters" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option class="text-black" value="">All</option>
                        <option class="text-black" value="metal">Metal</option>
                        <option class="text-black" value="plastic">Plastic</option>
                        <option class="text-black" value="wood">Wood</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-100">Order ID</label>
                    <input v-model="filters.color" @change="applyFilters" type="number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Search by color">
                </div>
            </div>
         </div>

         <!-- Orders Table -->
          <div class="rounded-lg shadow overflow-x-auto bg-zinc-700">
            <table class="min-w-full divide-y divide-yellow-100">
                <thead class="bg-sky-900">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Material</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Color</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Order ID</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-yellow-100">
                    <tr v-for="table in tables?.data" :key="table.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.material }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.color || 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.price }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ table.orders_id }}</td>
                    </tr>
                </tbody>
            </table>
          </div>

          <!-- Paginator -->
           <div class="mt-4 flex justify-between">
                <button :disabled="!tables?.prev_page_url" @click="loadPage(tables?.prev_page_url)" class="px-4 bg-sky-500 rounded disabled:bg-gray-500">Previous</button>
                <button :disabled="!tables?.next_page_url" @click="loadPage(tables?.next_page_url)" class="px-4 bg-sky-500 rounded disabled:bg-gray-500">Next</button>
            </div>
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    tables: Object,
    filters: Object
});

const filters = reactive({
    status: props.filters?.status || '',
    material: props.filters?.material || '',
    color: props.filters?.color || '',
});

const applyFilters = () => {
    router.get('./tables', filters, { preserveState: true, preserveScroll:true} );
};

const loadPage = (url: string) => {
    router.get(url, {}, { preserveState: true, preserveScroll:true})
}
</script>

<style scoped>

</style>
