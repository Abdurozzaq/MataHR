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
  { label: 'Job Position' }
];

const positionData = ref([]);
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

const fetchPositions = () => {
  loading.value = true;
  router.get(
    '/position',
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
        positionData.value = page.props.positions.data;
        totalRecords.value = page.props.positions.total;
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );
};

watch([page, rows, sortField, sortOrder, searchValue], fetchPositions);

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
const savePosition = () => {
  errorMessage.value = '';
  if (!form.value.name) {
    errorMessage.value = 'Nama posisi wajib diisi';
    return;
  }
  loading.value = true;
  if (dialogMode.value === 'create') {
    router.post('/position', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchPositions();
      },
      onFinish: () => (loading.value = false),
    });
  } else {
    router.put(`/position/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchPositions();
      },
      onFinish: () => (loading.value = false),
    });
  }
};

const showDeleteConfirm = ref(false);
const deleteId = ref(null);
const deletePosition = (id) => {
  deleteId.value = id;
  showDeleteConfirm.value = true;
};

const confirmDeletePosition = () => {
  loading.value = true;
  router.delete(`/position/${deleteId.value}`, {
    onSuccess: () => {
      fetchPositions();
      showDeleteConfirm.value = false;
      deleteId.value = null;
    },
    onFinish: () => (loading.value = false),
  });
};

const { isDark } = useDark();

fetchPositions();
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Master Job Position" />
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
          <Button label="Cari" icon="pi pi-search" @click="fetchPositions" />
        </div>
        <Button label="Tambah Posisi" icon="pi pi-plus" @click="openDialog('create')" />
      </div>
      <div class="overflow-x-auto">
        <DataTable
          :value="positionData"
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
                  @click="deletePosition(data.id)" 
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
        @page="e => { page = e.page + 1; rows = e.rows; fetchPositions(); }"
        class="mt-6"
      />
    </div>
    <Dialog v-model:visible="showDialog" :header="dialogMode === 'create' ? 'Tambah Posisi' : 'Edit Posisi'" modal class="min-w-[350px]">
      <form @submit.prevent="savePosition" class="flex flex-col gap-4 p-2">
        <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm border border-red-300">
          {{ errorMessage }}
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Nama Posisi</label>
          <InputText v-model="form.name" placeholder="Nama Posisi" required class="h-10" />
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
        <p>Apakah Anda yakin ingin menghapus posisi ini? Data tidak akan hilang permanen.</p>
        <div class="flex gap-2 justify-end mt-4">
          <Button label="Batal" @click="showDeleteConfirm = false" />
          <Button label="Hapus" severity="danger" :loading="loading" @click="confirmDeletePosition" />
        </div>
      </div>
    </Dialog>
  </AppLayout>
</template>
