/**
 * imprimir.js - Funcionalidad de impresión sin ventanas nuevas
 */

function imprimirManifiesto() {
    // Verificar si hay resultados
    const tablaContainer = document.querySelector('.tabla-container');
    if (!tablaContainer) {
        alert('No hay resultados para imprimir');
        return;
    }
    
    // Verificar si hay checkboxes
    const checkboxes = document.querySelectorAll('.seleccionar-guia');
    
    // Si no hay checkboxes, imprimir directamente
    if (checkboxes.length === 0) {
        window.print();
        return;
    }
    
    // Verificar cuántas están seleccionadas
    const seleccionadas = document.querySelectorAll('.seleccionar-guia:checked');
    
    if (seleccionadas.length > 0) {
        // Hay seleccionadas - ocultar las no seleccionadas
        ocultarNoSeleccionadasYImprimir();
    } else {
        // No hay seleccionadas - ocultar la columna checkbox y imprimir
        ocultarColumnaCheckboxYImprimir();
    }
}

function ocultarNoSeleccionadasYImprimir() {
    const filas = document.querySelectorAll('#tablaResultados tbody tr');
    const filasOcultas = [];
    const cabeceras = document.querySelectorAll('#tablaResultados thead th');
    const celdasOcultas = [];
    
    // Guardar estado original de la primera cabecera (checkbox)
    const cabeceraOriginalDisplay = cabeceras[0].style.display;
    
    // Ocultar la primera cabecera (checkbox)
    cabeceras[0].style.display = 'none';
    
    // Ocultar la primera celda de cada fila (checkbox) Y las filas no seleccionadas
    filas.forEach(fila => {
        const checkbox = fila.querySelector('.seleccionar-guia');
        const primeraCelda = fila.querySelector('td:first-child');
        
        // Guardar estado original de la primera celda
        if (primeraCelda) {
            celdasOcultas.push({
                celda: primeraCelda,
                displayOriginal: primeraCelda.style.display
            });
        }
        
        if (checkbox && !checkbox.checked) {
            // Ocultar fila completa si no está seleccionada
            fila.style.display = 'none';
            filasOcultas.push(fila);
        } else if (checkbox && checkbox.checked) {
            // Solo ocultar la primera celda (checkbox) en filas seleccionadas
            if (primeraCelda) {
                primeraCelda.style.display = 'none';
            }
        }
    });
    
    // Imprimir
    window.print();
    
    // Restaurar todo
    setTimeout(() => {
        // Restaurar cabecera
        cabeceras[0].style.display = cabeceraOriginalDisplay;
        
        // Restaurar celdas
        celdasOcultas.forEach(item => {
            item.celda.style.display = item.displayOriginal;
        });
        
        // Restaurar filas ocultas
        filasOcultas.forEach(fila => {
            fila.style.display = '';
        });
    }, 100);
}

function ocultarColumnaCheckboxYImprimir() {
    const cabeceras = document.querySelectorAll('#tablaResultados thead th');
    const filas = document.querySelectorAll('#tablaResultados tbody tr');
    
    // Guardar estado original de las cabeceras
    const cabeceraOriginal = cabeceras[0].style.display;
    
    // Ocultar la primera cabecera (checkbox)
    cabeceras[0].style.display = 'none';
    
    // Guardar estado original de las primeras celdas
    const celdasOcultas = [];
    filas.forEach(fila => {
        const primeraCelda = fila.querySelector('td:first-child');
        if (primeraCelda) {
            celdasOcultas.push({
                celda: primeraCelda,
                displayOriginal: primeraCelda.style.display
            });
            primeraCelda.style.display = 'none';
        }
    });
    
    // Imprimir
    window.print();
    
    // Restaurar todo
    setTimeout(() => {
        cabeceras[0].style.display = cabeceraOriginal;
        celdasOcultas.forEach(item => {
            item.celda.style.display = item.displayOriginal;
        });
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

// Inicializar
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