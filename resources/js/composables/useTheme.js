import { ref } from 'vue'

const isDark = ref(false)

function toggleTheme() {
  // Dark mode removed – light mode only
}

export function useTheme() {
  return { isDark, toggleTheme }
}
