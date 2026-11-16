<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    form: { type: Object, required: true },
    textoSubmit: { type: String, required: true },
});

defineEmits(['submit']);

const campos = [
    { nombre: 'nombre', sinNumeros: true, etiqueta: 'Nombre', tipo: 'text', maxLength: 100, minLength: 3, requerido: true },
    { nombre: 'altura', etiqueta: 'Altura (cm)', tipo: 'number', min: 1, max: 300 },
    { nombre: 'masa', etiqueta: 'Masa (kg)', tipo: 'number', min: 1, max: 500 },
    { nombre: 'color_cabello', sinNumeros: true, etiqueta: 'Color de cabello', tipo: 'text', maxLength: 100 },
    { nombre: 'color_ojos', sinNumeros: true, etiqueta: 'Color de ojos', tipo: 'text', maxLength: 100 },
    { nombre: 'planeta_origen', etiqueta: 'Planeta de origen', tipo: 'text', maxLength: 100 },
];

const patronSinNumeros = '[^\\p{N}]*';

const estiloCampo = 'mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus-visible:outline-2 focus-visible:outline-slate-900';
</script>

<template>
    <form class="mt-6" :aria-busy="form.processing" @submit.prevent="$emit('submit')">
        <div class="grid gap-4 sm:grid-cols-2">
            <div v-for="campo in campos" :key="campo.nombre">
                <label :for="campo.nombre" class="block text-sm font-medium text-slate-900">{{ campo.etiqueta }}</label>
                <input
                    :id="campo.nombre"
                    v-model="form[campo.nombre]"
                    :name="campo.nombre"
                    :type="campo.tipo"
                    :pattern="campo.sinNumeros ? patronSinNumeros : undefined"
                    :title="campo.sinNumeros ? 'Ingresá el texto sin números.' : undefined"
                    :required="campo.requerido"
                    :minlength="campo.minLength"
                    :maxlength="campo.maxLength"
                    :min="campo.min"
                    :max="campo.max"
                    :aria-invalid="Boolean(form.errors[campo.nombre])"
                    :aria-describedby="form.errors[campo.nombre] ? `${campo.nombre}-error` : undefined"
                    :class="estiloCampo"
                />
                <p v-if="form.errors[campo.nombre]" :id="`${campo.nombre}-error`" class="mt-1 text-sm text-red-700" role="alert">
                    {{ form.errors[campo.nombre] }}
                </p>
            </div>

            <div>
                <label for="genero" class="block text-sm font-medium text-slate-900">Género</label>
                <select
                    id="genero"
                    v-model="form.genero"
                    name="genero"
                    required
                    :aria-invalid="Boolean(form.errors.genero)"
                    :aria-describedby="form.errors.genero ? 'genero-error' : undefined"
                    :class="estiloCampo"
                >
                    <option value="" disabled>Seleccioná una opción</option>
                    <option value="masculino">Masculino</option>
                    <option value="femenino">Femenino</option>
                    <option value="n/a">No aplica</option>
                </select>
                <p v-if="form.errors.genero" id="genero-error" class="mt-1 text-sm text-red-700" role="alert">
                    {{ form.errors.genero }}
                </p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-lg bg-slate-900 px-4 py-2 text-white hover:bg-slate-700 disabled:cursor-wait disabled:opacity-60"
            >
                {{ form.processing ? 'Guardando…' : textoSubmit }}
            </button>
            <Link href="/personajes" class="rounded-lg border border-slate-300 px-4 py-2 hover:bg-slate-100">
                Cancelar
            </Link>
        </div>
    </form>
</template>
