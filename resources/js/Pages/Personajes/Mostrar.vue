<script setup>
import { computed, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import EliminarPersonajeModal from '../../Components/EliminarPersonajeModal.vue';

const props = defineProps({ personaje: { type: Object, required: true } });
const page = usePage();
const personajeAEliminar = ref(null);
const generos = { masculino: 'Masculino', femenino: 'Femenino', 'n/a': 'No aplica' };
const volverHref = computed(() => {
    const indiceConsulta = page.url.indexOf('?');
    const consulta = indiceConsulta === -1 ? '' : page.url.slice(indiceConsulta);

    return '/personajes' + consulta;
});
</script>

<template>
    <Head :title="props.personaje.nombre + ' · Ficha de personaje'" />

    <main id="contenido" class="page-shell record-page">
        <Link :href="volverHref" class="back-link"><span aria-hidden="true">←</span> Volver al archivo</Link>

        <article class="record-sheet" aria-labelledby="ficha-titulo">
            <div class="record-topline">
                <span class="record-number">Ficha / {{ String(personaje.id).padStart(3, '0') }}</span>
                <span class="origin-badge" :class="{ 'origin-swapi': personaje.origen_swapi }">
                    {{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}
                </span>
            </div>

            <header class="record-heading">
                <div>
                    <p class="eyebrow"><span aria-hidden="true"></span> Archivo galáctico</p>
                    <h1 id="ficha-titulo" class="record-title">{{ personaje.nombre }}</h1>
                    <p class="record-lede">Ficha completa del personaje y sus datos registrados.</p>
                </div>
                <span class="record-emblem" aria-hidden="true">✦</span>
            </header>

            <section aria-labelledby="caracteristicas-titulo" class="record-section">
                <h2 id="caracteristicas-titulo" class="record-section-title">Características</h2>
                <dl class="record-data-grid">
                    <div class="record-data-item">
                        <dt>Altura</dt>
                        <dd>{{ personaje.altura != null ? personaje.altura + ' cm' : 'Sin datos registrados' }}</dd>
                    </div>
                    <div class="record-data-item">
                        <dt>Masa</dt>
                        <dd>{{ personaje.masa != null ? personaje.masa + ' kg' : 'Sin datos registrados' }}</dd>
                    </div>
                    <div class="record-data-item">
                        <dt>Género</dt>
                        <dd>{{ generos[personaje.genero] ?? personaje.genero }}</dd>
                    </div>
                    <div class="record-data-item">
                        <dt>Color de cabello</dt>
                        <dd>{{ personaje.color_cabello || 'Sin datos registrados' }}</dd>
                    </div>
                    <div class="record-data-item">
                        <dt>Color de ojos</dt>
                        <dd>{{ personaje.color_ojos || 'Sin datos registrados' }}</dd>
                    </div>
                    <div class="record-data-item">
                        <dt>Planeta de origen</dt>
                        <dd>{{ personaje.planeta_origen || 'Sin datos registrados' }}</dd>
                    </div>
                </dl>
            </section>

            <section aria-labelledby="procedencia-titulo" class="record-section record-provenance">
                <div>
                    <h2 id="procedencia-titulo" class="record-section-title">Procedencia</h2>
                    <p>{{ personaje.origen_swapi ? 'Importado desde la API de Star Wars.' : 'Creado manualmente en este archivo.' }}</p>
                </div>
                <p v-if="personaje.swapi_id != null" class="swapi-reference">ID de SWAPI <strong>#{{ personaje.swapi_id }}</strong></p>
            </section>

            <div class="record-actions" role="group" aria-label="Acciones de la ficha">
                <Link :href="'/personajes/' + personaje.id + '/editar'" class="btn-primary">Editar ficha <span aria-hidden="true">↗</span></Link>
                <button type="button" class="btn-danger" @click="personajeAEliminar = personaje">Eliminar personaje</button>
            </div>
        </article>

        <EliminarPersonajeModal
            :personaje="personajeAEliminar"
            @cerrar="personajeAEliminar = null"
            @eliminado="personajeAEliminar = null"
        />
    </main>
</template>
