<script setup>
import { ref, watch, h } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3';
import { useDark } from '@/composables/useDark';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Paginator from 'primevue/paginator';
import Button from 'primevue/button';
import InputText from 'primevue/inputtext';
import Dialog from 'primevue/dialog';
import Dropdown from 'primevue/dropdown';
import { Head } from '@inertiajs/vue3';

const breadcrumbs = [
  { label: 'Dashboard' },
  { label: 'Approval Cuti Bawahan' }
];

const leaveData = ref([]);
const totalRecords = ref(0);
const loading = ref(false);
const page = ref(1);
const rows = ref(10);
const pageSizeOptions = [
  { label: '10', value: 10 },
  { label: '20', value: 20 },
  { label: '50', value: 50 },
  { label: '100', value: 100 },
];
const sortField = ref('created_at');
const sortOrder = ref(-1);
const searchField = ref('employee_name');
const searchValue = ref('');
const statusFilter = ref('');

import { usePage } from '@inertiajs/vue3';
const pageData = usePage();

const leaveTypes = [
  { label: 'Tahunan', value: 'annual' },
  { label: 'Sakit', value: 'sick' },
  { label: 'Melahirkan', value: 'maternity' },
  { label: 'Penting', value: 'important' },
];

const statusOptions = [
  { label: 'Pending', value: 'pending' },
  { label: 'Disetujui', value: 'approved' },
  { label: 'Ditolak', value: 'rejected' },
];

const fetchLeaves = () => {
  loading.value = true;
  router.get(
    '/leave/subordinate', // endpoint baru untuk cuti bawahan
    {
      page: page.value,
      rows: rows.value,
      sortField: sortField.value,
      sortOrder: sortOrder.value,
      searchField: searchField.value,
      searchValue: searchValue.value,
      status: statusFilter.value,
    },
    {
      preserveState: true,
      onSuccess: (page) => {
        leaveData.value = page.props.leaves.data;
        totalRecords.value = page.props.leaves.total;
        loading.value = false;
      },
      onError: () => {
        loading.value = false;
      },
    }
  );
};

watch([page, rows, sortField, sortOrder, searchValue, statusFilter], fetchLeaves);

const { isDark } = useDark();

fetchLeaves();


function leaveTypeLabel(row) {
  const type = leaveTypes.find(t => t.value === row.leave_type);
  return type ? type.label : row.leave_type;
}



// ACTION APPROVE/REJECT
const showConfirmDialog = ref(false);
const confirmAction = ref(''); // 'approve' or 'reject'
const selectedLeaveId = ref(null);
const approvalNote = ref('');

function handleApprove(id) {
  confirmAction.value = 'approve';
  selectedLeaveId.value = id;
  approvalNote.value = '';
  showConfirmDialog.value = true;
}
function handleReject(id) {
  confirmAction.value = 'reject';
  selectedLeaveId.value = id;
  approvalNote.value = '';
  showConfirmDialog.value = true;
}
function confirmActionLeave() {
  if (!selectedLeaveId.value) return;
  loading.value = true;
  const endpoint = confirmAction.value === 'approve'
    ? `/leave/approve/${selectedLeaveId.value}`
    : `/leave/reject/${selectedLeaveId.value}`;
  router.post(endpoint, { approval_note: approvalNote.value }, {
    preserveState: true,
    onSuccess: () => {
      fetchLeaves();
      loading.value = false;
      showConfirmDialog.value = false;
      selectedLeaveId.value = null;
      approvalNote.value = '';
    },
    onError: () => {
      loading.value = false;
      showConfirmDialog.value = false;
      selectedLeaveId.value = null;
      approvalNote.value = '';
    },
  });
}
function cancelActionLeave() {
  showConfirmDialog.value = false;
  selectedLeaveId.value = null;
  approvalNote.value = '';
}

</script>

<template>
  <AppLayout :breadcrumbs="breadcrumbs">
    <Head title="Approval Cuti Bawahan" />
    <div style="padding: 24px;">
      <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: center; margin-bottom: 24px;">
        <InputText v-model="searchValue" placeholder="Cari..." @input="page.value = 1" />
        <Dropdown v-model="searchField" :options="[
          { label: 'Nama', value: 'employee_name' },
          { label: 'Jenis Cuti', value: 'leave_type' },
        ]" optionLabel="label" optionValue="value" />
        <Dropdown v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" />
        <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" @change="page.value = 1" />
        <Button label="Cari" icon="pi pi-search" @click="fetchLeaves" />
      </div>
      <div style="margin-bottom: 24px;">
        <DataTable
          :value="leaveData"
          :loading="loading"
          :paginator="false"
          :rows="rows"
          :totalRecords="totalRecords"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          @sort="e => { sortField = e.sortField; sortOrder = e.sortOrder }"
        >
          <Column field="employee_name" header="Nama" sortable />
          <Column field="leave_type" header="Jenis Cuti" sortable :body="leaveTypeLabel" />
          <Column field="start_date" header="Mulai" sortable />
          <Column field="end_date" header="Selesai" sortable />
          <Column field="reason" header="Alasan" />
          <Column field="approval_note" header="Catatan Approval" />
          <Column header="Status">
            <template #body="{ data }">
              <span>
                  <template v-if="data.approved_at">
                    <span>Disetujui</span>
                  </template>
                  <template v-else-if="data.rejected_at">
                    <span>Ditolak</span>
                  </template>
                  <template v-else>
                    <span>Pending</span>
                  </template>
              </span>
            </template>
          </Column>
          <Column header="Aksi">
            <template #body="{ data }">
              <div v-if="!data.approved_at && !data.rejected_at" style="display: flex; gap: 8px;">
                <Button label="Approve" icon="pi pi-check" @click="handleApprove(data.id)" />
                <Button label="Reject" icon="pi pi-times" @click="handleReject(data.id)" />
              </div>
            </template>
          </Column>
        </DataTable>
      </div>
      <div style="margin-bottom: 24px;">
        <Paginator
          :rows="rows"
          :totalRecords="totalRecords"
          :first="(page - 1) * rows"
          @page="e => { page = e.page + 1; rows = e.rows }"
        />
      </div>
      <Dialog v-model:visible="showConfirmDialog" :modal="true" :closable="false">
        <template #header>
          <span v-if="confirmAction === 'approve'">Konfirmasi Approve</span>
          <span v-else>Konfirmasi Reject</span>
        </template>
        <div style="display: flex; flex-direction: column; gap: 16px; margin-top: 8px; margin-bottom: 8px;">
          <span v-if="confirmAction === 'approve'">Apakah Anda yakin ingin <b>menyetujui</b> cuti ini?</span>
          <span v-else>Apakah Anda yakin ingin <b>menolak</b> cuti ini?</span>
          <InputText v-model="approvalNote" placeholder="Catatan approval (opsional)" />
        </div>
        <template #footer>
          <div style="display: flex; gap: 8px;">
            <Button label="Ya" icon="pi pi-check" @click="confirmActionLeave" />
            <Button label="Batal" icon="pi pi-times" @click="cancelActionLeave" />
          </div>
        </template>
      </Dialog>
    </div>
  </AppLayout>
</template>
