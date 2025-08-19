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
    <div :class="['p-6 rounded-xl shadow', isDark ? 'bg-gradient-to-br from-neutral-900 via-slate-800 to-neutral-800 text-neutral-100' : 'bg-gradient-to-br from-blue-50 via-sky-100 to-blue-200 text-neutral-900']">
      <div class="flex flex-wrap gap-3 items-center mb-6">
        <InputText v-model="searchValue" placeholder="Cari..." :class="[isDark ? 'min-w-[180px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[180px] h-10 border-blue-300 focus:border-blue-500']" @input="page.value = 1" />
        <Dropdown v-model="searchField" :options="[
          { label: 'Nama', value: 'employee_name' },
          { label: 'Jenis Cuti', value: 'leave_type' },
        ]" optionLabel="label" optionValue="value" :class="[isDark ? 'min-w-[120px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[120px] h-10 border-blue-300 focus:border-blue-500']" />
        <Dropdown v-model="statusFilter" :options="statusOptions" optionLabel="label" optionValue="value" placeholder="Status" :class="[isDark ? 'min-w-[120px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[120px] h-10 border-blue-300 focus:border-blue-500']" />
        <Dropdown v-model="rows" :options="pageSizeOptions" optionLabel="label" optionValue="value" placeholder="Baris per halaman" :class="[isDark ? 'min-w-[120px] h-10 border-slate-700 bg-neutral-900 text-neutral-100 focus:border-blue-500' : 'min-w-[120px] h-10 border-blue-300 focus:border-blue-500']" @change="page.value = 1" />
        <Button label="Cari" icon="pi pi-search" @click="fetchLeaves" :class="[isDark ? 'bg-blue-700 border-blue-700 text-white hover:bg-blue-800 hover:border-blue-800 h-10 px-4 rounded-lg' : 'bg-blue-500 border-blue-500 text-white hover:bg-blue-600 hover:border-blue-600 h-10 px-4 rounded-lg']" />
      </div>
      <div :class="[isDark ? 'overflow-x-auto rounded-xl shadow border border-slate-700 bg-neutral-900' : 'overflow-x-auto rounded-xl shadow border border-blue-100 bg-white']">
        <DataTable
          :value="leaveData"
          :loading="loading"
          :paginator="false"
          :rows="rows"
          :totalRecords="totalRecords"
          :sortField="sortField"
          :sortOrder="sortOrder"
          dataKey="id"
          class="shadow-none rounded-xl border-0"
          style="border: none;"
          @sort="e => { sortField.value = e.sortField; sortOrder.value = e.sortOrder }"
        >
          <Column field="employee_name" header="Nama" sortable :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="leave_type" header="Jenis Cuti" sortable :body="leaveTypeLabel" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="start_date" header="Mulai" sortable :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="end_date" header="Selesai" sortable :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="reason" header="Alasan" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column field="approval_note" header="Catatan Approval" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'" :bodyStyle="isDark ? 'font-weight: 500; color: #f3f4f6;' : 'font-weight: 500;'" />
          <Column header="Status" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'">
            <template #body="{ data }">
              <span>
                  <template v-if="data.approved_at">
                    <span :style="isDark ? 'color: #22c55e; font-weight: bold; background: #14532d; padding: 2px 10px; border-radius: 8px;' : 'color: #22c55e; font-weight: bold; background: #dcfce7; padding: 2px 10px; border-radius: 8px;'">Disetujui</span>
                  </template>
                  <template v-else-if="data.rejected_at">
                    <span :style="isDark ? 'color: #ef4444; font-weight: bold; background: #7f1d1d; padding: 2px 10px; border-radius: 8px;' : 'color: #ef4444; font-weight: bold; background: #fee2e2; padding: 2px 10px; border-radius: 8px;'">Ditolak</span>
                  </template>
                  <template v-else>
                    <span :style="isDark ? 'color: #eab308; font-weight: bold; background: #78350f; padding: 2px 10px; border-radius: 8px;' : 'color: #eab308; font-weight: bold; background: #fef9c3; padding: 2px 10px; border-radius: 8px;'">Pending</span>
                  </template>
              </span>
            </template>
          </Column>
            <Column header="Aksi" :headerStyle="isDark ? 'background: #1e293b; color: #38bdf8; font-weight: 600;' : 'background: #e0f2fe; color: #2563eb; font-weight: 600;'">
              <template #body="{ data }">
                <div v-if="!data.approved_at && !data.rejected_at" class="flex gap-2">
                  <Button label="Approve" icon="pi pi-check" @click="handleApprove(data.id)" :class="isDark ? 'bg-green-700 border-green-700 text-white hover:bg-green-800 hover:border-green-800 h-8 px-3 rounded-lg' : 'bg-green-500 border-green-500 text-white hover:bg-green-600 hover:border-green-600 h-8 px-3 rounded-lg'" />
                  <Button label="Reject" icon="pi pi-times" @click="handleReject(data.id)" :class="isDark ? 'bg-red-700 border-red-700 text-white hover:bg-red-800 hover:border-red-800 h-8 px-3 rounded-lg' : 'bg-red-500 border-red-500 text-white hover:bg-red-600 hover:border-red-600 h-8 px-3 rounded-lg'" />
                </div>
              </template>
            </Column>
        </DataTable>
      </div>
      <Paginator
        :rows="rows"
        :totalRecords="totalRecords"
        :first="(page - 1) * rows"
        @page="e => { page.value = e.page + 1; rows.value = e.rows }"
        :class="isDark ? 'mt-6 text-neutral-100' : 'mt-6'"
      />
        <Dialog v-model:visible="showConfirmDialog" :modal="true" :closable="false" :style="{ width: '400px' }">
          <template #header>
            <span v-if="confirmAction === 'approve'">Konfirmasi Approve</span>
            <span v-else>Konfirmasi Reject</span>
          </template>
          <div class="py-4 flex flex-col gap-3">
            <span v-if="confirmAction === 'approve'">Apakah Anda yakin ingin <b>menyetujui</b> cuti ini?</span>
            <span v-else>Apakah Anda yakin ingin <b>menolak</b> cuti ini?</span>
            <InputText v-model="approvalNote" placeholder="Catatan approval (opsional)" :class="isDark ? 'border-slate-700 bg-neutral-900 text-neutral-100' : 'border-blue-300'" />
          </div>
          <template #footer>
            <Button label="Ya" icon="pi pi-check" @click="confirmActionLeave" :class="confirmAction === 'approve' ? (isDark ? 'bg-green-700 border-green-700 text-white hover:bg-green-800 hover:border-green-800' : 'bg-green-500 border-green-500 text-white hover:bg-green-600 hover:border-green-600') : (isDark ? 'bg-red-700 border-red-700 text-white hover:bg-red-800 hover:border-red-800' : 'bg-red-500 border-red-500 text-white hover:bg-red-600 hover:border-red-600')" />
            <Button label="Batal" icon="pi pi-times" @click="cancelActionLeave" :class="isDark ? 'bg-slate-700 border-slate-700 text-white hover:bg-slate-800 hover:border-slate-800' : 'bg-blue-200 border-blue-200 text-blue-900 hover:bg-blue-300 hover:border-blue-300'" />
          </template>
        </Dialog>
    </div>
  </AppLayout>
</template>
