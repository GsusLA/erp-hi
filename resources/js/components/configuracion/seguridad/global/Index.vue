<template>
    <span id="configuracion-seguridad">
        <asignacion-roles-masiva ref="asignacion" v-if="$root.can('asignar_roles')"></asignacion-roles-masiva>
        <desasignacion-roles-masiva ref="desasignacion" v-if="$root.can('asignar_roles')"></desasignacion-roles-masiva>
        <seguridad-rol-create @creado="addRol($event)" v-if="$root.can('registrar_roles')"></seguridad-rol-create>
        <asignacion-permisos-global v-if="$root.can('asignar_permisos')"></asignacion-permisos-global>
        <rol-delete v-if="$root.can('eliminar_roles')"></rol-delete>
    </span>
</template>

<script>
    import AsignacionRolesMasiva from "./asignacion-roles/Masiva";
    import DesasignacionRolesMasiva from "./desasignacion-roles/Masiva";
    import AsignacionPermisosGlobal from "./asignacion-permisos/Global";
    import SeguridadRolCreate from "../../../seguridad/global/rol/Create";
    import RolDelete from "../../../seguridad/rol/Delete";
    export default {
        name: "configuracion-seguridad",
        components: {
            RolDelete,
            SeguridadRolCreate, AsignacionPermisosGlobal, DesasignacionRolesMasiva, AsignacionRolesMasiva
        },

        methods: {
            addRol(data) {
                this.$refs.asignacion.addRol(data);
                this.$refs.desasignacion.addRol(data);
            }
        }
    }
</script>