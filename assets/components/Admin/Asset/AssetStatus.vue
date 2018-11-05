<template>
    <div v-on:add-row="addrow">
        <div id="addrow">
            <addrow :label="label"></addrow>
        </div>
    </div>
</template>

<script>
import Vue from 'vue';
import AddRow from '../../Common/AddRow';

export default {
    name: 'AdminAssetStatus',
    components: {
        'addrow': AddRow,
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

