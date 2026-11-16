<script setup>
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Personajes de Star Wars" />
    <main class="mx-auto max-w-3xl px-6 py-12">
        <h1 class="text-3xl font-semibold">Personajes de Star Wars</h1>
        <Link href="/personajes" class="mt-6 inline-flex rounded-lg bg-slate-900 px-4 py-2 text-white hover:bg-slate-700">
            Ver personajes
        </Link>
    </main>
</template>
