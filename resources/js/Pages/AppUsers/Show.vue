<script setup lang="ts">
import { ref, computed } from 'vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppFormDialog from '@/Components/AppFormDialog.vue';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    user: Object
});

const health = computed(() => props.user.health_profile || {});

const formatKey = (key) => key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());

const healthSections = [
    { key: 'vitals', label: 'Vitals', icon: 'pi pi-heart' },
    { key: 'body_composition', label: 'Body Composition', icon: 'pi pi-user' },
    { key: 'lifestyle', label: 'Lifestyle', icon: 'pi pi-calendar' },
    { key: 'nutrition_stats', label: 'Nutrition', icon: 'pi pi-apple' },
    { key: 'medical_history', label: 'Medical History', icon: 'pi pi-briefcase' },
    { key: 'lab_results', label: 'Lab Results', icon: 'pi pi-file' },
    { key: 'mental_health', label: 'Mental Health', icon: 'pi pi-sun' },
];

// Diet Preview
const dietDialog = ref(false);
const dietLoading = ref(false);
const dietData = ref(null);

const mealIcons = {
    breakfast: 'pi-sun',
    lunch: 'pi-cloud',
    dinner: 'pi-moon',
    snack: 'pi-apple',
};

const mealColors = {
    breakfast: 'from-amber-400 to-orange-500',
    lunch: 'from-emerald-400 to-teal-500',
    dinner: 'from-indigo-400 to-purple-500',
    snack: 'from-rose-400 to-pink-500',
};

const openDietPreview = async () => {
    dietDialog.value = true;
    dietLoading.value = true;
    dietData.value = null;
    try {
        const { data } = await axios.get(route('app-users.diet-preview', props.user.id));
        dietData.value = data;
    } catch (e) {
        console.error('Diet preview failed:', e);
    } finally {
        dietLoading.value = false;
    }
};

