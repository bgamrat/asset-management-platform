export default {
    name: 'admin_asset_asset_status',
    namespaced: true,
    state: {
        items: []
    },
    getters: {
        items: () => {
            return state.items
        }
    },
    mutations: {
        setItems(state, items) {
            state.items = items
        },
    },
    actions: {
        load( {commit}){
            return new Promise((resolve) => {
                fetch('/api/asset_statuses.json')
                        .then(res => res.json())
                        .then(res => {
                            commit('setItems', res);
                            resolve();
                        })
            })
        }
    }
}