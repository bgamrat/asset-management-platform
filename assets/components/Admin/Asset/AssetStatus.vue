<template>
    <div>
        <b-row  class="font-weight-bold">
            <b-col cols="1" class="text-center"> {{ $t('available') }}</b-col>
            <b-col cols="4"> {{ $t('name') }}</b-col>
            <b-col cols="4"> {{ $t('comment') }}</b-col>
            <b-col cols="1" class="text-center"> {{ $t('default') }}</b-col>
            <b-col cols="1" class="text-center"> {{ $t('in_use') }}</b-col>
        </b-row>
        <asset-status-row />
        <div v-on:add-row="addrow">
            <div id="addrow">
                <addrow :label="label"></addrow>
            </div>
        </div>
    </div>
</template>

<script>
import Vue from 'vue';
import AddRow from '../../Common/AddRow';
import AssetStatusRow from './AssetStatusRow'

export default {
    name: 'AdminAssetStatus',
    components: {
        'addrow': AddRow,
        'asset-status-row': AssetStatusRow
    },
props: {label: String},
   computed: {
  }, mounted() {
  }, 
  actions: { addrow() { alert('someotheraction');}},
methods:{addrow() { alert('AssetStatus'); }
}};
</script>

<style  scoped>

</style>

