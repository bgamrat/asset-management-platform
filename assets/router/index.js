import Vue from 'vue';
import Router from 'vue-router';
import AdminAssetStatusView from '../views/Admin/AssetStatus';
import Home from '../views/Public/Home';
import Login from '../views/Public/Login';

Vue.use(Router);

export default new Router({
    routes: [
        {
            path: '/',
            name: 'home',
            component: Home,
        },
        {
            path: '/admin/asset/asset-status',
            name: 'admin-asset-status',
            component: AdminAssetStatusView,
            props: {label: 'Add Row'}
        },
        {
            path: '/login',
            name: 'login',
            component: Login
        }
    ],
});