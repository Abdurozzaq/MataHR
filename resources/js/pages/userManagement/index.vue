<script setup>
import { ref, watch } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { useDark } from '@/composables/useDark';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import { Head } from '@inertiajs/vue3';
// Komponen untuk field multiple value
import EmergencyContactTable from '@/components/profile/EmergencyContactTable.vue';
import JobHistoryTable from '@/components/profile/JobHistoryTable.vue';
import WorkGoalTable from '@/components/profile/WorkGoalTable.vue';
import CareerPlanTable from '@/components/profile/CareerPlanTable.vue';
import CertificationTable from '@/components/profile/CertificationTable.vue';
import TrainingTable from '@/components/profile/TrainingTable.vue';

const breadcrumbs = [
  { label: 'Dashboard' },
  { label: 'User Management' }
];

const userData = ref([]);
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
const searchField = ref('name');
const searchValue = ref('');

const showDialog = ref(false);
const dialogMode = ref('create');

const pageData = usePage();

const roleOptions = [
  { label: 'Manager', value: 'manager' },
  { label: 'Supervisor', value: 'supervisor' },
  { label: 'Staff', value: 'staff' },
];

const form = ref({
  id: null,
  name: '',
  email: '',
  role: 'staff',
  position_id: null,
  department_id: null,
  phone: '',
  gender: '',
  employment_status: '',
  address: '',
  birth_date: '',
  birth_place: '',
  marital_status: '',
  nik: '',
  employee_id: '',
  office_location: '',
  supervisor_id: null,
  salary: '',
  bank_account: '',
  bank_name: '',
  password: '',
  password_confirmation: '',
  emergency_contacts: [],
  job_histories: [],
  work_goals: [],
  trainings: [],
  certifications: [],
  career_plans: [],
});

const genderOptions = [
  { label: 'Laki-laki', value: 'male' },
  { label: 'Perempuan', value: 'female' },
  { label: 'Lainnya', value: 'other' },
];
const maritalStatusOptions = [
  { label: 'Single', value: 'single' },
  { label: 'Menikah', value: 'married' },
  { label: 'Cerai', value: 'divorced' },
  { label: 'Duda/Janda', value: 'widowed' },
];
const employmentStatusOptions = [
  { label: 'Tetap', value: 'permanent' },
  { label: 'Kontrak', value: 'contract' },
  { label: 'Magang', value: 'intern' },
];

// Relasi, dapatkan dari props page
const positions = pageData.props.positions || [];
const departments = pageData.props.departments || [];
const supervisors = pageData.props.supervisors || [];

const searchFields = [
  { label: 'Nama', value: 'name' },
  { label: 'Email', value: 'email' },
  { label: 'Role', value: 'role' },
  { label: 'NIK', value: 'nik' },
  { label: 'ID Karyawan', value: 'employee_id' },
  { label: 'Telepon', value: 'phone' },
  { label: 'Status', value: 'employment_status' },
  { label: 'Departemen', value: 'department_id' },
  { label: 'Posisi', value: 'position_id' },
];

const fetchUsers = () => {
  loading.value = true;
  router.get(
    '/users',
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
        userData.value = page.props.users.data;
        totalRecords.value = page.props.users.total;
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );
};

watch([page, rows, sortField, sortOrder, searchValue], fetchUsers);

import axios from 'axios';

