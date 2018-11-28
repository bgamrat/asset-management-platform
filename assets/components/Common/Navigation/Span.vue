<template>
    <b-nav-item href="#">
        <i v-if="icon" :class="icon"></i>
        {{$t(label)}}
    </b-nav-item>
</template>

<script>
export default {
    name: 'Span',
    props: { label: '' }
};
</script>

<style  scoped>
</style>
