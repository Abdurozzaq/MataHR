<script setup>
import { ref, watch, h } from 'vue';
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
    { label: 'Dashboard' },
    { label: 'Pengajuan Cuti' }
];

const leaveData = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const page = ref(1);
const rows = ref(10);
const pageSizeOptions = [
  { label: '10', value: 10 },
  { label: '20', value: 20 },
  { label: '50', value: 50 },
  { label: '100', value: 100 },
];
const sortField = ref('created_at');
const sortOrder = ref(-1);
const searchField = ref('employee_name');
const searchValue = ref('');
const statusFilter = ref('');

const showDialog = ref(false);
const dialogMode = ref('create');

// Assume employee_name is available from page.props.auth.user.name (Inertia default)
import { usePage } from '@inertiajs/vue3';
const pageData = usePage();
const employeeName = pageData.props.auth?.user?.name || '';

const form = ref({
  id: null,
  employee_name: employeeName,
  leave_type: '',
  start_date: '',
  end_date: '',
  reason: '',
  // status field dihapus, status akan otomatis terdeteksi
  approved_at: null,
  rejected_at: null,
});

const leaveTypes = [
  { label: 'Tahunan', value: 'annual' },
  { label: 'Sakit', value: 'sick' },
  { label: 'Melahirkan', value: 'maternity' },
  { label: 'Penting', value: 'important' },
];

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'Disetujui', value: 'approved' },
  { label: 'Ditolak', value: 'rejected' },
];

