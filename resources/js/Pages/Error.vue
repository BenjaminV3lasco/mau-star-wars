<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import Boton from '../Components/Atoms/Boton.vue';
import EsquinasHud from '../Components/Atoms/EsquinasHud.vue';
import TituloPagina from '../Components/Atoms/TituloPagina.vue';
import MarcoPagina from '../Components/Templates/MarcoPagina.vue';

const props = defineProps({ estado: { type: Number, required: true } });

const MENSAJES = {
    403: {
        titulo: 'Acceso restringido',
        descripcion: 'No tenés autorización para ver esta sección del archivo.',
    },
    404: {
        titulo: 'Ficha no encontrada',
        descripcion: 'Esta página no figura en los archivos. Puede que el personaje haya sido eliminado o que el enlace sea incorrecto.',
    },
    503: {
        titulo: 'Sistemas en mantenimiento',
        descripcion: 'El archivo vuelve a estar en línea en breve.',
    },
};

const mensaje = computed(() => MENSAJES[props.estado] ?? MENSAJES[404]);
</script>

<template>
    <Head :title="`${mensaje.titulo} · Star Wars`" />

    <MarcoPagina>
        <section
            class="escena-oscura relative isolate mx-auto flex min-h-[460px] max-w-[52rem] flex-col items-center justify-center overflow-hidden rounded-[3px]
                border border-white/15 bg-[#05070c] px-6 py-16 text-center"
        >
            <div class="absolute inset-0 -z-20 animate-revelar bg-[url(/images/a-new-hope.jpg)] bg-cover bg-center opacity-20" aria-hidden="true"></div>
            <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_center,#080a0e99,#080a0e_75%)]" aria-hidden="true"></div>
            <EsquinasHud />

            <p class="font-display text-[clamp(5rem,18vw,9rem)] leading-none font-semibold text-gold tabular-nums drop-shadow-[0_0_30px_#ffe15a40]" aria-hidden="true">
                {{ estado }}
            </p>
            <TituloPagina class="mt-3 text-[clamp(2rem,5vw,3rem)]">{{ mensaje.titulo }}</TituloPagina>
            <p class="mt-4 max-w-[30rem] text-sm leading-[1.8] text-soft">{{ mensaje.descripcion }}</p>
            <Boton href="/personajes" class="mt-8">Volver al archivo <span class="text-lg" aria-hidden="true">→</span></Boton>
        </section>
    </MarcoPagina>
</template>
