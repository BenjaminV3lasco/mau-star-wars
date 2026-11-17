// Datos y formatos de personajes que comparten varias pantallas.

// Mismos valores que valida GuardarPersonajeRequest; la etiqueta es lo que se muestra.
export const GENEROS = [
    { valor: 'masculino', etiqueta: 'Masculino' },
    { valor: 'femenino', etiqueta: 'Femenino' },
    { valor: 'n/a', etiqueta: 'No aplica' },
];

export function etiquetaGenero(valor) {
    return GENEROS.find((genero) => genero.valor === valor)?.etiqueta ?? valor;
}

// 7 → "007", para el rótulo "Ficha / 007".
export function numeroFicha(id) {
    return String(id).padStart(3, '0');
}

// Agrega a una ruta los filtros y la página actuales (?buscar=…&page=2), para
// que al ir a una ficha y volver el listado quede como estaba.
export function conConsultaActual(ruta, urlActual) {
    const inicioConsulta = urlActual.indexOf('?');

    return inicioConsulta === -1 ? ruta : ruta + urlActual.slice(inicioConsulta);
}
