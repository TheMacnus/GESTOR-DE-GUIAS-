function ocultarTabla() {
    const tabla = document.getElementById("tablaContainer");
    if (tabla) {
        tabla.classList.add("oculto");
    }
}

function mostrarSeccion(id) {
    console.log("Cambiando a sección:", id); 
    
    document.querySelectorAll('.seccion').forEach(sec => {
        sec.classList.remove('active');
        sec.style.display = 'none';
    });

    const seccionActiva = document.getElementById(id);
    if (seccionActiva) {
        seccionActiva.classList.add('active');
        seccionActiva.style.display = 'block';
    }

    document.querySelectorAll('.btn-menu').forEach(btn => {
        btn.classList.remove('active');
        
        if (btn.textContent.includes(id === 'buscar' ? 'Buscar' :
                                    id === 'insertar' ? 'Insertar' :
                                    id === 'actualizar' ? 'Actualizar' : 'Eliminar')) {
            btn.classList.add('active');
        }
    });

    ocultarTabla();

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

function limpiarBusqueda() {
    const form = document.querySelector('#buscar form');
    if (form) form.reset();

    ocultarTabla();
    
    console.log("Búsqueda limpiada");
}

function initNavegacion() {
    document.querySelectorAll('.btn-menu').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
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

window.onload = () => {
    console.log("Inicializando navegación...");
    
    document.querySelectorAll('.seccion').forEach(sec => {
        sec.style.display = 'none';
        sec.classList.remove('active');
    });
    
    mostrarSeccion('buscar');
    
    initNavegacion();
};

window.mostrarSeccion = mostrarSeccion;
window.limpiarBusqueda = limpiarBusqueda;
window.ocultarTabla = ocultarTabla;