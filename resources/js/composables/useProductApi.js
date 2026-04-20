import { useAuth } from './useAuth';

async function parseJsonResponse(response) {
    const text = await response.text();
    let data = null;
    try {
        data = text ? JSON.parse(text) : null;
    } catch {
        data = { message: text };
    }
    if (!response.ok) {
        const err = new Error(data?.message || response.statusText || 'Request failed');
        err.status = response.status;
        err.body = data;
        throw err;
    }
    return data;
}

export function useProductApi() {
    const { authHeader } = useAuth();

    const jsonHeaders = {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    };

    async function fetchProducts({ page = 1, categoryId = null, q = null } = {}) {
        const params = new URLSearchParams();
        params.set('page', String(page));
        if (categoryId) {
            params.set('category_id', String(categoryId));
        }
        if (q && String(q).trim()) {
            params.set('q', String(q).trim());
        }
        const res = await fetch(`/api/products?${params.toString()}`, {
            headers: { ...jsonHeaders },
        });
        return parseJsonResponse(res);
    }

    async function fetchProduct(id) {
        const res = await fetch(`/api/products/${id}`, {
            headers: { ...jsonHeaders },
        });
        return parseJsonResponse(res);
    }

    async function fetchCategories() {
        const res = await fetch('/api/categories', {
            headers: { ...jsonHeaders },
        });
        return parseJsonResponse(res);
    }

    async function createProduct(payload) {
        const res = await fetch('/api/products', {
            method: 'POST',
            headers: { ...jsonHeaders, ...authHeader() },
            body: JSON.stringify(payload),
        });
        return parseJsonResponse(res);
    }

    async function updateProduct(id, payload) {
        const res = await fetch(`/api/products/${id}`, {
            method: 'PUT',
            headers: { ...jsonHeaders, ...authHeader() },
            body: JSON.stringify(payload),
        });
        return parseJsonResponse(res);
    }

    async function deleteProduct(id) {
        const res = await fetch(`/api/products/${id}`, {
            method: 'DELETE',
            headers: { ...jsonHeaders, ...authHeader() },
        });
        if (res.status === 204) {
            return true;
        }
        return parseJsonResponse(res);
    }

    return {
        fetchProducts,
        fetchProduct,
        fetchCategories,
        createProduct,
        updateProduct,
        deleteProduct,
    };
}
