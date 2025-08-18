<template>
  <div>
    <form @submit.prevent="onSubmit" class="space-y-2">
      <div>
        <label>Nama</label>
        <InputText v-model="form.name" required />
      </div>
      <div>
        <label>Hubungan</label>
        <InputText v-model="form.relationship" required />
      </div>
      <div>
        <label>Nomor Telepon</label>
        <InputText v-model="form.phone" required />
      </div>
      <Button type="submit" :label="form.id ? 'Update' : 'Tambah'" />
      <Button v-if="form.id" label="Batal" severity="secondary" @click="resetForm" />
    </form>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
import axios from 'axios';
const props = defineProps({
  contact: Object,
  onSaved: Function,
});
const form = ref({
  id: null,
  name: '',
  relationship: '',
  phone: '',
});
watch(() => props.contact, (val) => {
  if (val) Object.assign(form.value, val);
  else resetForm();
});
function resetForm() {
  form.value = { id: null, name: '', relationship: '', phone: '' };
}
function onSubmit() {
  if (form.value.id) {
    // Update
    axios.patch(`/emergency-contacts/${form.value.id}`, form.value)
      .then(() => { props.onSaved(); resetForm(); });
  } else {
    // Create
    axios.post('/emergency-contacts', form.value)
      .then(() => { props.onSaved(); resetForm(); });
  }
}
</script>
