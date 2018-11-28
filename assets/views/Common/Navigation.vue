<template>
    <div>
        <b-navbar toggleable="md" type="light" variant="light">
            <b-navbar-toggle target="top_nav_collapse"></b-navbar-toggle>
            <b-navbar-brand href="#/" id="brand">Brand</b-navbar-brand>
            <b-collapse is-nav id="top_nav_collapse">
                <a-list :items="items.admin" />
                <b-nav-form class="ml-auto">
                    <b-form-input size="sm" class="mr-sm-2" type="text" placeholder="Search" />
                    <b-button size="sm" class="my-2 my-sm-0" type="submit">Search</b-button>
                </b-nav-form>
                <a-list :items="items.user" />
            </b-collapse>
        </b-navbar>
    </div>
</template>

<script>

import { mapState } from 'vuex'
import List from '../../components/Common/Navigation/List'

export default {
    name: 'Navigation',
    components: {
        'a-list': List
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

<style  scoped>

</style>

