<script setup>
import { useForm, usePage, Head} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import FlashMessage from '@/Components/FlashMessage.vue';

const form = useForm({
    name: '',
    email: '',
    amount: 100,
    months: 3
});

const submit = () => {
    form.post(route('bank-credits.store'), {
        onFinish: () => form.reset('amount', 'months')
    });
};
</script>

<template>
    <Head title="Create Bank Credit" />

    <AuthenticatedLayout>
        <div class="flex justify-center py-12">
            <div class="w-full max-w-4xl">
                <!-- Card for the Create Bank Credit section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-4 text-center">Create Bank Credit</h1>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Consumer Name" />
                                <TextInput id="name" type="text" v-model="form.name" required class="w-full" min="4" max="255" />
                                <InputError :message="form.errors.name" />
                            </div>
                            <div>
                                <InputLabel for="email" value="Consumer Email" />
                                <TextInput id="email" type="email" v-model="form.email" required class="w-full" max="255" />
                                <InputError :message="form.errors.email" />
                            </div>
                            <div>
                                <InputLabel for="amount" value="Credit Amount (BGN currency)" />
                                <TextInput id="amount" type="number" v-model="form.amount" required class="w-full" min="1" />
                                <InputError :message="form.errors.amount" />
                            </div>
                            <div>
                                <InputLabel for="months" value="Duration (Months)" />
                                <TextInput id="months" type="number" v-model="form.months" required class="w-full" min="3" max="120" />
                                <InputError :message="form.errors.months" />
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
