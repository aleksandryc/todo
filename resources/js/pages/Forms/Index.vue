<template>
    <div class="container flex justify-between p-2">
        <div v-for="form in forms" :key="form"  class="border border-red-600 p-3 hover:bg-red-950 hover:rounded-4xl">
            <a :href="'/forms/' + form.key"
                ><span class="group relative cursor-pointer">
                    {{ form.title }}
                    <span
                        class="absolute left-1/2 z-10 hidden w-48 -translate-x-1/2 rounded bg-green-900 bg-gradient-to-t from-green-800 via-green-700 to-green-900 px-2 py-1 text-sm text-white group-hover:block"
                        style="top: 120%"
                    >
                        {{ form.description }}
                    </span>
                </span></a
            >
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps({
    forms: Object,
});
</script>

<style scoped></style>
