<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import InsigniaOrigen from '../Atoms/InsigniaOrigen.vue';
import { etiquetaGenero, numeroFicha } from '../../utils/personaje.js';

const props = defineProps({
    personaje: { type: Object, required: true },
    hrefFicha: { type: String, required: true },
});
defineEmits(['eliminar']);

const medidas = computed(() => [
    { etiqueta: 'Altura', valor: props.personaje.altura, unidad: 'cm' },
    { etiqueta: 'Masa', valor: props.personaje.masa, unidad: 'kg' },
]);
</script>

<template>
    <li
        class="group relative animate-entrar rounded-[3px] border border-[#383b41] bg-linear-135 from-[#1b1e23] to-[#111317] to-80% transition
            focus-within:border-[#8b7d43] focus-within:shadow-[0_0_24px_#ffe15a0d]
            hover:border-[#8b7d43] hover:shadow-[0_12px_30px_#00000040,0_0_26px_#ffe15a12] motion-safe:hover:-translate-y-0.5
            light:border-[#d8d3c7] light:from-panel light:to-[#f7f5ee] light:shadow-[0_5px_18px_#34302a0c]
            light:focus-within:border-[#b69942] light:hover:border-[#b69942] light:hover:shadow-[0_12px_30px_#45402d16]
            before:pointer-events-none before:absolute before:inset-x-6 before:-top-px before:h-px before:bg-linear-to-r
            before:from-transparent before:via-gold before:to-transparent before:opacity-0 before:transition-opacity
            hover:before:opacity-100 focus-within:before:opacity-100"
    >
        <span
            class="absolute top-4 -left-px h-6 w-[3px] rounded-full transition group-focus-within:scale-y-160 group-hover:scale-y-160"
            :class="personaje.origen_swapi
                ? 'bg-ice shadow-[0_0_12px_#94d9ed40] group-focus-within:shadow-[0_0_18px_#94d9ed80] group-hover:shadow-[0_0_18px_#94d9ed80]'
                : 'bg-gold shadow-[0_0_12px_#ffe15a25] group-focus-within:shadow-[0_0_16px_#ffe15a55] group-hover:shadow-[0_0_16px_#ffe15a55]'"
            aria-hidden="true"
        ></span>

        <Link :href="hrefFicha" :aria-label="`Ver la ficha de ${personaje.nombre}`" class="group/ficha block px-4 pt-4 pb-3 xs:px-5">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[.62rem] tracking-[.16em] text-[#90959e] tabular-nums uppercase light:text-[#626975]">Ficha / {{ numeroFicha(personaje.id) }}</span>
                <InsigniaOrigen :swapi="personaje.origen_swapi" />
            </div>

            <h3 :title="personaje.nombre" class="mt-2.5 mb-3 truncate font-display text-[1.6rem] leading-[1.2] font-semibold tracking-[.015em] transition-colors group-hover/ficha:text-gold group-focus-visible/ficha:text-gold light:text-strong">
                {{ personaje.nombre }}
            </h3>

            <dl class="grid grid-cols-3 divide-x divide-line">
                <div v-for="medida in medidas" :key="medida.etiqueta" class="pr-3 not-first:pl-3">
                    <dt class="text-2xs text-muted">{{ medida.etiqueta }}</dt>
                    <dd class="font-display text-[1.45rem] leading-[1.3] font-semibold tabular-nums">
                        {{ medida.valor ?? '—' }}
                        <span v-if="medida.valor != null" class="font-sans text-2xs font-normal text-muted">{{ medida.unidad }}</span>
                        <span v-else class="sr-only">Sin datos</span>
                    </dd>
                </div>
                <div class="pl-3">
                    <dt class="text-2xs text-muted">Género</dt>
                    <dd class="truncate pt-1.5 text-ui">{{ etiquetaGenero(personaje.genero) }}</dd>
                </div>
            </dl>
        </Link>

        <div class="flex items-center justify-between gap-4 border-t border-line px-4 xs:px-5">
            <Link
                :href="`/personajes/${personaje.id}/editar`"
                :aria-label="`Editar a ${personaje.nombre}`"
                class="group/editar flex min-h-10 items-center gap-2 text-ui font-medium text-gold"
            >
                Editar
                <span class="transition-transform group-hover/editar:translate-x-0.5 group-hover/editar:-translate-y-0.5" aria-hidden="true">↗</span>
            </Link>
            <button
                type="button"
                class="-mr-2 min-h-10 rounded-[3px] px-2 text-ui text-[#ffabb2] transition-colors hover:bg-[#f1959510] hover:text-[#ffccd0] light:text-[#a3283a] light:hover:text-[#7d2630]"
                :aria-label="`Eliminar a ${personaje.nombre}`"
                @click="$emit('eliminar', personaje)"
            >
                Eliminar
            </button>
        </div>
    </li>
</template>
