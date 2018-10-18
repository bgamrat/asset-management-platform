// assets/js/app.js
import 'es6-promise/auto'

        import Vue from 'vue'
        import Vuex from 'vuex'

        import Example from './components/Example'
        import AppNav from './components/Public/Navigation'

        import BootstrapVue from 'bootstrap-vue'
        import 'bootstrap/dist/css/bootstrap.css'
        import 'bootstrap-vue/dist/bootstrap-vue.css'
        import { Navbar } from 'bootstrap-vue/es/components';

Vue.use(Vuex)
Vue.use(BootstrapVue)
Vue.use(Navbar)
/**
 * Create a fresh Vue Application instance
 */

new Vue({
    el: '#app',
    components: {example: Example}
});

/* Yay */
