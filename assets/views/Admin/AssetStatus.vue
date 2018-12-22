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
            <asset-status-row v-for="(item, key, index) in items" :key="item.id" :item="item"
            v-on:changed="update"
            v-on:remove="removeRow(index)" />
        </div>
        <addrow v-on:add-row="addRow" />
        <save-button v-on:save="save" />
    </div>
</template>

<script>

import { mapState } from 'vuex'
import AdminAssetStatusRow from '../../components/Admin/Asset/AssetStatusRow';
import AddRow from '../../components/Common/AddRow';
import SaveButton from '../../components/Common/SaveButton';

export default {
    name: 'AdminAssetStatusView', components: {
        'addrow': AddRow, 'asset-status-row': AdminAssetStatusRow, 'save-button': SaveButton
    }, beforeCreate() {
        this.$store.dispatch('admin_asset_asset_status/load');
    }, computed:
        mapState({
            items: state => state.admin_asset_asset_status.items
        }), methods: {
        addRow() {
            this.$store.dispatch('admin_asset_asset_status/add')
        },
        update(value) {
            this.$store.dispatch('admin_asset_asset_status/update',value)
        },
        removeRow(index) {
            this.$store.dispatch('admin_asset_asset_status/remove',index)
        },
        save(){
            this.$store.dispatch('admin_asset_asset_status/save')
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