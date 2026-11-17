<script setup>
// Mensaje de éxito (role="status") o de error (role="alert"), para que los
// lectores de pantalla lo anuncien sin mover el foco.
import { onMounted } from 'vue';
import { reproducir } from '../../composables/useSonido.js';

const props = defineProps({ tipo: { type: String, default: 'exito' } });

onMounted(() => {
    if (props.tipo === 'error') reproducir('error');
});
</script>

<template>
    <p
        v-if="tipo === 'exito'"
        role="status"
        class="flex animate-entrar items-center gap-3 rounded-[3px] border border-l-[3px] border-[#477866] border-l-[#a8e3be] bg-[#12251d] px-[1.2rem] py-4 text-sm text-[#c6efd7]
            light:border-[#9dc6ae] light:border-l-[#438764] light:bg-[#e8f5ec] light:text-[#1c5636]"
    >
        <span class="text-[#a8e3be] light:text-[#438764]" aria-hidden="true">✦</span>
        <span><slot /></span>
    </p>
    <p
        v-else
        role="alert"
        class="rounded-[3px] border border-[#a45460] bg-[#30171d] px-4 py-[.85rem] text-[.82rem] leading-relaxed text-[#ffbfc7]
            light:border-[#d7989e] light:bg-[#fff0f1] light:text-[#7d2630]"
    >
        <slot />
    </p>
</template>
