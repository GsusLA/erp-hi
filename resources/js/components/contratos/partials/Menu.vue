<template>
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Add icons to the links using the .nav-icon class
                 with font-awesome or any other icon font library -->
            <li class="nav-header">MÓDULOS</li>

            <li class="nav-item">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <p>
                       Gestión de Estimaciones
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" >
                        <router-link :to="{name: 'estimacion'}" class="nav-link" :class="{active: this.$route.name == 'estimacion'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Estimaciones</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item"  v-if="$root.can('consultar_formato_orden_pago_estimacion')">
                        <router-link :to="{name: 'formato-orden-pago'}" class="nav-link" :class="{active: this.$route.name == 'formato-orden-pago'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Orden de Pago Estimación</p>
                        </router-link>
                    </li>
                </ul>
            </li>

            <li class="nav-item" v-if="$root.can('consultar_solicitud_movimiento_fondo_garantia') || $root.can('registrar_solicitud_movimiento_fondo_garantia') || $root.can('cancelar_solicitud_movimiento_fondo_garantia')
            || $root.can('autorizar_solicitud_movimiento_fondo_garantia') || $root.can('rechazar_solicitud_movimiento_fondo_garantia') || $root.can('revertir_autorizacion_solicitud_movimiento_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia') || $root.can('generar_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia')
            || $root.can('registrar_solicitud_movimiento_fondo_garantia')">
                <a href="#" class="nav-link" @click="mostrarMenu($event)">
                    <p>
                        Gestión de Fondos de Garantía
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_detalle_fondo_garantia') || $root.can('generar_fondo_garantia') || $root.can('consultar_detalle_fondo_garantia')
            || $root.can('registrar_solicitud_movimiento_fondo_garantia')">
                        <router-link :to="{name: 'fondo-garantia'}" class="nav-link" :class="{active: this.$route.name == 'fondo-garantia'}">
                            <i class="fa fa-circle-o nav-icon"></i>
                            <p>Fondos de Garantía</p>
                        </router-link>
                    </li>
                </ul>
                <ul class="nav nav-treeview">
                    <li class="nav-item" v-if="$root.can('consultar_solicitud_movimiento_fondo_garantia') || $root.can('registrar_solicitud_movimiento_fondo_garantia') || $root.can('cancelar_solicitud_movimiento_fondo_garantia')
            || $root.can('autorizar_solicitud_movimiento_fondo_garantia') || $root.can('rechazar_solicitud_movimiento_fondo_garantia') || $root.can('revertir_autorizacion_solicitud_movimiento_fondo_garantia')">

                            <router-link :to="{name: 'solicitud-movimiento-fg'}" class="nav-link" :class="{active: this.$route.name == 'solicitud-movimiento-fg'}">
                                <i class="fa fa-circle-o nav-icon"></i>
                                <p>Solicitud de Movimiento</p>
                            </router-link>

                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.sidebar-menu -->
</template>

<script>
    export default {
        name: "contratos-menu",

        methods: {
            mostrarMenu(event) {
                event.stopPropagation();
                $(event.target).closest('li').toggleClass('menu-open');
            }
        }

    }
</script>

<style scoped>
    .sidebar-form, .nav-sidebar > .nav-header {
        padding: 1rem 0.5rem 0.5rem 1rem;
    }
</style>