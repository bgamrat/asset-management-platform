<template>
        <b-nav-item-dropdown v-if="item.has_children" :text="item.name" left>
            <b-dropdown-item v-for="child in item.children" :key="child.id" :href="child.href">
                <i :v-if="child.icon" :class="child.icon"></i>
                {{child.name}}
            </b-dropdown-item>
        </b-nav-item-dropdown>
        <b-nav-item v-else :href="item.href">{{item.name}}</b-nav-item>
</template>

<script>
export default {
    name: 'Children',
    props: {
           item: {}
    }
}
</script>

<style scoped>

</style>


