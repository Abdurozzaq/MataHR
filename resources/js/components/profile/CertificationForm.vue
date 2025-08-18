<template>
  <form @submit.prevent="onSubmit" class="space-y-2">
    <div>
      <label>Judul Sertifikasi</label>
      <InputText v-model="form.title" required />
    </div>
    <div>
      <label>Tanggal</label>
      <InputText v-model="form.date" type="date" />
    </div>
    <div>
      <label>Penerbit</label>
      <InputText v-model="form.issuer" />
    </div>
    <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" />
    <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" />
  </form>
</template>
<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
const props = defineProps({ certification: Object, onSaved: Function });
const form = ref({ id: null, title: '', date: '', issuer: '' });
watch(() => props.certification, (val) => { if (val) Object.assign(form.value, val); else resetForm(); });
function resetForm() { form.value = { id: null, title: '', date: '', issuer: '' }; }
function onSubmit() {
  if (form.value.id) {
    axios.patch(`/certifications/${form.value.id}`, form.value).then(() => { props.onSaved(); resetForm(); });
  } else {
    axios.post('/certifications', form.value).then(() => { props.onSaved(); resetForm(); });
  }
}
</script>
