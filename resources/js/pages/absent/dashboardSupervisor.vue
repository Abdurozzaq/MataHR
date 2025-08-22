<script setup>
import { defineProps } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  summary: Object,
  calendarDays: Array,
  breadcrumbs: Array
});
</script>


<template>
  <AppLayout :breadcrumbs="props.breadcrumbs">
    <InertiaHead title="Dashboard Supervisor" />
    <!-- Card Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
      <div class="rounded-xl border bg-background p-4 shadow-sm transition-colors dark:bg-[#18181b] dark:border-[#27272a]">
        <div class="font-semibold text-base text-foreground mb-2">Total Anak Buah</div>
        <div class="text-3xl font-bold text-primary">{{ props.summary.totalSubordinates }}</div>
      </div>
      <div class="rounded-xl border bg-green-50 p-4 shadow-sm transition-colors dark:bg-green-900/30 dark:border-green-800">
        <div class="font-semibold text-base text-foreground mb-2">Sudah Clock In</div>
        <div class="text-3xl font-bold text-green-700 dark:text-green-300">{{ props.summary.totalClockedIn }}</div>
      </div>
      <div class="rounded-xl border bg-red-50 p-4 shadow-sm transition-colors dark:bg-red-900/30 dark:border-red-800">
        <div class="font-semibold text-base text-foreground mb-2">Belum Clock In</div>
        <div class="text-3xl font-bold text-red-700 dark:text-red-300">{{ props.summary.totalNotClockedIn }}</div>
      </div>
      <div class="rounded-xl border bg-blue-50 p-4 shadow-sm transition-colors dark:bg-blue-900/30 dark:border-blue-800">
        <div class="font-semibold text-base text-foreground mb-2">Sudah Clock Out</div>
        <div class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ props.summary.totalClockedOut }}</div>
      </div>
    </div>
    <!-- Kalender Absensi Anak Buah -->
    <div class="w-full bg-background p-6 rounded-xl border shadow-sm transition-colors dark:bg-[#18181b] dark:border-[#27272a]">
      <div class="font-bold text-xl text-foreground mb-4">Kalender Absensi Anak Buah</div>
      <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2">
        <div v-for="day in props.calendarDays" :key="day.date" class="border rounded-lg p-2 bg-card transition-colors dark:bg-[#232326] dark:border-[#27272a]">
          <div class="font-semibold text-sm text-muted-foreground mb-1">{{ day.date }}</div>
          <div>
            <div v-for="sub in day.subordinates" :key="sub.id" class="flex items-center gap-2 mt-1">
              <span class="text-foreground">{{ sub.name }}</span>
              <span
                v-if="sub.status === 'clocked_in'"
                class="px-2 py-1 rounded bg-green-200 text-green-800 text-xs dark:bg-green-900/60 dark:text-green-300"
              >Clock In</span>
              <span
                v-else-if="sub.status === 'not_clocked_in'"
                class="px-2 py-1 rounded bg-red-200 text-red-800 text-xs dark:bg-red-900/60 dark:text-red-300"
              >Belum Clock In</span>
              <span
                v-else-if="sub.status === 'clocked_out'"
                class="px-2 py-1 rounded bg-blue-200 text-blue-800 text-xs dark:bg-blue-900/60 dark:text-blue-300"
              >Clock Out</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>