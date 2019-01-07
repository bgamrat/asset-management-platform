<template>
    <div>
        <div class="block">
                <table class="table table-sm table-hover">
                <thead>
                    <tr class="font-weight-bold a-header">
                        <th scope="col"> {{ $t('available') }}</th>
                        <th scope="col"> {{ $t('name') }}</th>
                        <th scope="col"> {{ $t('comment') }}</th>
                        <th scope="col"> {{ $t('default') }}</th>
                        <th scope="col"> {{ $t('in_use') }}</th>
                        <th scope="col"> {{ $t('remove') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <asset-status-row v-for="(item, key, index) in items" :key="item.id" :item="item"
                        v-on:changed="update"
                        v-on:remove="removeRow"></asset-status-row>
                </tbody>
            </table>
        </div>
        <addrow v-on:add-row="addRow" />
        <save-button v-on:save="save" />
    </div>
</template>

<script>

import { mapState } from 'vuex'

import AddRow from '../../components/Common/AddRow'
import AdminAssetStatusRow from '../../components/Admin/Asset/AssetStatusRow'
import SaveButton from '../../components/Common/SaveButton'

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
            this.$store.dispatch('admin_asset_asset_status/add');
        },
        update(value) {
            this.$store.dispatch('admin_asset_asset_status/update',value)
        },
        removeRow(index) {
            console.log(arguments);
            this.$store.dispatch('admin_asset_asset_status/remove',index)
        },
        save(){
            this.$store.dispatch('common_message/hideMessage');
            this.$store.dispatch('admin_asset_asset_status/save')
                    .then(() => {
                        this.$store.dispatch('common_message/setMessage', {variant:'success', message:this.$i18n.t('success'), visible:true});
                        this.$store.dispatch('admin_asset_asset_status/clean');}
                    ,(err) => {
                        this.$store.dispatch('common_message/setMessage', {variant:'danger', message:err.message, visible:true})})
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