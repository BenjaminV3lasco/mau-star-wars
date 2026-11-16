<script setup>
import Antetitulo from '../Atoms/Antetitulo.vue';
import EnlaceVolver from '../Atoms/EnlaceVolver.vue';
import TituloPagina from '../Atoms/TituloPagina.vue';
import MarcoPagina from './MarcoPagina.vue';

defineProps({
    titulo: { type: String, required: true },
    descripcion: { type: String, required: true },
});
</script>

<template>
    <MarcoPagina compacto>
        <EnlaceVolver class="mb-[1.1rem]" />

        <div class="grid grid-cols-1 items-stretch gap-6 sm:grid-cols-[minmax(0,.7fr)_minmax(0,1.3fr)] md:grid-cols-[minmax(0,.85fr)_minmax(0,1.5fr)]">
            <header class="escena-oscura relative isolate min-h-[230px] overflow-hidden rounded-[3px] border border-[#333943] bg-[#070b13] sm:min-h-[620px]">
                <div
                    class="absolute inset-0 -z-20 animate-revelar bg-[url(/images/a-new-hope.jpg)] bg-size-[auto_330px] bg-position-[96%_25%] bg-no-repeat
                        sm:top-[32%] sm:bg-size-[auto_100%] sm:bg-position-[92%_center]"
                    aria-hidden="true"
                ></div>
                <div
                    class="absolute inset-0 -z-10 bg-[linear-gradient(90deg,#080d16fa_0%,#080d16c0_50%,#080d1640_85%)]
                        sm:bg-[linear-gradient(180deg,#080d16_6%,#080d16f5_22%,#080d1650_55%,#080d1600_72%,#080d1670)]"
                    aria-hidden="true"
                ></div>

                <div class="max-w-[90%] px-6 py-8 xs:max-w-[75%] sm:max-w-none sm:p-6 md:p-9">
                    <Antetitulo class="mb-5">Star Wars</Antetitulo>
                    <TituloPagina class="text-[2.7rem] xs:text-5xl sm:text-[clamp(2.8rem,4.5vw,4rem)]">{{ titulo }}</TituloPagina>
                    <p class="mt-[1.3rem] max-w-[30rem] text-[.8rem] leading-[1.8] text-soft sm:text-[.85rem]">{{ descripcion }}</p>
                </div>
            </header>

            <slot />
        </div>
    </MarcoPagina>
</template>
