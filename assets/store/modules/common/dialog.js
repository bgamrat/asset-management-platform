export default {
    name: 'dialog',
    namespaced: true,
    state: {
        icon: 'fa fa-gears',
        title: 'Title!',
        content: 'Content!',
        available: false
    },
    getters: {
        icon: () => {
            return state.icon
        },
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
        setIcon(state, icon) {
            state.icon = icon;
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
            commit('setIcon',payload.icon);
        },
        enableDialog( {commit}) {
            commit('setAvailable', true);
        },
        disableDialog( {commit}) {
            commit('setAvailable', false);
        }
    }
}