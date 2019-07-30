const URI = '/api/fondo/';

export default {
    namespaced: true,
    state: {
        fondos: [],
        currentFondo: null,
        meta: {}
    },

    mutations: {
        SET_FONDOS(state, data) {
            state.fondos = data
        },

        SET_FONDO(state, data) {
            state.currentFondo = data;

        },
        SET_META(state, data){
            state.meta = data
        },
        UPDATE_FONDO(state, data){
            state.fondos = state.fondos.map(fondo => {
                if(fondo.id === data.id){
                    return Object.assign({}, fondo, data)
                }
                return fondo
            })
            state.currentFondo = state.currentFondo ? data : null;
        }
    },

    actions: {
        index(context, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get(URI, { params: payload.params })
                    .then(r => r.data)
                    .then(data => {
                        resolve(data);
                    })
                    .catch(error => {
                        reject(error)
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
        paginate(context, payload) {
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
            })
        },
        store(context,payload){

            return new Promise((resolve, reject) => {
                swal({
                    title: "Registrar Fondo",
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
                    }                })
                    .then((value) => {
                        if (value) {
                            axios
                                .post('/api/fondo/', payload)

                                .then(r => r.data)
                                .then(data => {
                                    swal("Fondo registrado correctamente", {
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
        storeResponsable(context,payload){
            return new Promise((resolve, reject) => {
                axios
                    .post('/api/empresa/', payload)
                    .then(r => r.data)
                    .then(data => {
                        resolve(data.id);
                    })
                    .catch(error => {
                        reject(error);
                    })
            });
        }
    },

    getters: {
        fondos(state) {
            return state.fondos;
        },
        meta(state) {
            return state.meta;
        },
        currentFondo(state) {
            return state.currentFondo;
        }
    }
}