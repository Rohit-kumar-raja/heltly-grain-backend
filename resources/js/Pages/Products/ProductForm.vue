<script setup>
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import InputNumber from 'primevue/inputnumber';
import FileUpload from 'primevue/fileupload';
import Select from 'primevue/select';
import Button from 'primevue/button';
import AppFormDialog from '@/Components/AppFormDialog.vue';
import { useToast } from 'primevue/usetoast';

const props = defineProps({
    visible: { type: Boolean, default: false },
    product: { type: Object, default: null },
    categories: { type: Array, default: () => [] },
});

const emit = defineEmits(['update:visible', 'saved']);

const toast = useToast();
const submitted = ref(false);
const previewImage = ref(null);
const nutritionItems = ref([]);

const form = useForm({
    id: null,
    name: '',
    description: '',
    price: 0,
    pack: '',
    benefits: '',
    image: null,
    category_id: null,
    rating: 4.5,
    calories: 0,
    nutrition: {}
});

// Helper to update form nutrition from items
const updateNutritionForm = () => {
    const nutrition = {};
    nutritionItems.value.forEach(item => {
        if (item.key) nutrition[item.key] = item.value;
    });
    form.nutrition = nutrition;
};

const addNutrient = () => {
    nutritionItems.value.push({ key: '', value: '' });
};

const removeNutrient = (index) => {
    nutritionItems.value.splice(index, 1);
};

// Watch for product changes to populate form for editing
watch(() => props.product, (newProduct) => {
    if (newProduct && newProduct.id) {
        form.id = newProduct.id;
        form.name = newProduct.name;
        form.description = newProduct.description;
        form.price = Number(newProduct.price);
        form.pack = newProduct.pack;
        form.benefits = newProduct.benefits;
        form.category_id = newProduct.category_id;
        form.rating = Number(newProduct.rating || 4.5);
        form.calories = Number(newProduct.calories || 0);

        // Parse nutrition object to array
        nutritionItems.value = [];
        if (newProduct.nutrition) {
            Object.entries(newProduct.nutrition).forEach(([key, value]) => {
                nutritionItems.value.push({ key, value });
            });
        }

        form.image = null;
        previewImage.value = newProduct.image;
    }
}, { immediate: true });

// Watch for dialog visibility to reset form when opening for new product
watch(() => props.visible, (isVisible) => {
    if (isVisible && !props.product?.id) {
        resetForm();
    }
});

const resetForm = () => {
    form.reset();
    form.clearErrors();
    previewImage.value = null;
    submitted.value = false;
};

const hideDialog = () => {
    emit('update:visible', false);
    resetForm();
};

const saveProduct = () => {
    submitted.value = true;

    if (!form.name?.trim()) return;

    updateNutritionForm();

    const onSuccess = (detail) => {
        toast.add({ severity: 'success', summary: 'Successful', detail, life: 3000 });
        emit('update:visible', false);
        emit('saved');
        resetForm();
    };

    if (form.id) {
        form.transform((data) => ({ ...data, _method: 'PUT' }))
            .post(route('products.update', form.id), {
                onSuccess: () => onSuccess('Product Updated')
            });
    } else {
        form.post(route('products.store'), {
            onSuccess: () => onSuccess('Product Created')
        });
    }
};

const onFileSelect = (event) => {
    form.image = event.files[0];
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.value = e.target.result;
    };
    reader.readAsDataURL(event.files[0]);
};

const dialogTitle = () => form.id ? 'Edit Product' : 'Add New Product';
const dialogSubtitle = () => form.id ? 'Update product details' : 'Create a new product entry';
const submitLabel = () => form.id ? 'Update Product' : 'Create Product';
</script>

