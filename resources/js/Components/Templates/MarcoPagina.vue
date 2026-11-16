<script setup>
defineProps({ compacto: { type: Boolean, default: false } });
</script>

<template>
    <main id="contenido" class="mx-auto max-w-[81rem] px-5 pt-5 pb-12 sm:px-8 sm:pb-16" :class="compacto ? 'sm:pt-6' : 'sm:pt-8'">
        <slot />
    </main>
</template>
