export default {
    name: 'admin_asset_asset_status',
    namespaced: true,
    state: {
        items: [],
        item: {index: 0, id: null, available: false, name: '', comment: '', default: false, inUse: true, dirty: true}
    },
    getters: {
        items: () => {
            return state.items
        }
    },
    mutations: {
        addItem(state) {
            state.item.index = state.items.length - 1;
            state.items.push(state.item)
        },
        removeItem(state,index){
            state.items.splice(index,1);
        },
        setItem(state, item) {
            var index = item.index;
            state.items[index] = item;
            state.items[index].dirty = true;
        },
        setItems(state, items) {
            var i, l;
            state.items = items;
            l = state.items.length;
            for (i = 0; i < l; i++) {
                state.items[i].index = i;
            }
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

        save( {commit, state}) {
            var i, l;
            l = state.items.length;
            for (i = 0; i < l; i++) {
                if (typeof state.items[i].dirty !== "undefined") {
                    new Promise((resolve) => {
                        var url;
                        var id = state.items[i].id;
                        url = id === null ? '' : '/' + state.items[i].id;
                        if (id === null) {
                            delete state.items[i].id;
                        }
                        fetch('/api/asset_statuses' + url + '.json',
                                {'method': id === null ? 'POST' : 'PUT',
                                    'body': JSON.stringify(state.items[i]),
                                    'headers': new Headers({'Content-Type': 'application/json; charset=utf-8'})})
                                .then(res => res.json())
                                .then(res => {
                                    console.log(res);
                                    resolve();
                                })
                    })
                }
            }
            ;
        },
        remove ({commit}, index) {
            commit('removeItem',index);
        },
        update ({commit}, item) {
            commit('setItem',item);
        },
    }
}