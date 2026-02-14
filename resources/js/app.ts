import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// PrimeVue imports
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import Tooltip from 'primevue/tooltip';
import 'primeicons/primeicons.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: '.dark',
                    }
                }
            })
            .use(ToastService)
            .use(ConfirmationService)
            .use(ToastService)
            .use(ConfirmationService)
            .directive('tooltip', Tooltip)
            .mixin({
                methods: {
                    asset(path: string) {
                        const assetUrl = (this.$page.props.asset_url as string) || '';
                        const cleanPath = path.replace(/^\/+/, '');
                        if (assetUrl) {
                            const cleanUrl = assetUrl.replace(/\/+$/, '');
                            return `${cleanUrl}/${cleanPath}`;
                        }
                        return `/${cleanPath}`;
                    }
                }
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
