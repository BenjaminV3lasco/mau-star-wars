<script setup>
import { nextTick, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Entrada from '../Atoms/Entrada.vue';
import CampoFormulario from '../Molecules/CampoFormulario.vue';
import ModalDialogo from '../Molecules/ModalDialogo.vue';

const props = defineProps({ abierto: { type: Boolean, default: false } });
const emit = defineEmits(['cerrar', 'importado']);

const campoId = ref(null);
const form = useForm({ swapi_id: '' });

watch(() => props.abierto, (abierto) => {
    if (!abierto) return;
    form.reset();
    form.clearErrors();
});

function importar() {
    if (form.processing) return;

    form.clearErrors();
    form.post('/personajes/importar', {
        preserveScroll: true,
        onSuccess: () => emit('importado'),
        onHttpException: (respuesta) => {
            form.setError('swapi_id', respuesta.status === 419
                ? 'La sesión venció. Recargá la página e intentá nuevamente.'
                : 'No pudimos completar la importación. Intentá nuevamente.');
            return false;
        },
        onNetworkError: () => {
            form.setError('swapi_id', 'No pudimos conectar con el servidor. Revisá la conexión e intentá nuevamente.');
            return false;
        },
        onFinish: async () => {
            await nextTick();
            if (props.abierto && form.hasErrors) campoId.value?.$el.focus();
        },
    });
}
</script>

<template>
    <ModalDialogo
        :abierto="abierto"
        :ocupado="form.processing"
        antetitulo="Transmisión entrante"
        titulo="Importar desde SWAPI"
        texto-confirmar="Importar"
        texto-ocupado="Estableciendo enlace…"
        holograma
        @cerrar="emit('cerrar')"
        @confirmar="importar"
    >
        <template #descripcion>
            Los datos del personaje se traducen al español y se guardan en tu archivo.
        </template>

        <CampoFormulario
            id="swapi-id"
            v-slot="{ invalido, descripcion }"
            class="mt-6"
            etiqueta="ID del personaje"
            requerido
            ayuda="Ingresá un número del 1 al 83. Por ejemplo, 3 corresponde a R2‑D2."
            :error="form.errors.swapi_id"
        >
            <Entrada
                id="swapi-id"
                ref="campoId"
                v-model="form.swapi_id"
                name="swapi_id"
                type="number"
                inputmode="numeric"
                min="1"
                max="83"
                step="1"
                required
                autofocus
                placeholder="Ej. 3"
                :disabled="form.processing"
                :aria-invalid="invalido"
                :aria-describedby="descripcion"
            />
        </CampoFormulario>
    </ModalDialogo>
</template>
