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
        <label :for="id" class="block text-ui font-medium text-[#dde0e6] transition-colors group-focus-within:text-ice light:text-[#3f444d]">
            {{ etiqueta }}<span v-if="unidad" class="sr-only"> ({{ unidad }})</span>
            <span v-if="requerido" class="ml-1 text-gold" aria-hidden="true">*</span>
        </label>
        <div class="relative mt-2">
            <slot :id="id" :invalido="Boolean(error)" :descripcion="descripcion" />
            <span v-if="unidad" class="pointer-events-none absolute top-1/2 right-[.85rem] -translate-y-1/2 text-xs text-muted" aria-hidden="true">
                {{ unidad }}
            </span>
        </div>
        <p v-if="ayuda" :id="`${id}-ayuda`" class="mt-2.5 text-ui leading-relaxed text-muted">{{ ayuda }}</p>
        <MensajeError v-if="error" :id="`${id}-error`">{{ error }}</MensajeError>
    </div>
</template>
