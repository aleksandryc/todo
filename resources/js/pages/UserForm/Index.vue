<template>
    <div v-if="flashMessage" class="bg-slate-900 p-4 text-center text-2xl font-bold text-red-600">
        <p class="text-sm font-bold">{{ flashMessage }}</p>
    </div>
<div class="flex items-center justify-center p-12">
    <div class="mx-auto w-full max-w-[550px] bg-white p-2">
        <form @submit.prevent="submitForm">
            <div class="mb-5">
                <label for="name" class="mb-3 block text-base font-medium text-[#07074D]">
                    Full Name
                </label>
                <input type="text" v-model="form.name" id="name" placeholder="Full Name"
                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
            </div>
            <div v-if="form.errors.name" class="text-red-600">{{ form.errors.name }}</div>
            <div class="mb-5">
                <label for="phone" class="mb-3 block text-base font-medium text-[#07074D]">
                    Phone Number
                </label>
                <input type="text" v-model="form.phone" id="phone" placeholder="Enter your phone number"
                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
            </div>
            <div v-if="form.errors.phone" class="text-red-600">{{ form.errors.phone }}</div>
            <div class="mb-5">
                <label for="email" class="mb-3 block text-base font-medium text-[#07074D]">
                    Email Address
                </label>
                <input type="email" v-model="form.email" id="email" placeholder="Enter your email" required
                    class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
            </div>
            <div v-if="form.errors.email" class="text-red-600">{{ form.errors.email }}</div>
            <div class="-mx-3 flex flex-wrap">
                <div class="w-full px-3 sm:w-1/2">
                    <div class="mb-5">
                        <label for="date" class="mb-3 block text-base font-medium text-[#07074D]">
                            Date
                        </label>
                        <input type="date" v-model="form.date" id="date"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                    </div>
                    <div v-if="form.errors.date" class="text-red-600">{{ form.errors.date }}</div>
                </div>
                <div class="w-full px-3 sm:w-1/2">
                    <div class="mb-5">
                        <label for="time" class="mb-3 block text-base font-medium text-[#07074D]">
                            Time
                        </label>
                        <input type="time" v-model="form.time" id="time"
                            class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                    </div>
                    <div v-if="form.errors.time" class="text-red-600">{{ form.errors.time }}</div>
                </div>
            </div>

            <div class="mb-5 pt-3">
                <label class="mb-5 block text-base font-semibold text-[#07074D] sm:text-xl">
                    Address Details
                </label>
                <div class="-mx-3 flex flex-wrap">
                    <div class="w-full px-3 sm:w-1/2">
                        <div class="mb-5">
                            <input type="text" v-model="form.address" id="address" placeholder="Enter adress"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                        </div>
                        <div v-if="form.errors.address" class="text-red-600">{{ form.errors.address }}</div>
                    </div>
                    <div class="w-full px-3 sm:w-1/2">
                        <div class="mb-5">
                            <input type="text" v-model="form.city" id="city" placeholder="Enter city"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                        </div>
                        <div v-if="form.errors.city" class="text-red-600">{{ form.errors.city }}</div>
                    </div>
                    <div class="w-full px-3 sm:w-1/2">
                        <div class="mb-5">
                            <input type="text" v-model="form.province" id="province" placeholder="Enter province"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                        </div>
                        <div v-if="form.errors.province" class="text-red-600">{{ form.errors.province }}</div>
                    </div>
                    <div class="w-full px-3 sm:w-1/2">
                        <div class="mb-5">
                            <input type="text" v-model="form.postCode" id="postCode" placeholder="Postal Code"
                                class="w-full rounded-md border border-[#e0e0e0] bg-white py-3 px-6 text-base font-medium text-[#6B7280] outline-none focus:border-[#6A64F1] focus:shadow-md" />
                        </div>
                        <div v-if="form.errors.postCode" class="text-red-600">{{ form.errors.postCode }}</div>
                    </div>
                </div>
            </div>

            <div>
                <button :disabled="form.processing"
                    class="hover:shadow-form w-full rounded-md bg-[#6A64F1] py-3 px-8 text-center text-base font-semibold text-white outline-none">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
</template>

<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    date: '',
    time: '',
    address: '',
    city: '',
    province: '',
    postCode: '',
});

const submitForm = () => {
    form.post(route('userform.submit'), {
        onSuccess: () => {
            form.reset()
        }
    })
};

const page = usePage();

const flashMessage = computed(() => {
    const flash = page.props.flash as { message?: string };
    return flash.message || '';
});
</script>

<style scoped>

</style>

<script lang="ts">
// Delete if needed
export default {
    layout: null,
};
</script>
