<template>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3" v-for="(aplicacion, i) in aplicaciones">
                    <div class="info-box">
                        <span class="bg-primary info-box-icon elevation-1"><i class="fa fa-window-maximize"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-number">{{aplicacion.menu}}</span>
                            <a :href="`${aplicacion.ruta}?origen=${url}`" target="_blank">
                                <span class="info-box-text">IR <i class="fa fa-arrow-circle-o-right"></i> </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "portal",
        mounted() {
            this.index();
        },
        methods: {
            index() {
                this.$store.commit('igh/aplicacion/SET_APLICACIONES', []);

                return this.$store.dispatch('igh/aplicacion/index', {
                    params: {
                        scope: 'PorUsuario:' + this.currentUser.idusuario,
                        sort: 'menu',
                        order: 'ASC'
                    }
                })
                    .then(data => {
                        this.$store.commit('igh/aplicacion/SET_APLICACIONES', data);
                    });
            }
        },

        computed:{
            currentUser(){
                return this.$store.getters['auth/currentUser']
            },
            aplicaciones() {
                return this.$store.getters['igh/aplicacion/aplicaciones']
            },
            url() {
                return process.env.MIX_APP_URL;
            }
        }
    }
</script>