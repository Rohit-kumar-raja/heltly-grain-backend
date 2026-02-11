import { ref } from 'vue';

// Dark mode state - shared across components - default to LIGHT mode
const isDark = ref(false);

// Initialize from localStorage
if (typeof window !== 'undefined') {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
}

export function useTheme() {
    const toggleDarkMode = () => {
        isDark.value = !isDark.value;
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    };

    return {
        isDark,
        toggleDarkMode
    };
}
