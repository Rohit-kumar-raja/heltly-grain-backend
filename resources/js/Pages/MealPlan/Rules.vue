<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AppDataTable from '@/Components/AppDataTable.vue';
import AppFormDialog from '@/Components/AppFormDialog.vue';
import { ref, computed } from 'vue';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import Button from 'primevue/button';
import Message from 'primevue/message';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    conditionOptions: Object, // { 'goal': 'User Goal', ... }
    actionOptions: Object,   // { 'calorie_adjustment': 'Adjust Calories', ... }
    products: Array,
});

const toast = useToast();
const ruleDialog = ref(false);
const submitted = ref(false);
const dataTable = ref(null);

const form = useForm({
    id: null,
    name: '',
    condition_field: '',
    operator: '=',
    condition_value: '',
    action_type: '',
    action_value: '',
    priority: 0,
    product_id: null,
});

const operators = [
    { label: 'Equals (=)', value: '=' },
    { label: 'Greater Than (>)', value: '>' },
    { label: 'Less Than (<)', value: '<' },
    { label: 'Contains', value: 'contains' },
    { label: 'Less Than or Equal (<=)', value: '<=' },
    { label: 'Greater Than or Equal (>=)', value: '>=' },
];



const priorityOptions = [
    { label: 'Low', value: 0 },
    { label: 'Mid', value: 10 },
    { label: 'High', value: 20 },
    { label: 'Very High', value: 30 },
];

const openNew = () => {
    form.reset();
    form.priority = 0;
    ruleDialog.value = true;
    submitted.value = false;
};


const editRule = (rule) => {
    form.id = rule.id;
    form.name = rule.name;
    form.condition_field = rule.condition_field;
    form.operator = rule.operator;
    form.condition_value = rule.condition_value;
    form.action_type = rule.action_type;
    form.action_value = typeof rule.action_value === 'object' ? JSON.stringify(rule.action_value) : rule.action_value;
    form.priority = rule.priority;
    form.product_id = rule.product_id;
    ruleDialog.value = true;
};

const saveRule = () => {
    submitted.value = true;

    if (!form.name || !form.condition_field || !form.action_type) return;

    const submitForm = () => {
        if (form.id) {
            form.put(route('meal-plan.rules.update', form.id), {
                onSuccess: () => {
                    ruleDialog.value = false;
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Rule Updated', life: 3000 });
                    dataTable.value.refresh();
                }
            });
        } else {
            form.post(route('meal-plan.rules.store'), {
                onSuccess: () => {
                    ruleDialog.value = false;
                    toast.add({ severity: 'success', summary: 'Successful', detail: 'Rule Created', life: 3000 });
                    dataTable.value.refresh();
                }
            });
        }
    };

    submitForm();
};

const fieldPlaceholder = computed(() => {
    if (!form.condition_field) return "Value to match";
    if (form.condition_field.includes('bp_')) return "e.g. 140";
    if (form.condition_field.includes('sugar')) return "e.g. 100";
    if (form.condition_field === 'bmi') return "e.g. 25.0";
    if (form.condition_field.includes('goal')) return "e.g. lose_weight";
    return "Enter value";
});

const actionHint = computed(() => {
    if (form.action_type === 'calorie_adjustment') return "Enter calories to add (positive) or remove (negative). E.g. -500";
    if (form.action_type === 'tip') return "Enter the health tip to display to the user.";
    if (form.action_type === 'macro_distribution') return "JSON format: {\"p\": 40, \"c\": 30, \"f\": 30}";
    return "";
});

const columns = [
    { field: 'name', header: 'Rule Name', sortable: true },
    { field: 'condition_field', header: 'Condition Field', sortable: true },
    { field: 'operator', header: 'Op' },
    { field: 'condition_value', header: 'Cond. Value' },
    { field: 'action_type', header: 'Action Type', sortable: true },
    {
        field: 'priority', header: 'Priority', sortable: true, format: (val) => {
            if (val >= 30) return 'Very High';
            if (val >= 20) return 'High';
            if (val >= 10) return 'Mid';
            return 'Low';
        }
    },
];

</script>

