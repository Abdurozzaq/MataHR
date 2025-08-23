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
                <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-sm text-red-600 hover:bg-red-100" @click="confirmDelete(training.id)" aria-label="Hapus" />
  <ConfirmDialog />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Dialog v-model:visible="showForm" modal header="Form Pelatihan" :style="{ width: '400px' }">
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div v-if="responseMessage" :class="[responseType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'rounded-lg px-4 py-2 mb-2 font-semibold']">
          {{ responseMessage }}
        </div>
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
import ConfirmDialog from 'primevue/confirmdialog';
import { useConfirm } from 'primevue/useconfirm';
import axios from 'axios';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
const props = defineProps({ trainings: Array, onSaved: Function, userId: [String, Number] });
const showForm = ref(false);
const form = ref({ id: null, title: '', date: '', provider: '' });
const responseMessage = ref('');
const responseType = ref('');
const confirm = useConfirm();
function confirmDelete(id) {
  confirm.require({
    message: 'Apakah Anda yakin ingin menghapus pelatihan ini?',
    header: 'Konfirmasi Hapus',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Ya, Hapus',
    rejectLabel: 'Batal',
    accept: () => {
      deleteTraining(id);
    },
    reject: () => {}
  });
}
function openForm(training = null) {
  responseMessage.value = '';
  responseType.value = '';
  if (training) {
    Object.assign(form.value, training);
    if (props.userId) form.value.user_id = props.userId;
  } else {
    resetForm();
    if (props.userId) form.value.user_id = props.userId;
  }
  showForm.value = true;
}
function resetForm() {
  form.value = { id: null, title: '', date: '', provider: '' };
  showForm.value = false;
  responseMessage.value = '';
  responseType.value = '';
}
function onSubmit() {
  if (props.userId) form.value.user_id = props.userId;
  if (form.value.id) {
    axios.patch(`/trainings/${form.value.id}`, form.value)
      .then((res) => {
        responseMessage.value = (res?.data?.message || (form.value.id ? 'Pelatihan berhasil diupdate' : 'Pelatihan berhasil ditambahkan')) + ' (Form akan tertutup dalam 3 detik)';
        responseType.value = 'success';
        if (typeof props.onSaved === 'function') props.onSaved();
        setTimeout(() => {
          resetForm();
        }, 3000);
      })
      .catch((err) => {
        responseMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
        responseType.value = 'error';
      });
  } else {
    axios.post('/trainings', form.value)
      .then((res) => {
        responseMessage.value = (res?.data?.message || (form.value.id ? 'Pelatihan berhasil diupdate' : 'Pelatihan berhasil ditambahkan')) + ' (Form akan tertutup dalam 3 detik)';
        responseType.value = 'success';
        if (typeof props.onSaved === 'function') props.onSaved();
        setTimeout(() => {
          resetForm();
        }, 3000);
      })
      .catch((err) => {
        responseMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
        responseType.value = 'error';
      });
  }
}
function deleteTraining(id) {
  axios.delete(`/trainings/${id}`)
    .then((res) => {
      responseMessage.value = res?.data?.message || 'Pelatihan berhasil dihapus';
      responseType.value = 'success';
      if (typeof props.onSaved === 'function') props.onSaved();
    })
    .catch((err) => {
      responseMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
      responseType.value = 'error';
    });
}
</script>
