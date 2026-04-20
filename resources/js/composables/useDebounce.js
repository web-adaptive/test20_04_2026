import { ref, watch } from 'vue';

/**
 * @template T
 * @param {import('vue').Ref<T>} source
 * @param {number} delayMs
 */
export function useDebouncedRef(source, delayMs = 350) {
    const debounced = ref(source.value);

    let timer = null;
    watch(
        source,
        (v) => {
            if (timer) {
                clearTimeout(timer);
            }
            timer = setTimeout(() => {
                debounced.value = v;
                timer = null;
            }, delayMs);
        },
        { flush: 'post' }
    );

    return debounced;
}
