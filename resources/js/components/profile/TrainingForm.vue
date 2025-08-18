<template>
  <form @submit.prevent="onSubmit" class="space-y-2">
    <div>
      <label>Judul Pelatihan</label>
      <InputText v-model="form.title" required />
    </div>
    <div>
      <label>Tanggal</label>
      <InputText v-model="form.date" type="date" />
    </div>
    <div>
      <label>Penyelenggara</label>
      <InputText v-model="form.provider" />
    </div>
    <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" />
    <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" />
  </form>
</template>
<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
const props = defineProps({ training: Object, onSaved: Function });
const form = ref({ id: null, title: '', date: '', provider: '' });
watch(() => props.training, (val) => { if (val) Object.assign(form.value, val); else resetForm(); });
function resetForm() { form.value = { id: null, title: '', date: '', provider: '' }; }
function onSubmit() {
  if (form.value.id) {
    axios.patch(`/trainings/${form.value.id}`, form.value).then(() => { props.onSaved(); resetForm(); });
  } else {
    axios.post('/trainings', form.value).then(() => { props.onSaved(); resetForm(); });
  }
}
</script>
