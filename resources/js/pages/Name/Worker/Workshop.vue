<template>
    <AppHead title="Wrokers page" />
    <div class="mb-4 mt-3 text-center">
        Active processes: {{ processLimit?.current }} / {{ processLimit?.max }}
        <span v-if="processLimit?.current >= processLimit?.max" class="text-red-800 ml-4 shadow-xs shadow-red-50 bg-slate-200 font-bold" >  Workshop is at full capacity!</span>
    </div>
    <div class="mb-4">
        <label for="status-filter" class="mr-2">Filter by Table status: </label>
        <select v-model="statusFilter" class="border rounded px-2 py-1 " id="status-filter">
            <option class="text-black" value="">All</option>
            <option class="text-black" value="in_acceptance">In acceptance</option>
            <option class="text-black" value="in_painting">In painting</option>
            <option class="text-black" value="in_assembly">In assembly</option>
            <option class="text-black" value="in_delivery">In delivery</option>
            <option class="text-black" value="completed">Completed</option>
        </select>
    </div>
    <div v-if="filterdProcesses?.length === 0">No active processes match your filter.</div>
    <div v-else class="overflow-hidden sm:rounded-lg">
        <table class="min-w-full divide-y divide-yellow-200">
            <thead class="bg-sky-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Table</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="process in filterdProcesses" :key="process.id" class="dark:odd:bg-gray-900/50 dark:even:bg-gray-950 first:border-y-sky-300/85 first:border-t-2">
                    <td class="px-6 py-4 whitespace-nowrap">{{ process.table?.name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ process.table?.orders_id ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ process.table?.status ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <button
                            v-if="process.status === 'in_progress' && process.table"
                            @click="completeProcess(process.id)"
                            class=" bg-sky-700 p-3 text-xs font-bold rounded-md shadow-xs shadow-lime-100 hover:bg-lime-600"
                        >
                            Complete
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import AppHead from '../../Shared/AppHead.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps ({
    processLimit: Object,
    processes: Array,
});

const statusFilter = ref('');
const filterdProcesses = computed(() => {
    if(!statusFilter.value) return props.processes;
    return props.processes?.filter((process: any) => process.table?.status === statusFilter.value);
});

/* const completeProcess = (processId) => {
    router.post(route('worker.process.complete', processId),{}, {
        onSuccess: () => {
            statusFilter.value = '';
        },
        onError: (errors) => {
            console.error('Error completing process:', errors);
        },
    });
}; */
const completeProcess = (processId) => {
            router.post(route('worker.process.complete', processId));
};
</script>

<style scoped>

</style>
