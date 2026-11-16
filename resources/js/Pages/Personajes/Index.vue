<script setup>
import { nextTick, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Aviso from '../../Components/Atoms/Aviso.vue';
import Boton from '../../Components/Atoms/Boton.vue';
import Paginacion from '../../Components/Molecules/Paginacion.vue';
import EliminarPersonajeModal from '../../Components/Organisms/EliminarPersonajeModal.vue';
import FiltrosPersonajes from '../../Components/Organisms/FiltrosPersonajes.vue';
import ImportarPersonajeModal from '../../Components/Organisms/ImportarPersonajeModal.vue';
import PersonajeCard from '../../Components/Organisms/PersonajeCard.vue';
import PortadaSaga from '../../Components/Organisms/PortadaSaga.vue';
import MarcoPagina from '../../Components/Templates/MarcoPagina.vue';
import { useFiltrosPersonajes } from '../../composables/useFiltrosPersonajes.js';
import { conConsultaActual } from '../../utils/personaje.js';

const props = defineProps({
    personajes: { type: Object, required: true },
    filtros: { type: Object, required: true },
});

const page = usePage();
const { buscar, genero, orden, direccion, cargando, hayFiltros, hayAjustes, limpiarFiltros } = useFiltrosPersonajes(() => props.filtros);

const importacionAbierta = ref(false);
const personajeAEliminar = ref(null);
const tituloListado = ref(null);

function cantidadDePersonajes(total) {
    return `${total} ${total === 1 ? 'personaje' : 'personajes'}`;
}

async function operacionCompletada() {
    importacionAbierta.value = false;
    personajeAEliminar.value = null;
    await nextTick();
    tituloListado.value?.focus();
}
</script>

<template>
    <Head title="Archivo de personajes · Star Wars" />

    <MarcoPagina>
        <PortadaSaga antetitulo="El universo Star Wars">
            <template #titulo>Personajes<br>de la <em>galaxia.</em></template>
            <template #descripcion>Cada personaje tiene una historia.<br>Explorá sus fichas y construí tu propio archivo.</template>
        </PortadaSaga>

        <Aviso v-if="page.flash?.exito" class="mt-6">{{ page.flash.exito }}</Aviso>

        <section class="mt-7 sm:mt-[2.4rem]" aria-labelledby="listado-titulo" :aria-busy="cargando">
            <div class="mb-6 flex flex-wrap items-start justify-between gap-4 border-b border-line pb-[1.4rem] sm:flex-nowrap sm:items-center">
                <div class="flex w-full flex-wrap items-center justify-between gap-2.5 xs:w-auto xs:justify-start sm:gap-4">
                    <h2 id="listado-titulo" ref="tituloListado" tabindex="-1" class="font-display text-[1.6rem] font-semibold tracking-[.025em] text-strong uppercase sm:text-[1.85rem]">
                        Tu archivo
                    </h2>
                    <span class="rounded-[3px] border border-line px-2 py-1 text-[.67rem] text-[#b8bcc3] light:text-[#525966]" aria-live="polite">
                        {{ cantidadDePersonajes(personajes.total) }}
                    </span>
                </div>
                <div class="flex w-full flex-wrap gap-3 *:w-full xs:w-auto xs:*:w-auto">
                    <Boton variante="secundario" @click="importacionAbierta = true">Importar desde SWAPI</Boton>
                    <Boton href="/personajes/crear"><span class="text-lg" aria-hidden="true">＋</span> Crear personaje</Boton>
                </div>
            </div>

            <FiltrosPersonajes
                v-model:buscar="buscar"
                v-model:genero="genero"
                v-model:orden="orden"
                v-model:direccion="direccion"
                :hay-ajustes="hayAjustes"
                @limpiar="limpiarFiltros"
            />

            <p class="mb-[1.1rem] min-h-[1.2rem] text-left text-[.68rem] text-muted xs:text-right" role="status" aria-live="polite">
                {{ cargando ? 'Actualizando el archivo…' : `Se muestran ${cantidadDePersonajes(personajes.total)}` }}
            </p>

            <ul v-if="personajes.data.length" class="grid grid-cols-1 gap-[1.4rem] xs:grid-cols-2 md:grid-cols-3">
                <PersonajeCard
                    v-for="personaje in personajes.data"
                    :key="personaje.id"
                    :personaje="personaje"
                    :href-ficha="conConsultaActual(`/personajes/${personaje.id}`, page.url)"
                    @eliminar="personajeAEliminar = $event"
                />
            </ul>
            <div v-else class="border border-dashed border-[#464951] bg-panel px-6 py-14 text-center light:border-[#c8c3b7]">
                <h3 class="mb-2.5 font-display text-[1.8rem] text-strong">
                    {{ hayFiltros ? 'No encontramos coincidencias.' : 'El comienzo de una nueva historia.' }}
                </h3>
                <p class="text-sm text-muted">
                    {{ hayFiltros
                        ? 'Probá cambiar el nombre o el género, o limpiá los filtros para ver todo el archivo.'
                        : 'Creá tu primer personaje o importalo desde SWAPI para empezar el archivo.' }}
                </p>
            </div>

            <Paginacion :paginador="personajes" />
        </section>

        <ImportarPersonajeModal :abierto="importacionAbierta" @cerrar="importacionAbierta = false" @importado="operacionCompletada" />
        <EliminarPersonajeModal :personaje="personajeAEliminar" @cerrar="personajeAEliminar = null" @eliminado="operacionCompletada" />
    </MarcoPagina>
</template>
