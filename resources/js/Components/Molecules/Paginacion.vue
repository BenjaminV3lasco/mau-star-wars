<script setup>
import Boton from '../Atoms/Boton.vue';

defineProps({ paginador: { type: Object, required: true } });
</script>

<template>
    <nav v-if="paginador.last_page > 1 || paginador.current_page > 1" aria-label="Paginación" class="mt-6 flex flex-wrap items-center gap-3 text-sm">
        <Boton v-if="paginador.prev_page_url" :href="paginador.prev_page_url" variante="secundario">Anterior</Boton>
        <span v-if="paginador.current_page <= paginador.last_page" class="text-muted">
            Página {{ paginador.current_page }} de {{ paginador.last_page }}
        </span>
        <Boton v-else :href="paginador.first_page_url" variante="secundario">Primera página</Boton>
        <Boton v-if="paginador.next_page_url" :href="paginador.next_page_url" variante="secundario">Siguiente</Boton>
    </nav>
</template>
