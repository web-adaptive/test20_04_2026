<template>
    <el-container class="min-h-screen bg-slate-50">
        <el-header class="flex items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm">
            <div class="flex items-center gap-6">
                <Link href="/" class="text-lg font-semibold text-slate-800">Каталог</Link>
                <nav class="hidden gap-4 sm:flex">
                    <Link href="/" class="text-slate-600 hover:text-blue-600">Товары</Link>
                    <Link v-if="isAuthed" href="/admin/products" class="text-slate-600 hover:text-blue-600">
                        Управление товарами
                    </Link>
                </nav>
            </div>
            <div class="flex items-center gap-2">
                <template v-if="!isAuthed">
                    <Link href="/login">
                        <el-button type="primary" plain>Вход</el-button>
                    </Link>
                </template>
                <template v-else>
                    <el-button type="danger" plain @click="logout">Выйти</el-button>
                </template>
            </div>
        </el-header>
        <el-main class="mx-auto w-full max-w-6xl px-4 py-8">
            <slot />
        </el-main>
    </el-container>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAuth } from '@/composables/useAuth';

const { token, clearToken } = useAuth();

const isAuthed = computed(() => Boolean(token.value));

function logout() {
    clearToken();
    router.visit('/');
}
</script>
