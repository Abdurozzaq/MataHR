
<script setup>
import { ref, computed } from 'vue';
import { defineProps } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';

const props = defineProps({
  summary: Object,
  calendarDays: Array,
  breadcrumbs: Array
});

// Flatten all subordinates from calendarDays for the table
const allSubordinates = props.calendarDays
  .flatMap(day => day.subordinates.map(sub => ({
    ...sub,
    date: day.date
  })));

// Filter states
const searchName = ref('');
const searchId = ref('');
const searchDate = ref('');

const filteredSubordinates = computed(() => {
  return allSubordinates.filter(sub => {
    const matchName = !searchName.value || sub.name.toLowerCase().includes(searchName.value.toLowerCase());
    const matchId = !searchId.value || (sub.employee_id && sub.employee_id.toString().includes(searchId.value));
    const matchDate = !searchDate.value || sub.date.includes(searchDate.value);
    return matchName && matchId && matchDate;
  });
});
</script>


<template>
  <AppLayout :breadcrumbs="props.breadcrumbs">
    <InertiaHead title="Dashboard Supervisor" />
    <!-- Modern Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
      <div class="rounded-2xl bg-gradient-to-br from-blue-500 to-blue-300 text-white shadow-lg p-6 flex flex-col items-center">
        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 0 0-3-3.87M9 20h6M3 20h5v-2a4 4 0 0 1 3-3.87M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0z"/></svg>
        <div class="font-semibold text-base">Total Karyawan</div>
        <div class="text-3xl font-bold">{{ props.summary.totalSubordinates }}</div>
      </div>
      <div class="rounded-2xl bg-gradient-to-br from-green-400 to-green-200 text-white shadow-lg p-6 flex flex-col items-center">
        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <div class="font-semibold text-base">Clock In</div>
        <div class="text-3xl font-bold">{{ props.summary.totalClockedIn }}</div>
      </div>
      <div class="rounded-2xl bg-gradient-to-br from-red-400 to-red-200 text-white shadow-lg p-6 flex flex-col items-center">
        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        <div class="font-semibold text-base">Belum Clock In</div>
        <div class="text-3xl font-bold">{{ props.summary.totalNotClockedIn }}</div>
      </div>
      <div class="rounded-2xl bg-gradient-to-br from-blue-400 to-blue-200 text-white shadow-lg p-6 flex flex-col items-center">
        <svg class="w-8 h-8 mb-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>
        <div class="font-semibold text-base">Clock Out</div>
        <div class="text-3xl font-bold">{{ props.summary.totalClockedOut }}</div>
      </div>
    </div>

    <!-- Search and DataTable for All Employees -->
    <div class="w-full bg-background p-6 rounded-2xl border shadow-lg">
      <div class="font-bold text-xl mb-4">Data Absensi Karyawan</div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <input
          v-model="searchName"
          type="text"
          placeholder="Cari nama karyawan..."
          class="px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <input
          v-model="searchId"
          type="text"
          placeholder="Cari Employee ID..."
          class="px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
        <input
          v-model="searchDate"
          type="date"
          placeholder="Filter tanggal..."
          class="px-4 py-2 rounded-lg border focus:outline-none focus:ring-2 focus:ring-blue-400"
        />
      </div>
      <DataTable :value="filteredSubordinates" paginator :rows="10" responsiveLayout="scroll" class="modern-table">
        <Column header="Karyawan">
          <template #body="{ data }">
            <div class="flex items-center gap-3">
              <img
                v-if="data.avatar"
                :src="data.avatar"
                alt="avatar"
                class="w-9 h-9 rounded-full object-cover border"
              />
              <div>
                <div class="font-semibold">{{ data.name }}</div>
                <div class="text-xs text-gray-500">{{ data.department.name || '-' }}</div>
                <div class="text-xs text-gray-400">ID: {{ data.employee_id || '-' }}</div>
              </div>
            </div>
          </template>
        </Column>
        <Column field="date" header="Tanggal" sortable></Column>
        <Column header="Status">
          <template #body="{ data }">
            <span
              v-if="data.status === 'clocked_in'"
              class="px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold"
            >Clock In</span>
            <span
              v-else-if="data.status === 'clocked_out'"
              class="px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold"
            >Clock Out</span>
            <span
              v-else
              class="px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold"
            >Belum Clock In</span>
          </template>
        </Column>
      </DataTable>
    </div>
  </AppLayout>
</template>

<!-- No additional script needed -->