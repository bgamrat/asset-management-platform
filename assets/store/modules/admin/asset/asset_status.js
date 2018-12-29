import assetStatusApi from '../../../../api/admin/asset/asset-status'

export default {
    name: 'admin_asset_asset_status',
    namespaced: true,
    state: {
        items: [],
        item: {index: 0, id: null, available: false, name: '', comment: '', default: false, inUse: true, dirty: true, removeable: true}
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
        removeItem(state, index) {
            state.items.splice(index, 1);
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
                state.items[i].removeable = false;
                state.items[i].index = i;
            }
        },
    },
    actions: {
        load( {commit}){
            return new Promise((resolve, reject) => {
                assetStatusApi.get().then(items => {
                    commit('setItems', items)
                    resolve();
                })
            }, (err => {
                reject(err)
            }))
        },
        add( {commit}) {
            commit('addItem');
        },
        save( {commit, state}) {
            let i, l, promises = [];
            l = state.items.length;
            for (i = 0; i < l; i++) {
                if (typeof state.items[i].dirty !== "undefined") {
                    promises.push(assetStatusApi.persist(state.items[i]));
                }
            }
            return Promise.all(promises);
            ;
        },
        remove( {commit}, index) {
            commit('removeItem', index);
        },
        update( {commit}, item) {
            commit('setItem', item);
        },
    }
}