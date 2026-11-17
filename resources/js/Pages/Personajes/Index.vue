<script setup>
import { nextTick, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Aviso from '../../Components/Atoms/Aviso.vue';
import Boton from '../../Components/Atoms/Boton.vue';
import EsquinasHud from '../../Components/Atoms/EsquinasHud.vue';
import LineaSable from '../../Components/Atoms/LineaSable.vue';
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
            <template #descripcion>Creá, editá o importá fichas desde SWAPI.</template>
        </PortadaSaga>

        <Aviso v-if="page.flash?.exito" class="mt-6">{{ page.flash.exito }}</Aviso>

        <section class="mt-7 sm:mt-[2.4rem]" aria-labelledby="listado-titulo" :aria-busy="cargando">
            <div class="relative mb-6 flex flex-wrap items-start justify-between gap-4 border-b border-line pb-[1.4rem] sm:flex-nowrap sm:items-center">
                <LineaSable />
                <div class="flex w-full flex-wrap items-center justify-between gap-2.5 xs:w-auto xs:justify-start sm:gap-4">
                    <h2 id="listado-titulo" ref="tituloListado" tabindex="-1" class="font-display text-[1.6rem] font-semibold tracking-[.025em] text-strong uppercase sm:text-[1.85rem]">
                        Tu archivo
                    </h2>
                    <span class="inline-flex items-center gap-2 rounded-[3px] border border-ice/30 bg-ice/5 px-2.5 py-1 text-[.67rem] tracking-[.06em] text-ice tabular-nums light:border-[#08718a]/30 light:text-[#08718a]" aria-live="polite">
                        <span class="size-1.5 rounded-full bg-current shadow-[0_0_8px_currentColor]" :class="{ 'animate-pulse': cargando }" aria-hidden="true"></span>
                        {{ cargando ? 'Buscando…' : cantidadDePersonajes(personajes.total) }}
                    </span>
                </div>
                <div class="flex w-full flex-wrap gap-3 *:w-full xs:w-auto xs:*:w-auto">
                    <Boton variante="secundario" @click="importacionAbierta = true">Importar desde SWAPI</Boton>
                    <Boton href="/personajes/crear"><span class="text-lg" aria-hidden="true">＋</span> Crear nuevo</Boton>
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

            <div class="relative mb-5 h-px overflow-hidden" aria-hidden="true">
                <span v-if="cargando" class="absolute inset-y-0 w-1/3 animate-escaneo bg-linear-to-r from-transparent via-gold to-transparent"></span>
            </div>

            <ul
                v-if="personajes.data.length"
                class="grid grid-cols-1 gap-4 transition-opacity xs:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                :class="{ 'opacity-60': cargando }"
            >
                <PersonajeCard
                    v-for="personaje in personajes.data"
                    :key="personaje.id"
                    :personaje="personaje"
                    :href-ficha="conConsultaActual(`/personajes/${personaje.id}`, page.url)"
                    @eliminar="personajeAEliminar = $event"
                />
            </ul>
            <div v-else class="relative border border-dashed border-[#464951] bg-panel px-6 py-14 text-center light:border-[#c8c3b7]">
                <EsquinasHud />
                <span class="mb-4 inline-block text-2xl text-gold" aria-hidden="true">✦</span>
                <h3 class="mb-2.5 font-display text-[1.8rem] font-semibold text-strong">
                    {{ hayFiltros ? 'Estos no son los personajes que buscás.' : 'El archivo está vacío… por ahora.' }}
                </h3>
                <p class="text-sm text-muted">
                    {{ hayFiltros
                        ? 'Probá con otro nombre o género, o limpiá los filtros para ver todo el archivo.'
                        : 'Creá tu primer personaje o traelo desde SWAPI para empezar la historia.' }}
                </p>
            </div>

            <Paginacion :paginador="personajes" />
        </section>

        <ImportarPersonajeModal :abierto="importacionAbierta" @cerrar="importacionAbierta = false" @importado="operacionCompletada" />
        <EliminarPersonajeModal :personaje="personajeAEliminar" @cerrar="personajeAEliminar = null" @eliminado="operacionCompletada" />
    </MarcoPagina>
</template>
