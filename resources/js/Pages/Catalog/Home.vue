<template>
    <MainLayout>
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Каталог товаров</h1>
                <p class="text-slate-500">Фильтр по категории и поиск с задержкой</p>
            </div>
            <div class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row">
                <el-select
                    v-model="categoryId"
                    clearable
                    placeholder="Все категории"
                    class="!w-full sm:!w-56"
                    @change="reloadPageOne"
                >
                    <el-option
                        v-for="c in categories"
                        :key="c.id"
                        :label="c.name"
                        :value="c.id"
                    />
                </el-select>
                <el-input
                    v-model="searchInput"
                    clearable
                    placeholder="Поиск по названию и описанию..."
                    class="!w-full sm:!w-72"
                />
            </div>
        </div>

        <el-skeleton v-if="loading" :rows="6" animated />

        <div v-else-if="error" class="rounded-lg border border-red-200 bg-red-50 p-4 text-red-800">
            {{ error }}
        </div>

        <div v-else class="space-y-4">
            <el-empty v-if="!items.length" description="Ничего не найдено" />
            <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <el-card
                    v-for="p in items"
                    :key="p.id"
                    shadow="hover"
                    class="cursor-pointer transition hover:-translate-y-0.5"
                    @click="goProduct(p.id)"
                >
                    <template #header>
                        <div class="flex items-start justify-between gap-2">
                            <span class="font-semibold text-slate-900">{{ p.name }}</span>
                            <el-tag size="small" type="info">{{ p.category?.name }}</el-tag>
                        </div>
                    </template>
                    <p class="line-clamp-3 text-sm text-slate-600">{{ p.description || '—' }}</p>
                    <div class="mt-4 text-lg font-bold text-blue-600">
                        {{ formatPrice(p.price) }}
                    </div>
                </el-card>
            </div>

            <div v-if="meta.last_page > 1" class="flex justify-center pt-4">
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
import { router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useProductApi } from '@/composables/useProductApi';
import { useDebouncedRef } from '@/composables/useDebounce';

function goProduct(id) {
    router.visit(`/product/${id}`);
}

const { fetchProducts, fetchCategories } = useProductApi();

const categories = ref([]);
const categoryId = ref(null);
const searchInput = ref('');
const searchDebounced = useDebouncedRef(searchInput, 400);

const loading = ref(true);
const error = ref('');
const items = ref([]);
const page = ref(1);
const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: 12,
    total: 0,
});

function formatPrice(v) {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(Number(v));
}

async function load() {
    loading.value = true;
    error.value = '';
    try {
        const data = await fetchProducts({
            page: page.value,
            categoryId: categoryId.value || null,
            q: searchDebounced.value || null,
        });
        items.value = data.data || [];
        meta.value = {
            current_page: data.meta?.current_page ?? 1,
            last_page: data.meta?.last_page ?? 1,
            per_page: data.meta?.per_page ?? 12,
            total: data.meta?.total ?? 0,
        };
    } catch (e) {
        error.value = e.body?.message || e.message || 'Ошибка загрузки';
        items.value = [];
    } finally {
        loading.value = false;
    }
}

function reloadPageOne() {
    page.value = 1;
    load();
}

onMounted(async () => {
    try {
        const catData = await fetchCategories();
        categories.value = catData.data || [];
    } catch {
        categories.value = [];
    }
    await load();
});

watch(searchDebounced, () => {
    page.value = 1;
    load();
});
</script>
