<template>
    <tr class="asset-status-row">
        <td class="text-center">
            <input type="hidden" :value="localitem.index" />
            <input type="hidden" :value="localitem.id" />
            <b-form-checkbox v-model="localitem.available"></b-form-checkbox>
        </td>
        <td><b-form-input size="sm" type="text" v-model="localitem.name"></b-form-input></td>
        <td><b-form-input size="sm" type="text" v-model="localitem.comment"></b-form-input></td>
        <td class="text-center">
            <template v-if=localitem.default>
            <b-form-radio name="default"></b-form-radio>
            </template>
            <template v-else>
            <b-form-radio name="default" checked=false></b-form-radio>
            </template>
        </td>
        <td class="text-center"><b-form-checkbox v-model="localitem.inUse"></b-form-checkbox></td>
        <td class="text-center">
            <i class="fa fa-remove" v-on:click="$emit('remove',localitem.index)"></i>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'AdminAssetStatusRow',
    props: {
            item: { type : Object }
    },
    data() {
        return  { localitem : Object.assign({},this.item) }
    },
    watch: {
        localitem: { handler(value){
            this.$emit('changed',value);
        }, deep: true
        }
    }
}
</script>

<style scoped lang="scss">
@import "node_modules/bootstrap/scss/bootstrap";
.asset-status-row {
    margin: 5px 0;
    padding: 7px 0;
    div {
        text-align: center;
    }
    input[type="checkbox"],
    label {
        display: none;
    }
    td .custom-control {
        position: relative;
        left: 13px;
    }
}
.asset-status-row:nth-child(even) {
    background-color: theme-color-level(dark, -10);
}
.fa.fa-remove {
    cursor: pointer;
}
</style>