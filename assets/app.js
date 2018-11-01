// assets/app.js

// Thanks to: https://github.com/igeligel/vuex-simple-structure

import Vue from 'vue';
import App from './App';
import router from './router';
import store from './store';

import Example from './components/User/Example'
import BootstrapVue from 'bootstrap-vue'
import { Navbar, Form, Layout, Button } from 'bootstrap-vue/es/components'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

Vue.use(Button);
Vue.use(Layout);
Vue.use(BootstrapVue)
Vue.use(Navbar)
Vue.use(Form);

Vue.config.productionTip = false;

import './components/globals'

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
});