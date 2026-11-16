<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    form: { type: Object, required: true },
    textoSubmit: { type: String, required: true },
});
defineEmits(['submit']);

const secciones = [
    { titulo: 'Identidad', campos: [
        { nombre: 'nombre', etiqueta: 'Nombre', tipo: 'text', sinNumeros: true, maxLength: 100, minLength: 3, requerido: true, ancho: true, ejemplo: 'Ej. Leia Organa' },
        { nombre: 'genero', etiqueta: 'Género', tipo: 'select', requerido: true },
        { nombre: 'planeta_origen', etiqueta: 'Planeta de origen', tipo: 'text', maxLength: 100, ejemplo: 'Ej. Alderaan' },
    ] },
    { titulo: 'Características', campos: [
        { nombre: 'altura', etiqueta: 'Altura', tipo: 'number', min: 1, max: 300, unidad: 'cm', ejemplo: 'Ej. 150' },
        { nombre: 'masa', etiqueta: 'Masa', tipo: 'number', min: 1, max: 500, unidad: 'kg', ejemplo: 'Ej. 49' },
        { nombre: 'color_cabello', etiqueta: 'Color de cabello', tipo: 'text', sinNumeros: true, maxLength: 100, ejemplo: 'Ej. Castaño' },
        { nombre: 'color_ojos', etiqueta: 'Color de ojos', tipo: 'text', sinNumeros: true, maxLength: 100, ejemplo: 'Ej. Marrón' },
    ] },
];
const patronSinNumeros = '[^\\p{N}]*';
</script>

<template>
    <form class="form-panel" :aria-busy="form.processing" @submit.prevent="$emit('submit')">
        <p class="form-note">Los campos con <span aria-hidden="true">*</span> son obligatorios.</p>
        <fieldset
            v-for="(seccion, indice) in secciones"
            :key="seccion.titulo"
            class="form-section"
            :disabled="form.processing"
        >
            <legend><span aria-hidden="true">0{{ indice + 1 }}</span> {{ seccion.titulo }}</legend>
            <div class="form-grid">
                <div v-for="campo in seccion.campos" :key="campo.nombre" class="form-field" :class="{ 'field-wide': campo.ancho }">
                    <label :for="campo.nombre" class="form-label">
                        {{ campo.etiqueta }}<span v-if="campo.unidad" class="sr-only"> ({{ campo.unidad }})</span>
                        <span v-if="campo.requerido" class="required-mark" aria-hidden="true">*</span>
                    </label>
                    <div class="control-wrap">
                        <select
                            v-if="campo.tipo === 'select'"
                            :id="campo.nombre"
                            v-model="form[campo.nombre]"
                            :name="campo.nombre"
                            required
                            class="form-control"
                            :aria-invalid="Boolean(form.errors[campo.nombre])"
                            :aria-describedby="form.errors[campo.nombre] ? campo.nombre + '-error' : undefined"
                        >
                            <option value="" disabled>Seleccioná una opción</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                            <option value="n/a">No aplica</option>
                        </select>
                        <input
                            v-else
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
                            :step="campo.tipo === 'number' ? 1 : undefined"
                            :placeholder="campo.ejemplo"
                            :aria-invalid="Boolean(form.errors[campo.nombre])"
                            :aria-describedby="form.errors[campo.nombre] ? campo.nombre + '-error' : undefined"
                            class="form-control"
                            :class="{ 'has-unit': campo.unidad }"
                        />
                        <span v-if="campo.unidad" class="input-unit" aria-hidden="true">{{ campo.unidad }}</span>
                    </div>
                    <p v-if="form.errors[campo.nombre]" :id="campo.nombre + '-error'" class="form-error" role="alert">{{ form.errors[campo.nombre] }}</p>
                </div>
            </div>
        </fieldset>
        <div class="form-actions">
            <button type="submit" :disabled="form.processing" class="btn-primary">
                <span v-if="form.processing" class="loading-spinner" aria-hidden="true"></span>
                {{ form.processing ? 'Guardando…' : textoSubmit }}
                <span v-if="!form.processing" aria-hidden="true">↗</span>
            </button>
            <Link href="/personajes" class="btn-secondary">Cancelar</Link>
        </div>
    </form>
</template>
