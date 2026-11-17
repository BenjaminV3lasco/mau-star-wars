import { ref } from 'vue';

// Sonidos sintetizados con Web Audio: sin archivos ni librerías.
const CLAVE = 'mau-star-wars-sonido';
const ESPERA_MINIMA_MS = 80;

const activo = ref(leerPreferencia());
let contexto = null;
const ultimoPorSonido = {};

function leerPreferencia() {
    try {
        return localStorage.getItem(CLAVE) !== 'off';
    } catch {
        return true;
    }
}

function obtenerContexto() {
    contexto ??= new AudioContext();
    if (contexto.state === 'suspended') contexto.resume();

    return contexto;
}

function tono(ctx, inicio, { onda = 'sine', desde, hasta = desde, duracion, volumen = 0.05, filtroDesde = null, filtroHasta = filtroDesde }) {
    const oscilador = ctx.createOscillator();
    const ganancia = ctx.createGain();
    let salida = oscilador;

    oscilador.type = onda;
    oscilador.frequency.setValueAtTime(desde, inicio);
    oscilador.frequency.exponentialRampToValueAtTime(hasta, inicio + duracion);

    if (filtroDesde) {
        const filtro = ctx.createBiquadFilter();
        filtro.type = 'lowpass';
        filtro.frequency.setValueAtTime(filtroDesde, inicio);
        filtro.frequency.exponentialRampToValueAtTime(filtroHasta, inicio + duracion);
        oscilador.connect(filtro);
        salida = filtro;
    }

    ganancia.gain.setValueAtTime(0.0001, inicio);
    ganancia.gain.exponentialRampToValueAtTime(volumen, inicio + Math.min(0.02, duracion / 4));
    ganancia.gain.exponentialRampToValueAtTime(0.0001, inicio + duracion);

    salida.connect(ganancia).connect(ctx.destination);
    oscilador.start(inicio);
    oscilador.stop(inicio + duracion + 0.02);
}

const SONIDOS = {
    // Pulsación de consola.
    clic: (ctx, t) => tono(ctx, t, { desde: 1300, hasta: 850, duracion: 0.05, volumen: 0.035 }),
    // Transmisión holográfica entrante.
    holograma: (ctx, t) => {
        tono(ctx, t, { onda: 'triangle', desde: 620, hasta: 1240, duracion: 0.12, volumen: 0.04 });
        tono(ctx, t + 0.13, { onda: 'triangle', desde: 930, hasta: 1860, duracion: 0.14, volumen: 0.035 });
    },
    // Aviso antes de una acción irreversible.
    alerta: (ctx, t) => {
        tono(ctx, t, { onda: 'square', desde: 330, duracion: 0.09, volumen: 0.025, filtroDesde: 1400 });
        tono(ctx, t + 0.12, { onda: 'square', desde: 247, duracion: 0.12, volumen: 0.025, filtroDesde: 1400 });
    },
    // Confirmación.
    exito: (ctx, t) => {
        tono(ctx, t, { desde: 880, duracion: 0.09, volumen: 0.045 });
        tono(ctx, t + 0.1, { desde: 1320, duracion: 0.16, volumen: 0.04 });
    },
    // Error de validación o de conexión.
    error: (ctx, t) => tono(ctx, t, { onda: 'square', desde: 170, hasta: 110, duracion: 0.2, volumen: 0.03, filtroDesde: 900, filtroHasta: 400 }),
    // Sable que se enciende.
    encender: (ctx, t) => {
        tono(ctx, t, { onda: 'sawtooth', desde: 55, hasta: 115, duracion: 0.5, volumen: 0.06, filtroDesde: 300, filtroHasta: 1700 });
        tono(ctx, t, { desde: 110, hasta: 230, duracion: 0.5, volumen: 0.04 });
    },
    // Sable que se apaga.
    apagar: (ctx, t) => {
        tono(ctx, t, { onda: 'sawtooth', desde: 120, hasta: 40, duracion: 0.4, volumen: 0.06, filtroDesde: 1600, filtroHasta: 200 });
        tono(ctx, t, { desde: 230, hasta: 80, duracion: 0.4, volumen: 0.035 });
    },
};

export function reproducir(nombre) {
    const ahora = performance.now();
    if (!activo.value || ahora - (ultimoPorSonido[nombre] ?? 0) < ESPERA_MINIMA_MS || typeof AudioContext === 'undefined') return;
    ultimoPorSonido[nombre] = ahora;

    try {
        const ctx = obtenerContexto();
        SONIDOS[nombre]?.(ctx, ctx.currentTime);
    } catch {
        // Sin audio disponible la interfaz sigue funcionando en silencio.
    }
}

export function useSonido() {
    function alternarSonido() {
        activo.value = !activo.value;
        try {
            localStorage.setItem(CLAVE, activo.value ? 'on' : 'off');
        } catch {
            // Sin almacenamiento la preferencia dura sólo esta visita.
        }
        if (activo.value) reproducir('clic');
    }

    return { sonidoActivo: activo, alternarSonido, reproducir };
}
