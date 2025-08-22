<script setup>
import { ref, watch, watchEffect } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { useDark } from '@/composables/useDark';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import { Head } from '@inertiajs/vue3';

const breadcrumbs = [
  { label: 'Master Data' },
  { label: 'Work Schedule' }
];

const inertiaPage = usePage();
const workSchedules = ref([]);
const totalRecords = ref(0);

// Sinkronisasi data dari props inertia setiap kali berubah
watchEffect(() => {
  workSchedules.value = inertiaPage.props.workSchedules?.data ?? [];
  totalRecords.value = inertiaPage.props.workSchedules?.total ?? 0;
});
const loading = ref(false);
const page = ref(1);
const rows = ref(10);
const pageSizeOptions = [
  { label: '10', value: 10 },
  { label: '20', value: 20 },
  { label: '50', value: 50 },
  { label: '100', value: 100 },
];
const sortField = ref('id');
const sortOrder = ref(1);
const searchField = ref('id');
const searchValue = ref('');

const showDialog = ref(false);
const dialogMode = ref('create');

const form = ref({
  id: null,
  day_of_week: 1,
  clock_in_start: '',
  clock_in_end: '',
  clock_out_start: '',
  clock_out_end: '',
  is_workday: true,
  min_work_duration: '',
});

const fetchWorkSchedules = () => {
  loading.value = true;
  router.get(
    '/work-schedule',
    {
      page: page.value,
      rows: rows.value,
      sortField: sortField.value,
      sortOrder: sortOrder.value,
      searchField: searchField.value,
      searchValue: searchValue.value,
    },
    {
      preserveState: true,
      onSuccess: (page) => {
        workSchedules.value = page.props.workSchedules.data;
        totalRecords.value = page.props.workSchedules.total;
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );
};

watch([page, rows, sortField, sortOrder, searchValue], fetchWorkSchedules);

const openDialog = (mode, data = null) => {
  dialogMode.value = mode;
  if (mode === 'edit' && data) {
    form.value = { ...data };
  } else {
    form.value = {
      id: null,
      day_of_week: 1,
      clock_in_start: '',
      clock_in_end: '',
      clock_out_start: '',
      clock_out_end: '',
      is_workday: true,
      min_work_duration: '',
    };
  }
  showDialog.value = true;
};

const errorMessage = ref('');
const saveWorkSchedule = () => {
  errorMessage.value = '';
  if (form.value.day_of_week === '' || form.value.clock_in_start === '' || form.value.clock_in_end === '' || form.value.clock_out_start === '' || form.value.clock_out_end === '') {
    errorMessage.value = 'Semua field jam dan hari wajib diisi';
    return;
  }
  loading.value = true;
  if (dialogMode.value === 'create') {
    router.post('/work-schedule', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchWorkSchedules();
      },
      onFinish: () => (loading.value = false),
    });
  } else {
    router.put(`/work-schedule/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchWorkSchedules();
      },
      onFinish: () => (loading.value = false),
    });
  }
};

const showDeleteConfirm = ref(false);
const deleteId = ref(null);
const deleteWorkSchedule = (id) => {
  deleteId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDeleteWorkSchedule = () => {
  loading.value = true;
  router.delete(`/work-schedule/${deleteId.value}`, {
    onSuccess: () => {
      fetchWorkSchedules();
      showDeleteConfirm.value = false;
      deleteId.value = null;
    },
    onFinish: () => (loading.value = false),
  });
};

const { isDark } = useDark();

fetchWorkSchedules();
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Master Work Schedule" />
    <div class="p-6 rounded-xl shadow">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
          <InputText v-model="searchValue" placeholder="Cari..." class="min-w-[180px] h-10" @input="page.value = 1" />
          <Dropdown v-model="searchField" :options="[
            { label: 'ID', value: 'id' },
            { label: 'Hari', value: 'day_of_week' },
            { label: 'Jam Masuk Mulai', value: 'clock_in_start' },
            { label: 'Jam Masuk Akhir', value: 'clock_in_end' },
            { label: 'Jam Pulang Mulai', value: 'clock_out_start' },
            { label: 'Jam Pulang Akhir', value: 'clock_out_end' },
            { label: 'Workday', value: 'is_workday' },
            { label: 'Durasi Minimal', value: 'min_work_duration' },
          ]" optionLabel="label" optionValue="value" class="min-w-[120px] h-10" />
          <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" class="min-w-[120px] h-10" @change="page.value = 1" />
          <Button label="Cari" icon="pi pi-search" @click="fetchWorkSchedules" />
        </div>
        <Button label="Tambah Jadwal" icon="pi pi-plus" @click="openDialog('create')" />
      </div>
      <div class="overflow-x-auto">
        <DataTable
          :value="workSchedules"
          :loading="loading"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          @sort="e => { sortField.value = e.sortField; sortOrder.value = e.sortOrder }"
        >
          <Column field="id" header="ID" sortable />
          <Column field="day_of_week" header="Hari" sortable>
            <template #body="{ data }">
              {{ ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][data.day_of_week] }}
            </template>
          </Column>
          <Column field="clock_in_start" header="Masuk Mulai" sortable />
          <Column field="clock_in_end" header="Masuk Akhir" sortable />
          <Column field="clock_out_start" header="Pulang Mulai" sortable />
          <Column field="clock_out_end" header="Pulang Akhir" sortable />
          <Column field="is_workday" header="Workday" sortable>
            <template #body="{ data }">
              <span :class="data.is_workday ? 'text-green-600' : 'text-red-600'">
                {{ data.is_workday ? 'Ya' : 'Tidak' }}
              </span>
            </template>
          </Column>
          <Column field="min_work_duration" header="Durasi Minimal (menit)" sortable />
          <Column header="Aksi">
            <template #body="{ data }">
              <div class="flex gap-2 justify-center">
                  <Button 
                    label="Edit" 
                    icon="pi pi-pencil" 
                    size="small"  
                    @click="openDialog('edit', data)" 
                  />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
      <Paginator
        :rows="rows"
        :totalRecords="totalRecords"
        :first="(page - 1) * rows"
        @page="e => { page = e.page + 1; rows = e.rows; fetchWorkSchedules(); }"
        class="mt-6"
      />
    </div>
    <Dialog v-model:visible="showDialog" :header="dialogMode === 'create' ? 'Tambah Jadwal' : 'Edit Jadwal'" modal class="min-w-[350px]">
      <form @submit.prevent="saveWorkSchedule" class="flex flex-col gap-4 p-2">
        <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm border border-red-300">
          {{ errorMessage }}
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Hari</label>
          <Dropdown v-model="form.day_of_week" :options="[
            { label: 'Minggu', value: 0 },
            { label: 'Senin', value: 1 },
            { label: 'Selasa', value: 2 },
            { label: 'Rabu', value: 3 },
            { label: 'Kamis', value: 4 },
            { label: 'Jumat', value: 5 },
            { label: 'Sabtu', value: 6 },
          ]" optionLabel="label" optionValue="value" class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Jam Masuk Mulai</label>
          <InputText v-model="form.clock_in_start" type="time" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Jam Masuk Akhir</label>
          <InputText v-model="form.clock_in_end" type="time" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Jam Pulang Mulai</label>
          <InputText v-model="form.clock_out_start" type="time" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Jam Pulang Akhir</label>
          <InputText v-model="form.clock_out_end" type="time" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Workday</label>
          <Dropdown v-model="form.is_workday" :options="[
            { label: 'Ya', value: true },
            { label: 'Tidak', value: false },
          ]" optionLabel="label" optionValue="value" class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Durasi Minimal (menit)</label>
          <InputText v-model="form.min_work_duration" type="number" min="0" class="h-10" />
        </div>
        <div class="flex gap-2 justify-end mt-2">
          <Button label="Batal" @click="showDialog = false" />
          <Button label="Simpan" type="submit" :loading="loading" />
        </div>
      </form>
    </Dialog>


  </AppLayout>
</template>
