/**
 * lightbox.js
 * Funcionalidad para ver imágenes ampliadas (modal)
 */

console.log("🚀 Lightbox: Cargando...");

// Variables globales
let modalActivo = false;

// Crear el modal si no existe
function crearModalSiNoExiste() {
    console.log("🔧 Lightbox: Creando modal...");
    
    if (document.getElementById('modalImagen')) {
        console.log("✅ Lightbox: Modal ya existe");
        return;
    }
    
    const modalHTML = `
        <div id="modalImagen" class="modal-imagen" style="display: none;">
            <span class="cerrar-modal" id="cerrarModalBtn">&times;</span>
            <img class="modal-contenido" id="imagenAmpliada">
            <div id="modal-titulo" class="modal-titulo"></div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    console.log("✅ Lightbox: Modal creado");
    
    // Agregar evento al botón cerrar
    const cerrarBtn = document.getElementById('cerrarModalBtn');
    if (cerrarBtn) {
        cerrarBtn.onclick = function(e) {
            e.stopPropagation();
            cerrarLightbox();
        };
    }
    
    // Cerrar al hacer clic fuera de la imagen
    const modal = document.getElementById('modalImagen');
    modal.onclick = function(e) {
        if (e.target === modal) {
            cerrarLightbox();
        }
    };
}

// Función para abrir el modal
function abrirLightbox(src, titulo = '') {
    console.log("🔍 Lightbox: Intentando abrir:", src);
    
    crearModalSiNoExiste();
    
    const modal = document.getElementById('modalImagen');
    const imgAmpliada = document.getElementById('imagenAmpliada');
    const modalTitulo = document.getElementById('modal-titulo');
    
    if (!modal) {
        console.error("❌ Lightbox: Modal no encontrado");
        return;
    }
    
    if (!imgAmpliada) {
        console.error("❌ Lightbox: Imagen ampliada no encontrada");
        return;
    }
    
    // Configurar la imagen
    imgAmpliada.src = src;
    modalTitulo.textContent = titulo;
    
    // Mostrar modal
    modal.style.display = 'block';
    modalActivo = true;
    
    // Prevenir scroll
    document.body.style.overflow = 'hidden';
    
    console.log("✅ Lightbox: Abierto correctamente");
    
    // Evento de carga exitosa
    imgAmpliada.onload = function() {
        console.log("✅ Lightbox: Imagen cargada:", src);
    };
    
    // Evento de error
    imgAmpliada.onerror = function() {
        console.error("❌ Lightbox: Error al cargar imagen:", src);
        modalTitulo.textContent = "Error al cargar la imagen";
    };
}

// Función para cerrar el modal
function cerrarLightbox() {
    console.log("🔍 Lightbox: Cerrando...");
    
    const modal = document.getElementById('modalImagen');
    if (modal) {
        modal.style.display = 'none';
        modalActivo = false;
        document.body.style.overflow = 'auto';
        console.log("✅ Lightbox: Cerrado");
    }
}

// Cerrar con tecla ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalActivo) {
        console.log("🔍 Lightbox: Cerrando con ESC");
        cerrarLightbox();
    }
});

// Inicializar cuando carga la página
document.addEventListener('DOMContentLoaded', function() {
    console.log("🚀 Lightbox: Inicializando...");
    crearModalSiNoExiste();
    console.log("✅ Lightbox: Listo");
});

// Hacer funciones globales
window.abrirLightbox = abrirLightbox;
window.cerrarLightbox = cerrarLightbox;