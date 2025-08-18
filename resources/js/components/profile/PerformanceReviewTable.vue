<template>
  <div>
    <div class="flex justify-end mb-4">
  <Button label="Tambah Penilaian" icon="pi pi-plus" @click="openForm()" class="" />
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm border-separate border-spacing-y-2 rounded-xl shadow-sm bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-900">
          <tr>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Hasil</th>
            <th class="px-4 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Tanggal</th>
            <th class="px-4 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="review in reviews" :key="review.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-lg border-b border-gray-200 dark:border-gray-700">
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ review.result }}</td>
            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ review.date }}</td>
            <td class="px-4 py-3 text-center">
              <div class="flex gap-2 justify-center">
                <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-sm text-blue-600 hover:bg-blue-100" @click="openForm(review)" aria-label="Edit" />
                <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-sm text-red-600 hover:bg-red-100" @click="deleteReview(review.id)" aria-label="Hapus" />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Dialog v-model:visible="showForm" modal header="Form Penilaian Kinerja" :style="{ width: '400px' }">
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div v-if="errorMessage" class="bg-red-100 text-red-700 px-3 py-2 rounded mb-2 text-sm">
          {{ errorMessage }}
        </div>
        <div>
          <label class="block font-semibold mb-1">Hasil Penilaian</label>
          <InputText v-model="form.result" required class="w-full" />
        </div>
        <div>
          <label class="block font-semibold mb-1">Tanggal</label>
          <InputText v-model="form.date" type="date" class="w-full" />
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" class="bg-primary text-white dark:bg-blue-600 dark:text-white rounded-lg shadow hover:bg-primary/90" />
          <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" class="bg-gray-200 text-gray-700 rounded-lg shadow hover:bg-gray-300" />
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
const props = defineProps({ reviews: Array, onSaved: Function });
const showForm = ref(false);
const form = ref({ id: null, result: '', date: '' });
const errorMessage = ref('');
function openForm(review = null) {
  if (review) Object.assign(form.value, review);
  else resetForm();
  showForm.value = true;
  errorMessage.value = '';
}
function resetForm() {
  form.value = { id: null, result: '', date: '' };
  showForm.value = false;
  errorMessage.value = '';
}
function onSubmit() {
  errorMessage.value = '';
  if (form.value.id) {
    axios.patch(`/performance-reviews/${form.value.id}`, form.value)
      .then(() => { props.onSaved(); resetForm(); })
      .catch(err => {
        errorMessage.value = err?.response?.data?.message || 'Gagal menyimpan data.';
      });
  } else {
    axios.post('/performance-reviews', form.value)
      .then(() => { props.onSaved(); resetForm(); })
      .catch(err => {
        errorMessage.value = err?.response?.data?.message || 'Gagal menyimpan data.';
      });
  }
}
function actionTemplate(row) {
  return [
    h(Button, { label: 'Edit', size: 'small', onClick: () => openForm(row) }),
    h(Button, { label: 'Hapus', size: 'small', severity: 'danger', onClick: () => deleteReview(row.id) })
  ];
}
function deleteReview(id) {
  axios.delete(`/performance-reviews/${id}`).then(() => props.onSaved());
}
</script>
