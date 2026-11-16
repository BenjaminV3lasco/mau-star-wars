export const GENEROS = [
    { valor: 'masculino', etiqueta: 'Masculino' },
    { valor: 'femenino', etiqueta: 'Femenino' },
    { valor: 'n/a', etiqueta: 'No aplica' },
];

export function etiquetaGenero(valor) {
    return GENEROS.find((genero) => genero.valor === valor)?.etiqueta ?? valor;
}

export function numeroFicha(id) {
    return String(id).padStart(3, '0');
}

export function conConsultaActual(ruta, urlActual) {
    const inicioConsulta = urlActual.indexOf('?');

    return inicioConsulta === -1 ? ruta : ruta + urlActual.slice(inicioConsulta);
}
