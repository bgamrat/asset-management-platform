<template>
    {%- if attributes.dropdown is defined %}
    <b-nav-item-dropdown{{ macros.attributes(attributes) }} text="{{block('label')}}" left>
    {%- endif %}
        {%- if item.uri is not empty and (not matcher.isCurrent(item) or options.currentAsLink) %}
        {%- if item.level <= 1 or not item.hasChildren %}
        {{ block('linkElement') }}
        {%- else %}
        {{ block('dropdownElement') }}
        {%- endif %}
        {%- endif %}
        {# render the list of children#}
        {%- set childrenClasses = item.childrenAttribute('class') is not empty ? [item.childrenAttribute('class')] : [] %}
        {%- set childrenClasses = childrenClasses|merge(['menu_level_' ~ item.level]) %}
        {%- set listAttributes = item.childrenAttributes|merge({'class': childrenClasses|join(' ') }) %}
        {{ block('list') }}
    {%- if attributes.dropdown is defined %}
    </b-nav-item-dropdown>
    {%- endif %}
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

