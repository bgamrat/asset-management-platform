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
import Vue from 'vue';

export default {
    name: 'Navigation',
    
    data() { return { items:  [0,1]}},
 
    mounted: function () {
        var self = this;
        fetch('/api/menu/')
                .then(res=>res.json())
                .then(res=> {
                   this.items = res

        })
        
    },
};
</script>

<style  scoped>

</style>

