<script setup>
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import Card from 'primevue/card';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import DonutWorkHourChart from '@/components/DonutWorkHourChart.vue';
import DonutActualWorkHourChart from '@/components/DonutActualWorkHourChart.vue';
import axios from 'axios';

// Leave requests for current user
const leaveRequests = ref([]);

// Default to dark mode
const darkMode = ref(true);
const toggleDarkMode = () => {
    darkMode.value = !darkMode.value;
    document.documentElement.classList.toggle('dark', darkMode.value);
};

const loading = ref(true);
const attendanceSummary = ref({ today: '-', monthTotal: 0, clockIn: '-', clockOut: '-' });
const leaveStats = ref({ taken: 0, remaining: 0 });
const workSchedules = ref([]);
const workHourStats = ref({ today: 0, week: 0, month: 0 });
const workHourTargets = ref({ today: 0, week: 0, month: 0 });
const currentTime = ref('');
const currentDate = ref('');

function updateClock() {
    const now = new Date();
    currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    currentDate.value = now.toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
}

onMounted(async () => {
    // Set dark mode on mount
    document.documentElement.classList.toggle('dark', darkMode.value);
    
    updateClock();
    setInterval(updateClock, 1000);
    try {
        const res = await axios.get('/dashboard-data');
        const data = res.data;
        attendanceSummary.value = data.attendanceSummary || attendanceSummary.value;
        leaveStats.value = data.leaveStats || leaveStats.value;
        workSchedules.value = data.workSchedules || [];
        workHourStats.value = data.workHourStats || workHourStats.value;
        workHourTargets.value = data.workHourTargets || workHourTargets.value;

        // Fetch leave requests for current user by employee_id
        const leaveRes = await axios.get('/leave-requests/me');
        leaveRequests.value = leaveRes.data || [];
    } finally {
        loading.value = false;
    }
});

const getStatusClass = (status) => {
    switch (status) {
        case 'pending':
            return 'px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300';
        case 'approved':
            return 'px-3 py-1.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300';
        case 'rejected':
            return 'px-3 py-1.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300';
        default:
            return '';
    }
};

const getStatusText = (status) => {
    switch (status) {
        case 'pending': return 'Pending';
        case 'approved': return 'Disetujui';
        case 'rejected': return 'Ditolak';
        default: return '';
    }
};
</script>

