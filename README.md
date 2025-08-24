
<img width="1448" height="816" alt="screenshot-2025-08-24-17-49-15" src="https://github.com/user-attachments/assets/ddb6bba5-8753-44ea-830a-017a289763a9" />

# MataHR

**Aplikasi Manajemen Karyawan, Absensi, Cuti, dan Data Karyawan**

![Static Badge](https://img.shields.io/badge/Laravel%20-%20v12%20-%20%23f9322c) ![Static Badge](https://img.shields.io/badge/Inertia.js%20-%20v2%20-%20%236b46c1) ![Static Badge](https://img.shields.io/badge/Vue.js%20-%20v3.5%20-%20rgb(66%20184%20131)) ![Static Badge](https://img.shields.io/badge/PrimeVue%20-%20v4%20-%20rgb(16%20185%20129)) ![Static Badge](https://img.shields.io/badge/Tailwind%20CSS%20-%20v4%20-%20%230284c7)

## Deskripsi

MataHR adalah aplikasi berbasis web untuk manajemen data karyawan, absensi, pengajuan cuti, dan pengelolaan informasi HR. Cocok untuk perusahaan yang ingin digitalisasi proses HR secara efisien dan terintegrasi.

## Fitur Utama

- **Absensi Karyawan**
	- Clock in & clock out dengan lokasi (latitude, longitude)
	- Riwayat absensi per karyawan
- **Pengajuan & Persetujuan Cuti**
	- Pengajuan cuti (annual, sick, dll)
	- Status cuti: pending, approved, rejected
	- Catatan persetujuan dan riwayat cuti
- **Manajemen Data Karyawan**
	- Data personal, kontak darurat, posisi, departemen
	- Riwayat pekerjaan, sertifikasi, pelatihan
	- Informasi gaji, benefit, status kepegawaian
- **Struktur Organisasi**
	- Departemen, posisi, supervisor
	- Relasi supervisor-employee
- **Jadwal Kerja**
	- Pengaturan jam kerja, hari kerja, minimal durasi kerja
- **Pengelolaan Tujuan Kerja & Rencana Karir**
	- Target kerja dan rencana pengembangan karir
- **Pengaturan Kantor**
	- Lokasi kantor, radius absensi, nama kantor

## Struktur Database

Tabel utama:

- `users`: Data karyawan, supervisor, posisi, departemen, status, gaji, benefit, dll
- `attendances`: Data absensi (clock in/out, lokasi)
- `leaves`: Pengajuan cuti, status, persetujuan
- `departments`, `positions`: Struktur organisasi
- `job_histories`, `certifications`, `trainings`: Riwayat pekerjaan, sertifikasi, pelatihan
- `emergency_contacts`: Kontak darurat
- `work_schedules`: Jadwal kerja mingguan
- `work_goals`, `career_plans`: Target kerja & rencana karir
- `settings`: Pengaturan kantor
- `supervisor_employees`: Relasi supervisor dan karyawan

## Contoh Departemen

- HRD (Human Resource Department)
- IT (Information Technology)
- Finance (Finance and Accounting)
- Marketing, Sales, Production, Logistics, Procurement, R&D, Customer Service, General Affairs, Legal, Quality Assurance, Public Relations, Maintenance, Security, Warehouse, Design, Export Import, Health and Safety

## Contoh Posisi

- Staff
- Supervisor
- Manager
- Assistant Manager
- Head of Department
- Director
- Intern

## Teknologi

- Laravel
- Inertia.js
- Vue.js
- PrimeVue
- Tailwind CSS

## Instalasi

1. Clone repository:
	 ```bash
	 git clone https://github.com/abdurozzaq/MataHR.git
	 cd MataHR
	 ```
2. Install dependencies:
	 ```bash
	 composer install
	 npm install
	 ```
3. Copy file .env dan konfigurasi database
	 ```bash
	 cp .env.example .env
	 # Edit .env sesuai konfigurasi database Anda
	 ```
4. Generate key dan migrate database:
	 ```bash
	 php artisan key:generate
	 php artisan migrate --seed
	 ```
5. Jalankan aplikasi:
	 ```bash
	 npm run dev
	 php artisan serve
	 ```

## Kontribusi

Silakan buat pull request atau issue untuk saran dan perbaikan.

## Lisensi

GNU General Public License v2.0
