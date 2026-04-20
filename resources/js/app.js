import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import ru from 'element-plus/es/locale/lang/ru';

createInertiaApp({
    title: (title) => (title ? `${title} — ${import.meta.env.VITE_APP_NAME || 'Каталог'}` : (import.meta.env.VITE_APP_NAME || 'Каталог')),
    resolve: (name) => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true });
        const path = `./Pages/${name}.vue`;
        const page = pages[path];
        if (!page) {
            throw new Error(`Unknown page: ${name}`);
        }
        return page.default;
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.use(plugin);
        app.use(ElementPlus, { locale: ru });
        app.mount(el);
    },
    progress: {
        color: '#409eff',
    },
});
