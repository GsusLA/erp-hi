const URI = '/api/finanzas/distribuir-recurso-remesa/';

export default {
    namespaced: true,
    state: {
        distribuciones: [],
        currentDistribucion: null,
        meta: {}
    },

    mutations: {
        SET_DISTRIBUCIONES(state, data) {
            state.distribuciones = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_DISTRIBUCION(state, data) {
            state.currentDistribucion = data
        },
        UPDATE_DISTRIBUCION(state, data) {
            state.distribuciones = state.distribuciones.map(distribucion => {
                if (distribucion.id === data.id) {
                    return Object.assign({}, distribucion, data)
                }
                return distribucion
            })
            state.currentDistribucion != null ? data : null;
        }
    },

    actions: {
        paginate (context, payload){
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + 'paginate', { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar la Distribucion de Recurso de Remesa",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Registrar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post(URI, payload)
                                .then(r => r.data)
                                .then(data => {
                                    swal("Distribución de recurso registrada correctamente", {
                                        icon: "success",
                                        timer: 2000,
                                        buttons: false
                                    }).then(() => {
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                });
                        }
                    });
            });
        },
        cancel(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Cancelar distribucion de recurso autorizado de remesa",
                    text: "¿Estás seguro/a de que deseas cancelar esta solicitud?",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                            visible: true
                        },
                        confirm: {
                            text: 'Si, Cancelar',
                            closeModal: false,
                        }
                    },
                    dangerMode: true,
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI+ payload.id +'/cancelar',{id:payload.id}, { params: payload.params })
                                .then(r => r.data)
                                .then(data => {
                                    swal("Distribucion cancelada correctamente", {
                                        icon: "success",
                                        timer: 1500,
                                        buttons: false
                                    }).then(() => {
                                        context.commit('UPDATE_DISTRIBUCION', data);
                                        resolve(data);
                                    })
                                })
                                .catch(error => {
                                    reject(error);
                                })
                        }
                    });
            });
        },
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
    },

    getters: {
        distribuciones(state) {
            return state.distribuciones
        },

        meta(state) {
            return state.meta
        },

        currentDistribucion(state) {
            return state.currentDistribucion
        }
    }
}