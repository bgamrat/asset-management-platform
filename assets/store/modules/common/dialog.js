export default {
    name: 'dialog',
    namespaced: true,
    state: {
        title: 'Title!',
        content: 'Content!',
        available: false
    },
    getters: {
        title: () => {
            return $t(state.title)
        },
        content: () => {
            return state.content
        }
    },
    mutations: {
        setAvailable(state, available) {
            state.available = available;
        },
        setContent(state, content) {
            state.content = content;
        },
        setTitle(state, title) {
            state.title = title;
        }
    },
    actions: {
        setDialog( {commit}, payload) {
            commit('setAvailable', payload.available === undefined || payload.available );
            commit('setTitle', payload.title);
            commit('setContent', payload.content);
        },
        enableDialog( {commit}) {
            commit('setAvailable', true);
        },
        disableDialog( {commit}) {
            commit('setAvailable', false);
        }
    }
}