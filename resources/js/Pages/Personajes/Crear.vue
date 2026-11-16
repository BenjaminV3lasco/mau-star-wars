<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PersonajeForm from '../../Components/PersonajeForm.vue';

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

    <main class="mx-auto max-w-2xl px-4 py-10 sm:px-6">
        <h1 class="text-3xl font-semibold text-slate-900">Crear personaje</h1>
        <PersonajeForm :form="form" texto-submit="Guardar" @submit="guardar" />
    </main>
</template>
