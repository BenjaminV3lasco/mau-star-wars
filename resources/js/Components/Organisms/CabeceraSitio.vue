<script setup>
import { computed, ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useSonido } from '../../composables/useSonido.js';

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
const { sonidoActivo, alternarSonido, reproducir } = useSonido();

const CLASES_BOTON = `inline-flex min-h-[38px] min-w-[38px] items-center justify-center gap-[.3rem] rounded-[3px] border border-[#42464e] bg-white/[.02]
    text-[.59rem] text-[#e2e3e6] transition hover:border-gold/60 hover:bg-white/5 hover:text-white hover:shadow-[0_0_16px_#ffe15a30]
    xs:gap-[.45rem] xs:text-[.63rem] sm:min-h-10 sm:text-[.68rem]`;

function aplicarTema() {
    tema.value = tema.value === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = tema.value;
    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', COLOR_BARRA[tema.value]);

    try {
        localStorage.setItem(CLAVE_TEMA, tema.value);
    } catch {
        // Sin almacenamiento disponible el tema sólo dura esta visita.
    }
}

function alternarTema(evento) {
    reproducir('encender');
    const sinMovimiento = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (!document.startViewTransition || sinMovimiento) {
        aplicarTema();
        return;
    }

    // El nuevo tema se expande en círculo desde el botón.
    const { left, top, width, height } = evento.currentTarget.getBoundingClientRect();
    document.documentElement.style.setProperty('--origen-x', `${left + width / 2}px`);
    document.documentElement.style.setProperty('--origen-y', `${top + height / 2}px`);
    document.startViewTransition(aplicarTema);
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
                            after:absolute after:-bottom-0.5 after:left-0 after:h-0.5 after:w-full after:origin-left after:scale-x-0 after:bg-gold after:shadow-[0_0_10px_#ffe15a] after:transition-transform
                            hover:after:scale-x-100 focus-visible:after:scale-x-100 aria-[current=page]:after:scale-x-100"
                    >
                        {{ enlace.nombre }}
                    </Link>
                </div>
                <button
                    type="button"
                    :class="CLASES_BOTON"
                    :aria-pressed="!sonidoActivo"
                    :aria-label="sonidoActivo ? 'Silenciar efectos de sonido' : 'Activar efectos de sonido'"
                    :title="sonidoActivo ? 'Silenciar' : 'Activar sonido'"
                    @click="alternarSonido"
                >
                    <svg class="size-4 text-gold" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                        <path d="M2.5 6h2.5l3.5-3v10l-3.5-3H2.5z" fill="currentColor" stroke-linejoin="round" />
                        <path v-if="sonidoActivo" d="M11 5.5a3.5 3.5 0 0 1 0 5M12.8 3.8a6 6 0 0 1 0 8.4" stroke-linecap="round" />
                        <path v-else d="m11 6 3.5 4M14.5 6 11 10" stroke-linecap="round" />
                    </svg>
                </button>
                <button
                    type="button"
                    :class="[CLASES_BOTON, '2xs:px-[.45rem] xs:px-[.55rem] sm:px-[.7rem]']"
                    :aria-pressed="tema === 'light'"
                    :aria-label="tema === 'dark' ? 'Activar modo claro' : 'Activar modo oscuro'"
                    @click="alternarTema"
                >
                    <svg class="size-4 text-gold" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" aria-hidden="true">
                        <template v-if="tema === 'dark'">
                            <circle cx="8" cy="8" r="3" />
                            <path d="M8 1.5v1.6M8 12.9v1.6M1.5 8h1.6M12.9 8h1.6M3.4 3.4l1.1 1.1M11.5 11.5l1.1 1.1M3.4 12.6l1.1-1.1M11.5 4.5l1.1-1.1" />
                        </template>
                        <path v-else d="M13.5 9.6A5.8 5.8 0 0 1 6.4 2.5a5.8 5.8 0 1 0 7.1 7.1Z" fill="currentColor" stroke-linejoin="round" />
                    </svg>
                    <span class="hidden 2xs:inline">{{ tema === 'dark' ? 'Modo claro' : 'Modo oscuro' }}</span>
                </button>
            </div>
        </nav>
    </header>
</template>
