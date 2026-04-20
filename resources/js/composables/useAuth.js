import { ref, watch } from 'vue';

const STORAGE_KEY = 'catalog_auth_token';

const token = ref(
    typeof window !== 'undefined' ? localStorage.getItem(STORAGE_KEY) || '' : ''
);

watch(token, (v) => {
    if (typeof window === 'undefined') return;
    if (v) {
        localStorage.setItem(STORAGE_KEY, v);
    } else {
        localStorage.removeItem(STORAGE_KEY);
    }
});

export function useAuth() {
    function setToken(t) {
        token.value = t || '';
    }

    function clearToken() {
        token.value = '';
    }

    function authHeader() {
        return token.value ? { Authorization: `Bearer ${token.value}` } : {};
    }

    return {
        token,
        setToken,
        clearToken,
        authHeader,
    };
}
