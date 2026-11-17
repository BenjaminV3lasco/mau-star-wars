<script setup>
// Portada del sitio.
import { Head } from '@inertiajs/vue3';
import Boton from '../Components/Atoms/Boton.vue';
import PortadaSaga from '../Components/Organisms/PortadaSaga.vue';
import MarcoPagina from '../Components/Templates/MarcoPagina.vue';
</script>

<template>
    <Head title="Archivo galáctico · Star Wars" />

    <MarcoPagina>
        <PortadaSaga antetitulo="Una galaxia muy, muy lejana…" amplia>
            <template #titulo>Grandes historias.<br><em>Personajes eternos.</em></template>
            <template #descripcion>Armá tu propio archivo de la saga:<br>creá fichas o importalas desde SWAPI.</template>
            <Boton href="/personajes" class="mt-8 animate-respirar">Explorar personajes <span class="text-lg" aria-hidden="true">→</span></Boton>
        </PortadaSaga>
    </MarcoPagina>
</template>