const openDialog = async (mode, data = null) => {
  dialogMode.value = mode;
  if (mode === 'edit' && data && data.id) {
    loading.value = true;
    try {
      // Always fetch the latest user data from server using the id from the edit button
      const res = await axios.get(`/users/${data.id}`);
      const user = res.data;
      form.value = {
        id: user.id ?? null,
        name: user.name ?? '',
        email: user.email ?? '',
        role: user.role ?? 'staff',
        position_id: user.position_id ?? null,
        department_id: user.department_id ?? null,
        phone: user.phone ?? '',
        gender: user.gender ?? '',
        employment_status: user.employment_status ?? '',
        address: user.address ?? '',
        birth_date: user.birth_date ?? '',
        birth_place: user.birth_place ?? '',
        marital_status: user.marital_status ?? '',
        nik: user.nik ?? '',
        employee_id: user.employee_id ?? '',
        office_location: user.office_location ?? '',
        supervisor_id: user.supervisor_id ?? null,
        salary: user.salary ?? '',
        bank_account: user.bank_account ?? '',
        bank_name: user.bank_name ?? '',
        emergency_contacts: Array.isArray(user.emergency_contacts) ? user.emergency_contacts : [],
        job_histories: Array.isArray(user.job_histories) ? user.job_histories : [],
        work_goals: Array.isArray(user.work_goals) ? user.work_goals : [],
        trainings: Array.isArray(user.trainings) ? user.trainings : [],
        certifications: Array.isArray(user.certifications) ? user.certifications : [],
        career_plans: Array.isArray(user.career_plans) ? user.career_plans : [],
        password: '',
        password_confirmation: '',
      };
    } catch (err) {
      window.$toast?.add({
        severity: 'error',
        summary: 'Gagal mengambil data user',
        detail: err?.response?.data?.message || 'Terjadi kesalahan server',
        life: 4000,
      });
    } finally {
      loading.value = false;
    }
    showDialog.value = true;
  } else {
    // Reset form for create mode
    form.value = {
      id: null,
      name: '',
      email: '',
      role: 'staff',
      position_id: null,
      department_id: null,
      phone: '',
      gender: '',
      employment_status: '',
      address: '',
      birth_date: '',
      birth_place: '',
      marital_status: '',
      nik: '',
      employee_id: '',
      office_location: '',
      supervisor_id: null,
      salary: '',
      bank_account: '',
      bank_name: '',
      emergency_contacts: [],
      job_histories: [],
      work_goals: [],
      trainings: [],
      certifications: [],
      career_plans: [],
      password: '',
      password_confirmation: '',
    };
    showDialog.value = true;
  }
};

const errorMessage = ref('');
const saveUser = () => {
  errorMessage.value = '';
  loading.value = true;
  if (dialogMode.value === 'create') {
    router.post('/users', form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchUsers();
      },
      onFinish: () => (loading.value = false),
    });
  } else {
    router.put(`/users/${form.value.id}`, form.value, {
      onSuccess: () => {
        showDialog.value = false;
        fetchUsers();
      },
      onFinish: () => (loading.value = false),
    });
  }
};

const deleteUser = (id) => {
  loading.value = true;
  router.delete(`/users/${id}`, {
    onSuccess: fetchUsers,
    onFinish: () => (loading.value = false),
  });
};

const { isDark } = useDark();

