<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PersonajeForm from '../../Components/PersonajeForm.vue';
import PersonajeFormLayout from '../../Components/PersonajeFormLayout.vue';

const props = defineProps({
    personaje: { type: Object, required: true },
});

const form = useForm({
    nombre: props.personaje.nombre,
    altura: props.personaje.altura ?? '',
    masa: props.personaje.masa ?? '',
    color_cabello: props.personaje.color_cabello ?? '',
    color_ojos: props.personaje.color_ojos ?? '',
    genero: props.personaje.genero,
    planeta_origen: props.personaje.planeta_origen ?? '',
});

function actualizar() {
    form.put(`/personajes/${props.personaje.id}`);
}
</script>

<template>
    <Head title="Editar personaje" />

    <PersonajeFormLayout titulo="Editar personaje" :descripcion="`Actualizá los datos de ${personaje.nombre} y mantené su historia al día.`">
        <PersonajeForm :form="form" texto-submit="Actualizar personaje" @submit="actualizar" />
    </PersonajeFormLayout>
</template>
