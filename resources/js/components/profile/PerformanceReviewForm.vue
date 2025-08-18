<template>
  <form @submit.prevent="onSubmit" class="space-y-2">
    <div>
      <label>Hasil Penilaian</label>
      <InputText v-model="form.result" required />
    </div>
    <div>
      <label>Tanggal</label>
      <InputText v-model="form.date" type="date" />
    </div>
    <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" />
    <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" />
  </form>
</template>
<script setup>
import { ref, watch } from 'vue';
const props = defineProps({ review: Object, onSaved: Function });
const form = ref({ id: null, result: '', date: '' });
watch(() => props.review, (val) => { if (val) Object.assign(form.value, val); else resetForm(); });
function resetForm() { form.value = { id: null, result: '', date: '' }; }
function onSubmit() {
  if (form.value.id) {
    window.axios.patch(`/performance-reviews/${form.value.id}`, form.value).then(() => { props.onSaved(); resetForm(); });
  } else {
    window.axios.post('/performance-reviews', form.value).then(() => { props.onSaved(); resetForm(); });
  }
}
</script>