fetchUsers();
</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="User Management" />
    <div class="p-6 rounded-xl shadow">
      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div class="flex flex-wrap gap-3 items-center">
          <InputText v-model="searchValue" placeholder="Cari..." class="min-w-[180px] h-10" @input="page.value = 1" />
          <Dropdown v-model="searchField" :options="searchFields" optionLabel="label" optionValue="value" class="min-w-[120px] h-10" />
          <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" class="min-w-[120px] h-10" @change="page.value = 1" />
          <Button label="Cari" icon="pi pi-search" @click="fetchUsers" />
        </div>
        <Button label="Tambah User" icon="pi pi-plus" @click="openDialog('create')" />
      </div>
      <div class="overflow-x-auto">
        <DataTable
          :value="userData"
          :loading="loading"
          :paginator="false"
          :rows="rows"
          :totalRecords="totalRecords"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          @sort="e => { sortField = e.sortField; sortOrder = e.sortOrder }"
        >
          <Column field="name" header="Nama" sortable />
          <Column field="email" header="Email" sortable />
          <Column field="role" header="Role" sortable />
          <Column field="nik" header="NIK" sortable />
          <Column field="employee_id" header="ID Karyawan" sortable />
          <Column field="phone" header="Telepon" sortable />
          <Column field="employment_status" header="Status" sortable />
          <Column field="department.name" header="Departemen" :body="row => row.department ? row.department.name : '-'" sortable />
          <Column field="position.name" header="Posisi" :body="row => row.position ? row.position.name : '-'" sortable />
          <Column field="gender" header="Gender" sortable />
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
                  @click="deleteUser(data.id)" 
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

    <Dialog v-model:visible="showDialog" :header="dialogMode === 'create' ? 'Tambah User' : 'Edit User'" modal class="min-w-[350px]">
      <form @submit.prevent="saveUser" class="flex flex-col gap-4 p-2">
        <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm border border-red-300">
          {{ errorMessage }}
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Nama</label>
          <InputText v-model="form.name" placeholder="Nama" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Email</label>
          <InputText v-model="form.email" placeholder="Email" required class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Password</label>
          <InputText v-model="form.password" type="password" placeholder="Password" :required="dialogMode === 'create'" class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Konfirmasi Password</label>
          <InputText v-model="form.password_confirmation" type="password" placeholder="Konfirmasi Password" :required="dialogMode === 'create'" class="h-10" />
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Role</label>
          <Dropdown v-model="form.role" :options="roleOptions" optionLabel="label" optionValue="value" placeholder="Pilih Role" class="h-10" required />
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">NIK</label>
            <InputText v-model="form.nik" placeholder="NIK" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">ID Karyawan</label>
              <InputText v-model="form.employee_id" placeholder="Auto Generate" class="h-10" :disabled="dialogMode === 'create'" />
          </div>
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Telepon</label>
            <InputText v-model="form.phone" placeholder="Telepon" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Status</label>
            <Dropdown v-model="form.employment_status" :options="employmentStatusOptions" optionLabel="label" optionValue="value" placeholder="Status" class="h-10" />
          </div>
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Departemen</label>
            <Dropdown v-model="form.department_id" :options="departments" optionLabel="name" optionValue="id" placeholder="Departemen" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Posisi</label>
            <Dropdown v-model="form.position_id" :options="positions" optionLabel="name" optionValue="id" placeholder="Posisi" class="h-10" />
          </div>
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Gender</label>
            <Dropdown v-model="form.gender" :options="genderOptions" optionLabel="label" optionValue="value" placeholder="Gender" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Status Pernikahan</label>
            <Dropdown v-model="form.marital_status" :options="maritalStatusOptions" optionLabel="label" optionValue="value" placeholder="Status Pernikahan" class="h-10" />
          </div>
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Alamat</label>
          <InputText v-model="form.address" placeholder="Alamat" class="h-10" />
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Tanggal Lahir</label>
            <InputText v-model="form.birth_date" type="date" placeholder="Tanggal Lahir" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Tempat Lahir</label>
            <InputText v-model="form.birth_place" placeholder="Tempat Lahir" class="h-10" />
          </div>
        </div>
        <div class="flex flex-col gap-2">
          <label class="text-sm font-medium">Lokasi Kantor</label>
          <InputText v-model="form.office_location" placeholder="Lokasi Kantor" class="h-10" />
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Supervisor</label>
            <Dropdown v-model="form.supervisor_id" :options="supervisors" optionLabel="name" optionValue="id" placeholder="Supervisor" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Gaji</label>
            <InputText v-model="form.salary" type="number" placeholder="Gaji" class="h-10" />
          </div>
        </div>
        <div class="flex gap-2">
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Bank Account</label>
            <InputText v-model="form.bank_account" placeholder="Bank Account" class="h-10" />
          </div>
          <div class="flex flex-col gap-2 w-1/2">
            <label class="text-sm font-medium">Bank Name</label>
            <InputText v-model="form.bank_name" placeholder="Bank Name" class="h-10" />
          </div>
        </div>
        <!-- Komponen multiple value -->
        <template v-if="dialogMode !== 'create'">
          <div class="mt-6 mb-2 border-b pb-2 font-bold text-lg text-slate-700 dark:text-slate-200">Kontak Darurat</div>
          <EmergencyContactTable :contacts="form.emergency_contacts" :userId="form.id" />
          <div class="mt-6 mb-2 border-b pb-2 font-bold text-lg text-slate-700 dark:text-slate-200">Riwayat Pekerjaan</div>
          <JobHistoryTable :jobs="form.job_histories" :userId="form.id" />
          <div class="mt-6 mb-2 border-b pb-2 font-bold text-lg text-slate-700 dark:text-slate-200">Target Kerja</div>
          <WorkGoalTable :goals="form.work_goals" :userId="form.id" />
          <div class="mt-6 mb-2 border-b pb-2 font-bold text-lg text-slate-700 dark:text-slate-200">Pelatihan</div>
          <TrainingTable :trainings="form.trainings" :userId="form.id" />
          <div class="mt-6 mb-2 border-b pb-2 font-bold text-lg text-slate-700 dark:text-slate-200">Sertifikasi</div>
          <CertificationTable :certifications="form.certifications" :userId="form.id" />
          <div class="mt-6 mb-2 border-b pb-2 font-bold text-lg text-slate-700 dark:text-slate-200">Rencana Karir</div>
          <CareerPlanTable :plans="form.career_plans" :userId="form.id" />
        </template>
        <div class="flex gap-2 justify-end mt-2">
          <Button label="Batal" @click="showDialog = false" />
          <Button label="Simpan" type="submit" :loading="loading" />
        </div>
      </form>
    </Dialog>
  </AppLayout>
</template>
