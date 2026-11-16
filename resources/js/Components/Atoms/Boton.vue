<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Spinner from './Spinner.vue';

const props = defineProps({
    variante: { type: String, default: 'primario' },
    tamano: { type: String, default: 'normal' },
    href: { type: String, default: null },
    tipo: { type: String, default: 'button' },
    cargando: { type: Boolean, default: false },
});

const VARIANTES = {
    primario: `overflow-hidden border-brand bg-brand text-[#14130c]
        hover:border-[#ffea8c] hover:bg-[#ffea8c] hover:shadow-[0_0_20px_#ffe15a20]
        before:pointer-events-none before:absolute before:inset-y-0 before:-left-[45%] before:w-[32%] before:-skew-x-[18deg]
        before:bg-linear-[105deg,transparent,#ffffff65,transparent]
        hover:before:animate-brillo focus-visible:before:animate-brillo
        light:border-[#d4b52f] light:hover:border-[#b99a22]`,
    secundario: `border-[#42464e] text-[#dddfe4] hover:border-[#9a9fa9] hover:bg-white/[.02]
        light:border-[#c8c3b7] light:bg-panel light:text-[#343942] light:hover:border-[#8f8876] light:hover:bg-[#f0ede4]`,
    peligro: 'border-[#d25d6e] bg-[#a82e40] text-white hover:enabled:border-[#e57887] hover:enabled:bg-[#bd364a]',
};

const TAMANOS = {
    normal: 'min-h-[46px] px-[1.15rem] py-3 text-ui',
    compacto: 'min-h-12 px-2 text-[.69rem] xs:px-3 xs:text-ui',
};

const clases = computed(() => [
    'relative inline-flex items-center justify-center gap-3 rounded-[3px] border font-semibold leading-[1.4] whitespace-nowrap transition',
    'active:translate-y-px disabled:cursor-not-allowed disabled:opacity-55 disabled:shadow-none',
    VARIANTES[props.variante],
    TAMANOS[props.tamano],
]);
</script>

<template>
    <Link v-if="href" :href="href" :class="clases">
        <slot />
    </Link>
    <button v-else :type="tipo" :class="clases">
        <Spinner v-if="cargando" />
        <slot />
    </button>
</template>
