<template>
    <div>
        <div class="block">
        <b-row class="font-weight-bold header">
            <b-col col sm="1"> {{ $t('available') }}</b-col>
            <b-col col sm="3"> {{ $t('name') }}</b-col>
            <b-col col sm="5"> {{ $t('comment') }}</b-col>
            <b-col col sm="1"> {{ $t('default') }}</b-col>
            <b-col col sm="1"> {{ $t('in_use') }}</b-col>
            <b-col col sm="1"><i class="fa fa-delete"></i></b-col>
        </b-row>
        <asset-status-row v-for="item in items" :key="item.id" :item="item" />
        </div>
        <addrow v-on:add-row="addRow" />
    </div>
</template>

<script>

import { mapState } from 'vuex'
import AdminAssetStatusRow from '../../components/Admin/Asset/AssetStatusRow';
import AddRow from '../../components/Common/AddRow';

export default {
    name: 'AdminAssetStatusView', components: {
        'addrow': AddRow, 'asset-status-row': AdminAssetStatusRow
    }, beforeCreate() {
        this.$store.dispatch('admin_asset_asset_status/load');
    }, computed:
        mapState({
            items: state => state.admin_asset_asset_status.items
        }), methods: {
        addRow() {
            this.$store.dispatch('admin_asset_asset_status/add')
        }
    }
};
</script>

<style lang="scss" scoped>
.header > * {
    text-align: center;
}
.block {
    padding: 7px 0;
    margin: 7px 0;
    border-top: 1px solid theme-color-level(dark, -10);
    border-bottom: 1px solid theme-color-level(dark, -10);
}
</style>