<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const LOGO = '/images/star-wars-logo.svg';
const CLAVE_TEMA = 'mau-star-wars-theme';
const COLOR_BARRA = { dark: '#090a0c', light: '#f5f3ed' };
const ENLACES = [
    { nombre: 'Inicio', href: '/' },
    { nombre: 'Personajes', href: '/personajes' },
];

const page = usePage();
const rutaActual = computed(() => page.url.split('?')[0]);
const tema = ref(document.documentElement.dataset.theme || 'dark');

function alternarTema() {
    tema.value = tema.value === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = tema.value;
    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', COLOR_BARRA[tema.value]);

    try {
        localStorage.setItem(CLAVE_TEMA, tema.value);
    } catch {
        // Sin almacenamiento disponible el tema sólo dura esta visita.
    }
}
</script>

<template>
    <header
        class="escena-oscura relative border-b border-white/10 bg-[#090a0cf5]
            after:absolute after:inset-x-0 after:-bottom-px after:h-px after:bg-linear-to-r after:from-transparent after:via-brand/45 after:to-transparent"
    >
        <nav class="mx-auto flex h-[76px] max-w-[81rem] items-center gap-3 px-5 sm:h-[88px] sm:gap-6 sm:px-8" aria-label="Navegación principal">
            <Link href="/" class="flex w-[70px] shrink-0 xs:w-[90px] sm:w-[104px]" aria-label="Star Wars, inicio">
                <img :src="LOGO" alt="Star Wars" width="512" height="244" class="h-auto w-full brightness-0 invert">
            </Link>
            <span class="hidden h-[27px] w-px bg-line sm:block" aria-hidden="true"></span>
            <span class="hidden text-[.67rem] tracking-[.18em] text-muted uppercase sm:inline">Archivo galáctico</span>

            <div class="ml-auto flex items-center gap-[.45rem] xs:gap-2.5 sm:gap-[1.1rem]">
                <div class="flex items-center gap-[.55rem] 2xs:gap-[.8rem] xs:gap-4 sm:gap-6">
                    <Link
                        v-for="enlace in ENLACES"
                        :key="enlace.href"
                        :href="enlace.href"
                        :aria-current="rutaActual === enlace.href ? 'page' : undefined"
                        class="relative inline-flex min-h-11 items-center text-[.67rem] text-[#eeeeef] 2xs:text-xs xs:text-[.8rem]
                            hover:text-gold aria-[current=page]:text-gold
                            after:absolute after:-bottom-0.5 after:left-0 after:h-0.5 after:w-full after:origin-left after:scale-x-0 after:bg-gold after:transition-transform
                            hover:after:scale-x-100 focus-visible:after:scale-x-100 aria-[current=page]:after:scale-x-100"
                    >
                        {{ enlace.nombre }}
                    </Link>
                </div>
                <button
                    type="button"
                    class="inline-flex min-h-[38px] w-[38px] items-center justify-center gap-[.3rem] rounded-[3px] border border-[#42464e] bg-white/[.02] text-[.59rem] text-[#e2e3e6]
                        transition hover:border-[#888d96] hover:bg-white/5 hover:text-white
                        2xs:w-auto 2xs:px-[.45rem] xs:gap-[.45rem] xs:px-[.55rem] xs:text-[.63rem] sm:min-h-10 sm:px-[.7rem] sm:text-[.68rem]"
                    :aria-pressed="tema === 'light'"
                    :aria-label="tema === 'dark' ? 'Activar modo claro' : 'Activar modo oscuro'"
                    @click="alternarTema"
                >
                    <span class="text-base leading-none text-gold" aria-hidden="true">{{ tema === 'dark' ? '☼' : '◐' }}</span>
                    <span class="hidden 2xs:inline">{{ tema === 'dark' ? 'Modo claro' : 'Modo oscuro' }}</span>
                </button>
            </div>
        </nav>
    </header>
</template>
