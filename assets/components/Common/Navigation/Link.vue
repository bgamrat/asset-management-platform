<template>
    <b-nav-item :href="href">
         <i v-if="icon" :class="icon"></i>
        {{$t(label)
    </b-nav-item>
</template>

<script>
export default {
    name: 'Item',
    props: { href: '#', label: '' }
};
</script>

<style  scoped>
</style>