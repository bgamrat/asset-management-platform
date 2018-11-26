export default {
    name: 'common_navigation',
    namespaced: true,
    state: {
        nav_items: {}
    },
    getters: {
        nav_items: () => {
            return state.nav_items
        }
    },
    mutations: {
        setNavItems(state, nav_items) {
            state.nav_items = nav_items
        },
    },
    actions: {
        refreshNavItems( {commit}){
            return new Promise((resolve) => {
                fetch('/api/menu/')
                        .then(res => res.json())
                        .then(res => {
                            commit('setNavItems', res);
                            resolve();

                        })
            })
        }
    }
}