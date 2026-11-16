<script setup>
import CabeceraSitio from '../Organisms/CabeceraSitio.vue';
</script>

<template>
    <div class="flex min-h-svh flex-col">
        <CabeceraSitio />
        <div class="flex-1">
            <slot />
        </div>
        <footer
            class="mx-5 flex flex-wrap justify-between gap-2 border-t border-white/10 py-6 text-2xs leading-[1.8] text-[#818792]
                sm:mx-8 sm:flex-nowrap sm:gap-4 lg:mx-auto lg:w-full lg:max-w-[77rem] light:border-[#d8d3c7] light:text-[#626975]"
        >
            <span>Star Wars · Archivo de personajes</span>
            <span>Que la Fuerza te acompañe.</span>
        </footer>
    </div>
</template>
