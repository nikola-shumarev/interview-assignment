<template>
  <div>
    <select v-model="selectedValue" @change="onChange" class="form-select block w-full mt-1">
      <option disabled value="">{{ placeholder }}</option>
      <option v-for="option in options" :key="option[optionValue]" :value="option[optionValue]">
        {{ option[optionLabel] }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  options: Array,
  optionLabel: String,
  optionValue: String,
  modelValue: [String, Number],
  placeholder: String
});

const selectedValue = ref(props.modelValue);

watch(selectedValue, (newValue) => {
  emit('update:modelValue', newValue);
});

const onChange = () => {
  emit('change', selectedValue.value);
};
</script>
