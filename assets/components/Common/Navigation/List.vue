<template>
    <b-navbar-nav>
        <a-children v-for="item in items" v-bind:key="item.id" v-bind:item="item" />
    </b-navbar-nav>
</template>

<script>

import Children from './Children'

export default {
    name: 'Navigation',
    components: {
        'a-children': Children
    },
    props: {
            items: {}
    }
};
</script>

<style scoped>

</style>

