<template>
  <form @submit.prevent="onSubmit" class="space-y-2">
    <div>
      <label>Rencana Pengembangan Karir</label>
      <InputText v-model="form.plan" required />
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
const props = defineProps({ plan: Object, onSaved: Function });
const form = ref({ id: null, plan: '', target_date: '' });
watch(() => props.plan, (val) => { if (val) Object.assign(form.value, val); else resetForm(); });
function resetForm() { form.value = { id: null, plan: '', target_date: '' }; }
function onSubmit() {
  if (form.value.id) {
    axios.patch(`/career-plans/${form.value.id}`, form.value).then(() => { props.onSaved(); resetForm(); });
  } else {
    axios.post('/career-plans', form.value).then(() => { props.onSaved(); resetForm(); });
  }
}
</script>
