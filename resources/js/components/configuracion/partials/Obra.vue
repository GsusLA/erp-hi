<template>
    <div class="card" id="configuracion-obra" v-if="$root.can('administracion_configuracion_obra')">
        <div class="card-header">
            <h3 class="card-title">Configuración de Obra</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body" v-if="form">
            <h5 id="identificacion">Identificación</h5>
            <div class="form-group row">
                <label for="logotipo_original" class="col-lg-2 col-form-label">Logotipo</label>
                <div :class="{'col-lg-5': logo, 'col-lg-10': !logo}">
                    <input type="file" class="form-control" id="logotipo_original" @change="onLogoSelected"
                           row="3"
                           v-validate="{ image: true, ext: 'png' }"
                           name="logotipo_original"
                           data-vv-as="Logotipo"
                           :class="{'is-invalid': errors.has('logotipo_original')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('logotipo_original')">{{ errors.first('logotipo_original') }}</div>
                </div>
                <div v-if="logo" class="thumbnail col-lg-5">
                    <img :src="logo" class="img-thumbnail">
                </div>
            </div>
            <div class="form-group row">
                <label for="nombre" class="col-lg-2 col-form-label">Abreviatura</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="nombre" v-model="form.nombre"
                           v-validate="{max: 16}"
                           name="nombre"
                           data-vv-as="Abreviatura"
                           :class="{'is-invalid': errors.has('nombre')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('nombre')">{{ errors.first('nombre') }}</div>
                </div>
           <!-- </div>

            <div class="form-group row">-->
                <label for="descripcion" class="col-lg-2 col-form-label">Descripción</label>
                <div class="col-lg-4">
                    <textarea class="form-control" id="descripcion" v-model="form.descripcion"
                              v-validate="{max: 255}"
                              name="descripcion"
                              data-vv-as="Descripción"
                              :class="{'is-invalid': errors.has('descripcion')}"
                    >

                    </textarea>
                    <div class="invalid-feedback" v-show="errors.has('descripcion')">{{ errors.first('descripcion') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="constructora" class="col-lg-2 col-form-label">Constructora</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" id="constructora" v-model="form.constructora"
                           v-validate="{max: 255}"
                           name="constructora"
                           data-vv-as="Constructora"
                           :class="{'is-invalid': errors.has('constructora')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('constructora')">{{ errors.first('constructora') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="cliente" class="col-lg-2 col-form-label">Cliente</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" id="cliente" v-model="form.cliente"
                           v-validate="{max: 255}"
                           name="cliente"
                           data-vv-as="Cliente"
                           :class="{'is-invalid': errors.has('cliente')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('cliente')">{{ errors.first('cliente') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="facturar" class="col-lg-2 col-form-label">Facturar a</label>
                <div class="col-lg-5">
                    <input type="text" class="form-control" id="facturar" v-model="form.facturar"
                           v-validate="{max: 255}"
                           name="facturar"
                           data-vv-as="Facturar a"
                           :class="{'is-invalid': errors.has('facturar')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('facturar')">{{ errors.first('facturar') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="responsable" class="col-lg-2 col-form-label">Responsable</label>
                <div class="col-lg-4">
                    <usuario-select
                            name="responsable"
                            data-vv-as="Responsable"
                            v-validate="{integer: true}"
                            v-model="form.configuracion.id_responsable"
                            :placeholder="form.configuracion.id_responsable ? form.responsable : '-- Buscar --'"
                            :error="errors.has('responsable')"
                    >
                    </usuario-select>
                    <div class="error-label" v-show="errors.has('responsable')">{{ errors.first('responsable') }}</div>
                </div>
  <!--          </div>

            <div class="form-group row">-->
                <label for="administrador" class="col-lg-2 col-form-label">Administrador</label>
                <div class="col-lg-4">
                    <usuario-select
                            name="administrador"
                            data-vv-as="Administrador"
                            v-validate="{integer: true}"
                            v-model="form.configuracion.id_administrador"
                            :placeholder="form.configuracion.id_administrador ? form.administrador : '-- Buscar --'"
                            :error="errors.has('administrador')"
                    >
                    </usuario-select>
                    <div class="error-label" v-show="errors.has('administrador')">{{ errors.first('responsable') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="rfc" class="col-lg-2 col-form-label">RFC</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="rfc" v-model="form.rfc"
                           v-validate="{max: 16}"
                           name="rfc"
                           data-vv-as="RFC"
                           :class="{'is-invalid': errors.has('rfc')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('rfc')">{{ errors.first('rfc') }}</div>
                </div>
            </div>

            <hr>
            <h5 id="informacion_financiera">Información Financiera</h5>

            <div class="form-group row">
                <label for="id_moneda" class="col-lg-2 col-form-label">Moneda</label>
                <div class="col-lg-4">
                    <select class="form-control" id="id_moneda" v-model="form.id_moneda"
                            v-validate="{integer: true}"
                            name="id_moneda"
                            data-vv-as="Moneda"
                            :class="{'is-invalid': errors.has('id_moneda')}"
                    >
                        <option value>-- Moneda --</option>
                        <option v-for="moneda in monedas" :value="moneda.id">{{ moneda.nombre }}</option>
                    </select>
                    <div class="invalid-feedback" v-show="errors.has('id_moneda')">{{ errors.first('id_moneda') }}</div>
                </div>

                <label for="iva" class="col-lg-2 col-form-label">Porcentaje de IVA</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="iva" v-model="form.iva"
                           v-validate="{ decimal: true,  max_value: 100, min_value: 0}"
                           name="iva"
                           data-vv-as="Porcentaje de IVA"
                           :class="{'is-invalid': errors.has('iva')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('iva')">{{ errors.first('iva') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="valor_contrato" class="col-lg-2 col-form-label">Valor del Contrato</label>
                <div class="col-lg-4">
                    <input type="number" step="any" class="form-control" id="valor_contrato" v-model="form.valor_contrato"
                           v-validate="{ decimal: true }"
                           name="valor_contrato"
                           data-vv-as="Valor del Contrato"
                           :class="{'is-invalid': errors.has('valor_contrato')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('valor_contrato')">{{ errors.first('valor_contrato') }}</div>
                </div>
            </div>

            <hr>
            <h5 id="plan">Plan de Obra</h5>

            <div class="form-group row">
                <label for="fecha_inicial" class="col-lg-2 col-form-label">Inicio de Obra</label>
                <div class="col-lg-4">
                    <input type="date" class="form-control" id="fecha_inicial" v-model="form.fecha_inicial"
                           v-validate="{date_format: 'yyyy-MM-dd'}"
                           name="fecha_inicial"
                           data-vv-as="Inicio de Obra"
                           :class="{'is-invalid': errors.has('fecha_inicial')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('fecha_inicial')">{{ errors.first('fecha_inicial') }}</div>
                </div>
                <label for="fecha_final" class="col-lg-2 col-form-label">Fin de Obra</label>
                <div class="col-lg-4">
                    <input type="date" id="fecha_final" class="form-control" v-model="form.fecha_final"
                           v-validate="{date_format: 'yyyy-MM-dd'}"
                           name="fecha_final"
                           data-vv-as="Fin de Obra"
                           :class="{'is-invalid': errors.has('fecha_final')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('fecha_final')">{{ errors.first('fecha_final') }}</div>
                </div>
            </div>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-lg-2 pt-0"><b>Estado</b></legend>
                    <div class="col-lg-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra0" value="0" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra0"> En ejecución </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra1" value="1" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra1"> En Proyecto</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="tipo_obra2" value="2" v-model="form.tipo_obra">
                            <label class="form-check-label" for="tipo_obra2"> Terminada</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="form-group row">
                <label for="id_tipo_proyecto" class="col-lg-2 col-form-label">Tipo de Proyecto</label>
                <div class="col-lg-4">
                    <select class="form-control" id="id_tipo_proyecto" v-model="form.configuracion.id_tipo_proyecto"
                            v-validate="{integer: true}"
                            name="id_tipo_proyecto"
                            data-vv-as="Tipo de Proyecto"
                            :class="{'is-invalid': errors.has('id_tipo_proyecto')}"
                    >
                        <option v-for="tipo in tipos_proyecto" :value="tipo.id">{{ tipo.descripcion }}</option>
                    </select>
                    <div class="invalid-feedback" v-show="errors.has('id_tipo_proyecto')">{{ errors.first('id_tipo_proyecto') }}</div>
                </div>
            </div>

            <hr>
            <h5 id="seguridad">Seguridad</h5>

            <fieldset class="form-group">
                <div class="row">
                    <legend class="col-form-label col-lg-2 pt-0"><b>Esquema de Permisos</b></legend>
                    <div class="col-lg-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="esquema_permisos1" value="1"
                                   v-model="form.configuracion.esquema_permisos"
                                   :disabled="!$root.can('modificar_esquema_permisos_proyecto')">
                            <label class="form-check-label" for="esquema_permisos1"> Esquema Global</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="esquema_permisos2" value="2"
                                   v-model="form.configuracion.esquema_permisos"
                                   :disabled="!$root.can('modificar_esquema_permisos_proyecto')">
                            <label class="form-check-label" for="esquema_permisos2"> Esquema Personalizado</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            <hr>
            <h5 id="ubicacion">Ubicación</h5>

            <div class="form-group row">
                <label for="direccion" class="col-lg-2 col-form-label">Dirección</label>
                <div class="col-lg-10">
                    <textarea id="direccion" class="form-control" v-model="form.direccion"
                              v-validate="{max: 255}"
                              name="direccion"
                              data-vv-as="Dirección"
                              :class="{'is-invalid': errors.has('direccion')}"
                    ></textarea>
                    <div class="invalid-feedback" v-show="errors.has('direccion')">{{ errors.first('direccion') }}</div>
                </div>
            </div>
            <div class="form-group row">
                <label for="ciudad" class="col-lg-2 col-form-label">Ciudad</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="ciudad" v-model="form.ciudad"
                           v-validate="{max: 255}"
                           name="ciudad"
                           data-vv-as="Ciudad"
                           :class="{'is-invalid': errors.has('ciudad')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('ciudad')">{{ errors.first('ciudad') }}</div>
                </div>
         <!--   </div>

            <div class="form-group row">-->
                <label for="estado" class="col-lg-2 col-form-label">Estado</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="estado" v-model="form.estado"
                           v-validate="{max: 255}"
                           name="estado"
                           data-vv-as="Estado"
                           :class="{'is-invalid': errors.has('estado')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('estado')">{{ errors.first('estado') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <label for="codigo_postal" class="col-lg-2 col-form-label">Código Postal</label>
                <div class="col-lg-4">
                    <input type="text" class="form-control" id="codigo_postal" v-model="form.codigo_postal"
                           v-validate="{integer: true, digits: 5}"
                           name="codigo_postal"
                           data-vv-as="Código Postal"
                           :class="{'is-invalid': errors.has('codigo_postal')}"
                    >
                    <div class="invalid-feedback" v-show="errors.has('codigo_postal')">{{ errors.first('codigo_postal') }}</div>
                </div>
            </div>

            <div class="form-group row">
                <div class="col">
                    <button type="submit" @click="validate" class="btn btn-outline-primary pull-right" :disabled="guardando">
                        <i class="fa fa-spin fa-spinner" v-if="guardando"></i>
                        <i class="fa fa-save" v-else></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import UsuarioSelect from "../../igh/usuario/Select";
    export default {
        name: "configuracion-obra",
        components: {UsuarioSelect},
        props: ['obra'],
        data() {
            return {
                user: '',
                logo: '',
                logo_nuevo: null,
                form: null,
                cargando: true,
                guardando: false,
                tipos_proyecto: []
            }
        },

        mounted() {
            this.getTiposProyectos();
            this.form = JSON.parse(JSON.stringify(this.obra));
            setTimeout(() => {
                if (this.form.configuracion.logotipo_original) {
                    this.logo = `data:image/png;base64,${this.form.configuracion.logotipo_original}`;
                }
            }, 100);
        },

        methods: {
            getTiposProyectos() {
                this.cargando = true;
                return this.$store.dispatch('seguridad/tipo-proyecto/index')
                    .then(data => {
                        this.tipos_proyecto = data;
                    })
                    .finally(() => {
                        this.cargando = false;
                    })
            },

            onLogoSelected(event) {
                this.logo_nuevo = event.target.files[0]
            },

            validate() {
                this.$validator.validate().then(result => {
                    if (result) {
                        this.update()
                    }
                });
            },

            update() {
                this.guardando = true;
                var formData = new FormData();

                formData.append('ciudad', this.form.ciudad);
                formData.append('cliente', this.form.cliente)
                formData.append('codigo_postal', this.form.codigo_postal);

                formData.append('configuracion[esquema_permisos]', this.form.configuracion.esquema_permisos);
                if (this.form.configuracion.id_administrador) formData.append('configuracion[id_administrador]', this.form.configuracion.id_administrador);
                if (this.form.configuracion.id_responsable) formData.append('configuracion[id_responsable]', this.form.configuracion.id_responsable);
                formData.append('configuracion[id_tipo_proyecto]', this.form.configuracion.id_tipo_proyecto);
                if (this.logo_nuevo) formData.append('configuracion[logotipo_original]', this.logo_nuevo, this.logo_nuevo.name);

                formData.append('constructora', this.form.constructora)
                formData.append('descripcion', this.form.descripcion);
                formData.append('direccion', this.form.direccion);
                formData.append('estado', this.form.estado);
                formData.append('facturar', this.form.facturar)
                formData.append('fecha_final', this.form.fecha_final)
                formData.append('fecha_inicial', this.form.fecha_inicial)
                formData.append('id_moneda', this.form.id_moneda)
                formData.append('iva', this.form.iva)
                formData.append('nombre', this.form.nombre);
                formData.append('rfc', this.form.rfc)
                formData.append('tipo_obra', this.form.tipo_obra)
                formData.append('valor_contrato', this.form.valor_contrato)

                formData.forEach((value, key) => {
                    if(value == 'null' || value == '')
                        formData.delete(key);
                });

                return this.$store.dispatch('cadeco/obras/update', {
                    id: this.obra.id_obra,
                    data: formData,
                    config: {
                        params: { _method: 'PATCH', include: 'configuracion'}
                    }
                })
                    .then(data => {
                        if (data) {
                            this.$store.commit('auth/setObra', { obra: data });
                            this.form = data
                            setTimeout(() => {
                                if (data.configuracion.logotipo_original) {
                                    this.logo = `data:image/png;base64,${data.configuracion.logotipo_original}`;
                                }
                            }, 100);
                        }
                    })
                    .finally(() => {
                        this.guardando = false;
                    })
            }
        },

        computed: {
            monedas() {
                return this.$store.getters['cadeco/moneda/monedas'];
            }
        }
    }
</script>

<style>
    .error-label {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 80%;
        color: #dc3545;
    }
    .vue-treeselect__placeholder {
        color: #495057
    }
</style>