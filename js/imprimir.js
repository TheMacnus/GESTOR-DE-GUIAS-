
function imprimirManifiesto() {
    const tabla = document.querySelector('.tabla-container');
    if (!tabla) {
        alert('No hay resultados para imprimir');
        return;
    }
    window.print();
}

