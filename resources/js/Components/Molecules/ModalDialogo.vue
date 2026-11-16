<script setup>
import { onBeforeUnmount, ref, useId, watch } from 'vue';
import Antetitulo from '../Atoms/Antetitulo.vue';
import Boton from '../Atoms/Boton.vue';
import EsquinasHud from '../Atoms/EsquinasHud.vue';

const props = defineProps({
    abierto: { type: Boolean, default: false },
    ocupado: { type: Boolean, default: false },
    antetitulo: { type: String, required: true },
    titulo: { type: String, required: true },
    textoConfirmar: { type: String, required: true },
    textoOcupado: { type: String, required: true },
    variante: { type: String, default: 'primario' },
    holograma: { type: Boolean, default: false },
});
const emit = defineEmits(['cerrar', 'confirmar']);

const id = useId();
const dialogo = ref(null);

watch(() => props.abierto, (abierto) => {
    if (abierto && !dialogo.value.open) dialogo.value.showModal();
    if (!abierto && dialogo.value.open) dialogo.value.close();
}, { flush: 'post' });

onBeforeUnmount(() => dialogo.value?.close());

function cerrar() {
    if (!props.ocupado) emit('cerrar');
}
</script>

<template>
    <dialog
        ref="dialogo"
        :aria-labelledby="`${id}-titulo`"
        :aria-describedby="`${id}-descripcion`"
        class="m-auto max-h-[calc(100dvh-2rem)] w-[min(28rem,calc(100%-2rem))] overflow-auto rounded-[4px] border border-t-2 border-[#62616a] bg-panel p-6 text-fg
            shadow-[0_24px_100px_#000000aa] open:animate-entrar xs:p-8 light:border-x-[#d4d0c7] light:border-b-[#d4d0c7]"
        :class="variante === 'peligro' ? 'border-t-[#e57887]' : holograma ? 'border-t-ice [--color-gold:var(--color-ice)]' : 'border-t-gold'"
        @cancel.prevent="cerrar"
    >
        <span
            v-if="holograma"
            class="pointer-events-none absolute inset-0 bg-[repeating-linear-gradient(0deg,#94d9ed0b_0_1px,transparent_1px_4px)]"
            aria-hidden="true"
        ></span>
        <EsquinasHud />
        <form :aria-busy="ocupado" @submit.prevent="emit('confirmar')">
            <Antetitulo class="mb-5">{{ antetitulo }}</Antetitulo>
            <h2 :id="`${id}-titulo`" class="font-display text-[2.2rem] leading-[1.1] font-semibold uppercase">{{ titulo }}</h2>
            <p :id="`${id}-descripcion`" class="mt-4 text-sm leading-[1.8] wrap-anywhere text-soft">
                <slot name="descripcion" />
            </p>

            <slot />

            <div class="mt-7 flex flex-col gap-3 xs:flex-row xs:justify-end">
                <Boton variante="secundario" :disabled="ocupado" autofocus @click="cerrar">Cancelar</Boton>
                <Boton tipo="submit" :variante="variante" :disabled="ocupado" :cargando="ocupado">
                    {{ ocupado ? textoOcupado : textoConfirmar }}
                </Boton>
            </div>
            <p v-if="ocupado" class="sr-only" role="status">{{ textoOcupado }}</p>
        </form>
    </dialog>
</template>
