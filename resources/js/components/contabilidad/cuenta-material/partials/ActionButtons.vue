<template>
    <div class="btn-group">
        <button @click="show" v-if="value.show" type="button" class="btn btn-sm btn-outline-secondary "><i class="fa fa-eye"></i></button>
        <CuentaMaterialEdit v-if="value.edit" v-bind:id="value.id" :key="value.id"/>
        <button @click="destroy"  v-if="value.delete" type="button" class="btn btn-sm btn-outline-danger "><i class="fa fa-trash"></i></button>

    </div>
</template>

<script>
    import CuentaMaterialEdit from "../Edit";
    export default {
        name: "action-buttons",
        components: {CuentaMaterialEdit},
        props: ['value'],
        methods: {
            destroy() {

            },
            show() {

            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>