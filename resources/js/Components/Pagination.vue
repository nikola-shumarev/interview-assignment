<template>
  <nav aria-label="Pagination">
    <ul class="flex list-none pl-0 rounded my-2 justify-center">

      <!-- Page Numbers -->
      <li v-for="link in links" :key="link.label" :class="{ 'active': link.active, 'mx-1': true }">
        <button
          v-if="link.url"
          @click="handlePageChange(link.url)"
          :class="['px-3 py-1 rounded', link.active ? 'bg-blue-500 text-white' : 'bg-transparent text-gray-700 hover:bg-gray-200']"
        >
          {{ formatLabel(link.label) }}
        </button>
        <span v-else class="px-3 py-1 rounded text-gray-300">
          {{ formatLabel(link.label) }}
        </span>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { Inertia } from '@inertiajs/inertia';

defineProps({
  links: Array
});

const handlePageChange = (url) => {
  Inertia.visit(url);
};

const formatLabel = (label) => {
  return label.replace('&laquo;', '').replace('&raquo;', '');
};
</script>

<style scoped>
.disabled {
  pointer-events: none;
  opacity: 0.5;
}

.active {
  background-color: #007bff; /* Bootstrap primary color */
  color: white;
}

button {
  min-width: 75px; /* Ensures buttons have a consistent width */
}
</style>
