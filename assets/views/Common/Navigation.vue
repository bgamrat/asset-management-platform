<template>
    <div>
        <b-navbar toggleable="md" type="light" variant="light">
            <b-navbar-toggle target="user_nav_collapse"></b-navbar-toggle>
            <b-navbar-brand href="#/" id="brand">Brand</b-navbar-brand>
            <b-collapse is-nav id="user_nav_collapse">
                <b-navbar-nav class="ml-auto">
                <b-nav-item v-for="item in items" v-bind:key="item.id" v-bind:href="item.href">{{item.name}}</b-nav-item>
                </b-navbar-nav>
            </b-collapse>
        </b-navbar>
    </div>
</template>

<script>
import Vue from 'vue'
import { mapState, mapActions } from 'vuex'

export default {
    name: 'Navigation',
    created() {
        this.$store.dispatch('common_navigation/refreshNavItems');
    },
    computed:
        mapState({
            items: state => state.common_navigation.nav_items
        })
};
</script>

<style  scoped>

</style>

