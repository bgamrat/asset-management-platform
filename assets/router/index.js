import Vue from 'vue';
import Router from 'vue-router';
import AdminAssetStatusView from '../views/Admin/AssetStatus';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/',
      name: 'AdminAssetStatus',
      component: AdminAssetStatusView,
      props: { label: 'Add Row' }
    }
  ],
});