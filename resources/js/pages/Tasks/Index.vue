<template>
    <div>
        <h1 class="mb-8 text-3xl">This is tasks page!</h1>
    </div>
    <div class="flex flex-wrap space-x-2 ml-1">
        <div v-for="task in tasks" :key="task.id" class="relative my-6 flex w-96 flex-col rounded-lg border border-slate-200  shadow-sm">
            <div class="mx-3 mb-0 border-b border-slate-200 px-1 pb-2 pt-3">
                <span class="text-sm font-medium text-white"> {{ task.name }} </span>
            </div>
            <div class="p-4">
                <h5 class="mb-2 text-xl font-semibold text-white">{{ task.description }}</h5>
                <p v-for="user in task.users" :key="user.name" class="font-light leading-normal text-white">
                    User: {{ user.name }}
                </p>
            </div>
        </div>
    </div>

</template>

<script setup lang="ts">
defineProps({
    tasks: Object,
});
</script>

<style scoped></style>
