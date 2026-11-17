<script setup>
// Indica si la ficha es manual o vino de SWAPI (con su ID, si se pasa).
defineProps({
    swapi: { type: Boolean, default: false },
    swapiId: { type: Number, default: null },
});
</script>

<template>
    <span
        class="inline-flex items-center gap-1.5 rounded-[2px] border px-[.45rem] py-1 text-[.6rem] tracking-[.07em] uppercase"
        :class="swapi
            ? 'border-[#365963] text-ice light:border-[#8ebac3] light:text-[#075f72]'
            : 'border-[#414349] text-[#c4c7ce] light:border-[#d1ccbf] light:text-[#525966]'"
    >
        <span class="size-1 rounded-full bg-current" aria-hidden="true"></span>
        {{ swapi ? 'SWAPI' : 'Manual' }}<span v-if="swapiId" class="tabular-nums">#{{ swapiId }}</span>
    </span>
</template>
