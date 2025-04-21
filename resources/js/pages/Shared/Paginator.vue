<!-- eslint-disable vue/no-v-text-v-html-on-component -->
<template>
    <div class="mb-4">
        <div v-if="!users?.length">
            <div class="flex  justify-between">
                <template v-for="(link, key) in users?.links">
                    <div
                        v-if="link.url === null"
                        :key="key"
                        class=" mr-1 rounded border px-1 py-3 text-xs leading-4 text-gray-400"
                        v-html="link.label"
                        :class="{ 'hidden': link.label === '...' }"
                    />
                    <Link
                        v-else
                        :key="`link-${key}`"
                        class=" mr-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-slate-800 hover:shadow-md hover:shadow-blue-400 focus:border-indigo-500 focus:text-indigo-500"
                        :class="{ 'bg-amber-50': link.active }"
                        :href="link.url"
                        v-html="link.label"
                    />
                </template>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps({
    users: Object,
});
</script>

<style scoped>

</style>
