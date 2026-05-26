import './bootstrap';
import { createApp } from 'vue';

window.axios.defaults.headers.common['Accept'] = 'application/json';

const pages = import.meta.glob('./Pages/*.vue');

const path = window.location.pathname;
const name = path === '/' ? 'App' : path.slice(1);
const component = name.charAt(0).toUpperCase() + name.slice(1);

pages[`./Pages/${component}.vue`]().then(({ default: Page }) => {
    createApp(Page).mount('#app');
});
