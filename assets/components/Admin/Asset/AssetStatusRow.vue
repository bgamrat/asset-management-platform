<template>
    <b-row>
        <b-col class="id d-none"><input type="hidden" id="id[]" /></b-col>
        <b-col cols="1" class="text-center"><input type="checkbox" id="available[]" /></b-col>
        <b-col cols="4" class="name"><input type="text" id="name[]" /></b-col>
        <b-col cols="4" class="comment"><input type="text" id="comment[]" /></b-col>
        <b-col cols="1" class="text-center"><input type="checkbox" id="default[]" /></b-col>
        <b-col cols="1" class="text-center"><input type="checkbox" id="in_use[]" /></b-col>
    </b-row>
</template>

<script>

export default {
    name: 'AssetStatusRow',
    props: {
            row: {},
    }
};
</script>

<style scoped>
</style>