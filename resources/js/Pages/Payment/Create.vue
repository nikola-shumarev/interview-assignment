<script setup>
import { ref, watch } from 'vue';
import { useForm, usePage, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import vueMultiselect from 'vue-multiselect';
import { throttle } from 'lodash';
import '../../../../node_modules/vue-multiselect/dist/vue-multiselect.css';

const form = useForm({
    bank_credit: '',
    amount: ''
});

const bankCredits = ref([]);
const isLoading = ref(false);

const fetchBankCredits = throttle(async (query) => {
  if (query.trim()) {
    isLoading.value = true;
    try {
      const response = await axios.get('/bank-credits/search', { params: { search: query } });
      bankCredits.value = response.data.bankCredits.map(credit => ({
        ...credit,
        label: `${credit.unique_id} - ${credit.consumer.name} - ${credit.remaining_amount} BGN`
      }));
    } catch (error) {
      console.error('Error fetching bank credits:', error);
    } finally {
      isLoading.value = false;
    }
  }
}, 500, { leading: false, trailing: true });

const submit = () => {
    form.post(route('payment.store'), {
        onFinish: () => {
            resetForm();
        }
    });
};

const resetForm = () => {
    form.amount = '';
    form.bank_credit = '';

    bankCredits.value = [];
};
</script>

<template>
    <Head title="Payment" />
    <AuthenticatedLayout>
        <div class="flex justify-center py-12">
            <div class="w-full max-w-4xl">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-4 text-center">Payment</h1>
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="bank_credit" value="Select Bank Credit" />
                                <vueMultiselect
                                    v-model="form.bank_credit"
                                    :options="bankCredits"
                                    :searchable="true"
                                    :internal-search="false"
                                    :loading="isLoading"
                                    :multiple="false"
                                    :close-on-select="true"
                                    :clear-on-select="false"
                                    :preserve-search="true"
                                    placeholder="Search by consumer name, email or credit unique id"
                                    label="label"
                                    track-by="id"
                                    @search-change="fetchBankCredits"
                                />
                                <InputError :message="form.errors.bank_credit" />
                            </div>
                            <div>
                                <InputLabel for="amount" value="Payment Amount (BGN)" />
                                <TextInput id="amount" type="number" v-model="form.amount" required class="w-full" />
                                <InputError :message="form.errors.amount" />
                            </div>
                            <PrimaryButton class="w-full bg-blue-500 text-white hover:bg-blue-600 focus:ring-2 focus:ring-blue-300 disabled:opacity-50 justify-center" :class="{ 'opacity-100': !form.processing }" :disabled="form.processing">
                                Submit
                            </PrimaryButton>
                            <FlashMessage />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
