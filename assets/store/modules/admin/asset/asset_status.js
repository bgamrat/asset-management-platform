export default {
    name: 'admin_asset_asset_status',
    namespaced: true,
    state: {
        items: [],
        item: {available: false, name: '', comment: '', default: false, inUse: true}
    },
    getters: {
        items: () => {
            return state.items
        }
    },
    mutations: {
        addItem(state) {
            state.items.push(state.item)
        },
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
        },
        add( {commit}) {
            commit('addItem');
        },
        update( {commit,state}) {
            return new Promise((resolve) => {
                fetch('/api/asset_statuses.json',
                        {'method': 'POST', 'body': JSON.stringify(state.items), 'headers': 'Content-Type:application/json'})
                        .then(res => res.json())
                        .then(res => {
                            alert('ha');
                            console.log(res);
                            resolve();
                        })
            })
        }
    }
}