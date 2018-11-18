<template>
    <div id="app">
        <b-container fluid>
            <navigation />
            <header class="d-flex justify-content-between">
                <h1 v-bind:title.sync="title">{{title}}</h1>
                <div v-bind:dialog.sync="dialog">
                    <b-btn v-if="dialog" v-b-modal.settings class="btn-settings"><i class="fa fa-gears"></i></b-btn>
                </div>
            </header>
            <b-row v-if="error">
                <b-col>
                    <b-alert show variant="warning">
                    </b-alert>
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
  name: 'app',
  components: { 'navigation':Navigation },
  data() { return { 'error': false, 'dialog' : true }},
  beforeMount() {this.$router.push({path:'home',name:'home'})},
  computed: {
      title() {
          return this.$i18n.t(this.$route.name);
      }
  }
}
</script>

<style lang="scss">
#app {
  font-family: 'ComicSans','Avenir', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
}
span >>> .btn-settings {
    height: 50px;
    vertical-align: middle;
    padding-top: 15px;
}
</style>