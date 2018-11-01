import Vue from 'vue';
import Router from 'vue-router';
import AdminAssetStatusView from '../views/Admin/AssetStatus';

Vue.use(Router);

export default new Router({
  routes: [
    {
      path: '/admin/asset/asset-status/',
      name: 'AdminAssetStatus',
      components: {AdminAssetStatusView},
    }
  ],
});