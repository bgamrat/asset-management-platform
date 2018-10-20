// assets/js/app.js
import 'es6-promise/auto'

        import Vue from 'vue'
        import Vuex from 'vuex'

        import Example from './components/Example'

        import BootstrapVue from 'bootstrap-vue'
        import 'bootstrap/dist/css/bootstrap.css'
        import 'bootstrap-vue/dist/bootstrap-vue.css'
        import { Navbar,Form } from 'bootstrap-vue/es/components';
        import { Layout } from 'bootstrap-vue/es/components';
 import { Button } from 'bootstrap-vue/es/components';

Vue.use(Button);
Vue.use(Layout);

Vue.use(Vuex)
Vue.use(BootstrapVue)
Vue.use(Navbar)
Vue.use(Form);
Vue.use(Layout);

/**
 * Create a fresh Vue Application instance
 */

new Vue({
    el: '#app',
    components: {example: Example}
});

/* Yay */
