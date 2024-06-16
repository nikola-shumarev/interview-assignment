<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';
import Pagination from '@/Components/Pagination.vue'; // Import the pagination component
import { format } from 'date-fns';

const props = usePage().props;
const bankCredits = computed(() => props.bankCredits);

const toCurrency = (value) => {
    return Number(value).toFixed(2);
};

const formatDate = (date) => {
    return format(new Date(date), 'yyyy-MM-dd');
};
</script>

<template>
    <Head title="Bank Credits" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Card for the Bank Credits section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-4">Bank Credits</h1>
                        <div v-if="bankCredits.data.length">
                            <div class="table-container">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Unique ID
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Consumer Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Remaining Amount
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Due Date
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Monthly Payment
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="credit in bankCredits.data" :key="credit.id">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ credit.unique_id }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ credit.consumer.name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ toCurrency(credit.remaining_amount) }} BGN
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ formatDate(credit.due_date) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ toCurrency(credit.monthly_payment) }} BGN
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <Pagination :links="bankCredits.links" />

                            <FlashMessage />
                        </div>
                        <div v-else class="text-center py-6">
                            <p class="text-lg text-gray-800 mb-5">No bank credits found, why not create a new one?</p>
                            <Link href="/bank-credits/create" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition duration-300">
                                Create New Credit
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.table-container {
    min-height: 650px; /* Adjust based on your needs */
    overflow-y: auto; /* Enables scrolling */
}
</style>