<template>
    <AppFormDialog :visible="visible" @update:visible="$emit('update:visible', $event)" :title="dialogTitle()"
        :subtitle="dialogSubtitle()" icon="pi-box" :submitLabel="submitLabel()" :loading="form.processing"
        @submit="saveProduct" @cancel="hideDialog">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Image Upload Section -->
            <div class="lg:col-span-1">
                <div
                    class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-4 border-2 border-dashed border-slate-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                        <i class="pi pi-image mr-2"></i>Product Image
                    </label>
                    <div class="aspect-square rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-700 mb-4">
                        <img v-if="previewImage" :src="previewImage" alt="Preview" class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex flex-col items-center justify-center">
                            <i class="pi pi-cloud-upload text-4xl text-slate-300 dark:text-slate-500 mb-2"></i>
                            <span class="text-sm text-slate-400">No image selected</span>
                        </div>
                    </div>
                    <FileUpload mode="basic" name="image" accept="image/*" :maxFileSize="1000000" @select="onFileSelect"
                        :auto="false" chooseLabel="Choose Image"
                        class="w-full [&_.p-button]:w-full! [&_.p-button]:rounded-xl! [&_.p-button]:bg-primary-500! [&_.p-button]:border-primary-500! [&_.p-button]:text-white! hover:[&_.p-button]:bg-primary-600!" />
                </div>
            </div>

            <!-- Form Fields Section -->
            <div class="lg:col-span-2 space-y-5">
                <!-- Product Name -->
                <div class="space-y-2">
                    <label for="name"
                        class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <i class="pi pi-tag text-primary-500"></i>
                        Product Name <span class="text-red-500">*</span>
                    </label>
                    <InputText id="name" v-model.trim="form.name" required="true" autofocus
                        :invalid="submitted && !form.name"
                        class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors!"
                        placeholder="Enter product name" />
                    <small class="text-red-500 text-xs flex items-center gap-1" v-if="submitted && !form.name">
                        <i class="pi pi-exclamation-circle"></i> Name is required.
                    </small>
                </div>

                <!-- Description -->
                <div class="space-y-2">
                    <label for="description"
                        class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <i class="pi pi-align-left text-primary-500"></i>
                        Description
                    </label>
                    <Textarea id="description" v-model="form.description" rows="3"
                        class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors! resize-none!"
                        placeholder="Enter product description..." />
                </div>

                <!-- Category & Calories & Rating -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <i class="pi pi-tags text-primary-500"></i> Category
                        </label>
                        <Select filter v-model="form.category_id" :options="categories" optionLabel="name" optionValue="id"
                            placeholder="Select Category"
                            class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors!" />
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <i class="pi pi-bolt text-primary-500"></i> Calories
                        </label>
                        <InputNumber v-model="form.calories" suffix=" kcal"
                            class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors!" />
                    </div>
                    <div class="space-y-2">
                        <label class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <i class="pi pi-star text-primary-500"></i> Rating
                        </label>
                        <InputNumber v-model="form.rating" :min="0" :max="5" :maxFractionDigits="1" :step="0.1"
                            class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors!" />
                    </div>
                </div>

                <!-- Price & Pack Size Grid -->
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="price"
                            class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <i class="pi pi-indian-rupee text-primary-500"></i>
                            Price
                        </label>
                        <InputNumber id="price" v-model="form.price" mode="currency" currency="INR" locale="en-IN"
                            class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors!" />
                    </div>
                    <div class="space-y-2">
                        <label for="pack"
                            class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                            <i class="pi pi-box text-primary-500"></i>
                            Pack Size
                        </label>
                        <InputText id="pack" v-model="form.pack"
                            class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors! resize-none!"
                            placeholder="e.g., 500g, 1kg" />
                    </div>
                </div>

                <!-- Nutrition Builder -->
                <div class="space-y-2">
                    <label
                        class="flex items-center justify-between text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <span class="flex items-center gap-2"><i class="pi pi-chart-pie text-primary-500"></i> Nutrition
                            Facts</span>
                        <Button icon="pi pi-plus" size="small" rounded text label="Add Nutrient" @click="addNutrient" />
                    </label>

                    <div v-for="(item, index) in nutritionItems" :key="index" class="flex gap-2 mb-2">
                        <InputText v-model="item.key" placeholder="Name (e.g. Protein)" class="flex-1 rounded-xl!" />
                        <InputText v-model="item.value" placeholder="Value (e.g. 12g)" class="flex-1 rounded-xl!" />
                        <Button icon="pi pi-trash" rounded text severity="danger" @click="removeNutrient(index)" />
                    </div>
                    <div v-if="nutritionItems.length === 0"
                        class="text-xs text-slate-400 italic text-center py-2 bg-slate-50 dark:bg-slate-800 rounded-lg">
                        No nutrition data added. Click 'Add Nutrient' to start.
                    </div>
                </div>

                <!-- Benefits -->
                <div class="space-y-2">
                    <label for="benefits"
                        class="flex items-center gap-2 text-sm font-semibold text-slate-700 dark:text-slate-300">
                        <i class="pi pi-heart text-primary-500"></i>
                        Health Benefits
                    </label>
                    <Textarea id="benefits" v-model="form.benefits" rows="2"
                        class="w-full! rounded-xl! border-slate-200! dark:border-slate-700! focus:border-primary-500! transition-colors! resize-none!"
                        placeholder="List the health benefits..." />
                </div>
            </div>
        </div>
    </AppFormDialog>
</template>
