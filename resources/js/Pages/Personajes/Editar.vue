<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PersonajeForm from '../../Components/PersonajeForm.vue';

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

    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <h1 class="text-3xl font-semibold text-slate-900">Editar personaje</h1>
        <PersonajeForm :form="form" texto-submit="Actualizar" @submit="actualizar" />
    </main>
</template>
