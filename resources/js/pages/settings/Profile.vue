<script setup>
import { ref, computed, onMounted } from 'vue';
import Dropdown from 'primevue/dropdown';
import axios from 'axios';
import { useForm, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/UserSettingsLayout.vue';
import {
    EmergencyContactTable,
    JobHistoryTable,
    TrainingTable,
    CertificationTable,
    WorkGoalTable,
    CareerPlanTable
} from '@/components/profile';

import Divider from 'primevue/divider';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

// State dan handler untuk data multiple
const jobs = ref([]);
const showJobForm = ref(false);
const editingJob = ref(null);
function refreshJobs() {
    axios.get('/job-histories').then(res => {
        jobs.value = res.data;
        showJobForm.value = false;
        editingJob.value = null;
    });
}
function addJob() { editingJob.value = null; showJobForm.value = true; }
function editJob(job) { editingJob.value = { ...job }; showJobForm.value = true; }
function deleteJob(id) { axios.delete(`/job-histories/${id}`).then(() => refreshJobs()); }

const trainings = ref([]);
const showTrainingForm = ref(false);
const editingTraining = ref(null);
function refreshTrainings() {
    axios.get('/trainings').then(res => {
        trainings.value = res.data;
        showTrainingForm.value = false;
        editingTraining.value = null;
    });
}
function addTraining() { editingTraining.value = null; showTrainingForm.value = true; }
function editTraining(training) { editingTraining.value = { ...training }; showTrainingForm.value = true; }
function deleteTraining(id) { axios.delete(`/trainings/${id}`).then(() => refreshTrainings()); }

const certifications = ref([]);
const showCertificationForm = ref(false);
const editingCertification = ref(null);
function refreshCertifications() {
    axios.get('/certifications').then(res => {
        certifications.value = res.data;
        showCertificationForm.value = false;
        editingCertification.value = null;
    });
}
function addCertification() { editingCertification.value = null; showCertificationForm.value = true; }
function editCertification(certification) { editingCertification.value = { ...certification }; showCertificationForm.value = true; }
function deleteCertification(id) { axios.delete(`/certifications/${id}`).then(() => refreshCertifications()); }

const goals = ref([]);
const showGoalForm = ref(false);
const editingGoal = ref(null);
function refreshGoals() {
    axios.get('/work-goals').then(res => {
        goals.value = res.data;
        showGoalForm.value = false;
        editingGoal.value = null;
    });
}
function addGoal() { editingGoal.value = null; showGoalForm.value = true; }
function editGoal(goal) { editingGoal.value = { ...goal }; showGoalForm.value = true; }
function deleteGoal(id) { axios.delete(`/work-goals/${id}`).then(() => refreshGoals()); }

const plans = ref([]);
const showPlanForm = ref(false);
const editingPlan = ref(null);
function refreshPlans() {
    axios.get('/career-plans').then(res => {
        plans.value = res.data;
        showPlanForm.value = false;
        editingPlan.value = null;
    });
}
function addPlan() { editingPlan.value = null; showPlanForm.value = true; }
function editPlan(plan) { editingPlan.value = { ...plan }; showPlanForm.value = true; }
function deletePlan(id) { axios.delete(`/career-plans/${id}`).then(() => refreshPlans()); }

onMounted(() => {
    refreshContacts();
    refreshJobs();
    refreshTrainings();
    refreshCertifications();
    refreshGoals();
    refreshPlans();
});
const breadcrumbs = [
    { label: 'Dashboard', route: route('dashboard') },
    { label: 'Profile settings' },
];

const deleteUserModalOpen = ref(false);

// Ambil user dari response backend (langsung dari tabel users), dan old value dari session jika ada
const user = usePage().props.user;
console.log('User data:', user);
const old = usePage().props.old ?? {};
const contacts = ref([]);
const showContactForm = ref(false);
const editingContact = ref(null);
function refreshContacts() {
    axios.get('/emergency-contacts').then(res => {
        contacts.value = res.data;
        showContactForm.value = false;
        editingContact.value = null;
    });
}
function addContact() {
    editingContact.value = null;
    showContactForm.value = true;
}
function editContact(contact) {
    editingContact.value = { ...contact };
    showContactForm.value = true;
}
function deleteContact(id) {
    axios.delete(`/emergency-contacts/${id}`).then(() => refreshContacts());
}
onMounted(() => {
    refreshContacts();
});
const toast = useToast();
console.log(user.email)
const updateProfileForm = useForm({
    name: user.name ?? '',
    email: user.email ?? '',
    full_name: user.full_name ?? '',
    birth_date: user.birth_date ?? '',
    birth_place: user.birth_place ?? '',
    gender: user.gender ?? '',
    address: user.address ?? '',
    phone: user.phone ?? '',
    personal_email: user.personal_email ?? '',
    marital_status: user.marital_status ?? '',
    nik: user.nik ?? '',
    employee_id: user.employee_id ?? '',
    position_id: user.position_id ?? '',
    department_id: user.department_id ?? '',
    start_date: user.start_date ?? '',
    employment_status: user.employment_status ?? '',
    office_location: user.office_location ?? '',
    supervisor: user.supervisor ?? '',
    salary: user.salary ?? '',
    benefits: user.benefits ?? '',
    bank_account: user.bank_account ?? '',
    bank_name: user.bank_name ?? '',
});

const departments = ref(usePage().props.departments ?? []);
const positions = ref(usePage().props.positions ?? []);

// PrimeVue Dropdown expects objects, so we use positions.value & departments.value directly

const sendVerificationForm = useForm({});
const sendEmailVerification = () => {
    sendVerificationForm.post(route('verification.send'));
};

const showSuccessToast = () => {
    toast.add({
        severity: 'success',
        summary: 'Saved',
        detail: 'Profile information has been updated',
        life: 3000,
    });
};
const updateProfileInformation = () => {
    updateProfileForm.patch(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showSuccessToast();
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs>
        <InertiaHead title="Profile settings" />

        <SettingsLayout>
            <div class="w-full mx-auto px-2 md:px-8 py-8">
                <form class="space-y-10" @submit.prevent="updateProfileInformation">
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>
                            Data Pribadi
                        </template>
                        <template #content>
                            <div v-if="Object.keys(updateProfileForm.errors).length" class="mb-4">
                                <Message severity="error" class="mb-2">
                                    <ul>
                                        <li v-for="(error, field) in updateProfileForm.errors" :key="field">{{ error }}</li>
                                    </ul>
                                </Message>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- DEBUG: tampilkan isi departments dan positions -->
                                    <div>
                                       <label for="email" class="font-semibold text-gray-700 dark:text-gray-200">Email</label>
                                       <InputText id="email" v-model="updateProfileForm.email" type="email" fluid />
                                   </div>
                                   <div>
                                       <label for="name" class="font-semibold text-gray-700 dark:text-gray-200">Nama Panggilan</label>
                                       <InputText id="name" v-model="updateProfileForm.name" fluid />
                                   </div>
                                <div>
                                    <label for="full_name" class="font-semibold text-gray-700 dark:text-gray-200">Nama Lengkap</label>
                                    <InputText id="full_name" v-model="updateProfileForm.full_name" fluid />
                                </div>
                                <div>
                                    <label for="birth_place" class="font-semibold text-gray-700 dark:text-gray-200">Tempat Lahir</label>
                                    <InputText id="birth_place" v-model="updateProfileForm.birth_place" fluid />
                                </div>
                                <div>
                                    <label for="birth_date" class="font-semibold text-gray-700 dark:text-gray-200">Tanggal Lahir</label>
                                    <InputText id="birth_date" v-model="updateProfileForm.birth_date" type="date" fluid />
                                </div>
                                <div>
                                    <label for="gender" class="font-semibold text-gray-700 dark:text-gray-200">Jenis Kelamin</label>
                                    <select id="gender" v-model="updateProfileForm.gender" class="p-inputtext w-full">
                                        <option value="">Pilih</option>
                                        <option value="male">Laki-laki</option>
                                        <option value="female">Perempuan</option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label for="address" class="font-semibold text-gray-700 dark:text-gray-200">Alamat Lengkap</label>
                                        <Textarea id="address" v-model="updateProfileForm.address" autoResize rows="2" class="w-full" />
                                </div>
                                <div>
                                    <label for="phone" class="font-semibold text-gray-700 dark:text-gray-200">Nomor Telepon</label>
                                    <InputText id="phone" v-model="updateProfileForm.phone" fluid />
                                </div>
                                <div>
                                    <label for="personal_email" class="font-semibold text-gray-700 dark:text-gray-200">Email Pribadi</label>
                                    <InputText id="personal_email" v-model="updateProfileForm.personal_email" fluid />
                                </div>
                                <div>
                                    <label for="marital_status" class="font-semibold text-gray-700 dark:text-gray-200">Status Perkawinan</label>
                                        <select id="marital_status" v-model="updateProfileForm.marital_status" class="p-inputtext w-full">
                                            <option value="">Pilih</option>
                                            <option value="single">Belum Menikah</option>
                                            <option value="married">Menikah</option>
                                            <option value="divorced">Cerai</option>
                                            <option value="widowed">Duda/Janda</option>
                                        </select>
                                </div>
                                <div>
                                    <label for="nik" class="font-semibold text-gray-700 dark:text-gray-200">Nomor KTP/NIK</label>
                                    <InputText id="nik" v-model="updateProfileForm.nik" fluid />
                                </div>
                                   
                            </div>
                        </template>
                    </Card>
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>
                            Data Pekerjaan
                        </template>
                        <template #content>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="employee_id" class="font-semibold text-gray-700 dark:text-gray-200">Nomor ID Karyawan</label>
                                    <InputText disabled id="employee_id" v-model="updateProfileForm.employee_id" fluid :disabled="user.role === 'karyawan'" />
                                </div>
                                    <div>
                                        <label for="position_id" class="font-semibold text-gray-700 dark:text-gray-200">Jabatan</label>
                                            <Dropdown
                                                id="position_id"
                                                v-model="updateProfileForm.position_id"
                                                :options="positions"
                                                optionLabel="name"
                                                optionValue="id"
                                                placeholder="Pilih Jabatan"
                                                :filter="true"
                                                filterPlaceholder="Cari Jabatan..."
                                                class="w-full"
                                                disabled
                                            />
                                    </div>
                                    <div>
                                        <label for="department_id" class="font-semibold text-gray-700 dark:text-gray-200">Departemen/Divisi</label>
                                            <Dropdown
                                                id="department_id"
                                                v-model="updateProfileForm.department_id"
                                                :options="departments"
                                                optionLabel="name"
                                                optionValue="id"
                                                placeholder="Pilih Departemen"
                                                :filter="true"
                                                filterPlaceholder="Cari Departemen..."
                                                class="w-full"
                                                disabled
                                            />
                                    </div>
                                <div>
                                    <label for="start_date" class="font-semibold text-gray-700 dark:text-gray-200">Tanggal Mulai Bekerja</label>
                                    <InputText disabled id="start_date" v-model="updateProfileForm.start_date" type="date" fluid :disabled="user.role === 'karyawan'" />
                                </div>
                                <div>
                                    <label for="employment_status" class="font-semibold text-gray-700 dark:text-gray-200">Status Karyawan</label>
                                    <select disabled id="employment_status" v-model="updateProfileForm.employment_status" class="p-inputtext w-full" :disabled="user.role === 'karyawan'">
                                        <option value="">Pilih</option>
                                        <option value="permanent">Tetap</option>
                                        <option value="contract">Kontrak</option>
                                        <option value="intern">Magang</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="office_location" class="font-semibold text-gray-700 dark:text-gray-200">Lokasi Kantor</label>
                                    <InputText disabled id="office_location" v-model="updateProfileForm.office_location" fluid :disabled="user.role === 'karyawan'" />
                                </div>
                                <div>
                                       <label for="supervisor_id" class="font-semibold text-gray-700 dark:text-gray-200">Supervisor ID</label>
                                       <InputText disabled id="supervisor_id" v-model="updateProfileForm.supervisor_id" type="number" fluid />
                                   </div>
                                <div>
                                    <label for="supervisor" class="font-semibold text-gray-700 dark:text-gray-200">Atasan Langsung</label>
                                    <InputText disabled id="supervisor" v-model="updateProfileForm.supervisor" fluid :disabled="user.role === 'karyawan'" />
                                </div>
                                <div>
                                    <label for="bank_account" class="font-semibold text-gray-700 dark:text-gray-200">Nomor Rekening Bank</label>
                                    <InputText disabled id="bank_account" v-model="updateProfileForm.bank_account" fluid :disabled="user.role === 'karyawan'" />
                                </div>
                                   <div>
                                       <label for="bank_name" class="font-semibold text-gray-700 dark:text-gray-200">Nama Bank</label>
                                       <InputText disabled id="bank_name" v-model="updateProfileForm.bank_name" fluid :disabled="user.role === 'karyawan'" />
                                   </div>
                            </div>
                        </template>
                    </Card>

                    <div class="flex justify-end mt-8">
                        <Button
                            :loading="updateProfileForm.processing"
                            type="submit"
                            label="Simpan Perubahan"
                            class="px-8 py-3"
                        />
                    </div>

                    <Divider type="solid" />

                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>Kontak Darurat</template>
                        <template #content>
                            <EmergencyContactTable :contacts="contacts" :onSaved="refreshContacts" />
                        </template>
                    </Card>
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>Riwayat Jabatan</template>
                        <template #content>
                            <JobHistoryTable :jobs="jobs" :onSaved="refreshJobs" />
                        </template>
                    </Card>
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>Riwayat Pelatihan</template>
                        <template #content>
                            <TrainingTable :trainings="trainings" :onSaved="refreshTrainings" />
                        </template>
                    </Card>
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>Sertifikasi Profesional</template>
                        <template #content>
                            <CertificationTable :certifications="certifications" :onSaved="refreshCertifications" />
                        </template>
                    </Card>
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>Tujuan & Sasaran Kerja</template>
                        <template #content>
                            <WorkGoalTable :goals="goals" :onSaved="refreshGoals" />
                        </template>
                    </Card>
                    <Card pt:body:class="p-6 md:p-10">
                        <template #title>Rencana Pengembangan Karir</template>
                        <template #content>
                            <CareerPlanTable :plans="plans" :onSaved="refreshPlans" />
                        </template>
                    </Card>
                    <div v-if="mustVerifyEmail && user.email_verified_at === null" class="bg-white dark:bg-gray-900 rounded-xl shadow p-6 md:p-10 border border-gray-200 dark:border-gray-700">
                        <p class="text-sm mt-2">
                            <span class="font-semibold text-danger dark:text-red-400">Your email address is unverified.</span>
                            <Button
                                :loading="sendVerificationForm.processing"
                                class="p-0 text-sm"
                                variant="link"
                                label="Click here to re-send the verification email."
                                @click="sendEmailVerification"
                            />
                            <Message
                                v-if="status === 'verification-link-sent'"
                                severity="success"
                                :closable="false"
                                class="shadow-sm mt-4"
                            >
                                A new verification link has been sent to your email address.
                            </Message>
                        </p>
                    </div>
                </form>
                <br><br>
                <!-- <Card
                    pt:body:class="max-w-2xl space-y-3 mt-5"
                    pt:caption:class="space-y-1"
                >
                    <template #title>
                        Delete account
                    </template>
                    <template #subtitle>
                        Delete your account and all of its resources
                    </template>
                    <template #content>
                        <DeleteUserModal v-model="deleteUserModalOpen" />
                        <Message
                            severity="error"
                            pt:root:class="p-2"
                        >
                            <div class="flex flex-col gap-4">
                                <div>
                                    <div class="text-lg">
                                        Warning
                                    </div>
                                    <div>
                                        Please proceed with caution, this cannot be undone.
                                    </div>
                                </div>
                                <div>
                                    <Button
                                        label="Delete account"
                                        severity="danger"
                                        @click="deleteUserModalOpen = true"
                                    />
                                </div>
                            </div>
                        </Message>
                    </template>
                </Card> -->
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
