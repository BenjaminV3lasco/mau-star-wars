import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import AppLayout from './Components/Templates/AppLayout.vue';

const pages = import.meta.glob('./Pages/**/*.vue');

createInertiaApp({
    resolve: async (name) => {
        const page = await pages['./Pages/' + name + '.vue']();
        page.default.layout = AppLayout;
        return page;
    },
    progress: { color: '#ffe15a' },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) }).use(plugin).mount(el);
    },
});
