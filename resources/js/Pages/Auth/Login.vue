<template>
    <MainLayout>
        <div class="mx-auto max-w-md rounded-xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="mb-6 text-center text-xl font-bold text-slate-900">Вход администратора</h1>
            <el-form label-position="top" @submit.prevent="submit">
                <el-form-item label="Email" :error="fieldError('email')">
                    <el-input v-model="form.email" type="email" autocomplete="username" />
                </el-form-item>
                <el-form-item label="Пароль" :error="fieldError('password')">
                    <el-input v-model="form.password" type="password" show-password autocomplete="current-password" />
                </el-form-item>
                <el-alert v-if="formError" :title="formError" type="error" class="mb-4" show-icon />
                <el-button type="primary" class="w-full" native-type="submit" :loading="submitting">
                    Войти
                </el-button>
            </el-form>
        </div>
    </MainLayout>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { router } from '@inertiajs/vue3';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useAuth } from '@/composables/useAuth';

const { setToken } = useAuth();

const form = reactive({
    email: '',
    password: '',
});

const submitting = ref(false);
const formError = ref('');
const errors = ref({});

function fieldError(key) {
    return errors.value[key]?.[0] || '';
}

async function submit() {
    submitting.value = true;
    formError.value = '';
    errors.value = {};
    try {
        const res = await fetch('/api/login', {
            method: 'POST',
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(form),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            if (data.errors) {
                errors.value = data.errors;
            }
            formError.value = data.message || 'Неверные учётные данные';
            return;
        }
        setToken(data.token);
        router.visit('/admin/products');
    } catch (e) {
        formError.value = e.message || 'Ошибка сети';
    } finally {
        submitting.value = false;
    }
}
</script>
