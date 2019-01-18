const URI = '/api/contabilidad/poliza/';
export default {
    namespaced: true,
    state: {
        polizas: [],
        meta: {},
        cargando: true
    },

    mutations: {
        fetch(state, payload) {
            state.polizas = payload.data;
            state.meta = payload.meta
        },

        update(state, payload) {
            state.polizas = state.polizas.map(poliza => {
                if (poliza.id === payload.id) {
                    return Object.assign({}, poliza, payload.data)
                }
                return poliza
            })
        },

        cargando(state, is) {
            state.cargando = is
        }
    },

    actions: {
        paginate (context, payload){
            context.commit('cargando', true);
            axios.get(URI + 'paginate', {params: payload})
                .then(res => {
                    context.commit('fetch', res.data)
                })
                .catch(err => {
                    alert(err);
                })
                .then(() => {
                    context.commit('cargando', false);
                })
        },

        find(context, payload) {
            context.commit('cargando', true);
            return new Promise((resolve, reject) => {
                axios.get(URI + payload.id, {params: payload.params})
                    .then(res => {
                        resolve(res.data)
                    })
                    .catch(err => {
                        reject(err)
                    })
                    .then(() => {
                        context.commit('cargando', false);
                    })
            });
        },

        update(context, payload) {
            context.commit('cargando', true)
            return new Promise((resolve, reject) => {
                axios.patch(URI + payload.id, payload.data, {params: payload.params})
                    .then(response => {
                        context.commit('update', response.data)
                        resolve(response.data)
                    })
                    .catch(error => {
                        reject(error)
                    })
                    .then(() => {
                        context.commit('cargando', false)
                    })
            })
        },

        validar(context, id) {
            context.commit('cargando', true)
            return new Promise((resolve, reject) => {
                axios.patch(URI + id + '/validar')
                    .then(response => {
                        context.commit('update', response.data)
                        resolve(response.data)
                    })
                    .catch(error => {
                        reject(error)
                    })
                    .then(() => {
                        context.commit('cargando', false)
                    })
            })
        }
    },

    getters: {
        polizas(state) {
            return state.polizas
        },

        meta(state) {
            return state.meta
        },

        cargando(state) {
            return state.cargando
        }
    }
}