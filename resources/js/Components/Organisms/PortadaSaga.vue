<script setup>
import Antetitulo from '../Atoms/Antetitulo.vue';
import TituloPagina from '../Atoms/TituloPagina.vue';

defineProps({
    antetitulo: { type: String, required: true },
    amplia: { type: Boolean, default: false },
});
</script>

<template>
    <section
        class="escena-oscura relative isolate flex items-end overflow-hidden rounded-[3px] border border-white/15 bg-[#05070c] sm:items-center"
        :class="amplia ? 'min-h-[570px] sm:min-h-[min(680px,calc(100svh-220px))]' : 'min-h-[440px] sm:min-h-[410px]'"
    >
        <div
            class="absolute inset-0 -z-20 animate-revelar bg-[url(/images/a-new-hope.jpg)] bg-size-[auto_380px] bg-position-[90%_top] bg-no-repeat
                sm:bg-size-[auto_125%] sm:bg-position-[right_top]"
            aria-hidden="true"
        ></div>
        <div
            class="absolute inset-0 -z-10 bg-[linear-gradient(0deg,#080a0e_0%,#080a0ef0_30%,#080a0e50_65%,transparent)]
                sm:bg-[linear-gradient(90deg,#080a0ef5_0%,#080a0ed9_28%,#080a0e45_57%,#080a0e00_80%),linear-gradient(0deg,#080a0e9c,transparent_28%)]"
            aria-hidden="true"
        ></div>

        <div class="relative max-w-full animate-entrar px-5 pt-44 pb-8 xs:px-6 sm:p-8 md:p-12" :class="amplia ? 'sm:max-w-[70%]' : 'sm:max-w-[65%]'">
            <Antetitulo linea class="mb-5">{{ antetitulo }}</Antetitulo>
            <TituloPagina class="text-[clamp(2.65rem,8vw,3.8rem)] sm:text-[clamp(3rem,5.7vw,4.8rem)]">
                <slot name="titulo" />
            </TituloPagina>
            <p class="mt-[1.3rem] max-w-[30rem] text-[.83rem] leading-[1.8] text-soft sm:text-sm">
                <slot name="descripcion" />
            </p>
            <slot />
        </div>
    </section>
</template>
