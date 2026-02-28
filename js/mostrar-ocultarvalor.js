function toggleValorActualizar() {
    const tipo = document.getElementById('tipoPagoActualizar').value;
    const valor = document.getElementById('valorActualizar');

    if (tipo === 'COBRO') {
        valor.style.display = 'block';
        valor.required = true;
    } else {
        valor.style.display = 'none';
        valor.required = false;
        valor.value = 0;
    }
}
