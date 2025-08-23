
<script setup>
import AppLayout from '@/layouts/AppLayout.vue';
import axios from 'axios';
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
// import { useToast } from 'primevue/usetoast';
import { router } from '@inertiajs/vue3';

// Ambil flash message dari backend
const flash = usePage().props.flash || {};

const breadcrumbs = [
  { label: 'Dashboard' },
  { label: 'Absensi' }
];

// const toast = useToast();
const duration = ref(0);
const loading = ref(false);
const user = usePage().props.auth.user;
const clockedIn = ref(false);
const clockedOut = ref(false);
// min_work_duration dalam detik, pastikan backend mengirimkan props ini
// Pastikan minWorkDuration satuan menit, jika dari backend detik maka konversi ke menit
let rawMinWorkDuration = usePage().props.min_work_duration ? Number(usePage().props.min_work_duration) : 0;
// Jika lebih dari 300 dan kelipatan 60, kemungkinan detik, ubah ke menit
if (rawMinWorkDuration > 300 && rawMinWorkDuration % 60 === 0) {
  rawMinWorkDuration = rawMinWorkDuration / 60;
}
const minWorkDuration = ref(rawMinWorkDuration);
// Format minWorkDuration (menit) ke jam:menit:detik
function formatMinWorkDuration() {
  const totalMinutes = minWorkDuration.value;
  const hours = Math.floor(totalMinutes / 60);
  const minutes = totalMinutes % 60;
  return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:00`;
}
const errorMessage = ref('');
const workDuration = ref('');
const successMessage = ref('');
const officeName = ref(usePage().props.office_name || 'Kantor Pusat');
const officeLat = ref(usePage().props.office_latitude ? Number(usePage().props.office_latitude) : -6.2);
const officeLon = ref(usePage().props.office_longitude ? Number(usePage().props.office_longitude) : 106.8)
const attendances = ref(usePage().props.attendances || []);


// Hitung durasi kerja dari clock_in_at dan clock_out_at
function getLastAttendance() {
  // Ambil absensi terakhir milik user (berdasarkan clock_in_at terbaru)
  const userAttendances = attendances.value.filter(a => a.user_id === user.id);
  if (userAttendances.length === 0) return null;
  return userAttendances.reduce((latest, current) => {
    if (!latest) return current;
    return new Date(current.clock_in_at) > new Date(latest.clock_in_at) ? current : latest;
  }, null);
}

function getCompletedWorkDuration() {
  // Filter attendances for today and user
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = (today.getMonth() + 1).toString().padStart(2, '0');
  const dd = today.getDate().toString().padStart(2, '0');
  const todayStr = `${yyyy}-${mm}-${dd}`;
  const userAttendancesToday = attendances.value.filter(a => {
    if (a.user_id !== user.id) return false;
    if (!a.clock_in_at) return false;
    // Compare only date part
    const clockInDate = new Date(a.clock_in_at);
    const clockInStr = `${clockInDate.getFullYear()}-${(clockInDate.getMonth() + 1).toString().padStart(2, '0')}-${clockInDate.getDate().toString().padStart(2, '0')}`;
    return clockInStr === todayStr;
  });

  // Sum only attendances with both clock_in_at and clock_out_at on same day
  let totalSeconds = 0;
  userAttendancesToday.forEach(a => {
    if (a.clock_in_at && a.clock_out_at) {
      const clockIn = new Date(a.clock_in_at);
      const clockOut = new Date(a.clock_out_at);
      if (
        clockOut > clockIn &&
        clockIn.getFullYear() === clockOut.getFullYear() &&
        clockIn.getMonth() === clockOut.getMonth() &&
        clockIn.getDate() === clockOut.getDate()
      ) {
        const diff = (clockOut - clockIn) / 1000;
        totalSeconds += Math.floor(diff);
      }
    }
  });
  return totalSeconds;
}

function getRunningWorkDuration() {
  // Filter attendances for today and user
  const today = new Date();
  const yyyy = today.getFullYear();
  const mm = (today.getMonth() + 1).toString().padStart(2, '0');
  const dd = today.getDate().toString().padStart(2, '0');
  const todayStr = `${yyyy}-${mm}-${dd}`;
  const userAttendancesToday = attendances.value.filter(a => {
    if (a.user_id !== user.id) return false;
    if (!a.clock_in_at) return false;
    // Compare only date part
    const clockInDate = new Date(a.clock_in_at);
    const clockInStr = `${clockInDate.getFullYear()}-${(clockInDate.getMonth() + 1).toString().padStart(2, '0')}-${clockInDate.getDate().toString().padStart(2, '0')}`;
    return clockInStr === todayStr;
  });

  // Find the latest running attendance (no clock_out_at)
  const runningAttendances = userAttendancesToday.filter(a => a.clock_in_at && !a.clock_out_at);
  if (runningAttendances.length > 0) {
    const runningAttendance = runningAttendances.sort((a, b) => new Date(b.clock_in_at) - new Date(a.clock_in_at))[0];
    if (runningAttendance) {
      const clockIn = new Date(runningAttendance.clock_in_at);
      const now = new Date();
      return Math.floor((now - clockIn) / 1000);
    }
  }
  return 0;
}

function calculateWorkDuration() {
  const completedSeconds = getCompletedWorkDuration();
  const runningSeconds = getRunningWorkDuration();
  const totalSeconds = completedSeconds + runningSeconds;
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);
  const seconds = totalSeconds % 60;
  workDuration.value = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  workDuration.seconds = totalSeconds;
}

// Update status clockedIn dan clockedOut dari attendances
function updateClockStatus() {
  const myAttendance = getLastAttendance();
  if (myAttendance && myAttendance.clock_in_at) {
    clockedIn.value = true;
    clockedOut.value = !!myAttendance.clock_out_at;
  } else {
    clockedIn.value = false;
    clockedOut.value = false;
  }
}

// Update workDuration dan status clock setiap detik dan saat attendances berubah
setInterval(() => {
  calculateWorkDuration();
  updateClockStatus();
}, 1000);
watch(attendances, () => {
  calculateWorkDuration();
  updateClockStatus();
});
const officeLocationName = ref('');
const officeLocationLoading = ref(false);
const officeLocationError = ref('');
// Ambil lokasi kantor (alamat) saat page load
function fetchOfficeLocation() {
  officeLocationLoading.value = true;
  officeLocationError.value = '';
  axios.get(`/reverse-geocode?lat=${officeLat.value}&lon=${officeLon.value}`)
    .then(res => {
      officeLocationName.value = res.data.location_name;
      officeLocationLoading.value = false;
    })
    .catch(() => {
      officeLocationName.value = '';
      officeLocationError.value = 'Gagal mendapatkan nama lokasi kantor.';
      officeLocationLoading.value = false;
    });
}

fetchOfficeLocation();
const userLocationName = ref(usePage().props.user_location_name || '');
const userLat = ref(usePage().props.user_latitude ? Number(usePage().props.user_latitude) : null);
const userLon = ref(usePage().props.user_longitude ? Number(usePage().props.user_longitude) : null);
const userLocationLoading = ref(false);
const userLocationError = ref('');

// Tampilkan pesan flash jika ada
if (flash.error) {
  errorMessage.value = flash.error;
}
if (flash.success) {
  successMessage.value = flash.success;
}

// Ambil lokasi user saat page load
function fetchUserLocation() {
  userLocationLoading.value = true;
  userLocationError.value = '';
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userLat.value = position.coords.latitude;
        userLon.value = position.coords.longitude;
        // Reverse geocoding ke backend
  axios.get(`/reverse-geocode?lat=${userLat.value}&lon=${userLon.value}`)
          .then(res => {
            userLocationName.value = res.data.location_name;
            userLocationLoading.value = false;
          })
          .catch(() => {
            userLocationName.value = '';
            userLocationError.value = 'Gagal mendapatkan nama lokasi.';
            userLocationLoading.value = false;
          });
      },
      () => {
        userLocationName.value = '';
        userLocationError.value = 'Gagal mendapatkan lokasi dari browser.';
        userLocationLoading.value = false;
      }
    );
  } else {
    userLocationError.value = 'Browser tidak mendukung geolocation.';
    userLocationLoading.value = false;
  }
}

fetchUserLocation();
const now = ref(new Date());

setInterval(() => {
  calculateWorkDuration();
}, 1000);
watch(attendances, () => {
  calculateWorkDuration();
});

function formatDateTime(date) {
  return date.toLocaleString('id-ID', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  });
}

// Clock In
const handleClockIn = () => {
  errorMessage.value = '';
  if (navigator.geolocation) {
    loading.value = true;
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        axios.post('/attendances/clock-in', {
          latitude,
          longitude,
        })
        .then((response) => {
          // toast.add({ severity: 'success', summary: 'Sukses', detail: response.data.success, life: 3000 });
          successMessage.value = response.data.success;
          clockedIn.value = true;
          // Set lokasi kantor dan user
          officeName.value = response.data.office_name;
          officeLat.value = response.data.office_latitude;
          officeLon.value = response.data.office_longitude;
          userLocationName.value = response.data.user_location_name;
          userLat.value = response.data.user_latitude;
          userLon.value = response.data.user_longitude;
          // Refresh data absensi
          router.get(route('absent'), {}, {
            preserveState: true,
            onSuccess: (page) => {
              attendances.value = page.props.attendances || [];
            }
          });
        })
        .catch((error) => {
          const msg = error.response?.data?.error || 'Terjadi kesalahan.';
          // toast.add({ severity: 'error', summary: 'Gagal', detail: msg, life: 3000 });
          errorMessage.value = msg;
        })
        .finally(() => {
          loading.value = false;
        });
      },
      (error) => {
  // toast.add({ severity: 'error', summary: 'Error', detail: 'Akses lokasi ditolak.', life: 3000 });
        errorMessage.value = 'Akses lokasi ditolak.';
        loading.value = false;
      }
    );
  } else {
  // toast.add({ severity: 'error', summary: 'Error', detail: 'Browser Anda tidak mendukung Geolocation.', life: 3000 });
    errorMessage.value = 'Browser Anda tidak mendukung Geolocation.';
  }
};

// Clock Out
const handleClockOut = () => {
  errorMessage.value = '';
  if (navigator.geolocation) {
    loading.value = true;
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        axios.post('/attendances/clock-out', {
          latitude,
          longitude,
        })
        .then((response) => {
          // toast.add({ severity: 'success', summary: 'Sukses', detail: response.data.success, life: 3000 });
          successMessage.value = response.data.success;
          clockedOut.value = true;
          duration.value = '';
          // Set lokasi kantor dan user
          officeName.value = response.data.office_name;
          officeLat.value = response.data.office_latitude;
          officeLon.value = response.data.office_longitude;
          userLocationName.value = response.data.user_location_name;
          userLat.value = response.data.user_latitude;
          userLon.value = response.data.user_longitude;
          // Refresh data absensi
          router.get(route('absent'), {}, {
            preserveState: true,
            onSuccess: (page) => {
              attendances.value = page.props.attendances || [];
            }
          });
        })
        .catch((error) => {
          console.log(error);
          const msg = error.response?.data?.error || 'Terjadi kesalahan.';
          // toast.add({ severity: 'error', summary: 'Gagal', detail: msg, life: 3000 });
          errorMessage.value = msg;
        })
        .finally(() => {
          loading.value = false;
        });
      },
      (error) => {
  // toast.add({ severity: 'error', summary: 'Error', detail: 'Akses lokasi ditolak.', life: 3000 });
        errorMessage.value = 'Akses lokasi ditolak.';
        loading.value = false;
      }
    );
  } else {
  // toast.add({ severity: 'error', summary: 'Error', detail: 'Browser Anda tidak mendukung Geolocation.', life: 3000 });
    errorMessage.value = 'Browser Anda tidak mendukung Geolocation.';
  }
};

</script>


<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <InertiaHead title="Absensi" />
    <div class="w-full max-w-[100vw] mx-auto rounded-2xl border border-blue-100 dark:border-neutral-700 shadow-xl bg-white dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100 p-6 md:p-10 flex flex-col gap-8">
      <div class="w-full flex flex-col items-start gap-1 mb-2">
        <span class="text-base font-semibold text-blue-800 dark:text-blue-200">Waktu Sekarang:</span>
        <span class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ formatDateTime(now) }}</span>
  <span v-if="clockedIn && workDuration" class="text-base font-semibold text-green-700 dark:text-green-300 mt-2">Sudah bekerja hari ini: <span class="font-mono">{{ workDuration }}</span></span>
  <span v-if="minWorkDuration" class="text-base font-semibold text-yellow-700 dark:text-yellow-300 mt-1">Minimal kerja hari ini (jadwal): <span class="font-mono">{{ formatMinWorkDuration() }}</span></span>
      </div>
        <div class="w-full flex flex-col gap-2">
          <h2 class="text-3xl font-bold mb-1 text-blue-700 dark:text-blue-400">Absensi Kehadiran</h2>
          <p class="mb-2 text-gray-600 dark:text-gray-300">Klik tombol di bawah untuk melakukan clock in. Lokasi Anda akan diverifikasi.</p>
        </div>
        <transition name="fade">
          <div v-if="errorMessage" class="w-full bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg mb-2 text-base text-center border border-red-200 dark:border-red-700 shadow">
            {{ errorMessage }}
          </div>
        </transition>
        <transition name="fade">
          <div v-if="successMessage" class="w-full bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg mb-2 text-base text-center border border-green-200 dark:border-green-700 shadow">
            {{ successMessage }}
            <div v-if="officeName || userLocationName" class="mt-2 text-left">
              <div class="text-sm text-blue-700 dark:text-blue-300 font-semibold">Lokasi Kantor: {{ officeName }} <span v-if="officeLat && officeLon">({{ officeLat }}, {{ officeLon }})</span></div>
              <div class="text-sm text-green-700 dark:text-green-300 font-semibold" v-if="userLocationName">Lokasi Anda: {{ userLocationName }} <span v-if="userLat && userLon">({{ userLat }}, {{ userLon }})</span></div>
            </div>
          </div>
        </transition>
        <div class="flex flex-col md:flex-row gap-4 w-full">
          <div class="w-full mb-4 flex flex-col gap-2">
            <div class="text-sm text-blue-700 dark:text-blue-300 font-semibold">Lokasi Kantor: {{ officeName }} <span v-if="officeLat && officeLon">({{ officeLat }}, {{ officeLon }})</span></div>
            <div v-if="officeLocationLoading" class="text-sm text-gray-500">Mengambil alamat kantor...</div>
            <div v-else-if="officeLocationError" class="text-sm text-red-600">{{ officeLocationError }}</div>
            <div class="text-sm text-blue-700 dark:text-blue-300" v-if="officeLocationName">Alamat Kantor: {{ officeLocationName }}</div>
            <div v-if="userLocationLoading" class="text-sm text-gray-500">Mengambil lokasi Anda...</div>
            <div v-else-if="userLocationError" class="text-sm text-red-600">{{ userLocationError }}</div>
            <div class="text-sm text-green-700 dark:text-green-300 font-semibold" v-if="userLocationName">Lokasi Anda: {{ userLocationName }} <span v-if="userLat && userLon">({{ userLat }}, {{ userLon }})</span></div>
            <div class="text-sm text-gray-500" v-if="!userLocationName && !userLocationLoading && !userLocationError">Lokasi Anda belum tersedia.</div>
          </div>
          <Button 
            label="Clock In" 
            icon="pi pi-clock" 
            @click="handleClockIn" 
            :loading="loading" 
            :disabled="clockedIn && !clockedOut" 
            class="w-full md:w-1/2 h-14 text-lg font-semibold bg-blue-600 border-blue-600 text-white hover:bg-blue-700 hover:border-blue-700 dark:bg-blue-700 dark:border-blue-700 dark:hover:bg-blue-800 dark:hover:border-blue-800 rounded-xl shadow transition-colors duration-200" 
          />
          <Button 
            label="Clock Out" 
            icon="pi pi-sign-out" 
            @click="handleClockOut" 
            :loading="loading" 
            :disabled="!clockedIn || clockedOut" 
            class="w-full md:w-1/2 h-14 text-lg font-semibold bg-yellow-500 border-yellow-500 text-white hover:bg-yellow-600 hover:border-yellow-600 dark:bg-yellow-600 dark:border-yellow-600 dark:hover:bg-yellow-700 dark:hover:border-yellow-700 rounded-xl shadow transition-colors duration-200" 
          />
        </div>
        <div class="w-full mt-6">
          <h3 class="text-xl font-semibold mb-3 text-blue-700 dark:text-blue-400">Dashboard Absensi Hari Ini</h3>
          <div v-if="attendances.length === 0" class="text-gray-500 dark:text-gray-400">Belum ada data absensi hari ini.</div>
          <ul v-else class="divide-y divide-blue-100 dark:divide-neutral-700">
            <li v-for="item in attendances" :key="item.id" class="py-3 flex justify-between items-center">
              <span class="font-medium text-blue-900 dark:text-blue-300">{{ item.user_name || item.user?.name }}</span>
              <span class="flex gap-2">
                <span v-if="item.clock_in_at" class="bg-green-200 dark:bg-green-800 text-green-800 dark:text-green-200 px-2 py-1 rounded text-xs font-semibold shadow">Clock In: {{ item.clock_in_at }}</span>
                <span v-if="item.clock_out_at" class="bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 px-2 py-1 rounded text-xs font-semibold shadow">Clock Out: {{ item.clock_out_at }}</span>
              </span>
            </li>
          </ul>
        </div>
  <!-- <Toast /> -->
      </div>
  </AppLayout>
</template>
