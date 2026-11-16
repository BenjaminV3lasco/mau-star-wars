<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    paginador: { type: Object, required: true },
});
</script>

<template>
    <nav v-if="paginador.last_page > 1 || paginador.current_page > 1" aria-label="Paginación" class="mt-6 flex flex-wrap items-center gap-3 text-sm">
        <Link
            v-if="paginador.prev_page_url"
            :href="paginador.prev_page_url"
            class="rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-100"
        >
            Anterior
        </Link>
        <span v-if="paginador.current_page <= paginador.last_page" class="text-slate-700">
            Página {{ paginador.current_page }} de {{ paginador.last_page }}
        </span>
        <Link
            v-else
            :href="paginador.first_page_url"
            class="rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-100"
        >
            Primera página
        </Link>
        <Link
            v-if="paginador.next_page_url"
            :href="paginador.next_page_url"
            class="rounded-lg border border-slate-300 px-3 py-2 hover:bg-slate-100"
        >
            Siguiente
        </Link>
    </nav>
</template>
