<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Spinner from './Spinner.vue';
import { reproducir } from '../../composables/useSonido.js';

const props = defineProps({
    variante: { type: String, default: 'primario' },
    tamano: { type: String, default: 'normal' },
    href: { type: String, default: null },
    tipo: { type: String, default: 'button' },
    cargando: { type: Boolean, default: false },
});

const VARIANTES = {
    primario: `overflow-hidden border-brand bg-brand text-[#14130c] shadow-[0_0_16px_#ffe15a2e,inset_0_1px_0_#ffffff60]
        not-disabled:hover:border-[#fff0a8] not-disabled:hover:bg-[#ffea8c]
        not-disabled:hover:shadow-[0_0_0_1px_#ffe15a,0_0_22px_#ffe15a80,0_0_52px_#ffe15a30,inset_0_1px_0_#ffffff80]
        before:pointer-events-none before:absolute before:inset-y-0 before:-left-[45%] before:w-[32%] before:-skew-x-[18deg]
        before:bg-linear-[105deg,transparent,#ffffff70,transparent]
        not-disabled:hover:before:animate-brillo focus-visible:before:animate-brillo
        light:border-[#d4b52f] light:shadow-[0_2px_10px_#b9930033]
        light:not-disabled:hover:shadow-[0_0_0_1px_#d4b52f,0_6px_22px_#b9930060]`,
    secundario: `border-[#42464e] bg-white/[.02] text-[#dddfe4]
        not-disabled:hover:border-ice not-disabled:hover:bg-[#94d9ed0d] not-disabled:hover:text-white
        not-disabled:hover:shadow-[0_0_0_1px_#94d9ed40,0_0_20px_#94d9ed38,inset_0_0_14px_#94d9ed14]
        light:border-[#c8c3b7] light:bg-panel light:text-[#343942]
        light:not-disabled:hover:border-[#08718a] light:not-disabled:hover:bg-[#eef7f9] light:not-disabled:hover:text-[#08718a]
        light:not-disabled:hover:shadow-[0_0_16px_#08718a2e]`,
    peligro: `border-[#d25d6e] bg-[#a82e40] text-white shadow-[0_0_16px_#e5788726]
        not-disabled:hover:border-[#ff9aa6] not-disabled:hover:bg-[#bd364a]
        not-disabled:hover:shadow-[0_0_0_1px_#e57887,0_0_24px_#e5788780,0_0_52px_#e5788730]`,
};

const TAMANOS = {
    normal: 'min-h-[46px] px-5 py-3 text-[.72rem]',
    compacto: 'min-h-12 px-2 text-[.64rem] xs:px-3 xs:text-[.68rem]',
};

const clases = computed(() => [
    'relative inline-flex items-center justify-center gap-3 rounded-[3px] border font-semibold leading-[1.4] tracking-[.1em] whitespace-nowrap uppercase',
    'transition duration-200 active:translate-y-px active:brightness-110 disabled:cursor-not-allowed disabled:opacity-55 disabled:shadow-none',
    VARIANTES[props.variante],
    TAMANOS[props.tamano],
]);
</script>

<template>
    <Link v-if="href" :href="href" :class="clases" @click="reproducir('clic')">
        <slot />
    </Link>
    <button v-else :type="tipo" :class="clases" @click="reproducir('clic')">
        <Spinner v-if="cargando" />
        <slot />
    </button>
</template>
