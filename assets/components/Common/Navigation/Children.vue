<template> 
    <b-navbar-nav class="ml-auto">
        <template v-if="item.has_children">
        <p>a-dropdown item="item" /</p>
        </template>
        <template v-else>
        <b-nav-item v-for="item in item.children" v-bind:key="item.id" v-bind:href="item.href">{{item.name}}</b-nav-item>
        </template>
    </b-navbar-nav>
</template>

<script>

export default {
    name: 'Children',
    props: {
           item: {}
    }
};
</script>

<style  scoped>

</style>


