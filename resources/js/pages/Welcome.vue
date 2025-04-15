<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({ time: String, stats: Object });

const math = computed(() => {
    return {
        userWTasks: Math.round((props.stats?.usersWithTasks * 100) / props.stats?.usersCount),
        userWOTasks: Math.round((props.stats?.usersWithoutTasks * 100) / props.stats?.usersCount),
        completedTasks: Math.round((props.stats?.completedTasks * 100) / props.stats?.allTasks),
        inProgressTaskas: Math.round((props.stats?.inProgressTasks * 100) / props.stats?.allTasks),
        notStartedTasks: Math.round((props.stats?.pendingTasks * 100) / props.stats?.allTasks),
    };
});
</script>

<template>
    <div class="mb-8">
        <h1 class="text-3xl">This is Home page</h1>
    </div>
    <div class="flex flex-row items-center justify-between">
        <div class="gate-components">
            <div class="gate left-gate">
                <span class="gate-text">Produ</span>
            </div>
            <div class="gate right-gate">
                <span class="gate-text">ction</span>
            </div>
            <div class="hidden-text flex flex-col">
                <Link class="p-2 hover:text-red-600" href="/tasks">Tasks</Link>
                <Link class="p-2 hover:text-red-600" href="/users/create">Create User</Link>
                <Link class="p-2 hover:text-red-600" href="/users">All Users</Link>
            </div>
        </div>
        <div class="container mx-auto min-h-60 max-w-sm overflow-hidden rounded-xl bg-transparent shadow-md md:max-w-2xl border-solid border-2 border-gray-200">
            <div class="p-4">
                <div class="p-1">
                    <p>All users: {{ stats?.usersCount }}</p>
                    <div class="container">
                        <div class="bg-cyan-100 p-2 text-left rounded-xl font-bold text-gray-800" :style="{ maxWidth: '100%' }">
                            <p>User with tasks</p>
                            <div class="container">
                                <div class="bg-blue-800 rounded-xl p-2 font-bold text-gray-200 hover:text-center hover:text-xl" :style="{ maxWidth: `${math.userWTasks}%` }">
                                    {{ math.userWTasks }}%
                                </div>
                            </div>
                            <p>User without tasks</p>
                            <div class="container">
                                <div class="bg-lime-800 rounded-xl p-2 font-bold text-gray-200 hover:text-center hover:text-xl" :style="{ maxWidth: `${math.userWOTasks}%` }">
                                    {{ math.userWOTasks }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-1">
                    <p>All tasks: {{ stats?.allTasks }}</p>
                    <div class="container">
                        <div class="bg-orange-100 rounded-xl p-2 text-left font-bold text-gray-800" :style="{ maxWidth: '100%' }">
                            <p>Completed tasks</p>
                            <div class="container">
                                <div class="bg-lime-700 p-2 rounded-xl font-bold text-gray-200 hover:text-center hover:text-xl" :style="{ maxWidth: `${math.completedTasks}%` }">
                                    {{ math.completedTasks }}%
                                </div>
                            </div>
                            <p>In progress tasks</p>
                            <div class="container">
                                <div class="bg-yellow-400 p-2 rounded-xl font-bold text-gray-800 hover:text-center hover:text-xl" :style="{ maxWidth: `${math.inProgressTaskas}%` }">
                                    {{ math.inProgressTaskas }}%
                                </div>
                            </div>
                            <p>Pending tasks</p>
                            <div class="container">
                                <div class="bg-red-400 p-2 rounded-xl font-bold text-gray-800 hover:text-center hover:text-xl" :style="{ maxWidth: `${math.notStartedTasks}%` }">
                                    {{ math.notStartedTasks }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div>
        <label for="users" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white">Users without task</label>
        <div class="mt-3 container mx-auto min-h-28 max-w-auto rounded-xl bg-transparent shadow-md shadow-slate-400 md:max-w-2xl border-solid border-2 border-gray-200">
            <p v-for="(user, index) in props.stats?.listOfUsersWOTasks" :key="index" value="user" class="ml-2 p-1">{{ user.name }}</p>
        </div>
    </div>
    <div style="margin-top: 800px">
        <p>The current time is {{ time }}</p>
        <Link class="hover:text-red-600" href="/" as="button" preserve-scroll>Refresh time</Link>
    </div>
</template>

<style scoped>


</style>
