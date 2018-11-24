export default {
    name: 'common_message',
    namespaced: true,
    state: {
        visible: false,
        variant: null,
        message: ''
    },
    getters: {
        visible: () => {
            return state.visible
        },
        variant: () => {
            return state.variant
        },
        message: () => {
            return state.message
        }
    },
    mutations: {
        setVisible(state, visible) {
            state.visible = visible
        },
        setVariant(state, variant) {
            state.variant = variant
        },
        setMessage(state, message) {
            state.message = message;
        }
    },
    actions: {
        setMessage( {commit}, payload) {
            commit('setVisible', payload.visible === undefined || payload.visible);
            commit('setVariant', payload.variant);
            commit('setMessage', payload.message);
        },
        showMessage( {commit}) {
            commit('setVisible', true);
        },
        hideMessage( {commit}) {
            commit('setVisible', false);
        }
    }
}