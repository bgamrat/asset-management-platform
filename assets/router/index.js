import Vue from 'vue';
import Router from 'vue-router';
import AdminAssetStatusView from '../views/Admin/AssetStatus';
import Login from '../views/Public/Login';

Vue.use(Router);

export default new Router({
  routes: [
      {
        path: '/',
        name: 'Home',
        component: Home,
      },
    {
      path: '/',
      name: 'AdminAssetStatus',
      component: AdminAssetStatusView,
      props: { label: 'Add Row' }
    },
    {
        path: '/login',
        name: 'Login',
        component: Login
    }
  ],
});