// assets/js/app.js
import 'es6-promise/auto'
import Vue from 'vue'
import Vuex from 'vuex'
import Example from './user/components/Example'
import BootstrapVue from 'bootstrap-vue'
import { Navbar, Form, Layout, Button } from 'bootstrap-vue/es/components'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(Button);
Vue.use(Layout);
Vue.use(Vuex)
Vue.use(BootstrapVue)
Vue.use(Navbar)
Vue.use(Form);

/**
 * Create a fresh Vue Application instance
 */

new Vue({
    el: '#app',
    components: {example: Example}
});

/* Yay */
