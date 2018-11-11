<template>
    <div id="app">
        <b-container fluid>
            <navigation />
            <router-view />
        </b-container>
    </div>
</template>

<script>

import Navigation from './views/Common/Navigation';

export default {
  name: 'app',
  components: { 'navigation':Navigation },
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans - serif; - webkit - font - smoothing: antialiased; - moz - osx - font - smoothing: grayscale;
}
</style>