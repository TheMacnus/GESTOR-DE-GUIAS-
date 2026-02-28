document.addEventListener("DOMContentLoaded", () => {

    console.log("✅ JavaScript cargado correctamente");
    
    const inputAct = document.getElementById("buscarActualizar");
    const card = document.getElementById("previewActualizar");

    if (!inputAct) {
        console.log("❌ No se encontró el input buscarActualizar");
        return;
    } else {
        console.log("✅ Input encontrado:", inputAct);
    }

    inputAct.addEventListener("input", () => {

        const v = inputAct.value.trim();
        console.log("🔍 Escribiendo:", v);

        if (v.length < 4) {
            console.log("📏 Menos de 4 caracteres, ocultando preview");
            card.style.display = "none";
            return;
        }

        console.log("📡 Enviando petición AJAX para:", v);
        
        fetch("php/controller.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({
                accion: "buscar_auto",
                numero_guia: v
            })
        })
        .then(res => res.json())
        .then(d => {
            
            console.log("📦 Datos recibidos:", d);

            if (!d || !d.numero_guia) {
                card.style.display = "none";
                return;
            }

            // Mostrar datos básicos
            document.getElementById("pGuia").textContent = d.numero_guia;
            document.getElementById("pDestinatario").textContent = d.destinatario;
            document.getElementById("pSucursal").textContent = d.sucursal;
            document.getElementById("pPago").textContent = d.tipo_pago;
            document.getElementById("pValor").textContent = d.valor_cobro ?? 0;
            
            // ===== ASIGNAR FECHA =====
            const fechaSpan = document.getElementById("pFechaCreacion");
            if (fechaSpan) {
                fechaSpan.textContent = d.fecha_creacion || "No disponible";
                console.log("✅ Fecha asignada:", fechaSpan.textContent);
            } else {
                console.error("❌ No se encontró el elemento pFechaCreacion");
            }

// ===== MANEJO DE VOUCHER - CON CLIC PARA AMPLIAR =====
const img = document.getElementById("pVoucher");
const texto = document.getElementById("sinVoucher");

if (d.voucher && d.voucher !== null && d.voucher !== "") {
    
    // Limpiar el nombre del archivo
    let nombreArchivo = d.voucher;
    if (nombreArchivo.includes('/')) {
        nombreArchivo = nombreArchivo.split('/').pop();
    }
    if (nombreArchivo.includes('\\')) {
        nombreArchivo = nombreArchivo.split('\\').pop();
    }
    
    console.log("📸 Nombre del voucher:", nombreArchivo);
    
    // URL para la imagen (usando el endpoint)
    const urlVoucher = 'php/get_voucher.php?voucher=' + encodeURIComponent(nombreArchivo);
    
    // Configurar la imagen
    img.src = urlVoucher;
    img.style.display = "block";
    img.style.maxWidth = "200px";
    img.style.maxHeight = "200px";
    img.style.borderRadius = "8px";
    img.style.border = "1px solid #ccc";
    img.style.padding = "5px";
    img.style.background = "#f9f9f9";
    img.style.objectFit = "contain";
    img.style.cursor = "pointer";
    img.title = "Haz clic para ampliar";
    texto.style.display = "none";
    
// Agregar evento de clic para ampliar usando lightbox
img.onclick = function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log("🖱️ Clic en voucher - Abriendo lightbox");
    
    // Usar la ruta directa para ampliar
    const urlDirecta = window.location.origin + '/coordinadora_tdd/vouchers/' + nombreArchivo;
    console.log("🔗 URL para ampliar:", urlDirecta);
    
    abrirLightbox(urlDirecta, `Voucher - Guía: ${d.numero_guia}`);
};
    
    // Manejar error
    img.onerror = function() {
        console.error("❌ Error al cargar voucher:", urlVoucher);
        
        // Intentar con ruta directa
        const rutaDirecta = window.location.origin + '/coordinadora_tdd/vouchers/' + nombreArchivo;
        img.src = rutaDirecta;
        
        img.onerror = function() {
            console.error("❌ También falló ruta directa");
            img.style.display = "none";
            texto.style.display = "block";
            texto.innerHTML = "Error al cargar el voucher";
            texto.style.color = "#e74c3c";
            texto.style.fontWeight = "bold";
        };
        
        img.onload = function() {
            console.log("✅ Voucher cargado con ruta directa");
            img.onclick = function(e) {
                e.stopPropagation();
                abrirModal(rutaDirecta, `Voucher - Guía: ${d.numero_guia}`);
            };
        };
    };
    
} else {
    console.log("ℹ️ La guía no tiene voucher");
    img.style.display = "none";
    texto.style.display = "block";
    texto.innerHTML = "No tiene voucher";
}
            // Formulario
            document.getElementById("uNumeroGuia").value = d.numero_guia;
            document.getElementById("uSucursal").value = d.sucursal;
            document.getElementById("uEstado").value = d.estado;
            document.getElementById("uPaquetes").value = d.numero_paquetes ?? '';
            document.getElementById("uTipoPago").value = d.tipo_pago;
            document.getElementById("uValor").value = d.valor_cobro ?? '';
            document.getElementById("uPaquetes").value = d.numero_paquetes ?? '';

            card.style.display = "block";
        })
        .catch(error => {
            console.error("Error:", error);
            card.style.display = "none";
        });
    });

});