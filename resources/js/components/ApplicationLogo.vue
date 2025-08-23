<template>
    <span class="text-5xl sm:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-indigo-500 to-green-500 dark:from-red-400 dark:via-indigo-400 dark:to-green-400">{{ appName }}</span>
</template>

<script setup>
const appName = import.meta.env.VITE_APP_NAME || 'MataHR';
</script>
