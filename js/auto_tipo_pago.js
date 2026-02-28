/**
 * auto_tipo_pago.js
 * Cambia automáticamente el tipo de pago a COBRO cuando se ingresa un valor
 */

document.addEventListener("DOMContentLoaded", function() {
    
    console.log("💰 Auto Tipo Pago: Sistema iniciado");
    
    // =============================================
    // PARA LA SECCIÓN INSERTAR
    // =============================================
    function initInsertar() {
        const insertarSection = document.getElementById("insertar");
        if (!insertarSection) return false;
        
        const valorInput = document.querySelector('#insertar input[name="valor_cobro"]');
        const tipoPagoSelect = document.querySelector('#insertar select[name="tipo_pago"]');
        
        if (!valorInput || !tipoPagoSelect) return false;
        
        console.log("💰 Auto Tipo Pago: Insertar configurado");
        
        // Crear indicador visual
        const indicador = document.createElement('span');
        indicador.className = 'tipo-pago-indicador';
        indicador.style.cssText = `
            display: inline-block;
            margin-left: 10px;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            transition: all 0.3s ease;
        `;
        tipoPagoSelect.parentNode.appendChild(indicador);
        
        function actualizarTipoPago() {
            const valor = parseFloat(valorInput.value) || 0;
            
            if (valor > 0) {
                tipoPagoSelect.value = "COBRO";
                tipoPagoSelect.style.backgroundColor = "#fff3e0";
                tipoPagoSelect.style.borderColor = "#ff9800";
                indicador.textContent = "💰 Al cobro";
                indicador.style.backgroundColor = "#ff9800";
                indicador.style.color = "white";
            } else {
                tipoPagoSelect.value = "CORRIENTE";
                tipoPagoSelect.style.backgroundColor = "";
                tipoPagoSelect.style.borderColor = "";
                indicador.textContent = "💳 Corriente";
                indicador.style.backgroundColor = "#e0e0e0";
                indicador.style.color = "#666";
            }
        }
        
        valorInput.addEventListener("input", actualizarTipoPago);
        valorInput.addEventListener("blur", actualizarTipoPago);
        
        // Ejecutar al inicio
        actualizarTipoPago();
        
        return true;
    }
    
    // =============================================
    // PARA LA SECCIÓN ACTUALIZAR
    // =============================================
    function initActualizar() {
        const actualizarSection = document.getElementById("actualizar");
        if (!actualizarSection) return false;
        
        const valorInput = document.getElementById("uValor");
        const tipoPagoSelect = document.getElementById("uTipoPago");
        
        if (!valorInput || !tipoPagoSelect) return false;
        
        console.log("💰 Auto Tipo Pago: Actualizar configurado");
        
        function actualizarTipoPago() {
            const valor = parseFloat(valorInput.value) || 0;
            
            if (valor > 0) {
                tipoPagoSelect.value = "COBRO";
                tipoPagoSelect.style.backgroundColor = "#fff3e0";
                tipoPagoSelect.style.borderColor = "#ff9800";
            } else {
                // En actualizar, no cambiar automáticamente a CORRIENTE
                // porque podría estar modificando solo el valor
                tipoPagoSelect.style.backgroundColor = "";
                tipoPagoSelect.style.borderColor = "";
            }
        }
        
        valorInput.addEventListener("input", actualizarTipoPago);
        
        return true;
    }
    
    // =============================================
    // INICIALIZAR
    // =============================================
    initInsertar();
    initActualizar();
    
});