<template>
    <div>
        <AppDataTable ref="dataTable" title="Smart Rules" :apiUrl="route('meal-plan.rules.data')" :columns="columns"
            @edit="editRule">
            <template #header-actions>
                <Button label="New Rule" icon="pi pi-plus" class="w-full sm:w-auto rounded-xl!" @click="openNew" />
            </template>
        </AppDataTable>

        <AppFormDialog v-model:visible="ruleDialog" :title="form.id ? 'Edit Rule' : 'New Rule'" @submit="saveRule"
            @cancel="ruleDialog = false">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name & Priority -->
                <div class="space-y-2 col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300">Rule Name</label>
                    <InputText v-model="form.name" class="w-full! rounded-xl!" placeholder="e.g. High BP Sodium Warning"
                        autofocus :class="{ 'p-invalid': submitted && !form.name }" />
                    <small class="text-red-500" v-if="submitted && !form.name">Name is required.</small>
                    <small class="block text-slate-400">Give this rule a descriptive name for internal use.</small>
                </div>

                <!-- Condition Section -->
                <div class="col-span-1 md:col-span-2 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold">
                            1</div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200">Condition</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2 col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">If
                                User's...</label>
                            <Select v-model="form.condition_field"
                                :options="Object.entries(conditionOptions).map(([k, v]) => ({ label: v, value: k }))"
                                optionLabel="label" optionValue="value" placeholder="Select Health Parameter"
                                class="w-full! rounded-xl!" :class="{ 'p-invalid': submitted && !form.condition_field }"
                                filter showClear />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Operator</label>
                            <Select v-model="form.operator" :options="operators" optionLabel="label" optionValue="value"
                                class="w-full! rounded-xl!" filter />
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Value</label>
                            <InputText v-model="form.condition_value" class="w-full! rounded-xl!"
                                :placeholder="fieldPlaceholder" />
                        </div>
                    </div>
                </div>


                <!-- Action Section -->
                <div class="col-span-1 md:col-span-2 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center text-emerald-600 dark:text-emerald-400 font-bold">
                            2</div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200">Action & Result</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Do
                                this...</label>
                            <Select v-model="form.action_type"
                                :options="Object.entries(actionOptions).map(([k, v]) => ({ label: v, value: k }))"
                                optionLabel="label" optionValue="value" placeholder="Select Action Type"
                                class="w-full! rounded-xl!" filter />
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Priority (Higher
                                runs
                                later)</label>
                            <Select v-model="form.priority" :options="priorityOptions" optionLabel="label"
                                optionValue="value" class="w-full! rounded-xl!" placeholder="Select Priority" />
                        </div>

                        <div class="space-y-2 col-span-1 md:col-span-2">
                            <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Action
                                Value</label>

                            <div v-if="form.action_type === 'calorie_adjustment'">
                                <div class="p-inputgroup flex-1">
                                    <span class="p-inputgroup-addon bg-slate-100 dark:bg-slate-700">Cal</span>
                                    <InputNumber v-model="form.action_value" placeholder="-500"
                                        class="w-full! rounded-xl!" />
                                </div>
                            </div>

                            <div v-else-if="form.action_type === 'macro_distribution'">
                                <Textarea v-model="form.action_value" rows="3" class="w-full! rounded-xl!"
                                    placeholder='{"p": 40, "c": 30, "f": 30}' />
                            </div>

                            <div v-else>
                                <InputText v-model="form.action_value" class="w-full! rounded-xl!"
                                    placeholder="e.g. 'Limit Sodium Intake'" />
                            </div>

                            <Message v-if="actionHint" severity="info" size="small" variant="simple" class="mt-1">{{
                                actionHint
                                }}</Message>
                        </div>
                    </div>
                </div>

                <!-- Recommendation Section -->
                <div class="col-span-1 md:col-span-2 bg-slate-50 dark:bg-slate-800 p-4 rounded-xl space-y-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div
                            class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900 flex items-center justify-center text-purple-600 dark:text-purple-400 font-bold">
                            3</div>
                        <h3 class="font-bold text-slate-800 dark:text-slate-200">Upsell (Optional)</h3>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-600 dark:text-slate-400">Recommend
                            Product</label>
                        <Select v-model="form.product_id" :options="props.products" optionLabel="name" optionValue="id"
                            filter placeholder="Search & Select Product..." class="w-full! rounded-xl!" showClear />
                        <small class="text-slate-400">If this rule triggers, this product will be recommended to the
                            user.</small>
                    </div>
                </div>

            </div>
        </AppFormDialog>
    </div>
</template>
