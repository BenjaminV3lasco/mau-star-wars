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
            class="btn-secondary"
        >
            Anterior
        </Link>
        <span v-if="paginador.current_page <= paginador.last_page" class="text-muted">
            Página {{ paginador.current_page }} de {{ paginador.last_page }}
        </span>
        <Link
            v-else
            :href="paginador.first_page_url"
            class="btn-secondary"
        >
            Primera página
        </Link>
        <Link
            v-if="paginador.next_page_url"
            :href="paginador.next_page_url"
            class="btn-secondary"
        >
            Siguiente
        </Link>
    </nav>
</template>
