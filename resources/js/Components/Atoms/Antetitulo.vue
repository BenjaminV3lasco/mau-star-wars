<script setup>
defineProps({ linea: { type: Boolean, default: false } });
</script>

<template>
    <p class="flex items-center gap-3 text-[.59rem] leading-[1.7] font-semibold tracking-[.12em] text-gold uppercase xs:text-2xs xs:tracking-[.18em]">
        <span v-if="linea" class="h-0.5 w-[25px] shrink-0 bg-gold" aria-hidden="true"></span>
        <slot />
    </p>
</template>
