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
  { label: 'Master Data' },
  { label: 'Department' }
];

const departmentData = ref([]);
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
const sortField = ref('id');
const sortOrder = ref(1);
const searchField = ref('name');
const searchValue = ref('');

const showDialog = ref(false);
const dialogMode = ref('create');

const form = ref({
  id: null,
  name: '',
  description: '',
});

const fetchDepartments = () => {
  loading.value = true;
  router.get(
    '/department',
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
        departmentData.value = page.props.departments.data;
        totalRecords.value = page.props.departments.total;
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );
};

watch([page, rows, sortField, sortOrder, searchValue], fetchDepartments);

const openDialog = (mode, data = null) => {
  dialogMode.value = mode;
  if (mode === 'edit' && data) {
    form.value = { ...data };
  } else {
    form.value = {
      id: null,
      name: '',
      description: '',
    };
  }
  showDialog.value = true;
};

const errorMessage = ref('');
const saveDepartment = () => {
  errorMessage.value = '';
  if (!form.value.name) {
    errorMessage.value = 'Nama department wajib diisi';
    return;
  }
  loading.value = true;
  if (dialogMode.value === 'create') {
    router.post('/department', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchDepartments();
      },
      onFinish: () => (loading.value = false),
    });
  } else {
    router.put(`/department/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchDepartments();
      },
      onFinish: () => (loading.value = false),
    });
  }
};


const showDeleteConfirm = ref(false);
const deleteId = ref(null);
const deleteDepartment = (id) => {
  deleteId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDeleteDepartment = () => {
  loading.value = true;
  router.delete(`/department/${deleteId.value}`, {
    onSuccess: () => {
      fetchDepartments();
      showDeleteConfirm.value = false;
      deleteId.value = null;
    },
    onFinish: () => (loading.value = false),
  });
};

const { isDark } = useDark();

fetchDepartments();
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Master Department" />
    <div class="p-6 rounded-xl shadow">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
          <InputText v-model="searchValue" placeholder="Cari..." class="min-w-[180px] h-10" @input="page.value = 1" />
          <Dropdown v-model="searchField" :options="[
            { label: 'ID', value: 'id' },
            { label: 'Nama', value: 'name' },
            { label: 'Deskripsi', value: 'description' },
          ]" optionLabel="label" optionValue="value" class="min-w-[120px] h-10" />
          <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" class="min-w-[120px] h-10" @change="page.value = 1" />
          <Button label="Cari" icon="pi pi-search" @click="fetchDepartments" />
        </div>
        <Button label="Tambah Department" icon="pi pi-plus" @click="openDialog('create')" />
      </div>
      <div class="overflow-x-auto">
        <DataTable
          :value="departmentData"
          :loading="loading"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          @sort="e => { sortField.value = e.sortField; sortOrder.value = e.sortOrder }"
        >
          <Column field="id" header="ID" sortable />
          <Column field="name" header="Nama" sortable />
          <Column field="description" header="Deskripsi" sortable />
          <Column header="Aksi">
            <template #body="{ data }">
              <div class="flex gap-2 justify-center">
                <Button 
                  label="Edit" 
                  icon="pi pi-pencil" 
                  size="small"  
                  @click="openDialog('edit', data)" 
                />
                <Button 
                  label="Hapus" 
                  icon="pi pi-trash" 
                  size="small" 
                  @click="deleteDepartment(data.id)" 
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
    @page="e => { page = e.page + 1; rows = e.rows; fetchDepartments(); }"
        class="mt-6"
      />
    </div>
    <Dialog v-model:visible="showDialog" :header="dialogMode === 'create' ? 'Tambah Department' : 'Edit Department'" modal class="min-w-[350px]">
      <form @submit.prevent="saveDepartment" class="flex flex-col gap-4 p-2">
        <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm border border-red-300">
          {{ errorMessage }}
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Nama Department</label>
          <InputText v-model="form.name" placeholder="Nama Department" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Deskripsi</label>
          <InputText v-model="form.description" placeholder="Deskripsi" class="h-10" />
        </div>
        <div class="flex gap-2 justify-end mt-2">
          <Button label="Batal" @click="showDialog = false" />
          <Button label="Simpan" type="submit" :loading="loading" />
        </div>
      </form>
    </Dialog>

    <Dialog v-model:visible="showDeleteConfirm" header="Konfirmasi Hapus" modal class="min-w-[350px]">
      <div class="p-4">
        <p>Apakah Anda yakin ingin menghapus department ini? Data tidak akan hilang permanen.</p>
        <div class="flex gap-2 justify-end mt-4">
          <Button label="Batal" @click="showDeleteConfirm = false" />
          <Button label="Hapus" severity="danger" :loading="loading" @click="confirmDeleteDepartment" />
        </div>
      </div>
    </Dialog>
  </AppLayout>
</template>
