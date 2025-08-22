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
  <div class="p-6 rounded-xl shadow">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
          <InputText v-model="searchValue" placeholder="Cari..." class="min-w-[180px] h-10" @input="page.value = 1" />
          <Dropdown v-model="searchField" :options="[
            { label: 'Nama', value: 'employee_name' },
            { label: 'Jenis Cuti', value: 'leave_type' },
          ]" optionLabel="label" optionValue="value" class="min-w-[120px] h-10" />
          <Dropdown v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="min-w-[120px] h-10" />
          <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" class="min-w-[120px] h-10" @change="page.value = 1" />
          <Button label="Cari" icon="pi pi-search" @click="fetchLeaves" />
        </div>
  <Button label="Tambah Pengajuan" icon="pi pi-plus" @click="openDialog('create')" />
      </div>
  <div class="overflow-x-auto">
        <DataTable
          :value="leaveData"
          :loading="loading"
          :paginator="false"
          :rows="rows"
          :totalRecords="totalRecords"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          @sort="e => { sortField = e.sortField; sortOrder = e.sortOrder }"
        >
          <Column field="employee_name" header="Nama" sortable />
          <Column field="leave_type" header="Jenis Cuti" sortable :body="leaveTypeLabel" />
          <Column field="start_date" header="Mulai" sortable />
          <Column field="end_date" header="Selesai" sortable />
          <Column field="reason" header="Alasan" />
          <Column header="Status">
            <template #body="{ data }">
              <span>
                  <template v-if="data.approved_at">
                    <span>Disetujui</span>
                  </template>
                  <template v-else-if="data.rejected_at">
                    <span>Ditolak</span>
                  </template>
                  <template v-else>
                    <span>Pending</span>
                  </template>
              </span>
            </template>
          </Column>
          <Column header="Aksi">
            <template #body="{ data }">
              <div class="flex gap-2 justify-center">
                <Button 
                  label="Edit" 
                  icon="pi pi-pencil" 
                  size="small"  
                  @click="openDialog('edit', data)" 
                  :disabled="data.approved_at || data.rejected_at"
                />
                <Button 
                  label="Hapus" 
                  icon="pi pi-trash" 
                  size="small" 
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
    @page="e => { page = e.page + 1; rows = e.rows }"
    class="mt-6"
      />
    </div>

    <Dialog v-model:visible="showDialog" :header="dialogMode === 'create' ? 'Tambah Pengajuan' : 'Edit Pengajuan'" modal class="min-w-[350px]">
      <form @submit.prevent="saveLeave" class="flex flex-col gap-4 p-2">
  <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm border border-red-300">
          {{ errorMessage }}
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Nama Karyawan</label>
          <InputText :value="form.employee_name" placeholder="Nama Karyawan" disabled class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Jenis Cuti</label>
          <Dropdown v-model="form.leave_type" :options="leaveTypes" optionLabel="label" optionValue="value" placeholder="Jenis Cuti" required class="h-10" />
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Mulai</label>
            <InputText v-model="form.start_date" type="date" placeholder="Mulai" required class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Selesai</label>
            <InputText v-model="form.end_date" type="date" placeholder="Selesai" required class="h-10" />
          </div>
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Alasan</label>
          <InputText v-model="form.reason" placeholder="Alasan" required class="h-10" />
        </div>
        <!-- Status otomatis, tidak bisa dipilih manual -->
        <div class="flex gap-2 justify-end mt-2">
          <Button label="Batal" @click="showDialog = false" />
          <Button label="Simpan" type="submit" :loading="loading" />
        </div>
      </form>
    </Dialog>
    <Dialog v-model:visible="showCancelDialog" header="Konfirmasi Cancel Cuti" modal class="min-w-[350px]">
  <div class="mb-4">Apakah Anda yakin ingin membatalkan cuti ini? Data cuti tidak akan dihapus, hanya statusnya menjadi batal.</div>
      <div class="flex gap-2 justify-end">
  <Button label="Batal" @click="showCancelDialog = false" />
  <Button label="Ya, Cancel" severity="warning" :loading="loading" @click="cancelLeave" />
      </div>
    </Dialog>
  </AppLayout>
</template>
