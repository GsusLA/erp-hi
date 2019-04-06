const URI = '/api/contabilidad/tipo-cuenta-contable/';

export default {
    namespaced: true,
    state: {
        tipos: [],
        currentTipo: null,
        meta: {}
    },

    mutations: {
        SET_TIPOS(state, data) {
            state.tipos = data
        },

        SET_META(state, data) {
            state.meta = data
        },

        SET_TIPO(state, data) {
            state.currentTipo = data
        },
        UPDATE_TIPO(state, data) {
            state.tipos = state.tipos.map(tipo => {
                if (tipo.id === data.id) {
                    return Object.assign([], tipo, data)
                }
                return tipo
            })
            state.currentTipo = data
        },

        UPDATE_ATTRIBUTE(state, data) {
            state.currentTipo[data.attribute] = data.value
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios.get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data)
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        },
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
        find(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI + payload.id, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
                    })
            });
        },
        store(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Cuenta Contable",
                    text: "¿Estás seguro/a de que la información es correcta?",
                    icon: "info",
                    buttons: {
                        cancel:{
                            text: "Cancelar"
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
                                    swal({
                                        title: "Cuenta contable registrada correctamente",
                                        text: " ",
                                        icon: "success",
                                        timer: 1500,
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
        update(context, payload) {
            return new Promise((resolve, reject) => {
                swal({
                    title: "¿Estás seguro?",
                    text: "Actualizar Tipo de Cuenta Contable",
                    icon: "warning",
                    buttons: {
                        cancel: {
                            text: 'Cancelar',
                        },
                        confirm: {
                            text: 'Si, Actualizar',
                            closeModal: false,
                        }
                    }
                })
                    .then((value) => {
                        if (value) {
                            axios
                                .patch(URI + payload.id, payload.data)
                                .then(r => r.data)
                                .then(data => {
                                    swal({
                                        title: " ",
                                        text: "Cuenta actualizada correctamente",
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
    },

    getters: {
        tipos(state) {
            return state.tipos
        },

        meta(state) {
            return state.meta
        },

        currentTipo(state) {
            return state.currentTipo
        }
    }
}