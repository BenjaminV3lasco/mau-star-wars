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
            hover:border-[#8b7d43] hover:shadow-[0_12px_30px_#00000040] motion-safe:hover:-translate-y-[3px]
            light:border-[#d8d3c7] light:from-panel light:to-[#f7f5ee] light:shadow-[0_5px_18px_#34302a0c]
            light:focus-within:border-[#b69942] light:hover:border-[#b69942] light:hover:shadow-[0_12px_30px_#45402d16]"
    >
        <span
            class="absolute top-[23px] -left-px h-[30px] w-[3px] bg-gold shadow-[0_0_12px_#ffe15a25] transition
                group-focus-within:scale-y-160 group-focus-within:shadow-[0_0_16px_#ffe15a55] group-hover:scale-y-160 group-hover:shadow-[0_0_16px_#ffe15a55]"
            aria-hidden="true"
        ></span>

        <Link :href="hrefFicha" :aria-label="`Abrir ficha completa de ${personaje.nombre}`" class="group/ficha block px-4 pt-5 pb-[.9rem] xs:px-6 xs:pt-[1.4rem] xs:pb-4">
            <div class="flex items-center justify-between gap-3">
                <span class="text-[.62rem] tracking-[.16em] text-[#90959e] uppercase light:text-[#626975]">Ficha / {{ numeroFicha(personaje.id) }}</span>
                <InsigniaOrigen :swapi="personaje.origen_swapi" />
            </div>

            <h3 class="my-5 font-display text-[2rem] leading-[1.15] font-semibold tracking-[.015em] transition-colors group-hover/ficha:text-gold group-focus-visible/ficha:text-gold light:text-strong">
                {{ personaje.nombre }}
            </h3>

            <dl class="grid grid-cols-2 gap-4">
                <div
                    v-for="(medida, indice) in medidas"
                    :key="medida.etiqueta"
                    :class="{ 'border-l border-line pl-5': indice > 0 }"
                >
                    <dt class="text-xs text-muted">{{ medida.etiqueta }}</dt>
                    <dd class="font-display text-[2rem] leading-[1.3] font-semibold">
                        {{ medida.valor ?? '—' }}
                        <span v-if="medida.valor != null" class="font-sans text-xs font-normal text-muted">{{ medida.unidad }}</span>
                        <span v-else class="sr-only">Sin datos</span>
                    </dd>
                </div>
                <div class="col-span-2 flex items-center justify-between pt-3 pb-4">
                    <dt class="text-xs text-muted">Género</dt>
                    <dd class="text-ui">{{ etiquetaGenero(personaje.genero) }}</dd>
                </div>
            </dl>

            <span class="mt-[.8rem] flex items-center justify-end gap-2 text-2xs tracking-[.04em] text-muted">
                Abrir ficha completa <span class="text-[.9rem] text-gold" aria-hidden="true">↗</span>
            </span>
        </Link>

        <div class="flex items-center justify-between gap-4 border-t border-line px-4 py-2.5 xs:px-6">
            <Link
                :href="`/personajes/${personaje.id}/editar`"
                :aria-label="`Editar ficha de ${personaje.nombre}`"
                class="group/editar flex min-h-11 items-center gap-2.5 text-ui font-medium text-gold"
            >
                Editar ficha
                <span class="text-lg transition-transform group-hover/editar:translate-x-0.5 group-hover/editar:-translate-y-0.5" aria-hidden="true">↗</span>
            </Link>
            <button
                type="button"
                class="min-h-11 rounded-[3px] p-2 text-ui text-[#ffabb2] transition-colors hover:bg-[#f1959510] hover:text-[#ffccd0] light:text-[#a3283a] light:hover:text-[#7d2630]"
                :aria-label="`Eliminar a ${personaje.nombre}`"
                @click="$emit('eliminar', personaje)"
            >
                Eliminar
            </button>
        </div>
    </li>
</template>
