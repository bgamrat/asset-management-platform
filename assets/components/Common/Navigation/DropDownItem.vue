<template>
    <b-dropdown-item :href="href">
        <i :v-if="icon" :class="icon"></i>
        {{$t(label)}}
    </b-dropdown-item>
</template>

<script>
export default {
    name: 'DropDown',
    props: { href: '#', label: '' }
};
</script>

<style  scoped>
</style>
