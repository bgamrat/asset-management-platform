<template>
<div>
    <b-button @click="addRow" size="sm" class="float-right add-one-more-row" v-bind:id="id" >{{ label }}</b-button>
</div>
</template>

<script>
import Vue from 'vue'
import { Button } from 'bootstrap-vue/es/components' 
import { mapGetters } from 'vuex';

export default {
    name: 'addrow',
 computed: {
  }, mounted() {
  }, props: {label: String,id: String},
  data() { 
      return { addRow (event) {
  // dispatch('addRow');
              alert("here") }
    } 
  },
  methods: {
     // addRow: 'addRow'
  }

};
</script>

<style lang="scss" scoped>
h1 {
  font-family: "Raleway", "Helvetica Neue", Helvetica, Arial, sans - serif;
  text-align: center;
  font-weight: 100;
  font-size: 3em;
  letter-spacing: 0.05em;
  color: #4b4b4b;
}
</style>
