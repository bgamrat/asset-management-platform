<template>
    <div>
        <p>{{ count }}</p>
        <p>
            <button @click="increment"> + </button>
            <button @click="decrement"> - </button>
        </p>
    </div>
</template>

<script>
import { mapActions } from 'vuex'
export var Counter = {
    name: "counter",
  // This makes your getters, mutations, and actions accessed by, eg: 'myModule/myModularizedNumber' instead of mounting getters, mutations, and actions to the root namespace.
  namespaced: false,
  state: {
    count: 0
  },
  getters: {
    count: state => state.count
  },
  mutations: {
    setcount(state, newValue) {
      state.count = newValue
    }
  }
}

export var store = new Vuex.Store({
  modules: {
  	Counter
  },
  state: {
    count: 0
  }
});
</script>

<style scoped>
p {
  font - size: 2em;
  text - align: center;
}
</style>