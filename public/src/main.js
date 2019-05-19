import Vue from 'vue';
import App from './App.vue';
import FlashMessage from '@smartweb/vue-flash-message';
Vue.use(FlashMessage, { time: 4000 });
/**
 * Event Bus
 * @type {CombinedVueInstance<V extends Vue, Object, Object, Object, Record<never, any>>}
 */
window.events = new Vue();

new Vue({
    el: '#app',
    render: h => h(App)
});