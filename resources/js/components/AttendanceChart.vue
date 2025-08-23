<script setup>
import { Bar } from 'vue-chartjs';
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const props = defineProps({
  data: Object,
  options: Object
});
</script>

<template>
  <Bar :data="data" :options="options" />
</template>
