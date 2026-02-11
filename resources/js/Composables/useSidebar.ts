import { ref, computed } from 'vue';

// Sidebar state - shared across components
const sidebarCollapsed = ref(false);
const mobileSidebarOpen = ref(false);

// Initialize from localStorage
if (typeof window !== 'undefined') {
    const savedSidebar = localStorage.getItem('sidebarCollapsed');
    if (savedSidebar === 'true') {
        sidebarCollapsed.value = true;
    }
}

export function useSidebar() {
    const toggleSidebar = () => {
        if (window.innerWidth < 1024) {
            mobileSidebarOpen.value = !mobileSidebarOpen.value;
        } else {
            sidebarCollapsed.value = !sidebarCollapsed.value;
            localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value.toString());
        }
    };

    const closeMobileSidebar = () => {
        mobileSidebarOpen.value = false;
    };

    const openMobileSidebar = () => {
        mobileSidebarOpen.value = true;
    };

    const sidebarWidth = computed(() => {
        return sidebarCollapsed.value ? 'w-20' : 'w-64';
    });

    return {
        sidebarCollapsed,
        mobileSidebarOpen,
        toggleSidebar,
        closeMobileSidebar,
        openMobileSidebar,
        sidebarWidth
    };
}
