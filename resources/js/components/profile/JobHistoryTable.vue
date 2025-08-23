<template>
  <div>
    <div class="flex justify-end mb-4">
  <Button label="Tambah Riwayat Jabatan" icon="pi pi-plus" @click="openForm" class="" />
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-separate border-spacing-y-2 rounded-xl shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Jabatan</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Departemen</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Mulai</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Selesai</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Jenis</th>
            <th class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="job in jobs" :key="job.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-lg border-b border-gray-200 dark:border-gray-700">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ job.position }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ job.department }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ job.start_date }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ job.end_date }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ job.type }}</td>
            <td class="px-4 py-3 text-center">
              <div class="flex gap-2 justify-center">
                <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-sm text-blue-600 hover:bg-blue-100" @click="openForm(job)" aria-label="Edit" />
                <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-sm text-red-600 hover:bg-red-100" @click="confirmDelete(job.id)" aria-label="Hapus" />
  <ConfirmDialog />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Dialog v-model:visible="showForm" modal header="Form Riwayat Jabatan" :style="{ width: '400px' }">
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm">
          {{ errorMessage }}
        </div>
        <div>
          <label class="block font-semibold mb-1">Jabatan</label>
          <InputText v-model="form.position" required class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Departemen</label>
          <InputText v-model="form.department" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Tanggal Mulai</label>
          <InputText v-model="form.start_date" type="date" required class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Tanggal Selesai</label>
          <InputText v-model="form.end_date" type="date" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Jenis (Promosi/Mutasi)</label>
          <InputText v-model="form.type" class="w-full" />
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" icon="pi pi-save" class="" />
          <Button v-if="form.id" label="Batal Edit" severity="secondary" @click="resetForm" icon="pi pi-times" class="" />
        </div>
      </form>
    </Dialog>
  </div>
</template>
<script setup>
import { ref, h } from 'vue';
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
const props = defineProps({ jobs: Array, onSaved: Function, userId: [String, Number] });
const showForm = ref(false);
const form = ref({ id: null, position: '', department: '', start_date: '', end_date: '', type: '' });
const errorMessage = ref('');
const confirm = useConfirm();
function confirmDelete(id) {
  confirm.require({
    message: 'Apakah Anda yakin ingin menghapus riwayat jabatan ini?',
    header: 'Konfirmasi Hapus',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Ya, Hapus',
    rejectLabel: 'Batal',
    accept: () => {
      deleteJob(id);
    },
    reject: () => {}
  });
}
function openForm(job = null) {
  if (job) {
    Object.assign(form.value, job);
    if (props.userId) form.value.user_id = props.userId;
  } else {
    resetForm();
    if (props.userId) form.value.user_id = props.userId;
  }
  showForm.value = true;
  errorMessage.value = '';
}
function resetForm() {
  form.value = { id: null, position: '', department: '', start_date: '', end_date: '', type: '' };
  showForm.value = false;
  errorMessage.value = '';
}
function onSubmit() {
  if (props.userId) form.value.user_id = props.userId;
  errorMessage.value = '';
  if (form.value.id) {
    axios.patch(`/job-histories/${form.value.id}`, form.value)
      .then(() => { if (typeof props.onSaved === 'function') props.onSaved(); resetForm(); })
      .catch((err) => {
        errorMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
      });
  } else {
    axios.post('/job-histories', form.value)
      .then(() => { if (typeof props.onSaved === 'function') props.onSaved(); resetForm(); })
      .catch((err) => {
        errorMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
      });
  }
}
function actionTemplate(row) {
  return h('div', { class: 'flex gap-2 justify-center' }, [
    h(Button, {
      icon: 'pi pi-pencil',
      class: 'p-button-rounded p-button-text p-button-sm text-blue-600 hover:bg-blue-100',
      onClick: () => openForm(row),
      'aria-label': 'Edit',
      tooltip: 'Edit',
    }),
    h(Button, {
      icon: 'pi pi-trash',
      class: 'p-button-rounded p-button-text p-button-sm text-red-600 hover:bg-red-100',
      onClick: () => deleteJob(row.id),
      'aria-label': 'Hapus',
      tooltip: 'Hapus',
    })
  ]);
}
function deleteJob(id) {
  axios.delete(`/job-histories/${id}`).then(() => props.onSaved());
}
</script>
