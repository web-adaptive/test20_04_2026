<template>
    <MainLayout>
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="text-2xl font-bold text-slate-900">Управление товарами</h1>
            <el-button type="primary" @click="router.visit('/admin/products/create')">Добавить товар</el-button>
        </div>

        <el-alert v-if="!token" type="warning" title="Требуется вход" show-icon class="mb-4">
            <Link href="/login" class="text-blue-600 underline">Перейти на страницу входа</Link>
        </el-alert>

        <el-skeleton v-else-if="loading" :rows="6" animated />

        <div v-else-if="error" class="rounded border border-red-200 bg-red-50 p-4 text-red-800">{{ error }}</div>

        <div v-else>
            <el-table :data="items" stripe style="width: 100%">
                <el-table-column prop="name" label="Название" min-width="160" />
                <el-table-column label="Категория" width="140">
                    <template #default="{ row }">
                        {{ row.category?.name }}
                    </template>
                </el-table-column>
                <el-table-column label="Цена" width="120">
                    <template #default="{ row }">
                        {{ formatPrice(row.price) }}
                    </template>
                </el-table-column>
                <el-table-column label="Описание" min-width="200" show-overflow-tooltip>
                    <template #default="{ row }">
                        {{ row.description }}
                    </template>
                </el-table-column>
                <el-table-column label="" width="200" fixed="right">
                    <template #default="{ row }">
                        <el-button size="small" @click="router.visit(`/admin/products/${row.id}/edit`)">
                            Редактировать
                        </el-button>
                        <el-button size="small" type="danger" @click="confirmDelete(row)">Удалить</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div v-if="meta.last_page > 1" class="mt-6 flex justify-center">
                <el-pagination
                    v-model:current-page="page"
                    background
                    layout="prev, pager, next"
                    :page-size="meta.per_page"
                    :total="meta.total"
                    @current-change="load"
                />
            </div>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { ElMessageBox } from 'element-plus';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useAuth } from '@/composables/useAuth';
import { useProductApi } from '@/composables/useProductApi';

const { token } = useAuth();
const { fetchProducts, deleteProduct } = useProductApi();

const loading = ref(false);
const error = ref('');
const items = ref([]);
const page = ref(1);
const meta = ref({ last_page: 1, per_page: 12, total: 0 });

function formatPrice(v) {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(Number(v));
}

async function load() {
    if (!token.value) {
        return;
    }
    loading.value = true;
    error.value = '';
    try {
        const data = await fetchProducts({ page: page.value });
        items.value = data.data || [];
        meta.value = {
            current_page: data.meta?.current_page ?? 1,
            last_page: data.meta?.last_page ?? 1,
            per_page: data.meta?.per_page ?? 12,
            total: data.meta?.total ?? 0,
        };
    } catch (e) {
        if (e.status === 401) {
            error.value = 'Сессия истекла — войдите снова';
        } else {
            error.value = e.body?.message || e.message;
        }
        items.value = [];
    } finally {
        loading.value = false;
    }
}

function confirmDelete(row) {
    ElMessageBox.confirm(`Удалить «${row.name}»?`, 'Подтверждение', {
        confirmButtonText: 'Удалить',
        cancelButtonText: 'Отмена',
        type: 'warning',
    })
        .then(async () => {
            await deleteProduct(row.id);
            await load();
        })
        .catch(() => {});
}

onMounted(() => {
    if (token.value) {
        load();
    }
});

watch(token, (t) => {
    if (t) {
        load();
    } else {
        items.value = [];
    }
});
</script>
