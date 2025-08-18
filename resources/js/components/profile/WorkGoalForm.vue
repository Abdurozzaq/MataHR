<template>
  <form @submit.prevent="onSubmit" class="space-y-2">
    <div>
      <label>Tujuan Kerja</label>
      <InputText v-model="form.goal" required />
    </div>
    <div>
      <label>Tanggal Target</label>
      <InputText v-model="form.target_date" type="date" />
    </div>
    <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" />
    <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" />
  </form>
</template>
<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
const props = defineProps({ goal: Object, onSaved: Function });
const form = ref({ id: null, goal: '', target_date: '' });
watch(() => props.goal, (val) => { if (val) Object.assign(form.value, val); else resetForm(); });
function resetForm() { form.value = { id: null, goal: '', target_date: '' }; }
function onSubmit() {
  if (form.value.id) {
    axios.patch(`/work-goals/${form.value.id}`, form.value).then(() => { props.onSaved(); resetForm(); });
  } else {
    axios.post('/work-goals', form.value).then(() => { props.onSaved(); resetForm(); });
  }
}
</script>
