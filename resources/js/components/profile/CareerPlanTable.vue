<template>
  <div class="space-y-6">
    <div class="flex justify-end">
      <Button label="Tambah Rencana Karir" icon="pi pi-plus" @click="openForm()" class="" />
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-base border-separate border-spacing-y-2 rounded-xl shadow bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
        <thead class="bg-gray-100 dark:bg-gray-900">
          <tr>
            <th class="px-5 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Rencana</th>
            <th class="px-5 py-3 text-left font-semibold text-gray-900 dark:text-gray-100">Target</th>
            <th class="px-5 py-3 text-center font-semibold text-gray-900 dark:text-gray-100">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="plan in plans" :key="plan.id" class="hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-xl border-b border-gray-200 dark:border-gray-700">
            <td class="px-5 py-3 text-gray-900 dark:text-gray-100">{{ plan.plan }}</td>
            <td class="px-5 py-3 text-gray-900 dark:text-gray-100">{{ plan.target_date }}</td>
            <td class="px-5 py-3 text-center">
              <div class="flex gap-2 justify-center">
                <Button icon="pi pi-pencil" class="p-button-rounded p-button-text p-button-sm text-blue-600 hover:bg-blue-100 dark:text-blue-300 dark:hover:bg-blue-900 transition" @click="openForm(plan)" aria-label="Edit" />
                <Button icon="pi pi-trash" class="p-button-rounded p-button-text p-button-sm text-red-600 hover:bg-red-100 dark:text-red-300 dark:hover:bg-red-900 transition" @click="confirmDelete(plan.id)" aria-label="Hapus" />
  <ConfirmDialog />
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <Dialog v-model:visible="showForm" modal header="Form Rencana Karir" :style="{ width: '400px' }" class="rounded-xl">
      <form @submit.prevent="onSubmit" class="space-y-4">
        <div v-if="responseMessage" :class="[responseType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'rounded-lg px-4 py-2 mb-2 font-semibold']">
          {{ responseMessage }}
        </div>
        <div>
          <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-200">Rencana Pengembangan Karir</label>
          <InputText v-model="form.plan" required class="w-full rounded-lg border border-slate-300 dark:border-slate-700" />
        </div>
        <div>
          <label class="block font-semibold mb-1 text-slate-700 dark:text-slate-200">Tanggal Target</label>
          <InputText v-model="form.target_date" type="date" class="w-full rounded-lg border border-slate-300 dark:border-slate-700" />
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" class="" />
          <Button v-if="form.id" label="Batal Edit" severity="secondary" @click="resetForm" class="bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200 rounded-xl shadow px-6 py-2 font-semibold text-base hover:bg-slate-300 dark:hover:bg-slate-800 transition" />
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
const props = defineProps({ plans: Array, onSaved: Function, userId: [String, Number] });
const showForm = ref(false);
const form = ref({ id: null, plan: '', target_date: '' });
const responseMessage = ref('');
const responseType = ref('');
const confirm = useConfirm();
function confirmDelete(id) {
  confirm.require({
    message: 'Apakah Anda yakin ingin menghapus rencana karir ini?',
    header: 'Konfirmasi Hapus',
    icon: 'pi pi-exclamation-triangle',
    acceptLabel: 'Ya, Hapus',
    rejectLabel: 'Batal',
    accept: () => {
      deletePlan(id);
    },
    reject: () => {}
  });
}
function openForm(plan = null) {
  responseMessage.value = '';
  responseType.value = '';
  if (plan) {
    Object.assign(form.value, plan);
    if (props.userId) form.value.user_id = props.userId;
  } else {
    resetForm();
    if (props.userId) form.value.user_id = props.userId;
  }
  showForm.value = true;
}
function resetForm() {
  form.value = { id: null, plan: '', target_date: '' };
  showForm.value = false;
  responseMessage.value = '';
  responseType.value = '';
}
function onSubmit() {
  if (props.userId) form.value.user_id = props.userId;
  if (form.value.id) {
    axios.patch(`/career-plans/${form.value.id}`, form.value)
      .then((res) => {
        responseMessage.value = (res?.data?.message || (form.value.id ? 'Rencana karir berhasil diupdate' : 'Rencana karir berhasil ditambahkan')) + ' (Form akan tertutup dalam 3 detik)';
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
    axios.post('/career-plans', form.value)
      .then((res) => {
        responseMessage.value = (res?.data?.message || 'Rencana karir berhasil ditambahkan') + ' (Form akan tertutup dalam 3 detik)';
        responseType.value = 'success';
        if (typeof props.onSaved === 'function') props.onSaved();
        setTimeout(() => {
          resetForm();
        }, 3000);
      })
      .catch((err) => {
        console.log(err)
        responseMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
        responseType.value = 'error';
      });
  }
}
function deletePlan(id) {
  axios.delete(`/career-plans/${id}`)
    .then((res) => {
      responseMessage.value = res?.data?.message || 'Rencana karir berhasil dihapus';
      responseType.value = 'success';
      if (typeof props.onSaved === 'function') props.onSaved();
    })
    .catch((err) => {
      responseMessage.value = err?.response?.data?.message || 'Terjadi kesalahan server';
      responseType.value = 'error';
    });
}
</script>
