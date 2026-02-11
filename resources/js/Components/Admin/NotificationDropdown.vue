<script setup lang="ts">
import Popover from 'primevue/popover';
import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { router } from '@inertiajs/vue3';

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    time: string;
    read: boolean;
    icon: string;
    color: string;
    link?: string;
    reference_id?: number | null;
}

const notificationPanel = ref();
const notifications = ref<Notification[]>([]);
const unreadCount = ref(0);
let pollInterval: NodeJS.Timeout | null = null;

const fetchNotifications = async () => {
    try {
        const response = await axios.get(route('admin.notifications'));
        notifications.value = response.data.notifications;
        unreadCount.value = response.data.unreadCount;
    } catch (error) {
        console.error('Failed to fetch notifications', error);
    }
};

const togglePanel = (event: Event) => {
    notificationPanel.value.toggle(event);
    if (!notificationPanel.value.visible) {
        fetchNotifications();
    }
};

const markAsRead = async (notification: Notification) => {
    if (notification.read) return;

    try {
        await axios.post(route('admin.notifications.read', notification.id));
        notification.read = true;
        unreadCount.value = Math.max(0, unreadCount.value - 1);
    } catch (error) {
        console.error('Failed to mark as read', error);
    }

    if (notification.link) {
        router.visit(route(notification.link, notification.reference_id ? { id: notification.reference_id } : {}));
        notificationPanel.value.hide();
    }
};

const markAllAsRead = async () => {
    try {
        await axios.post(route('admin.notifications.read-all'));
        notifications.value.forEach((n) => n.read = true);
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to mark all as read', error);
    }
};

const clearAll = async () => {
    try {
        await axios.post(route('admin.notifications.clear'));
        notifications.value = [];
        unreadCount.value = 0;
    } catch (error) {
        console.error('Failed to clear notifications', error);
    }
};

const viewAll = () => {
    // For now, just close panel. In real app, could go to proper /notifications page
    notificationPanel.value.hide();
};

onMounted(() => {
    fetchNotifications();
    pollInterval = setInterval(fetchNotifications, 30000); // Poll every 30 seconds
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});
</script>

<template>
    <div class="relative">
        <button @click="togglePanel"
            class="relative p-2 rounded-full text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            v-tooltip.bottom="'Notifications'">
            <i class="pi pi-bell text-lg"></i>
            <span v-if="unreadCount > 0"
                class="absolute -top-0.5 -right-0.5 min-w-5 h-5 flex items-center justify-center text-xs font-bold text-white bg-red-500 rounded-full px-1.5 ring-2 ring-white dark:ring-slate-900">
                {{ unreadCount > 9 ? '9+' : unreadCount }}
            </span>
        </button>

        <Popover ref="notificationPanel" :pt="{
            root: { class: 'rounded-2xl! shadow-2xl! border! border-slate-200! dark:border-slate-700! w-80! sm:w-96! mt-2!' },
            content: { class: 'p-0!' }
        }">
            <!-- Header -->
            <div
                class="flex items-center justify-between px-4 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-t-2xl">
                <div class="flex items-center gap-2">
                    <i class="pi pi-bell text-primary-500"></i>
                    <span class="font-semibold text-slate-900 dark:text-slate-100">Notifications</span>
                    <span v-if="unreadCount > 0"
                        class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                        {{ unreadCount }}
                    </span>
                </div>
                <button v-if="notifications.length > 0" @click="markAllAsRead"
                    class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    Mark all read
                </button>
            </div>

            <!-- Notifications List -->
            <div class="max-h-80 overflow-y-auto">
                <div v-if="notifications.length === 0"
                    class="flex flex-col items-center justify-center py-8 text-slate-400 dark:text-slate-500">
                    <i class="pi pi-inbox text-4xl mb-2"></i>
                    <span class="text-sm">No notifications</span>
                </div>

                <div v-for="notification in notifications" :key="notification.id" @click="markAsRead(notification)"
                    class="flex items-start gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800/50 cursor-pointer transition-colors border-b border-slate-100 dark:border-slate-800 last:border-b-0"
                    :class="{ 'bg-primary-50/50 dark:bg-primary-500/5': !notification.read }">
                    <!-- Icon -->
                    <div
                        :class="[notification.color, 'w-10 h-10 rounded-xl flex items-center justify-center shrink-0']">
                        <i :class="[notification.icon, 'text-white']"></i>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="font-medium text-sm text-slate-900 dark:text-slate-100 truncate">
                                {{ notification.title }}
                            </span>
                            <span v-if="!notification.read" class="w-2 h-2 rounded-full bg-primary-500 shrink-0"></span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate mt-0.5">
                            {{ notification.message }}
                        </p>
                        <span class="text-xs text-slate-400 dark:text-slate-500 mt-1 block">
                            {{ notification.time }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="flex items-center justify-between px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 rounded-b-2xl">
                <button @click="clearAll"
                    class="text-xs text-slate-500 dark:text-slate-400 hover:text-red-500 dark:hover:text-red-400 font-medium">
                    Clear all
                </button>
                <button @click="viewAll"
                    class="text-xs text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-medium">
                    View all notifications
                </button>
            </div>
        </Popover>
    </div>
</template>
