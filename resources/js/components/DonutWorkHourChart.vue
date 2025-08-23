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
  workSchedules: {
    type: Array,
    required: true,
  },
});

const chartData = computed(() => {
  const labels = props.workSchedules.map(ws => ws.day);
  const data = props.workSchedules.map(ws => ws.min_work_duration ? Math.round(ws.min_work_duration / 60 * 100) / 100 : 0);
  return {
    labels,
    datasets: [
      {
        label: 'Jam Kerja per Hari',
        data,
        backgroundColor: [
          '#42A5F5', '#66BB6A', '#FFA726', '#AB47BC', '#FF7043', '#26A69A', '#D4E157'
        ],
      },
    ],
  };
});

const chartOptions = {
  responsive: true,
  plugins: {
    legend: { display: true },
    title: { display: true, text: 'Jam Kerja per Hari (Donat)' },
  },
};
</script>

<template>
  <Doughnut :data="chartData" :options="chartOptions" />
</template>
