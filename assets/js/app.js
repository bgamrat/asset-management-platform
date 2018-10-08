// assets/js/app.js
import Vue from 'vue';

import Example from './components/Example'

import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import { Navbar } from 'bootstrap-vue/es/components';

Vue.use(BootstrapVue);
Vue.use(Navbar);
/**
 * Create a fresh Vue Application instance
 */
new Vue({
    el: '#app',
    components: {Example, Navbar}
});



