<script setup>
import { computed, ref } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import Antetitulo from '../../Components/Atoms/Antetitulo.vue';
import Boton from '../../Components/Atoms/Boton.vue';
import EnlaceVolver from '../../Components/Atoms/EnlaceVolver.vue';
import InsigniaOrigen from '../../Components/Atoms/InsigniaOrigen.vue';
import EliminarPersonajeModal from '../../Components/Organisms/EliminarPersonajeModal.vue';
import MarcoPagina from '../../Components/Templates/MarcoPagina.vue';
import { conConsultaActual, etiquetaGenero, numeroFicha } from '../../utils/personaje.js';

const props = defineProps({ personaje: { type: Object, required: true } });

const page = usePage();
const personajeAEliminar = ref(null);
const SIN_DATOS = 'Sin datos registrados';

const caracteristicas = computed(() => [
    { etiqueta: 'Altura', valor: props.personaje.altura != null ? `${props.personaje.altura} cm` : SIN_DATOS },
    { etiqueta: 'Masa', valor: props.personaje.masa != null ? `${props.personaje.masa} kg` : SIN_DATOS },
    { etiqueta: 'Género', valor: etiquetaGenero(props.personaje.genero) },
    { etiqueta: 'Color de cabello', valor: props.personaje.color_cabello || SIN_DATOS },
    { etiqueta: 'Color de ojos', valor: props.personaje.color_ojos || SIN_DATOS },
    { etiqueta: 'Planeta de origen', valor: props.personaje.planeta_origen || SIN_DATOS },
]);
</script>

<template>
    <Head :title="`${personaje.nombre} · Ficha de personaje`" />

    <MarcoPagina compacto>
        <EnlaceVolver :href="conConsultaActual('/personajes', page.url)" class="mb-[1.1rem]" />

        <article
            class="relative isolate mx-auto max-w-[68rem] overflow-hidden rounded-[3px] border border-[#45454a] p-[clamp(1.35rem,4vw,3rem)] shadow-[0_20px_60px_#00000025]
                bg-[radial-gradient(ellipse_at_95%_0%,#ffe15a0b,transparent_36%),linear-gradient(135deg,#17191e,#101216_78%)]
                light:border-[#d8d3c7] light:shadow-[0_16px_45px_#34302a10]
                light:bg-[radial-gradient(ellipse_at_95%_0%,#d3a9000b,transparent_36%),linear-gradient(135deg,#fffefa,#f6f3e9_78%)]"
            aria-labelledby="ficha-titulo"
        >
            <span class="absolute top-12 left-0 -z-10 h-16 w-[3px] bg-gold shadow-[0_0_16px_#ffe15a40]" aria-hidden="true"></span>

            <div class="flex items-center justify-between gap-4">
                <span class="text-2xs tracking-[.16em] text-muted uppercase">Ficha / {{ numeroFicha(personaje.id) }}</span>
                <InsigniaOrigen :swapi="personaje.origen_swapi" />
            </div>

            <header class="flex items-start justify-between gap-6 py-8 xs:items-center sm:pt-[2.7rem] sm:pb-[2.2rem]">
                <div>
                    <Antetitulo linea class="mb-2.5">Archivo galáctico</Antetitulo>
                    <h1 id="ficha-titulo" class="font-display text-[clamp(3rem,7vw,5.4rem)] leading-[.98] font-semibold text-strong">{{ personaje.nombre }}</h1>
                    <p class="mt-[.8rem] text-[.86rem] leading-[1.7] text-soft">Ficha completa del personaje y sus datos registrados.</p>
                </div>
                <span
                    class="inline-flex size-11 flex-none items-center justify-center rounded-full border border-gold/25 text-base text-gold shadow-[0_0_35px_#ffe15a0b] xs:size-[4.5rem] xs:text-2xl"
                    aria-hidden="true"
                >✦</span>
            </header>

            <section class="border-t border-line pt-6" aria-labelledby="caracteristicas-titulo">
                <h2 id="caracteristicas-titulo" class="mb-4 text-2xs font-semibold tracking-[.14em] text-soft uppercase">Características</h2>
                <dl class="grid grid-cols-1 gap-[.8rem] xs:grid-cols-2 sm:grid-cols-3">
                    <div
                        v-for="dato in caracteristicas"
                        :key="dato.etiqueta"
                        class="min-w-0 rounded-[3px] border border-white/[.07] bg-white/[.016] p-4 xs:min-h-[6.1rem] light:border-[#ddd8cc] light:bg-white/65"
                    >
                        <dt class="mb-2.5 text-xs text-muted">{{ dato.etiqueta }}</dt>
                        <dd class="font-display text-[clamp(1.2rem,2vw,1.7rem)] leading-[1.25] font-medium wrap-anywhere text-strong">{{ dato.valor }}</dd>
                    </div>
                </dl>
            </section>

            <section
                class="mt-6 flex flex-col items-start gap-[.8rem] border-t border-line pt-6 xs:flex-row xs:items-center xs:justify-between xs:gap-6"
                aria-labelledby="procedencia-titulo"
            >
                <div>
                    <h2 id="procedencia-titulo" class="mb-2 text-2xs font-semibold tracking-[.14em] text-soft uppercase">Procedencia</h2>
                    <p class="text-[.82rem] leading-[1.6] text-soft">
                        {{ personaje.origen_swapi ? 'Importado desde la API de Star Wars.' : 'Creado manualmente en este archivo.' }}
                    </p>
                </div>
                <p v-if="personaje.swapi_id != null" class="text-[.73rem] text-muted">
                    ID de SWAPI <strong class="ml-1.5 font-semibold text-ice">#{{ personaje.swapi_id }}</strong>
                </p>
            </section>

            <div class="mt-8 flex flex-col gap-3 border-t border-line pt-6 xs:flex-row xs:flex-wrap xs:*:min-w-40" role="group" aria-label="Acciones de la ficha">
                <Boton :href="`/personajes/${personaje.id}/editar`">Editar ficha <span class="text-lg" aria-hidden="true">↗</span></Boton>
                <Boton variante="peligro" @click="personajeAEliminar = personaje">Eliminar personaje</Boton>
            </div>
        </article>

        <EliminarPersonajeModal :personaje="personajeAEliminar" @cerrar="personajeAEliminar = null" @eliminado="personajeAEliminar = null" />
    </MarcoPagina>
</template>
