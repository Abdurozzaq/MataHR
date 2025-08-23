<template>
  <form @submit.prevent="onSubmit" class="space-y-2">
    <div>
      <label>Jabatan</label>
      <InputText v-model="form.position" required />
    </div>
    <div>
      <label>Departemen</label>
      <InputText v-model="form.department" />
    </div>
    <div>
      <label>Tanggal Mulai</label>
      <InputText v-model="form.start_date" type="date" required />
    </div>
    <div>
      <label>Tanggal Selesai</label>
      <InputText v-model="form.end_date" type="date" />
    </div>
    <div>
      <label>Jenis (Promosi/Mutasi)</label>
      <InputText v-model="form.type" />
    </div>
    <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" />
    <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" />
  </form>
</template>
<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
const props = defineProps({ job: Object, onSaved: Function });
const form = ref({ id: null, position: '', department: '', start_date: '', end_date: '', type: '' });
watch(() => props.job, (val) => { if (val) Object.assign(form.value, val); else resetForm(); });
function resetForm() { form.value = { id: null, position: '', department: '', start_date: '', end_date: '', type: '' }; }
function onSubmit() {
  if (form.value.id) {
    axios.patch(`/job-histories/${form.value.id}`, form.value).then(() => { if (typeof props.onSaved === 'function') props.onSaved(); resetForm(); });
  } else {
    axios.post('/job-histories', form.value).then(() => { if (typeof props.onSaved === 'function') props.onSaved(); resetForm(); });
  }
}
</script>
