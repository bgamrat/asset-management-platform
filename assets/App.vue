<template>
    <div id="app">
        <b-container fluid>
            <navigation />
            <b-row v-if="error">
                <b-col>
                    <b-alert show variant="warning">
                    </b-alert>
                </b-col>
            </b-row>
            <router-view />
        </b-container>
    </div>
</template>

<script>

import Navigation from './views/Common/Navigation';

export default {
  name: 'app',
  components: { 'navigation':Navigation },
  data() { return { 'error': false }}
}
</script>

<style>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans - serif; - webkit - font - smoothing: antialiased; - moz - osx - font - smoothing: grayscale;
}
</style>