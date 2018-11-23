import Vue from 'vue'
import Vuex from 'vuex'
import modules from './modules.js'

Vue.use(Vuex)

var debug = process.env.NODE_ENV !== 'production'

var state = {
    count: 0,
    history: []
}

var store = new Vuex.Store({
    //state,
    modules,
    strict: debug
})

export default store