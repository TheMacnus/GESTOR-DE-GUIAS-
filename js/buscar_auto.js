console.log("🔵 ESTE ES buscar_auto.js el que se está ejecutando");
alert("SI VES ESTA ALERTA, ESTÁS USANDO buscar_auto.js");

const inputAct = document.getElementById("buscarActualizar");
const card = document.getElementById("previewActualizar");

inputAct.addEventListener("input", () => {
    const v = inputAct.value.trim();
    if (v.length < 4) {
        card.style.display = "none";
        return;
    }

    fetch("php/controller.php", {
        method: "POST",
        headers: {"Content-Type":"application/x-www-form-urlencoded"},
        body: new URLSearchParams({
            accion: "buscar_auto",
            numero_guia: v
        })
    })
    .then(r => r.json())
    .then(d => {
        if (!d) {
            card.style.display = "none";
            return;
        }

        // PREVIEW
        document.getElementById("pGuia").textContent = d.numero_guia;
        document.getElementById("pDestinatario").textContent = d.destinatario;
        document.getElementById("pSucursal").textContent = d.sucursal;
        document.getElementById("pPago").textContent = d.tipo_pago;
        document.getElementById("pValor").textContent = d.valor_cobro;

        // FORM
        document.getElementById("uNumeroGuia").value = d.numero_guia;
        document.getElementById("uSucursal").value = d.sucursal;
        document.getElementById("uEstado").value = d.estado;
        document.getElementById("uTipoPago").value = d.tipo_pago;
        document.getElementById("uValor").value = d.valor_cobro;

        card.style.display = "block";
    });
});