const calorieAccuracy = computed(() => {
    if (!dietData.value) return 0;
    const target = dietData.value.user_stats.target_calories;
    const actual = dietData.value.plan_totals.calories;
    return Math.min(100, Math.round((actual / target) * 100));
});
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-start justify-between flex-wrap gap-4">
            <div>
                <Link :href="route('app-users.index')">
                    <Button icon="pi pi-arrow-left" label="Back to Users" text class="mb-2 pl-0" />
                </Link>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-3">
                    {{ user.name }}
                    <Tag :value="user.goal" severity="info" class="text-sm" v-if="user.goal"></Tag>
                </h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">{{ user.email }} • {{ user.gender }} • {{ user.age }}
                    yo</p>
            </div>
            <Button label="Preview Diet Plan" icon="pi pi-sparkles" @click="openDietPreview"
                class="bg-linear-to-r! from-primary-500! to-emerald-500! border-0! rounded-xl! shadow-lg! hover:shadow-xl! transition-all!" />
        </div>

        <!-- Health Profile Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <template v-for="section in healthSections" :key="section.key">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-100 dark:border-slate-700 p-5"
                    v-if="health[section.key] && Object.keys(health[section.key]).length > 0">
                    <h3
                        class="flex items-center gap-2 font-semibold text-lg text-slate-700 dark:text-slate-200 mb-4 border-b border-slate-100 dark:border-slate-700 pb-2">
                        <i :class="[section.icon, 'text-primary-500']"></i> {{ section.label }}
                    </h3>

                    <div class="space-y-3">
                        <div v-for="(value, key) in health[section.key]" :key="key"
                            class="flex justify-between items-center text-sm">
                            <span class="text-slate-500 dark:text-slate-400">{{ formatKey(key) }}</span>
                            <span class="font-medium text-slate-700 dark:text-slate-200">
                                <template v-if="Array.isArray(value)">
                                    <Tag v-for="tag in value" :key="tag" :value="tag" class="mr-1 mb-1"></Tag>
                                </template>
                                <template v-else-if="typeof value === 'object'">
                                    {{ JSON.stringify(value) }}
                                </template>
                                <template v-else>
                                    {{ value }}
                                </template>
                            </span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Empty State if no health data -->
            <div v-if="!props.user.health_profile"
                class="col-span-full py-12 flex flex-col items-center justify-center text-slate-400 bg-slate-50 dark:bg-slate-800/50 rounded-2xl border-2 border-dashed border-slate-200 dark:border-slate-700">
                <i class="pi pi-file-excel text-4xl mb-3 opacity-50"></i>
                <p>No health profile data available for this user.</p>
            </div>
        </div>

        <!-- Diet Preview Dialog -->
        <AppFormDialog v-model:visible="dietDialog" title="Diet Plan Preview" :subtitle="`Generated for ${user.name}`"
            icon="pi-sparkles" width="960px" :showFooter="false">

            <!-- Loading State -->
            <div v-if="dietLoading" class="flex flex-col items-center justify-center py-16 gap-4">
                <i class="pi pi-spin pi-spinner text-4xl text-primary-500"></i>
                <p class="text-slate-500 font-medium">Generating diet plan...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="!dietData" class="flex flex-col items-center justify-center py-16 gap-4">
                <i class="pi pi-exclamation-triangle text-4xl text-amber-500"></i>
                <p class="text-slate-500">Failed to load diet preview. Please try again.</p>
            </div>

            <!-- Diet Plan Content -->
            <div v-else class="space-y-6">

                <!-- User Stats Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-blue-400 mb-1">BMR</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ dietData.user_stats.bmr }}</p>
                        <p class="text-[10px] text-blue-400">kcal/day</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 mb-1">TDEE</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ dietData.user_stats.tdee
                        }}</p>
                        <p class="text-[10px] text-emerald-400">kcal/day</p>
                    </div>
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-4 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-purple-400 mb-1">Target</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{
                            dietData.user_stats.target_calories }}</p>
                        <p class="text-[10px] text-purple-400">kcal/day</p>
                    </div>
                    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 text-center">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-amber-400 mb-1">Goal</p>
                        <p class="text-lg font-bold text-amber-600 dark:text-amber-400 capitalize">{{
                            dietData.user_stats.goal
                        }}</p>
                        <p class="text-[10px] text-amber-400">{{ calorieAccuracy }}% meal fit</p>
                    </div>
                </div>

                <!-- Meals Grid -->
                <div>
                    <h3 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <i class="pi pi-list text-primary-500"></i> Suggested Meals
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div v-for="(meal, type) in dietData.meals" :key="type"
                            class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-xl overflow-hidden shadow-sm">
                            <div class="flex items-center gap-3 p-4">
                                <div
                                    :class="['w-12 h-12 rounded-xl bg-linear-to-br flex items-center justify-center shrink-0', mealColors[type]]">
                                    <i :class="['pi', mealIcons[type], 'text-white text-lg']"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">{{
                                        type }}
                                    </p>
                                    <p class="font-semibold text-slate-800 dark:text-white text-sm truncate">{{
                                        meal.name }}</p>
                                    <div class="flex gap-3 mt-1 text-[11px] text-slate-500">
                                        <span><span class="font-bold text-slate-700 dark:text-slate-300">{{
                                            meal.calories
                                        }}</span> cal</span>
                                        <span><span class="font-bold text-blue-500">{{ meal.protein }}g</span> P</span>
                                        <span><span class="font-bold text-amber-500">{{ meal.carbs }}g</span> C</span>
                                        <span><span class="font-bold text-rose-500">{{ meal.fats }}g</span> F</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Plan Totals -->
                    <div
                        class="mt-3 bg-slate-50 dark:bg-slate-800 rounded-xl p-3 flex justify-between items-center text-sm">
                        <span class="font-medium text-slate-500">Plan Total</span>
                        <div class="flex gap-4 font-semibold">
                            <span class="text-slate-700 dark:text-white">{{ dietData.plan_totals.calories }} cal</span>
                            <span class="text-blue-500">{{ dietData.plan_totals.protein }}g P</span>
                            <span class="text-amber-500">{{ dietData.plan_totals.carbs }}g C</span>
                            <span class="text-rose-500">{{ dietData.plan_totals.fats }}g F</span>
                        </div>
                    </div>
                </div>

                <!-- Tips -->
                <div v-if="dietData.tips.length > 0">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <i class="pi pi-lightbulb text-amber-500"></i> Health Tips
                    </h3>
                    <div
                        class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-xl p-4 space-y-2">
                        <div v-for="(tip, i) in dietData.tips" :key="i" class="flex items-start gap-2 text-sm">
                            <i class="pi pi-check-circle text-amber-500 mt-0.5 shrink-0"></i>
                            <span class="text-amber-800 dark:text-amber-200">{{ tip }}</span>
                        </div>
                    </div>
                </div>

                <!-- Product Recommendations -->
                <div v-if="dietData.recommendations.length > 0">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <i class="pi pi-shopping-bag text-purple-500"></i> Product Recommendations
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <div v-for="product in dietData.recommendations" :key="product.id"
                            class="inline-flex items-center gap-2 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg px-3 py-2">
                            <i class="pi pi-box text-purple-500 text-sm"></i>
                            <span class="text-sm font-medium text-purple-800 dark:text-purple-200">{{ product.name
                            }}</span>
                            <span class="text-xs text-purple-400">₹{{ product.price }}</span>
                        </div>
                    </div>
                </div>

                <!-- Triggered Rules (Admin Insight) -->
                <div v-if="dietData.triggered_rules.length > 0">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <i class="pi pi-cog text-slate-500"></i> Triggered Rules
                        <span class="text-xs font-normal text-slate-400">({{ dietData.triggered_rules.length }}
                            matched)</span>
                    </h3>
                    <div
                        class="bg-slate-50 dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-700">
                        <div v-for="(rule, i) in dietData.triggered_rules" :key="i"
                            class="flex items-center justify-between px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div
                                    :class="['w-2 h-2 rounded-full', rule.action_type === 'calorie_adjustment' ? 'bg-blue-500' : 'bg-amber-500']">
                                </div>
                                <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ rule.name
                                }}</span>
                            </div>
                            <Tag :value="rule.action_type === 'calorie_adjustment' ? `${rule.action_value > 0 ? '+' : ''}${rule.action_value} cal` : 'Tip'"
                                :severity="rule.action_type === 'calorie_adjustment' ? 'info' : 'warn'" class="text-xs">
                            </Tag>
                        </div>
                    </div>
                </div>

                <!-- No Rules Matched -->
                <div v-if="dietData.triggered_rules.length === 0 && dietData.tips.length === 0"
                    class="bg-slate-50 dark:bg-slate-800 rounded-xl p-6 text-center">
                    <i class="pi pi-info-circle text-2xl text-slate-400 mb-2"></i>
                    <p class="text-sm text-slate-500">No smart rules triggered for this user's profile. The plan is
                        based on
                        default TDEE calculations.</p>
                </div>
            </div>
        </AppFormDialog>
    </div>
</template>
