<!-- eslint-disable vue/no-v-text-v-html-on-component -->
<template>
    <div class="mt-4">
        <div v-if="!users?.length">
            <div class="-mb-1 flex  justify-between">
                <template v-for="(link, key) in users?.links">
                    <div
                        v-if="link.url === null"
                        :key="key"
                        class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 text-gray-400"
                        v-html="link.label"
                    />
                    <Link
                        v-else
                        :key="`link-${key}`"
                        class="mb-1 mr-1 rounded border px-4 py-3 text-sm leading-4 hover:bg-slate-800 focus:border-indigo-500 focus:text-indigo-500"
                        :class="{ 'bg-rose-900': link.active }"
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
