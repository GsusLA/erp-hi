    const URI = '/api/SEGURIDAD_ERP/configuracion-obra/';

export default {
    namespaced: true,

    state: {
        configuracionesObra: [],
    },

    mutations: {
        SET_CONFIGURACIONES(state, data) {
            state.configuracionesObra = data;
        }
    },

    actions: {
        index(context, payload = {}) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, payload.config)
                    .then(r => r.data)
                    .then((data) => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    });
            });
        }
    },

    getters: {
        configuracionesObra(state) {
            return state.configuracionesObra
        }
    }
}