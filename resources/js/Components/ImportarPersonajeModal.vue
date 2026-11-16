<script setup>
import { nextTick, onBeforeUnmount, ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({ abierto: { type: Boolean, default: false } });
const emit = defineEmits(['cerrar', 'importado']);
const dialogo = ref(null);
const campoId = ref(null);
const form = useForm({ swapi_id: '' });

watch(() => props.abierto, (abierto) => {
    if (abierto) {
        form.reset();
        form.clearErrors();
        if (!dialogo.value.open) dialogo.value.showModal();
    } else if (dialogo.value.open) {
        dialogo.value.close();
    }
}, { flush: 'post' });

onBeforeUnmount(() => dialogo.value?.close());

function cerrar() {
    if (!form.processing) emit('cerrar');
}

function importar() {
    if (form.processing) return;

    form.clearErrors();
    form.post('/personajes/importar', {
        preserveScroll: true,
        onSuccess: () => emit('importado'),
        onHttpException: (response) => {
            form.setError('swapi_id', response.status === 419
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
            if (props.abierto && form.hasErrors) campoId.value?.focus();
        },
    });
}
</script>

<template>
    <dialog
        ref="dialogo"
        class="confirm-dialog import-dialog"
        aria-labelledby="importar-titulo"
        aria-describedby="importar-descripcion"
        @cancel.prevent="cerrar"
    >
        <form :aria-busy="form.processing" @submit.prevent="importar">
            <p class="eyebrow">El universo en tu archivo</p>
            <h2 id="importar-titulo" class="modal-title">Importar desde SWAPI</h2>
            <p id="importar-descripcion" class="modal-description">
                Traé un personaje de la API de Star Wars usando su ID. Sus datos se guardarán en tu archivo.
            </p>
            <div class="import-field">
                <label for="swapi-id" class="form-label">ID del personaje <span class="required-mark" aria-hidden="true">*</span></label>
                <input
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
                    class="form-control"
                    :disabled="form.processing"
                    :aria-invalid="Boolean(form.errors.swapi_id)"
                    :aria-describedby="form.errors.swapi_id ? 'swapi-ayuda swapi-error' : 'swapi-ayuda'"
                />
                <p id="swapi-ayuda" class="input-help">Ingresá un número del 1 al 83. Por ejemplo, 3 corresponde a R2-D2.</p>
                <p v-if="form.errors.swapi_id" id="swapi-error" class="form-error" role="alert">{{ form.errors.swapi_id }}</p>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" :disabled="form.processing" @click="cerrar">Cancelar</button>
                <button type="submit" class="btn-primary" :disabled="form.processing">
                    <span v-if="form.processing" class="loading-spinner" aria-hidden="true"></span>
                    {{ form.processing ? 'Importando…' : 'Importar personaje' }}
                </button>
            </div>
            <p v-if="form.processing" class="sr-only" role="status">Consultando SWAPI. Esperá un momento.</p>
        </form>
    </dialog>
</template>
