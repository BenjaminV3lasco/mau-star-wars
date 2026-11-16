<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PersonajeForm from '../../Components/Organisms/PersonajeForm.vue';
import PersonajeFormLayout from '../../Components/Templates/PersonajeFormLayout.vue';

const form = useForm({
    nombre: '',
    altura: '',
    masa: '',
    color_cabello: '',
    color_ojos: '',
    genero: '',
    planeta_origen: '',
});

function guardar() {
    form.post('/personajes');
}
</script>

<template>
    <Head title="Crear personaje" />

    <PersonajeFormLayout
        titulo="Crear personaje"
        descripcion="Sumá un nuevo protagonista a tu galaxia. Completá su identidad y los detalles de su ficha."
    >
        <PersonajeForm :form="form" texto-enviar="Guardar personaje" @enviar="guardar" />
    </PersonajeFormLayout>
</template>
