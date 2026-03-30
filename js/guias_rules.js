// js/guias_transform.js - Transformación de guías

/**
 * Transforma el número de guía según la regla:
 * 1. Eliminar los últimos 3 dígitos
 * 2. Tomar los últimos 11 dígitos del resultado
 * 
 * @param {string} valor - Número de guía original
 * @returns {string|null} Guía transformada o null si no es válida
 */
function transformarGuia(valor) {
    // Limpiar a solo números
    const numeros = valor.replace(/\D/g, '');
    
    if (numeros.length >= 14) {
        // 1. Eliminar los últimos 3 dígitos
        const sinUltimos3 = numeros.slice(0, -3);
        
        // 2. Tomar los últimos 11 dígitos
        const resultado = sinUltimos3.slice(-11);
        
        return resultado;
    }
    
    return null;
}

/**
 * Aplica la transformación al input y lo modifica directamente
 * @param {HTMLInputElement} input - El input que contiene la guía
 * @returns {boolean} - True si se transformó, false si no
 */
function aplicarTransformacionGuia(input) {
    const valorOriginal = input.value;
    const transformada = transformarGuia(valorOriginal);
    
    if (transformada) {
        // Modificar directamente el valor del input
        input.value = transformada;
        
        // Mostrar mensaje de confirmación temporal
        mostrarMensajeTemporal(input, '✓ Transformado: ' + transformada);
        
        return true;
    } else if (valorOriginal.replace(/\D/g, '').length > 0 && valorOriginal.replace(/\D/g, '').length < 14) {
        mostrarMensajeTemporal(input, '⚠️ Mínimo 14 dígitos requeridos', '#f44336');
        return false;
    }
    
    return false;
}

/**
 * Muestra un mensaje temporal debajo del input
 */
function mostrarMensajeTemporal(input, mensaje, color = '#4CAF50') {
    // Buscar o crear contenedor de mensaje
    let mensajeDiv = input.parentElement.querySelector('.mensaje-transformacion');
    
    if (!mensajeDiv) {
        mensajeDiv = document.createElement('div');
        mensajeDiv.className = 'mensaje-transformacion';
        mensajeDiv.style.fontSize = '11px';
        mensajeDiv.style.marginTop = '4px';
        mensajeDiv.style.transition = 'opacity 0.3s';
        input.parentElement.appendChild(mensajeDiv);
    }
    
    mensajeDiv.textContent = mensaje;
    mensajeDiv.style.color = color;
    
    // Ocultar después de 2 segundos
    setTimeout(() => {
        mensajeDiv.style.opacity = '0';
        setTimeout(() => {
            if (mensajeDiv) mensajeDiv.textContent = '';
            mensajeDiv.style.opacity = '1';
        }, 300);
    }, 2000);
}

/**
 * Inicializa el campo de guía en el formulario de inserción
 */
function inicializarInserccionGuia() {
    const formInsertar = document.querySelector('#insertar form');
    if (!formInsertar) return;
    
    const inputGuia = formInsertar.querySelector('input[name="numero_guia"]');
    if (!inputGuia) return;
    
    // Obtener todos los inputs del formulario en orden para navegación con Enter
    const todosLosInputs = Array.from(formInsertar.querySelectorAll('input, select, textarea'))
        .filter(input => input.type !== 'hidden' && input.type !== 'file' && !input.disabled);
    
    // Función para manejar Enter
    function manejarEnter(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            
            // Aplicar transformación al campo actual
            aplicarTransformacionGuia(inputGuia);
            
            // Mover al siguiente campo
            const indexActual = todosLosInputs.indexOf(inputGuia);
            if (indexActual < todosLosInputs.length - 1) {
                todosLosInputs[indexActual + 1].focus();
            }
        }
    }
    
    // Función para manejar cuando pierde el foco (blur)
    function manejarBlur() {
        aplicarTransformacionGuia(inputGuia);
    }
    
    // Agregar event listeners
    inputGuia.addEventListener('keypress', manejarEnter);
    inputGuia.addEventListener('blur', manejarBlur);
}

/**
 * Inicializa el campo de búsqueda en actualización
 */
function inicializarActualizacionGuia() {
    const inputBuscar = document.getElementById('buscarActualizar');
    if (!inputBuscar) return;
    
    function manejarEnter(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            aplicarTransformacionGuia(inputBuscar);
            // Disparar la búsqueda automática
            if (typeof buscarGuia === 'function') {
                buscarGuia();
            }
        }
    }
    
    function manejarBlur() {
        aplicarTransformacionGuia(inputBuscar);
    }
    
    inputBuscar.addEventListener('keypress', manejarEnter);
    inputBuscar.addEventListener('blur', manejarBlur);
}

/**
 * Inicializa el campo de guía en el formulario de eliminación
 */
function inicializarEliminarGuia() {
    const formEliminar = document.querySelector('#eliminar form');
    if (!formEliminar) return;
    
    const inputGuia = formEliminar.querySelector('input[name="numero_guia"]');
    if (!inputGuia) return;
    
    const todosLosInputs = Array.from(formEliminar.querySelectorAll('input, select'))
        .filter(input => input.type !== 'hidden');
    
    function manejarEnter(event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            aplicarTransformacionGuia(inputGuia);
            
            const indexActual = todosLosInputs.indexOf(inputGuia);
            if (indexActual < todosLosInputs.length - 1) {
                todosLosInputs[indexActual + 1].focus();
            }
        }
    }
    
    function manejarBlur() {
        aplicarTransformacionGuia(inputGuia);
    }
    
    inputGuia.addEventListener('keypress', manejarEnter);
    inputGuia.addEventListener('blur', manejarBlur);
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    inicializarInserccionGuia();
    inicializarActualizacionGuia();
    inicializarEliminarGuia();
});