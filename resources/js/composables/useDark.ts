import { ref, onMounted } from 'vue';

export function useDark() {
  const isDark = ref(false);

  // Deteksi preferensi dark mode dari browser
  onMounted(() => {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      isDark.value = true;
    }
  });

  return { isDark };
}
