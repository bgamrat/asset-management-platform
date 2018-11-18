// assets/app.js

// Thanks to: https://github.com/igeligel/vuex-simple-structure

import Vue from 'vue';
import App from './App';
import router from './router';
import store from './store';

import Example from './components/User/Example';
import BootstrapVue from 'bootstrap-vue';
import { Button, Form, Layout, Modal, Navbar  } from 'bootstrap-vue/es/components';
import { i18n } from './plugins/i18n.js';

Vue.use(BootstrapVue);
Vue.use(Button);
Vue.use(Form);
Vue.use(Layout);
Vue.use(Modal);
Vue.use(Navbar);

Vue.config.productionTip = false;

//import './components/globals'

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  i18n,
  template: '<App />',
  components: { App },
});