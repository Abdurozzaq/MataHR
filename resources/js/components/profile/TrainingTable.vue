<template>
  <div>
    <div class="flex justify-end mb-4">
  <Button label="Tambah Pelatihan" icon="pi pi-plus" @click="openForm()" class="" />
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-separate border-spacing-y-2 rounded-xl shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Judul</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Tanggal</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Penyelenggara</th>
            <th class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="training in trainings" :key="training.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-lg border-b border-gray-200 dark:border-gray-700">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ training.title }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ training.date }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ training.provider }}</td>
            <td class="px-4 py-3 text-center">
              <div class="flex gap-2 justify-center">
                <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-sm text-blue-600 hover:bg-blue-100" @click="openForm(training)" aria-label="Edit" />
                <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-sm text-red-600 hover:bg-red-100" @click="deleteTraining(training.id)" aria-label="Hapus" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Dialog v-model:visible="showForm" modal header="Form Pelatihan" :style="{ width: '400px' }">
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div>
          <label class="block font-semibold mb-1">Judul Pelatihan</label>
          <InputText v-model="form.title" required class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Tanggal</label>
          <InputText v-model="form.date" type="date" class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Penyelenggara</label>
          <InputText v-model="form.provider" class="w-full" />
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" class="" />
          <Button v-if="form.id" label="Batal Edit" severity="secondary" @click="resetForm" class="" />
        </div>
      </form>
    </Dialog>
  </div>
</template>
<script setup>
import { ref, h } from 'vue';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
const props = defineProps({ trainings: Array, onSaved: Function });
const showForm = ref(false);
const form = ref({ id: null, title: '', date: '', provider: '' });
function openForm(training = null) {
  if (training) Object.assign(form.value, training);
  else resetForm();
  showForm.value = true;
}
function resetForm() {
  form.value = { id: null, title: '', date: '', provider: '' };
  showForm.value = false;
}
function onSubmit() {
  if (form.value.id) {
    axios.patch(`/trainings/${form.value.id}`, form.value)
      .then(() => { props.onSaved(); resetForm(); })
      .catch((err) => {
        window.$toast?.add({
          severity: 'error',
          summary: 'Gagal Update',
          detail: err?.response?.data?.message || 'Terjadi kesalahan server',
          life: 4000,
        });
      });
  } else {
    axios.post('/trainings', form.value)
      .then(() => { props.onSaved(); resetForm(); })
      .catch((err) => {
        window.$toast?.add({
          severity: 'error',
          summary: 'Gagal Menambah',
          detail: err?.response?.data?.message || 'Terjadi kesalahan server',
          life: 4000,
        });
      });
  }
}
function actionTemplate(row) {
  return [
    h(Button, { label: 'Edit', size: 'small', onClick: () => openForm(row) }),
    h(Button, { label: 'Hapus', size: 'small', severity: 'danger', onClick: () => deleteTraining(row.id) })
  ];
}
function deleteTraining(id) {
  axios.delete(`/trainings/${id}`).then(() => props.onSaved());
}
</script>
