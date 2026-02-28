/**
 * mostrar_ocultar.js
 * Maneja la navegación entre secciones y la visibilidad de elementos
 */

// Función para ocultar la tabla de resultados
function ocultarTabla() {
    const tabla = document.getElementById("tablaContainer");
    if (tabla) {
        tabla.classList.add("oculto");
    }
}

// Función principal para mostrar secciones
function mostrarSeccion(id) {
    console.log("Cambiando a sección:", id); // Para depuración
    
    // 1. Ocultar todas las secciones
    document.querySelectorAll('.seccion').forEach(sec => {
        sec.classList.remove('active');
        sec.style.display = 'none';
    });

    // 2. Mostrar la sección seleccionada
    const seccionActiva = document.getElementById(id);
    if (seccionActiva) {
        seccionActiva.classList.add('active');
        seccionActiva.style.display = 'block';
    }

    // 3. Actualizar botones del menú
    document.querySelectorAll('.btn-menu').forEach(btn => {
        btn.classList.remove('active');
        
        // Si el texto del botón coincide con la sección (aproximadamente)
        if (btn.textContent.includes(id === 'buscar' ? 'Buscar' :
                                    id === 'insertar' ? 'Insertar' :
                                    id === 'actualizar' ? 'Actualizar' : 'Eliminar')) {
            btn.classList.add('active');
        }
    });

    // 4. Ocultar la tabla de resultados al cambiar de sección
    ocultarTabla();

    // 5. Scroll suave hacia arriba
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Función para limpiar el formulario de búsqueda
function limpiarBusqueda() {
    // Limpiar formulario de búsqueda
    const form = document.querySelector('#buscar form');
    if (form) form.reset();

    // Ocultar tabla
    ocultarTabla();
    
    console.log("Búsqueda limpiada");
}

// Función para inicializar la navegación
function initNavegacion() {
    // Agregar event listeners a los botones del menú
    document.querySelectorAll('.btn-menu').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Determinar qué sección mostrar según el texto del botón
            const texto = this.textContent;
            let seccion = '';
            
            if (texto.includes('Buscar')) seccion = 'buscar';
            else if (texto.includes('Insertar')) seccion = 'insertar';
            else if (texto.includes('Actualizar')) seccion = 'actualizar';
            else if (texto.includes('Eliminar')) seccion = 'eliminar';
            
            if (seccion) mostrarSeccion(seccion);
        });
    });
}

// Mostrar buscar por defecto cuando carga la página
window.onload = () => {
    console.log("Inicializando navegación...");
    
    // Asegurar que todas las secciones están ocultas inicialmente
    document.querySelectorAll('.seccion').forEach(sec => {
        sec.style.display = 'none';
        sec.classList.remove('active');
    });
    
    // Mostrar la sección de búsqueda
    mostrarSeccion('buscar');
    
    // Inicializar event listeners
    initNavegacion();
};

// Exportar funciones para uso global
window.mostrarSeccion = mostrarSeccion;
window.limpiarBusqueda = limpiarBusqueda;
window.ocultarTabla = ocultarTabla;