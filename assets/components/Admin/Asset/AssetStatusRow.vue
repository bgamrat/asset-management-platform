<template>
    <b-row class="asset-status-row">
        <input type="hidden" :value="localitem.index" />
        <input type="hidden" :value="localitem.id" />
        <b-col cols="3" sm="1"><b-form-checkbox v-model="localitem.available"></b-form-checkbox></b-col>
        <b-col cols="9" sm="3"><b-form-input type="text" v-model="localitem.name"></b-form-input></b-col>
        <b-col cols="9" sm="5"><b-form-input type="text" v-model="localitem.comment"></b-form-input></b-col>
        <b-col cols="1" sm="1">
            <template v-if=localitem.default>
            <b-form-radio name="default"></b-form-radio>
            </template>
            <template v-else>
            <b-form-radio name="default" checked=false></b-form-radio>
            </template>
        </b-col>
        <b-col cols="1" sm="1"><b-form-checkbox v-model="localitem.inUse"></b-form-checkbox></b-col>
        <b-col cols="1" sm="1">
            <i v-if="localitem.removeable" class="fa fa-remove" v-on:click="$emit('remove')"></i>
        </b-col>
    </b-row>
</template>

<script>
export default {
    name: 'AdminAssetStatusRow',
    props: {
            item: { type : Object },
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
}
.asset-status-row:nth-child(even) {
    background-color: theme-color-level(dark, -10);
}
</style>