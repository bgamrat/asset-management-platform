<template>
    <div id="app">
        <b-container fluid>
            <navigation />
            <header class="d-flex justify-content-between">
                <h1 v-bind:title.sync="title">{{title}}</h1>
                <div v-bind:dialog.sync="dialog" v-if="dialog">
                    <b-btn v-b-modal.settings variant="primary"><i class="fa fa-gears"></i></b-btn>
                </div>
            </header>
            <b-row v-if="error">
                <b-col>
                    <b-alert show variant="warning"></b-alert>
                </b-col>
            </b-row>
            <router-view />
            <b-modal id="settings" title="Bootstrap-Vue">
                <p>Hello from modal!</p>
            </b-modal>
            <footer>
            </footer>
        </b-container>
    </div>
</template>

<script>

import Navigation from './views/Common/Navigation';

export default {
  name: 'app', components: { 
      'navigation':Navigation 
  }, props: { 
      'dialog': { 
          'default': false
      }, 'message' : { 
          'type' : String, 'default' : '' 
      }
  }, data() { 
      return { 'error': false }
  }, beforeMount() {
      this.$router.push({path:'home', name:'home'})
  }, computed: {
      title() {
          document.title = this.$i18n.t(this.$route.name);
          return this.$i18n.t(this.$route.name);
      }
  }, }
</script>

<style lang="scss" scoped>
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
}
</style>