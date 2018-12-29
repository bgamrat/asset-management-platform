<template>
    <div id="app">
        <b-container fluid>
            <navigation />
            <header class="d-flex justify-content-between">
                <h1 :title.sync="title">{{title}}</h1>
                <div v-if="dialog.available">
                    <b-btn class="dialog-btn" v-b-modal.settings variant="primary"><i :class="dialog.icon"></i></b-btn>
                </div>
            </header>
            <b-row v-if="message.visible">
                <b-col>
                    <b-alert show dismissible fade :variant="message.variant">{{message.message}}</b-alert>
                </b-col>
            </b-row>
            <transition name="slide-fade">
                <router-view />
            </transition>
            <b-modal id="settings" :title="dialog.title">
                {{dialog.content}}
            </b-modal>
            <footer>
            </footer>
        </b-container>
    </div>
</template>

<script>

import { mapState, mapActions } from 'vuex'
import Navigation from './views/Common/Navigation'

export default {
  name: 'app', components: {
      'navigation':Navigation
  }, props: { }, beforeMount() {
      this.$router.push({path:'home', name:'home'})
  }, data() { return { dialog_button: true }}, computed: mapState({
      title() {
          document.title = this.$i18n.t(this.$route.name);
          return this.$i18n.t(this.$route.name);
      }, dialog:  state => state.common_dialog,
      message:  state => state.common_message,
})
}
</script>

<style lang="scss">
@import "node_modules/bootstrap/scss/bootstrap";
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;
}
.dialog-btn {
    margin: 15px;
}
</style>