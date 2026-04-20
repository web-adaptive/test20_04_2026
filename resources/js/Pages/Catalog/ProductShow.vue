<template>
    <MainLayout>
        <el-button class="mb-6" text type="primary" @click="router.visit('/')">
            ← К списку
        </el-button>

        <el-skeleton v-if="loading" :rows="8" animated />

        <el-result
            v-else-if="notFound"
            icon="warning"
            title="Товар не найден"
            sub-title="Возможно, он был удалён"
        >
            <template #extra>
                <el-button type="primary" @click="router.visit('/')">На главную</el-button>
            </template>
        </el-result>

        <div v-else-if="product" class="rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <div class="mb-2 flex flex-wrap items-center gap-2">
                <el-tag>{{ product.category?.name }}</el-tag>
                <span class="text-3xl font-bold text-slate-900">{{ product.name }}</span>
            </div>
            <p class="mb-6 text-2xl font-semibold text-blue-600">{{ formatPrice(product.price) }}</p>
            <p class="whitespace-pre-wrap text-slate-700 leading-relaxed">{{ product.description || 'Нет описания' }}</p>
        </div>
    </MainLayout>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useProductApi } from '@/composables/useProductApi';

const props = defineProps({
    productId: { type: Number, required: true },
});

const { fetchProduct } = useProductApi();

const loading = ref(true);
const product = ref(null);
const notFound = ref(false);

function formatPrice(v) {
    return new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'RUB' }).format(Number(v));
}

onMounted(async () => {
    try {
        const data = await fetchProduct(props.productId);
        product.value = data.data;
    } catch (e) {
        if (e.status === 404) {
            notFound.value = true;
        }
    } finally {
        loading.value = false;
    }
});
</script>
