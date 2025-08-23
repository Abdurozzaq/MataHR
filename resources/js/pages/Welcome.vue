<template>
    <InertiaHead title="Welcome" />
    <div class="welcome-bg min-h-screen w-full flex items-center justify-center transition-colors duration-300">
        <div class="welcome-card shadow-xl rounded-3xl p-8 sm:p-16 bg-white dark:bg-[#18181b] border border-gray-200 dark:border-zinc-800 max-w-2xl w-full flex flex-col items-center">
            <div class="flex flex-col items-center gap-2 mb-8">
                <span class="text-5xl sm:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-indigo-500 to-green-500 dark:from-red-400 dark:via-indigo-400 dark:to-green-400">{{ appName }}</span>
                <p class="text-lg text-gray-500 dark:text-zinc-400 mt-2 text-center">
                    HRIS System<br>
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4 w-full justify-center mb-8">
                <template v-if="page.props.auth.user">
                    <InertiaLink :href="route('dashboard')" class="w-full sm:w-auto">
                        <button class="welcome-btn bg-indigo-600 hover:bg-indigo-700 text-white dark:bg-indigo-500 dark:hover:bg-indigo-600 font-semibold py-3 px-6 rounded-xl flex items-center justify-center gap-2 w-full transition">
                            <LayoutGrid />
                            Dashboard
                        </button>
                    </InertiaLink>
                    <InertiaLink :href="route('profile.edit')" class="w-full sm:w-auto">
                        <button class="welcome-btn bg-gray-100 hover:bg-gray-200 text-gray-900 dark:bg-zinc-700 dark:hover:bg-zinc-600 dark:text-white font-semibold py-3 px-6 rounded-xl flex items-center justify-center gap-2 w-full transition border border-gray-300 dark:border-zinc-700">
                            <Settings />
                            Profile Settings
                        </button>
                    </InertiaLink>
                </template>
                <template v-else>
                    <InertiaLink :href="route('login')" class="w-full sm:w-auto">
                        <button class="welcome-btn bg-indigo-600 hover:bg-indigo-700 text-white dark:bg-indigo-500 dark:hover:bg-indigo-600 font-semibold py-3 px-6 rounded-xl flex items-center justify-center gap-2 w-full transition">
                            <LogIn />
                            Login
                        </button>
                    </InertiaLink>
                </template>
            </div>
            <div class="mt-2 text-center">
                <span class="text-xs text-gray-400 dark:text-zinc-500">Laravel v{{ laravelVersion }} &nbsp;|&nbsp; PHP v{{ phpVersion }}</span>
            </div>
            <div class="mt-8 flex justify-center">
                <button @click="toggleTheme" class="theme-toggle-btn px-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800 text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700 transition">
                    <span v-if="isDark">🌙 Dark Mode</span>
                    <span v-else>☀️ Light Mode</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { LayoutGrid, LogIn, Settings, UserPlus } from 'lucide-vue-next';
import { ref, onMounted } from 'vue';

defineProps({
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const page = usePage();
const isDark = ref(false);
const appName = import.meta.env.VITE_APP_NAME || 'MataHR';
function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}
onMounted(() => {
    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<style scoped>
.welcome-bg {
    background: linear-gradient(135deg, #f8fafc 0%, #e0e7ff 100%);
}
.dark .welcome-bg {
    background: linear-gradient(135deg, #18181b 0%, #312e81 100%);
}
.welcome-card {
    box-shadow: 0 8px 32px 0 rgba(31, 41, 55, 0.12);
    border-radius: 1.5rem;
    border: 1px solid #e5e7eb;
}
.dark .welcome-card {
    background: #18181b;
    border-color: #27272a;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.32);
}
.welcome-link {
    color: #6366f1;
    text-decoration: underline;
    font-weight: 500;
    margin: 0 0.25em;
    transition: color 0.2s;
}
.welcome-link:hover {
    color: #4338ca;
}
.dark .welcome-link {
    color: #a5b4fc;
}
.dark .welcome-link:hover {
    color: #818cf8;
}
.welcome-btn {
    font-size: 1rem;
    font-weight: 600;
    transition: background 0.2s, color 0.2s;
}
.theme-toggle-btn {
    font-size: 0.95rem;
    font-weight: 500;
    cursor: pointer;
}
</style>