const fetchLeaves = () => {
  loading.value = true;
  router.get(
    '/leave',
    {
      page: page.value,
      rows: rows.value,
      sortField: sortField.value,
      sortOrder: sortOrder.value,
      searchField: searchField.value,
      searchValue: searchValue.value,
      status: statusFilter.value,
    },
    {
      preserveState: true,
      onSuccess: (page) => {
        leaveData.value = page.props.leaves.data;
        totalRecords.value = page.props.leaves.total;
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );
};

watch([page, rows, sortField, sortOrder, searchValue, statusFilter], fetchLeaves);

const openDialog = (mode, data = null) => {
  dialogMode.value = mode;
  if (mode === 'edit' && data) {
    form.value = { ...data };
    // Overwrite employee_name with session value for consistency
    form.value.employee_name = employeeName;
  } else {
    form.value = {
      id: null,
      employee_name: employeeName,
      leave_type: '',
      start_date: '',
      end_date: '',
      reason: '',
    };
  }
  showDialog.value = true;
};

const errorMessage = ref('');
const saveLeave = () => {
  // Validasi date range
  errorMessage.value = '';
  if (form.value.start_date && form.value.end_date && form.value.start_date > form.value.end_date) {
    errorMessage.value = 'Tanggal mulai harus lebih kecil atau sama dengan tanggal selesai';
    return;
  }
  loading.value = true;
  if (dialogMode.value === 'create') {
    router.post('/leave', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchLeaves();
      },
      onFinish: () => (loading.value = false),
    });
  } else {
    router.put(`/leave/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchLeaves();
      },
      onFinish: () => (loading.value = false),
    });
  }
};

const deleteLeave = (id) => {
  loading.value = true;
  router.delete(`/leave/${id}`, {
    onSuccess: fetchLeaves,
    onFinish: () => (loading.value = false),
  });
};

const { isDark } = useDark();

fetchLeaves();


function leaveTypeLabel(row) {
  const type = leaveTypes.find(t => t.value === row.leave_type);
  return type ? type.label : row.leave_type;
}

// Cancel (soft delete) logic
const showCancelDialog = ref(false);
const cancelId = ref(null);

const openCancelDialog = (id) => {
  cancelId.value = id;
  showCancelDialog.value = true;
};

const cancelLeave = () => {
  loading.value = true;
  router.post(`/leave/${cancelId.value}/cancel`, {}, {
    onSuccess: () => {
      showCancelDialog.value = false;
      fetchLeaves();
    },
    onFinish: () => (loading.value = false),
  });
};
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Pengajuan Cuti" />
    <div :class="['p-6 rounded-xl shadow', isDark ? 'bg-gradient-to-br from-neutral-900 via-slate-800 to-neutral-800 text-neutral-100' : 'bg-gradient-to-br from-blue-50 via-sky-100 to-blue-200 text-neutral-900']">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
          <InputText v-model="searchValue" placeholder="Cari..." :class="[isDark ? 'min-w-[180px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[180px] h-10 border-blue-300 focus:border-blue-500']" @input="page.value = 1" />
          <Dropdown v-model="searchField" :options="[
            { label: 'Nama', value: 'employee_name' },
            { label: 'Jenis Cuti', value: 'leave_type' },
          ]" optionLabel="label" optionValue="value" :class="[isDark ? 'min-w-[120px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[120px] h-10 border-blue-300 focus:border-blue-500']" />
          <Dropdown v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" :class="[isDark ? 'min-w-[120px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[120px] h-10 border-blue-300 focus:border-blue-500']" />
          <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" :class="[isDark ? 'min-w-[120px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[120px] h-10 border-blue-300 focus:border-blue-500']" @change="page.value = 1" />
          <Button label="Cari" icon="pi pi-search" @click="fetchLeaves" :class="[isDark ? 'bg-blue-700 border-blue-700 text-white hover:bg-blue-800 hover:border-blue-800 h-10 px-4 rounded-lg' : 'bg-blue-500 border-blue-500 text-white hover:bg-blue-600 hover:border-blue-600 h-10 px-4 rounded-lg']" />
        </div>
        <Button label="Tambah Pengajuan" icon="pi pi-plus" @click="openDialog('create')" :class="[isDark ? 'bg-green-700 border-green-700 text-white hover:bg-green-800 hover:border-green-800 h-10 px-4 rounded-lg' : 'bg-green-500 border-green-500 text-white hover:bg-green-600 hover:border-green-600 h-10 px-4 rounded-lg']" />
      </div>
      <div :class="[isDark ? 'overflow-x-auto rounded-xl shadow border border-slate-700 bg-neutral-900' : 'overflow-x-auto rounded-xl shadow border border-blue-100 bg-white']">
        <DataTable
          :value="leaveData"
          :loading="loading"
          :paginator="false"
          :rows="rows"
          :totalRecords="totalRecords"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          class="shadow-none rounded-xl border-0"
          style="border: none;"
          @sort="e => { sortField.value = e.sortField; sortOrder.value = e.sortOrder }"
        >
          <Column field="employee_name" header="Nama" sortable :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="leave_type" header="Jenis Cuti" sortable :body="leaveTypeLabel" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="start_date" header="Mulai" sortable :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="end_date" header="Selesai" sortable :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="reason" header="Alasan" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column header="Status" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'">
            <template #body="{ data }">
              <span>
                  <template v-if="data.approved_at">
                    <span :style="isDark ? 'color: #22c55e; font-weight: bold; background: #14532d; padding: 2px 10px; border-radius: 8px;' : 'color: #22c55e; font-weight: bold; background: #dcfce7; padding: 2px 10px; border-radius: 8px;'">Disetujui</span>
                  </template>
                  <template v-else-if="data.rejected_at">
                    <span :style="isDark ? 'color: #ef4444; font-weight: bold; background: #7f1d1d; padding: 2px 10px; border-radius: 8px;' : 'color: #ef4444; font-weight: bold; background: #fee2e2; padding: 2px 10px; border-radius: 8px;'">Ditolak</span>
                  </template>
                  <template v-else>
                    <span :style="isDark ? 'color: #eab308; font-weight: bold; background: #78350f; padding: 2px 10px; border-radius: 8px;' : 'color: #eab308; font-weight: bold; background: #fef9c3; padding: 2px 10px; border-radius: 8px;'">Pending</span>
                  </template>
              </span>
            </template>
          </Column>
          <Column header="Aksi" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'">
            <template #body="{ data }">
              <div class="flex gap-2 justify-center">
                <Button 
                  label="Edit" 
                  icon="pi pi-pencil" 
                  size="small" 
                  outlined 
                  :class="isDark ? 'border-blue-400 text-blue-300 hover:bg-slate-800 hover:border-blue-500' : 'border-blue-400 text-blue-700 hover:bg-blue-50 hover:border-blue-500'" 
                  @click="openDialog('edit', data)" 
                  :disabled="data.approved_at || data.rejected_at"
                />
                <Button 
                  label="Hapus" 
                  icon="pi pi-trash" 
                  size="small" 
                  outlined 
                  :class="isDark ? 'border-red-400 text-red-300 hover:bg-slate-800 hover:border-red-500' : 'border-red-400 text-red-700 hover:bg-red-50 hover:border-red-500'" 
                  @click="deleteLeave(data.id)" 
                  :disabled="data.approved_at || data.rejected_at"
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
        @page="e => { page.value = e.page + 1; rows.value = e.rows }"
        :class="isDark ? 'mt-6 text-neutral-100' : 'mt-6'"
      />
    </div>

    <Dialog v-model:visible="showDialog" :header="dialogMode === 'create' ? 'Tambah Pengajuan' : 'Edit Pengajuan'" modal class="min-w-[350px]">
      <form @submit.prevent="saveLeave" class="flex flex-col gap-4 p-2">
        <div v-if="errorMessage" :class="isDark ? 'bg-red-900 text-red-300 px-3 py-2 rounded mb-2 text-sm' : 'bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm'">
          {{ errorMessage }}
        </div>
        <div class="flex flex-col gap-2">
          <label :class="isDark ? 'text-sm font-medium text-blue-300' : 'text-sm font-medium text-blue-700'">Nama Karyawan</label>
          <InputText :value="form.employee_name" placeholder="Nama Karyawan" disabled :class="isDark ? 'h-10 border-slate-700 bg-neutral-900 text-neutral-100' : 'h-10 border-blue-300 bg-blue-50'" />
        </div>
        <div class="flex flex-col gap-2">
          <label :class="isDark ? 'text-sm font-medium text-blue-300' : 'text-sm font-medium text-blue-700'">Jenis Cuti</label>
          <Dropdown v-model="form.leave_type" :options="leaveTypes" optionLabel="label" optionValue="value" placeholder="Jenis Cuti" required :class="isDark ? 'h-10 border-slate-700 bg-neutral-900 text-neutral-100' : 'h-10 border-blue-300'" />
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label :class="isDark ? 'text-sm font-medium text-blue-300' : 'text-sm font-medium text-blue-700'">Mulai</label>
            <InputText v-model="form.start_date" type="date" placeholder="Mulai" required :class="isDark ? 'h-10 border-slate-700 bg-neutral-900 text-neutral-100' : 'h-10 border-blue-300'" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label :class="isDark ? 'text-sm font-medium text-blue-300' : 'text-sm font-medium text-blue-700'">Selesai</label>
            <InputText v-model="form.end_date" type="date" placeholder="Selesai" required :class="isDark ? 'h-10 border-slate-700 bg-neutral-900 text-neutral-100' : 'h-10 border-blue-300'" />
          </div>
        </div>
        <div class="flex flex-col gap-2">
          <label :class="isDark ? 'text-sm font-medium text-blue-300' : 'text-sm font-medium text-blue-700'">Alasan</label>
          <InputText v-model="form.reason" placeholder="Alasan" required :class="isDark ? 'h-10 border-slate-700 bg-neutral-900 text-neutral-100' : 'h-10 border-blue-300'" />
        </div>
        <!-- Status otomatis, tidak bisa dipilih manual -->
        <div class="flex gap-2 justify-end mt-2">
          <Button label="Batal" @click="showDialog = false" :class="isDark ? 'bg-slate-700 text-neutral-100 hover:bg-slate-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" />
          <Button label="Simpan" type="submit" :loading="loading" :class="isDark ? 'bg-blue-700 border-blue-700 text-white hover:bg-blue-800 hover:border-blue-800' : 'bg-blue-500 border-blue-500 text-white hover:bg-blue-600 hover:border-blue-600'" />
        </div>
      </form>
    </Dialog>
    <Dialog v-model:visible="showCancelDialog" header="Konfirmasi Cancel Cuti" modal class="min-w-[350px]">
      <div :class="isDark ? 'mb-4 text-blue-300' : 'mb-4 text-blue-700'">Apakah Anda yakin ingin membatalkan cuti ini? Data cuti tidak akan dihapus, hanya statusnya menjadi batal.</div>
      <div class="flex gap-2 justify-end">
        <Button label="Batal" @click="showCancelDialog = false" :class="isDark ? 'bg-slate-700 text-neutral-100 hover:bg-slate-800' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'" />
        <Button label="Ya, Cancel" severity="warning" :loading="loading" @click="cancelLeave" :class="isDark ? 'bg-yellow-700 border-yellow-700 text-white hover:bg-yellow-800 hover:border-yellow-800' : 'bg-yellow-400 border-yellow-400 text-white hover:bg-yellow-500 hover:border-yellow-500'" />
      </div>
    </Dialog>
  </AppLayout>
</template>
