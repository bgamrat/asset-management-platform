<template>
    <b-navbar-nav :class="xclass">
        <a-children v-for="item in items" :key="item.id" :item="item" />
    </b-navbar-nav>
</template>

<script>

import Children from './Children'

export default {
    name: 'AppNav',
    components: {
        'a-children': Children
    },
    props: {
            items: {},
            xclass: ''
    }
};
</script>

<style scoped>

</style>

