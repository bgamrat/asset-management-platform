<template>
   <div>
       <p>This is an example of a new component in VueJs</p>
       <p><img src="//placekitten.com/200/230"></p>
       <p>
        Value: {{ count }}
          <button @click="increment">+</button>
          <button @click="decrement">-</button>
        </p>
   </div>
</template>

<script>

import { mapGetters, mapActions } from 'vuex'

   export default {
       name: "example",
      
   computed: mapGetters([
    'count'
  ]),
  methods: mapActions([
    'increment',
    'decrement'
  ])
   }
</script>

<style scoped>

</style>