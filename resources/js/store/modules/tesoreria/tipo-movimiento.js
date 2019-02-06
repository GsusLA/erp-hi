const URI = '/api/tesoreria/tipo-movimiento/';

export default {
    namespaced: true,
    state: {
        tipos: []
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data
        }
    },

    actions: {
        fetch(context, payload) {
            axios.get(URI, { params: payload })
                .then(r => r.data)
                .then((data) => {
                    context.commit('SET_TIPOS', data.data)
                })
        }
    },

    getters: {
        tipos(state) {
            return state.tipos
        }
    }
}