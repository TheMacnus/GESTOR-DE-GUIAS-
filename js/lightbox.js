let modalActivo = false;

function crearModalSiNoExiste() {
    console.log("lightbox: Creando modal");
    
    if (document.getElementById('modalImagen')) {
        console.log("lightbox: Modal ya existe");
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
    console.log("lightbox: Modal creado");
    
    const cerrarBtn = document.getElementById('cerrarModalBtn');
    if (cerrarBtn) {
        cerrarBtn.onclick = function(e) {
            e.stopPropagation();
            cerrarLightbox();
        };
    }
    
    const modal = document.getElementById('modalImagen');
    modal.onclick = function(e) {
        if (e.target === modal) {
            cerrarLightbox();
        }
    };
}

function abrirLightbox(src, titulo = '') {
    console.log("lightbox: Intentando abrir:", src);
    
    crearModalSiNoExiste();
    
    const modal = document.getElementById('modalImagen');
    const imgAmpliada = document.getElementById('imagenAmpliada');
    const modalTitulo = document.getElementById('modal-titulo');
    
    if (!modal || !imgAmpliada) return;
    
    imgAmpliada.src = '';
    modalTitulo.textContent = titulo;
    
    modal.style.display = 'block';
    modalActivo = true;
    document.body.style.overflow = 'hidden';
    
    imgAmpliada.onload = function() {
        console.log("lightbox: Imagen cargada correctamente");
    };
    
imgAmpliada.onerror = function() {
    console.error("lightbox: Error al cargar imagen:", src);
    
    const rutaActual = window.location.pathname;
    let basePath = '';
    if (rutaActual.includes('/')) {
        const partes = rutaActual.split('/');
        if (partes.length > 1 && partes[1] !== '') {
            basePath = '/' + partes[1];
        }
    }
    
    if (src.includes('/vouchers/')) {
        const nombreArchivo = src.split('/').pop();
        const rutaDirecta = window.location.origin + basePath + '/vouchers/' + nombreArchivo;
        
        console.log("🔄 Lightbox: Intentando ruta directa:", rutaDirecta);
        imgAmpliada.src = rutaDirecta;
        
        imgAmpliada.onerror = function() {
            console.error("❌ Lightbox: También falló");
            modalTitulo.textContent = "Error: No se pudo cargar la imagen";
        };
        
        return;
    }
    
    modalTitulo.textContent = "Error: No se pudo cargar la imagen";
};
    
    imgAmpliada.src = src;
}

function cerrarLightbox() {
    console.log("lightbox: Cerrando...");
    
    const modal = document.getElementById('modalImagen');
    if (modal) {
        modal.style.display = 'none';
        modalActivo = false;
        document.body.style.overflow = 'auto';
        console.log("lightbox: Cerrado");
    }
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && modalActivo) {
        console.log("lightbox: Cerrando con ESC");
        cerrarLightbox();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    console.log("lightbox: Inicializando...");
    crearModalSiNoExiste();
    console.log("lightbox: Listo");
});

window.abrirLightbox = abrirLightbox;
window.cerrarLightbox = cerrarLightbox;