

function imprimirManifiesto() {
    const tablaContainer = document.querySelector('.tabla-container');
    if (!tablaContainer) {
        alert('No hay resultados para imprimir');
        return;
    }
    
    const checkboxes = document.querySelectorAll('.seleccionar-guia');
    
    if (checkboxes.length === 0) {
        window.print();
        return;
    }
    
    const seleccionadas = document.querySelectorAll('.seleccionar-guia:checked');
    
    if (seleccionadas.length > 0) {
        imprimirConSeleccion();
    } else {
        imprimirTodoSinCheckbox();
    }
}

function calcularResumen(filas = null) {
    let totalGuias = 0;
    let totalPaquetes = 0;
    let totalActivo = 0;
    let totalCancelado = 0;
    
    const filasAProcesar = filas || document.querySelectorAll('#tablaResultados tbody tr');
    
    filasAProcesar.forEach(fila => {
        const celdas = fila.querySelectorAll('td');
        if (celdas.length < 8) return;
        
        const estado = celdas[4]?.querySelector('.estado')?.textContent.trim() || celdas[4]?.textContent.trim() || '';
        const valorTexto = celdas[6]?.textContent.trim().replace(/[$,.]/g, '') || '0';
        const paquetes = parseInt(celdas[7]?.textContent.trim()) || 1;
        
        totalGuias++;
        totalPaquetes += paquetes;
        const valor = parseFloat(valorTexto) || 0;
        
        if (estado.includes('ACT')) {
            totalActivo += valor;
        } else if (estado.includes('CAN') || estado.includes('DEV')) {
            totalCancelado += valor;
        }
    });
    
    return {
        guias: totalGuias,
        paquetes: totalPaquetes,
        activo: totalActivo,
        cancelado: totalCancelado
    };
}

function crearResumenHTML(resumen) {
    return `
        <div class="resumen-impresion">
            <div class="grid">
                <div class="item">
                    <span class="label">Total Guías</span>
                    <span class="valor total-guias">${resumen.guias}</span>
                </div>
                <div class="item">
                    <span class="label">Total Paquetes</span>
                    <span class="valor total-paquetes">${resumen.paquetes}</span>
                </div>
                <div class="item">
                    <span class="label">Total Activo</span>
                    <span class="valor total-activo">$${resumen.activo.toLocaleString('es-CO')}</span>
                </div>
                <div class="item">
                    <span class="label">Total Cancelado</span>
                    <span class="valor total-cancelado">$${resumen.cancelado.toLocaleString('es-CO')}</span>
                </div>
            </div>
        </div>
    `;
}

function imprimirTodoSinCheckbox() {
    const cabeceras = document.querySelectorAll('#tablaResultados thead th');
    const filas = document.querySelectorAll('#tablaResultados tbody tr');
    const elementosOcultos = [];
    
    const resumen = calcularResumen(filas);
    const resumenDiv = document.createElement('div');

    resumenDiv.id = 'resumen-temporal';
    resumenDiv.innerHTML = crearResumenHTML(resumen);
    
    const tabla = document.querySelector('.tabla-container');

    tabla.parentNode.insertBefore(resumenDiv, tabla);
    elementosOcultos.push({
        el: resumenDiv,
        display: 'block'
    });
    
    if (cabeceras.length > 0) {
        elementosOcultos.push({
            el: cabeceras[0],
            display: cabeceras[0].style.display
        });
        cabeceras[0].style.display = 'none';
    }
    
    filas.forEach(fila => {
        const primeraCelda = fila.querySelector('td:first-child');
        if (primeraCelda) {
            elementosOcultos.push({
                el: primeraCelda,
                display: primeraCelda.style.display
            });
            primeraCelda.style.display = 'none';
        }
    });
    
    // Imprimir
    window.print();
    
    // Restaurar todo
    setTimeout(() => {
        elementosOcultos.forEach(item => {
            item.el.style.display = item.display;
        });
        if (resumenDiv.parentNode) {
            resumenDiv.parentNode.removeChild(resumenDiv);
        }
    }, 100);
}

function imprimirConSeleccion() {
    const filas = document.querySelectorAll('#tablaResultados tbody tr');
    const cabeceras = document.querySelectorAll('#tablaResultados thead th');
    const elementosOcultos = [];
    
    const filasSeleccionadas = [];
    filas.forEach(fila => {
        const checkbox = fila.querySelector('.seleccionar-guia');
        if (checkbox && checkbox.checked) {
            filasSeleccionadas.push(fila);
        }
    });
    
    const resumen = calcularResumen(filasSeleccionadas);
    
    const resumenDiv = document.createElement('div');
    resumenDiv.id = 'resumen-temporal';
    resumenDiv.innerHTML = crearResumenHTML(resumen);
    
    const tabla = document.querySelector('.tabla-container');
    tabla.parentNode.insertBefore(resumenDiv, tabla);
    elementosOcultos.push({
        el: resumenDiv,
        display: 'block'
    });
    
    if (cabeceras.length > 0) {
        elementosOcultos.push({
            el: cabeceras[0],
            display: cabeceras[0].style.display
        });
        cabeceras[0].style.display = 'none';
    }
    
    filas.forEach(fila => {
        const checkbox = fila.querySelector('.seleccionar-guia');
        const primeraCelda = fila.querySelector('td:first-child');
        
        if (primeraCelda) {
            elementosOcultos.push({
                el: primeraCelda,
                display: primeraCelda.style.display
            });
        }
        
        if (checkbox && !checkbox.checked) {
            elementosOcultos.push({
                el: fila,
                display: fila.style.display
            });
            fila.style.display = 'none';
        } else if (checkbox && checkbox.checked) {
            if (primeraCelda) {
                primeraCelda.style.display = 'none';
            }
        }
    });
    
    // Imprimir
    window.print();
    
    // Restaurar todo
    setTimeout(() => {
        elementosOcultos.forEach(item => {
            item.el.style.display = item.display;
        });
        if (resumenDiv.parentNode) {
            resumenDiv.parentNode.removeChild(resumenDiv);
        }
    }, 100);
}

// Funciones para checkboxes
function toggleTodo() {
    const checkTodo = document.getElementById('checkTodo');
    if (!checkTodo) return;
    
    document.querySelectorAll('.seleccionar-guia').forEach(cb => {
        cb.checked = checkTodo.checked;
    });
    actualizarContador();
}

function seleccionarTodas() {
    document.querySelectorAll('.seleccionar-guia').forEach(cb => cb.checked = true);
    const checkTodo = document.getElementById('checkTodo');
    if (checkTodo) checkTodo.checked = true;
    actualizarContador();
}

function deseleccionarTodas() {
    document.querySelectorAll('.seleccionar-guia').forEach(cb => cb.checked = false);
    const checkTodo = document.getElementById('checkTodo');
    if (checkTodo) checkTodo.checked = false;
    actualizarContador();
}

function actualizarContador() {
    const contador = document.getElementById('contadorSeleccionadas');
    if (!contador) return;
    
    const seleccionadas = document.querySelectorAll('.seleccionar-guia:checked').length;
    contador.textContent = seleccionadas + ' seleccionada' + (seleccionadas !== 1 ? 's' : '');
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.seleccionar-guia');
    if (checkboxes.length > 0) {
        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                const total = document.querySelectorAll('.seleccionar-guia').length;
                const seleccionadas = document.querySelectorAll('.seleccionar-guia:checked').length;
                
                const checkTodo = document.getElementById('checkTodo');
                if (checkTodo) {
                    checkTodo.checked = seleccionadas === total && total > 0;
                }
                actualizarContador();
            });
        });
        actualizarContador();
    }
});