<template>
    <AppLayout>
        <InertiaHead title="Dashboard" />
        <div class="min-h-screen transition-colors duration-300">
            
            <!-- Header Section -->
            <div class=" border-gray-200 dark:border-gray-700 sticky top-0 z-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center h-16">
                        <div class="flex items-center space-x-4">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
                            <div class="hidden sm:flex items-center space-x-2 px-3 py-1 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                <i class="pi pi-calendar text-blue-600 dark:text-blue-400"></i>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ currentDate }}</span>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <div class="hidden md:flex items-center space-x-2 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                <i class="pi pi-clock text-gray-600 dark:text-gray-400"></i>
                                <span class="font-mono text-lg font-semibold text-gray-900 dark:text-white">{{ currentTime }}</span>
                            </div>
                        
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div v-if="loading" class="flex flex-col items-center justify-center py-20">
                <i class="pi pi-spin pi-spinner text-4xl text-blue-600 dark:text-blue-400 mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Memuat data dashboard...</p>
            </div>

            <!-- Main Content -->
            <div v-else class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                
                <!-- Quick Stats Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 mb-8 w-full">
                    <!-- Attendance Today -->
                    <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                        <template #content>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Status Hari Ini</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ attendanceSummary.today }}</p>
                                </div>
                                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-full">
                                    <i class="pi pi-calendar text-2xl text-blue-600 dark:text-blue-400"></i>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Monthly Attendance -->
                    <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                        <template #content>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Hadir Bulan Ini</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ attendanceSummary.monthTotal }}</p>
                                </div>
                                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-full">
                                    <i class="pi pi-calendar text-2xl text-green-600 dark:text-green-400"></i>
                                </div>
                            </div>
                        </template>
                    </Card>

                    <!-- Leave Taken -->
                    <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:shadow-lg transition-shadow duration-200">
                        <template #content>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Cuti Diambil</p>
                                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ leaveStats.taken }}</p>
                                </div>
                                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-full">
                                    <i class="pi pi-calendar-minus text-2xl text-orange-600 dark:text-orange-400"></i>
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>

                <!-- Main Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- Work Hours Statistics -->
                        <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <template #content>
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Statistik Jam Kerja</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Progress jam kerja untuk periode berbeda</p>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <div class="text-center">
                                        <DonutActualWorkHourChart :actual="workHourStats.today" :target="workHourTargets.today" label="Hari Ini" />
                                        <h4 class="mt-4 font-medium text-gray-900 dark:text-white">Hari Ini</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ workHourStats.today }}h / {{ workHourTargets.today }}h</p>
                                    </div>
                                    <div class="text-center">
                                        <DonutActualWorkHourChart :actual="workHourStats.week" :target="workHourTargets.week" label="Minggu Ini" />
                                        <h4 class="mt-4 font-medium text-gray-900 dark:text-white">Minggu Ini</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ workHourStats.week }}h / {{ workHourTargets.week }}h</p>
                                    </div>
                                    <div class="text-center">
                                        <DonutActualWorkHourChart :actual="workHourStats.month" :target="workHourTargets.month" label="Bulan Ini" />
                                        <h4 class="mt-4 font-medium text-gray-900 dark:text-white">Bulan Ini</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ workHourStats.month }}h / {{ workHourTargets.month }}h</p>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <!-- Leave Requests Table -->
                        <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <template #content>
                                <div class="mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Status Request Cuti</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Daftar permintaan cuti terbaru</p>
                                </div>
                                
                                <DataTable :value="leaveRequests" 
                                    class="p-datatable-sm" 
                                    :rowHover="true" 
                                    :stripedRows="true"
                                    responsiveLayout="scroll">
                                    <Column field="leave_type" header="Jenis Cuti" class="font-medium" />
                                    <Column field="start_date" header="Tanggal Mulai" />
                                    <Column field="end_date" header="Tanggal Selesai" />
                                    <Column header="Status" class="text-center">
                                        <template #body="{ data }">
                                            <span :class="getStatusClass(data.status)">
                                                {{ getStatusText(data.status) }}
                                            </span>
                                        </template>
                                    </Column>
                                    <Column field="reason" header="Alasan" />
                                </DataTable>
                            </template>
                        </Card>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        
                        <!-- Clock In/Out Status -->
                        <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <template #content>
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Absensi Hari Ini</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Status jam masuk dan pulang</p>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-full">
                                                <i class="pi pi-sign-in text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Jam Masuk</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">Clock In</p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ attendanceSummary.clockIn }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-red-100 dark:bg-red-900/30 rounded-full">
                                                <i class="pi pi-sign-out text-red-600 dark:text-red-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">Jam Pulang</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400">Clock Out</p>
                                            </div>
                                        </div>
                                        <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ attendanceSummary.clockOut }}
                                        </span>
                                    </div>
                                </div>
                            </template>
                        </Card>

                        <!-- Work Schedule -->
                        <Card class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                            <template #content>
                                <div class="mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Jadwal Kerja</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Jadwal kerja mingguan</p>
                                </div>
                                
                                <div class="space-y-3">
                                    <div v-for="ws in workSchedules" :key="ws.day" 
                                        class="flex items-center justify-between p-3 rounded-lg"
                                        :class="ws.is_workday ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-gray-50 dark:bg-gray-700/50'">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-2 h-2 rounded-full" 
                                                :class="ws.is_workday ? 'bg-blue-500' : 'bg-gray-400'"></div>
                                            <span class="font-medium text-gray-900 dark:text-white">{{ ws.day }}</span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ ws.clock_in }}
                                            </p>
                                             <p class="text-sm font-medium text-gray-900 dark:text-white">
                                               {{ ws.clock_out }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ ws.is_workday ? 'Hari Kerja' : 'Libur' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>