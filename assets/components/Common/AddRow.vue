<template>
<div>
    <b-button @click="$emit('add-row')" size="sm" class="float-right add-one-more-row" v-bind:id="id">{{ label }}</b-button>
</div>
</template>

<script>
import Vue from 'vue'

import { mapActions } from 'vuex';
import bButton from 'bootstrap-vue/es/components/button/button';


Vue.component('b-button',bButton)
export default {
    
    name: 'AddRow',

 computed: { 
  }, mounted() {
  }, props: {label: String,id: String},
  data() { 
      return {
     
      }},
  
   // methods:
    // mapActions('admin-asset-asset-status',['addRow'])

   //,


};
</script>

<style  scoped>

</style>
