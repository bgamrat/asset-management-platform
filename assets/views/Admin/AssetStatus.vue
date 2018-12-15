<template>
    <div>
        <div class="block">
            <b-row class="font-weight-bold a-header">
                <b-col cols="3" sm="1"> {{ $t('available') }}</b-col>
                <b-col cols="9" sm="3"> {{ $t('name') }}</b-col>
                <b-col cols="9" sm="5"> {{ $t('comment') }}</b-col>
                <b-col cols="1" sm="1"> {{ $t('default') }}</b-col>
                <b-col cols="1" sm="1"> {{ $t('in_use') }}</b-col>
                <b-col cols="1" sm="1"> {{ $t('remove') }}</b-col>
            </b-row>
            <asset-status-row v-for="(item, key, index) in items" :key="item.id" :item="item" v-on:remove="items.splice(index, 1)" />
        </div>
        <addrow v-on:add-row="addRow" />
        <update-button v-on:update="update" />
    </div>
</template>

<script>

import { mapState } from 'vuex'
import AdminAssetStatusRow from '../../components/Admin/Asset/AssetStatusRow';
import AddRow from '../../components/Common/AddRow';
import UpdateButton from '../../components/Common/UpdateButton';

export default {
    name: 'AdminAssetStatusView', components: {
        'addrow': AddRow, 'asset-status-row': AdminAssetStatusRow, 'update-button': UpdateButton
    }, beforeCreate() {
        this.$store.dispatch('admin_asset_asset_status/load');
    }, computed:
        mapState({
            items: state => state.admin_asset_asset_status.items
        }), methods: {
        addRow() {
            this.$store.dispatch('admin_asset_asset_status/add')
        },
        update(){
            this.$store.dispatch('admin_asset_asset_status/update')
        }
    }
};
</script>

<style lang="scss" scoped>
.a-header {
    margin-bottom: 7px;
    div {
        text-align: center;
    }
}
.block {
    border-top: 1px solid theme-color-level(dark, -10);
    border-bottom: 1px solid theme-color-level(dark, -10);
}
</style>