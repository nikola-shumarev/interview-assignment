<template>
    <div>
      <div v-if="flashMessages.success" class="bg-green-500 text-white p-4 rounded-md mt-4 text-center">
        {{ flashMessages.success }}
      </div>

      <div v-if="flashMessages.warning" class="bg-orange-500 text-white p-4 rounded-md mt-4 text-center">
        {{ flashMessages.warning }}
      </div>

      <div v-if="flashMessages.error" class="bg-red-500 text-white p-4 rounded-md mt-4 text-center">
        {{ flashMessages.error }}
      </div>
    </div>
  </template>

  <script setup>
  import { usePage } from '@inertiajs/vue3';
  import { ref, watch } from 'vue';

  const page = usePage();
  const flashMessages = ref(page.props.flash);

  watch(() => page.props.flash, (newFlash) => {
    flashMessages.value = newFlash;
  }, { deep: true });
  </script>
