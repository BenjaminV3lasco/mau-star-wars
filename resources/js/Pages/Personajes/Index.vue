<script setup>
import { Head } from '@inertiajs/vue3';
import Paginacion from '../../Components/Paginacion.vue';

defineProps({
    personajes: { type: Object, required: true },
});
</script>

<template>
    <Head title="Personajes de Star Wars" />

    <main class="mx-auto max-w-5xl px-4 py-10 sm:px-6">
        <h1 class="text-3xl font-semibold text-slate-900">Personajes de Star Wars</h1>

        <ul v-if="personajes.data.length" class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <li v-for="personaje in personajes.data" :key="personaje.id" class="rounded-xl border border-slate-200 p-4">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h2 class="min-w-0 font-semibold wrap-anywhere text-slate-900">{{ personaje.nombre }}</h2>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-700">
                        {{ personaje.origen_swapi ? 'SWAPI' : 'Manual' }}
                    </span>
                </div>

                <dl class="mt-4 grid grid-cols-2 gap-x-3 gap-y-2 text-sm">
                    <dt class="text-slate-600">Altura</dt>
                    <dd>{{ personaje.altura == null ? '—' : `${personaje.altura} cm` }}</dd>
                    <dt class="text-slate-600">Masa</dt>
                    <dd>{{ personaje.masa == null ? '—' : `${personaje.masa} kg` }}</dd>
                    <dt class="text-slate-600">Género</dt>
                    <dd>{{ personaje.genero }}</dd>
                </dl>
            </li>
        </ul>
        <p v-else class="mt-6 rounded-xl border border-slate-200 p-5 text-slate-700">
            {{ personajes.total ? 'No hay personajes en esta página.' : 'Todavía no hay personajes registrados.' }}
        </p>

        <Paginacion :paginador="personajes" />
    </main>
</template>
