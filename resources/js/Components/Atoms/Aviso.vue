<script setup>
defineProps({ tipo: { type: String, default: 'exito' } });
</script>

<template>
    <p
        v-if="tipo === 'exito'"
        role="status"
        class="animate-entrar rounded-[3px] border border-l-[3px] border-[#477866] border-l-[#a8e3be] bg-[#12251d] px-[1.2rem] py-4 text-sm text-[#c6efd7]
            light:border-[#9dc6ae] light:border-l-[#438764] light:bg-[#e8f5ec] light:text-[#1c5636]"
    >
        <slot />
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
