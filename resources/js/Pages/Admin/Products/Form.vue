<template>
    <MainLayout>
        <el-button class="mb-6" text type="primary" @click="router.visit('/admin/products')">← К списку</el-button>

        <div class="mx-auto max-w-xl rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="mb-6 text-xl font-bold text-slate-900">
                {{ mode === 'create' ? 'Новый товар' : 'Редактирование' }}
            </h1>

            <el-alert v-if="!token" type="warning" class="mb-4" show-icon title="Нужна авторизация">
                <Link href="/login" class="text-blue-600 underline">Войти</Link>
            </el-alert>

            <el-form v-else label-position="top" @submit.prevent="save">
                <el-form-item label="Название" required :error="fieldError('name')">
                    <el-input v-model="form.name" minlength="1" />
                </el-form-item>
                <el-form-item label="Категория" required :error="fieldError('category_id')">
                    <el-select v-model="form.category_id" class="w-full" placeholder="Выберите категорию">
                        <el-option
                            v-for="c in categories"
                            :key="c.id"
                            :label="c.name"
                            :value="c.id"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item label="Описание" :error="fieldError('description')">
                    <el-input v-model="form.description" type="textarea" :rows="4" />
                </el-form-item>
                <el-form-item label="Цена" required :error="fieldError('price')">
                    <el-input-number v-model="form.price" :min="0.01" :step="0.01" class="!w-full" />
                </el-form-item>
                <el-alert v-if="formError" :title="formError" type="error" class="mb-4" show-icon />
                <el-button type="primary" native-type="submit" :loading="saving">Сохранить</el-button>
            </el-form>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useAuth } from '@/composables/useAuth';
import { useProductApi } from '@/composables/useProductApi';

const props = defineProps({
    mode: { type: String, required: true },
    productId: { type: Number, default: null },
});

const { token } = useAuth();
const { fetchCategories, fetchProduct, createProduct, updateProduct } = useProductApi();

const categories = ref([]);
const saving = ref(false);
const formError = ref('');
const errors = ref({});

const form = reactive({
    name: '',
    description: '',
    price: 1,
    category_id: null,
});

function fieldError(key) {
    return errors.value[key]?.[0] || '';
}

async function save() {
    if (!token.value) {
        return;
    }
    saving.value = true;
    formError.value = '';
    errors.value = {};
    const payload = {
        name: form.name,
        description: form.description || null,
        price: form.price,
        category_id: form.category_id,
    };
    try {
        if (props.mode === 'create') {
            await createProduct(payload);
        } else {
            await updateProduct(props.productId, payload);
        }
        router.visit('/admin/products');
    } catch (e) {
        if (e.body?.errors) {
            errors.value = e.body.errors;
        }
        formError.value = e.body?.message || e.message;
    } finally {
        saving.value = false;
    }
}

onMounted(async () => {
    try {
        const data = await fetchCategories();
        categories.value = data.data || [];
    } catch {
        categories.value = [];
    }
    if (props.mode === 'edit' && props.productId) {
        try {
            const data = await fetchProduct(props.productId);
            const p = data.data;
            form.name = p.name;
            form.description = p.description || '';
            form.price = Number(p.price);
            form.category_id = p.category_id;
        } catch (e) {
            formError.value = 'Не удалось загрузить товар';
        }
    }
});
</script>
