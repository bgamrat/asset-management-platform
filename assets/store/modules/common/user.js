export default {
    name: 'common_user',
    namespaced: true,
    state: {
        csrf_token: ''
    },
    getters: {
        csrf_token: () => {
            return state.csrf_token
        }
    },
    mutations: {
        setCsrfToken(state, token) {
            state.csrf_token = token
        },
    },
    actions: {
        refreshCsrfToken( {commit} ){
            return new Promise((resolve) => {
                fetch('/login')
                        .then(res => res.json())
                        .then(res => {
                            commit('setCsrfToken', res.csrf_token);
                            resolve();
                        })
            })
        }
    }
}