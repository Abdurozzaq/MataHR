
<script setup>
import { ref, onMounted } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import Card from 'primevue/card';
import Divider from 'primevue/divider';
import { Head } from '@inertiajs/vue3';
import { Icon } from '@iconify/vue';
import Message from 'primevue/message';

import { router } from '@inertiajs/vue3';

const breadcrumbs = [
  { label: 'Dashboard' },
  { label: 'Global Setting' }
];

const form = ref({
  office_lat: '',
  office_lon: '',
  office_name: '',
  office_radius: '',
});

const loading = ref(false);
const successMessage = ref('');
const errors = ref({});

function validateForm() {
  const newErrors = {};
  if (!form.value.office_name) {
    newErrors.office_name = 'Nama kantor wajib diisi';
  }
  if (!form.value.office_lat) {
    newErrors.office_lat = 'Latitude wajib diisi';
  } else if (isNaN(Number(form.value.office_lat))) {
    newErrors.office_lat = 'Latitude harus berupa angka';
  }
  if (!form.value.office_lon) {
    newErrors.office_lon = 'Longitude wajib diisi';
  } else if (isNaN(Number(form.value.office_lon))) {
    newErrors.office_lon = 'Longitude harus berupa angka';
  }
  if (!form.value.office_radius) {
    newErrors.office_radius = 'Radius wajib diisi';
  } else if (isNaN(Number(form.value.office_radius))) {
    newErrors.office_radius = 'Radius harus berupa angka';
  }
  errors.value = newErrors;
  return Object.keys(newErrors).length === 0;
}

function saveSettings() {
  if (!validateForm()) return;
  loading.value = true;
  router.put('/settings', form.value, {
    onSuccess: () => {
      successMessage.value = 'Pengaturan berhasil disimpan';
      setTimeout(() => successMessage.value = '', 2000);
    },
    onFinish: () => {
      loading.value = false;
    },
    onError: () => {
      loading.value = false;
    }
  });
}

onMounted(async () => {
  loading.value = true;
  try {
    const response = await fetch('/settings');
    if (response.ok) {
      const data = await response.json();
      form.value.office_lat = data.office_lat || '';
      form.value.office_lon = data.office_lon || '';
      form.value.office_name = data.office_name || '';
      form.value.office_radius = data.office_radius || '';
    }
  } catch (e) {
    // handle error if needed
  } finally {
    loading.value = false;
  }
});
</script>


<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Global Setting" />
    <div style="max-width: 420px; margin: 0 auto;">
      <Card>
        <template #title>
          <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 8px;">
            <Icon icon="mdi:cog" style="font-size: 2.5rem; color: var(--primary-color);" />
            <span style="font-size: 1.25rem; font-weight: 700; letter-spacing: 0.5px;">Edit Global Setting</span>
          </div>
        </template>
        <template #content>
          <form @submit.prevent="saveSettings">
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
              <div>
                <label for="office_name" style="font-weight: 500; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                  <Icon icon="mdi:office-building" style="font-size: 1.2rem; color: var(--text-color-secondary);" />
                  Nama Kantor
                </label>
                <InputText id="office_name" v-model="form.office_name" placeholder="Nama Kantor" class="p-inputtext" style="width: 100%; margin-top: 4px;" />
                <div v-if="errors.office_name" style="color: #e53935; font-size: 0.9rem; margin-top: 2px;">{{ errors.office_name }}</div>
              </div>
              <div style="display: flex; gap: 1rem;">
                <div style="flex: 1;">
                  <label for="office_lat" style="font-weight: 500; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <Icon icon="mdi:map-marker" style="font-size: 1.2rem; color: var(--text-color-secondary);" />
                    Latitude
                  </label>
                  <InputText id="office_lat" v-model="form.office_lat" placeholder="Latitude" class="p-inputtext" style="width: 100%; margin-top: 4px;" />
                  <div v-if="errors.office_lat" style="color: #e53935; font-size: 0.9rem; margin-top: 2px;">{{ errors.office_lat }}</div>
                </div>
                <div style="flex: 1;">
                  <label for="office_lon" style="font-weight: 500; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                    <Icon icon="mdi:map-marker" style="font-size: 1.2rem; color: var(--text-color-secondary);" />
                    Longitude
                  </label>
                  <InputText id="office_lon" v-model="form.office_lon" placeholder="Longitude" class="p-inputtext" style="width: 100%; margin-top: 4px;" />
                  <div v-if="errors.office_lon" style="color: #e53935; font-size: 0.9rem; margin-top: 2px;">{{ errors.office_lon }}</div>
                </div>
              </div>
              <div>
                <label for="office_radius" style="font-weight: 500; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 8px;">
                  <Icon icon="mdi:ruler" style="font-size: 1.2rem; color: var(--text-color-secondary);" />
                  Radius Kantor (meter)
                </label>
                <InputText id="office_radius" v-model="form.office_radius" placeholder="Radius" class="p-inputtext" style="width: 100%; margin-top: 4px;" />
                <div v-if="errors.office_radius" style="color: #e53935; font-size: 0.9rem; margin-top: 2px;">{{ errors.office_radius }}</div>
              </div>
              <div style="display: flex; justify-content: flex-end; margin-top: 8px;">
                <Button label="Simpan" type="submit" :loading="loading" icon="pi pi-save" style="min-width: 120px; font-weight: 600; font-size: 1rem;" />
              </div>
              <div v-if="successMessage" style="margin-top: 1rem;">
                <Message severity="success" :closable="false" style="font-size: 0.95rem;">
                  <span style="display: flex; align-items: center;">
                    <Icon icon="mdi:check-circle" style="font-size: 1.2rem; color: var(--primary-color); margin-right: 4px;" />
                    {{ successMessage }}
                  </span>
                </Message>
              </div>
            </div>
          </form>
        </template>
      </Card>
    </div>
  </AppLayout>
</template>

