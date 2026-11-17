<script setup>
// Layout persistente (ver app.js): la cabecera no se vuelve a montar al navegar.
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
            <span>
                Datos provistos por
                <a href="https://swapi.py4e.com" class="underline decoration-white/20 underline-offset-2 hover:text-gold light:decoration-[#c8c3b7]" target="_blank" rel="noopener">SWAPI</a>
            </span>
            <span>Que la Fuerza te acompañe.</span>
        </footer>
    </div>
</template>
