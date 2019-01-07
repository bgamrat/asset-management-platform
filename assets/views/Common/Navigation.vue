<template>
    <div>
        <b-navbar toggleable="md" type="light" variant="light">
            <b-navbar-toggle target="top_nav_collapse"></b-navbar-toggle>
            <b-navbar-brand href="#/" id="brand">Brand</b-navbar-brand>
            <b-collapse is-nav id="top_nav_collapse">
                <a-nav :items="items.user" />
                <a-nav :items="items.admin" />
                <template v-if=items.super_admin.length>
                    <b-nav-form class="ml-auto">
                        <b-form-input size="sm" class="mr-sm-2" type="text" placeholder="Search" />
                        <b-button size="sm" class="my-2 my-sm-0" type="submit">Search</b-button>
                    </b-nav-form>
                </template>
                <a-nav :items="items.account" xclass="ml-auto" />
            </b-collapse>
        </b-navbar>
        <template v-if=items.super_admin.length>
            <b-navbar toggleable="md" type="dark" variant="secondary" class="super-admin-navbar">
                <b-navbar-toggle target="superadmin_nav_collapse"></b-navbar-toggle>
                <b-collapse is-nav id="superadmin_nav_collapse">
                    <a-nav :items="items.super_admin" />
                </b-collapse>
            </b-navbar>
        </template>
    </div>
</template>

<script>

import { mapState } from 'vuex'
import AppNav from '../../components/Common/Navigation/AppNav'

export default {
    name: 'Navigation',
    components: {
        'a-nav': AppNav
    },
    created() {
        this.$store.dispatch('common_navigation/refreshNavItems');
    },
    computed:
        mapState({
            items: state => state.common_navigation.nav_items
        })
};
</script>

<style scoped>
.super-admin-navbar {
    height: 30px;
}
</style>

