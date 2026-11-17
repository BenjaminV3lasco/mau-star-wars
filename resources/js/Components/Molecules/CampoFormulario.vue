<script setup>
import { computed } from 'vue';
import MensajeError from '../Atoms/MensajeError.vue';

const props = defineProps({
    id: { type: String, required: true },
    etiqueta: { type: String, required: true },
    requerido: { type: Boolean, default: false },
    unidad: { type: String, default: null },
    ayuda: { type: String, default: null },
    error: { type: String, default: null },
});

const descripcion = computed(() => [
    props.ayuda && `${props.id}-ayuda`,
    props.error && `${props.id}-error`,
].filter(Boolean).join(' ') || undefined);
</script>

<template>
    <div class="group min-w-0">
        <label
            :for="id"
            class="flex items-center gap-2 text-[.68rem] font-medium tracking-[.14em] text-[#b9bec8] uppercase transition-colors
                group-focus-within:text-ice light:text-[#4b515b] light:group-focus-within:text-[#08718a]"
        >
            {{ etiqueta }}<span v-if="unidad" class="sr-only"> ({{ unidad }})</span>
            <span v-if="requerido" class="text-gold" aria-hidden="true">*</span>
        </label>
        <div class="relative mt-2">
            <slot :id="id" :invalido="Boolean(error)" :descripcion="descripcion" />
            <span v-if="unidad" class="pointer-events-none absolute top-1/2 right-[.9rem] -translate-y-1/2 text-[.68rem] tracking-[.1em] text-muted uppercase" aria-hidden="true">
                {{ unidad }}
            </span>
        </div>
        <p v-if="ayuda" :id="`${id}-ayuda`" class="mt-2.5 text-ui leading-relaxed text-muted">{{ ayuda }}</p>
        <MensajeError v-if="error" :id="`${id}-error`">{{ error }}</MensajeError>
    </div>
</template>
