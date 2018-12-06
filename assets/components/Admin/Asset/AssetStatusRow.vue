<template>
    <b-row class="asset-status-row">
        <b-col class="id d-none"><b-form-input type="hidden" id="id[]" :value="item.id"></b-form-input></b-col>
        <b-col cols="3" sm="1"><b-form-checkbox id="available[]" :value="on" v-model="item.available"></b-form-checkbox></b-col>
        <b-col cols="9" sm="4"><b-form-input type="text" id="name[]" :value="item.name"></b-form-input></b-col>
        <b-col cols="10" sm="5"><b-form-input type="text" id="comment[]" :value="item.comment"></b-form-input></b-col>
        <b-col cols="1" sm="1"><b-form-checkbox id="default[]" :value="on" v-model="item.default"></b-form-checkbox></b-col>
        <b-col cols="1" sm="1"><b-form-checkbox id="in_use[]" :value="on" v-model="item.inUse"></b-form-checkbox></b-col>
    </b-row>
</template>

<script>

export default {
    name: 'AdminAssetStatusRow',
    props: {
            item: { type : Object, default: {} },
            key: { type: Number }
    }
};
</script>

<style scoped lang="scss">
@import "node_modules/bootstrap/scss/bootstrap";
.asset-status-row {
    margin: 5px;
    padding: 7px;
}
.asset-status-row:nth-child(even) {
    background-color: theme-color-level(dark, -10);
}
.asset-status-row > div {
    text-align: center;
}
</style>