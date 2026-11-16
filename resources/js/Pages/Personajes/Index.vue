<script setup>
import { nextTick, ref } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import EliminarPersonajeModal from '../../Components/EliminarPersonajeModal.vue';
import Paginacion from '../../Components/Paginacion.vue';
import ImportarPersonajeModal from '../../Components/ImportarPersonajeModal.vue';

const page = usePage();
const personajeAEliminar = ref(null);
const importacionAbierta = ref(false);
const tituloArchivo = ref(null);
defineProps({ personajes: { type: Object, required: true } });
const generos = { masculino: 'Masculino', femenino: 'Femenino', 'n/a': 'No aplica' };

async function operacionCompletada() {
    personajeAEliminar.value = null;
    importacionAbierta.value = false;
    await nextTick();
    tituloArchivo.value?.focus();
}
</script>

<template>
    <Head title="Archivo de personajes · Star Wars" />
    <main id="contenido" class="page-shell">
        <header class="saga-hero">
            <div class="hero-copy">
                <p class="eyebrow"><span aria-hidden="true"></span> El universo Star Wars</p>
                <h1 class="page-title">Personajes<br>de la <em>galaxia.</em></h1>
                <p class="page-description">Cada personaje tiene una historia.<br>Explorá sus fichas y construí tu propio archivo.</p>
            </div>
        </header>

        <p v-if="page.flash?.exito" class="notice-success" role="status">{{ page.flash.exito }}</p>

        <section aria-labelledby="archivo-titulo" class="collection">
            <div class="collection-bar">
                <div class="collection-heading">
                    <h2 id="archivo-titulo" ref="tituloArchivo" tabindex="-1">Tu archivo</h2>
                    <span class="count-badge">{{ personajes.total }} {{ personajes.total === 1 ? 'personaje' : 'personajes' }}</span>
                </div>
                <div class="collection-actions">
                    <button type="button" class="btn-secondary" @click="importacionAbierta = true">Importar desde SWAPI</button>
                    <Link href="/personajes/crear" class="btn-primary"><span aria-hidden="true">＋</span> Crear personaje</Link>
                </div>
            </div>
            <ul v-if="personajes.data.length" class="character-grid">
                <li v-for="personaje in personajes.data" :key="personaje.id" class="character-card">
                    <div class="card-topline">
                        <span class="record-id">Ficha / {{ String(personaje.id).padStart(3, '0') }}</span>
                        <span class="origin-badge" :class="{ 'origin-swapi': personaje.origen_swapi }">{{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}</span>
                    </div>
                    <h3 class="card-heading">{{ personaje.nombre }}</h3>
                    <dl class="card-data">
                        <div>
                            <dt>Altura</dt>
                            <dd>{{ personaje.altura ?? '—' }} <span v-if="personaje.altura != null">cm</span><span v-else class="sr-only">Sin datos</span></dd>
                        </div>
                        <div>
                            <dt>Masa</dt>
                            <dd>{{ personaje.masa ?? '—' }} <span v-if="personaje.masa != null">kg</span><span v-else class="sr-only">Sin datos</span></dd>
                        </div>
                        <div class="gender-data">
                            <dt>Género</dt>
                            <dd>{{ generos[personaje.genero] ?? personaje.genero }}</dd>
                        </div>
                    </dl>
                    <div class="card-actions">
                        <Link :href="'/personajes/' + personaje.id + '/editar'" :aria-label="'Editar ficha de ' + personaje.nombre" class="card-action">
                            Editar ficha <span aria-hidden="true">↗</span>
                        </Link>
                        <button type="button" class="card-delete" :aria-label="'Eliminar a ' + personaje.nombre" @click="personajeAEliminar = personaje">
                            Eliminar
                        </button>
                    </div>
                </li>
            </ul>
            <div v-else class="empty-state">
                <h3>{{ personajes.total ? 'Esta página está vacía.' : 'El comienzo de una nueva historia.' }}</h3>
                <p v-if="!personajes.total">Creá tu primer personaje o importalo desde SWAPI para empezar el archivo.</p>
            </div>
            <Paginacion :paginador="personajes" />
        </section>
        <ImportarPersonajeModal
            :abierto="importacionAbierta"
            @cerrar="importacionAbierta = false"
            @importado="operacionCompletada"
        />
        <EliminarPersonajeModal
            :personaje="personajeAEliminar"
            @cerrar="personajeAEliminar = null"
            @eliminado="operacionCompletada"
        />
    </main>
</template>
