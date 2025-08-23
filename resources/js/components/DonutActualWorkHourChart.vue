<script setup>
import { computed } from 'vue';
import { Doughnut } from 'vue-chartjs';
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement);

const props = defineProps({
  actual: {
    type: Number,
    required: true,
  },
  target: {
    type: Number,
    required: true,
  },
  label: {
    type: String,
    required: true,
  },
});

const chartData = computed(() => {
  return {
    labels: ['Sudah tercapai', 'Sisa target'],
    datasets: [
      {
        label: props.label,
        data: [props.actual, Math.max(props.target - props.actual, 0)],
        backgroundColor: ['#42A5F5', '#E0E0E0'],
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: true },
    title: { display: true, text: props.label },
  },
};
</script>

<template>
  <Doughnut :data="chartData" :options="chartOptions" />
  <div class="text-center mt-2 text-sm text-gray-700">
    <span class="font-semibold">{{ actual }}</span> / <span>{{ target }}</span> jam
  </div>
</template>
