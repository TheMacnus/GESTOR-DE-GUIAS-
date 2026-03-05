<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title class="title">Coordinadora Trinidad</title>
    <link rel="stylesheet" href="css/index.css?v=<?= time() ?>">
    <link rel="stylesheet" href="css/lightbox.css">
</head>
<body>
<?php
// Iniciar sesión y obtener resultados
session_start();
$resultados = $_SESSION['resultados'] ?? [];
$resumen = $_SESSION['resumen'] ?? [];

// Limpiar sesión después de obtener los datos
unset($_SESSION['resultados']);
unset($_SESSION['resumen']);

// Mostrar mensajes si existen
if (isset($_GET['msg'])) {
    $mensaje = $_GET['msg'];
    echo "<script>alert('" . addslashes($mensaje) . "');</script>";
}
?>

<div class="container">
    <h1>Gestor de Guías - Coordinadora</h1>

    <!-- MENÚ CRUD -->
    <div class="menu">
        <button onclick="mostrarSeccion('buscar')" class="btn-menu">🔍 Buscar</button>
        <button onclick="mostrarSeccion('insertar')" class="btn-menu">➕ Insertar</button>
        <button onclick="mostrarSeccion('actualizar')" class="btn-menu">✏️ Actualizar</button>
        <button onclick="mostrarSeccion('eliminar')" class="btn-menu">🗑️ Eliminar</button>
    </div>

    <!-- ============================================= -->
    <!-- SECCIÓN BUSCAR -->
    <!-- ============================================= -->
    <section id="buscar" class="seccion active">
        <h2>Buscar Guías</h2>
        <form method="POST" action="php/controller.php" class="form-buscar">
            <input type="hidden" name="accion" value="buscar">

            <div class="form-grid">
                <div class="form-group">
                    <label>Número de Guía:</label>
                    <input type="number" name="numero_guia" placeholder="Completo o últimos 4 dígitos">
                </div>

                <div class="form-group">
                    <label>Sucursal:</label>
                    <select name="sucursal">
                        <option value="">Todas</option>
                        <option value="TRINIDAD">Trinidad</option>
                        <option value="SAN_LUIS">San Luis</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Tipo de Pago:</label>
                    <select name="tipo_pago">
                        <option value="">Todos</option>
                        <option value="CORRIENTE">Corriente</option>
                        <option value="COBRO">Al cobro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Estado:</label>
                    <select name="estado">
                        <option value="">Todos</option>
                        <option value="ACTIVA">Activa</option>
                        <option value="CANCELADA">Cancelada</option>
                        <option value="DEVOLUCION">Devolución</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Fecha Desde:</label>
                    <input type="date" name="fecha_desde">
                </div>

                <div class="form-group">
                    <label>Fecha Hasta:</label>
                    <input type="date" name="fecha_hasta">
                </div>
            </div>
            <div class="filtros-rapidos" style="margin: 10px 0; display: flex; gap: 10px;">
                <button type="submit" name="hoy" value="1" class="btn btn-info">📅 Guías de Hoy</button>
                <button type="submit" name="trinidad_hoy" value="1" class="btn btn-info">🏢 Trinidad Hoy</button>
                <button type="submit" name="sanluis_hoy" value="1" class="btn btn-info" style="background: #ffc107; color: black;"> 🏢 San Luis HOY</button>
            </div>

            <div class="acciones">
                <button type="submit" class="btn btn-primary">🔍 Buscar</button>
                <button type="submit" name="exportar" value="1" class="btn btn-success">📊 Exportar Excel</button>
                <button type="button" onclick="imprimirManifiesto()" class="btn btn-info">🖨️ Imprimir</button>
                <a href="php/manifiesto_pdf.php" target="_blank" class="btn btn-warning">📄 Descargar PDF</a>
            </div>
        </form>
    </section>

    <!-- ============================================= -->
    <!-- SECCIÓN INSERTAR -->
    <!-- ============================================= -->
    <section id="insertar" class="seccion">
        <h2>Insertar Nueva Guía</h2>
        <form method="POST" action="php/controller.php" class="form-insertar">
            <input type="hidden" name="accion" value="insertar">

            <div class="form-grid">
                <div class="form-group">
                    <label>Sucursal:*</label>
                    <select name="sucursal" required>
                        <option value="">Seleccione</option>
                        <option value="TRINIDAD">Trinidad</option>
                        <option value="SAN_LUIS">San Luis</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Número de Guía:*</label>
                    <input type="number" name="numero_guia" placeholder="Ej: 99170147078" required>
                </div>

                <div class="form-group">
                    <label>Destinatario:*</label>
                    <input type="text" name="destinatario" placeholder="Nombre completo" required>
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <input type="text" name="descripcion" placeholder="Descripción del envío">
                </div>

                <div class="form-group">
                    <label>Tipo de Pago:*</label>
                    <select name="tipo_pago" required>
                        <option value="CORRIENTE">Corriente</option>
                        <option value="COBRO">Al cobro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Valor a Cobrar:</label>
                    <input type="number" name="valor_cobro" placeholder="0.00" step="0.01">
                </div>
                <div class="form-group">
                    <label>Número de Paquetes:</label>
                    <input 
                        type="number" 
                        name="numero_paquetes" 
                        placeholder="Cantidad de bultos" 
                        min="1" 
                        value="1"
                        style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                    >
                    <small style="color: #666;">Cantidad de paquetes/bultos de esta guía</small>
                </div>

                <div class="form-group">
                    <label>Estado:*</label>
                    <select name="estado" required>
                        <option value="ACTIVA">Activa</option>
                        <option value="CANCELADA">Cancelada</option>
                        <option value="DEVOLUCION">Devolución</option>
                    </select>
                </div>
            </div>

            <div class="acciones">
                <button type="submit" class="btn btn-primary">💾 Guardar Guía</button>
                <button type="reset" class="btn btn-secondary">🔄 Limpiar</button>
            </div>
        </form>
    </section>

    <!-- ============================================= -->
    <!-- SECCIÓN ACTUALIZAR -->
    <!-- ============================================= -->
    <section id="actualizar" class="seccion">
        <h2>Actualizar Guía</h2>
        
        <div class="buscador-actualizar">
            <input
                type="number"
                id="buscarActualizar"
                placeholder="Ingrese guía completa o últimos 4 dígitos"
                autocomplete="off"
                class="input-buscar"
            >
        </div>

        <!-- PREVISUALIZACIÓN -->
        <div id="previewActualizar" style="display:none;" class="card-guia">
            <div class="datos-actuales">
                <h3>📋 Datos Actuales</h3>
                    <div class="grid-datos">
                        <p><strong>📦 Guía:</strong> <span id="pGuia"></span></p>
                        <p><strong>👤 Destinatario:</strong> <span id="pDestinatario"></span></p>
                        <p><strong>🏢 Sucursal:</strong> <span id="pSucursal"></span></p>
                        <p><strong>💳 Pago:</strong> <span id="pPago"></span></p>
                        <p><strong>💰 Valor:</strong> $<span id="pValor"></span></p>
                        <p><strong>📅 Fecha Creación:</strong> <span id="pFechaCreacion"></span></p>
                        <p><strong>📦 Número de Paquetes:</strong> <span id="pPaquetes"></span></p>
                    </div>

                <!-- VOUCHER ACTUAL -->
                <div class="voucher-actual">
                    <p><strong>📸 Voucher Actual:</strong></p>
                    <img id="pVoucher" style="display:none; max-width:200px; border-radius:8px; border: 1px solid #ccc;">
                    <p id="sinVoucher">No tiene voucher</p>
                </div>
            </div>

            <form method="POST" action="php/controller.php" enctype="multipart/form-data" class="form-actualizar">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" name="numero_guia" id="uNumeroGuia">

                <h3>Modificar Campos</h3>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Sucursal:</label>
                        <select name="sucursal" id="uSucursal">
                            <option value="">-- Mantener actual --</option>
                            <option value="TRINIDAD">Trinidad</option>
                            <option value="SAN_LUIS">San Luis</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Estado:</label>
                        <select name="estado" id="uEstado">
                            <option value="">-- Mantener actual --</option>
                            <option value="ACTIVA">Activa</option>
                            <option value="CANCELADA">Cancelada</option>
                            <option value="DEVOLUCION">Devolución</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Tipo de Pago:</label>
                        <select name="tipo_pago" id="uTipoPago">
                            <option value="">-- Mantener actual --</option>
                            <option value="CORRIENTE">Corriente</option>
                            <option value="COBRO">Al cobro</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Valor a Cobrar:</label>
                        <input
                            type="number"
                            name="valor_cobro"
                            id="uValor"
                            placeholder="Dejar vacío para mantener actual"
                            step="0.01"
                        >
                    </div>

                    <div class="form-group">
                        <label>Número de Paquetes:</label>
                        <input
                            type="number"
                            name="numero_paquetes"
                            id="uPaquetes"
                            placeholder="Cantidad de bultos"
                            min="1"
                            step="1"
                            style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"
                        >
                        <small style="color: #666;">Dejar vacío para mantener actual</small>
                    </div>

                    <div class="form-group full-width">
                        <label>Nuevo Voucher (opcional):</label>
                        <input type="file" name="voucher" accept="image/*">
                        <small>Selecciona solo si quieres cambiar el voucher actual</small>
                    </div>
                </div>

                <div class="acciones">
                    <button type="submit" class="btn btn-primary">✅ Actualizar Guía</button>
                </div>
            </form>
        </div>
    </section>

    <!-- ============================================= -->
    <!-- SECCIÓN ELIMINAR -->
    <!-- ============================================= -->
    <section id="eliminar" class="seccion">
        <h2>Eliminar Guía</h2>
        <form method="POST" action="php/controller.php" class="form-eliminar" onsubmit="return confirm('¿Está seguro de eliminar esta guía?');">
            <input type="hidden" name="accion" value="eliminar">

            <div class="form-grid">
                <div class="form-group">
                    <label>Sucursal:*</label>
                    <select name="sucursal" required>
                        <option value="">Seleccione</option>
                        <option value="TRINIDAD">Trinidad</option>
                        <option value="SAN_LUIS">San Luis</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Número de Guía:*</label>
                    <input type="number" name="numero_guia" placeholder="Ej: 99170147078" required>
                </div>
            </div>

            <div class="acciones">
                <button type="submit" class="btn btn-danger">🗑️ Eliminar Guía</button>
            </div>
        </form>
    </section>

    <!-- ============================================= -->
    <!-- RESUMEN DE BÚSQUEDA -->
    <!-- ============================================= -->
    <?php if (!empty($resumen)) { ?>
    <div class="resumen">
        <h3>📊 Resumen de Búsqueda</h3>
        <div class="resumen-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px;">
            <div class="resumen-item" style="background: #e3f2fd; padding: 15px; border-radius: 8px; text-align: center;">
                <span class="label" style="display: block; font-size: 0.9em; color: #555;">Total Guías</span>
                <span class="valor" style="display: block; font-size: 1.8em; font-weight: bold; color: #1976d2;">
                    <?= $resumen['cantidad'] ?>
                </span>
            </div>
            
            <div class="resumen-item" style="background: #e8f5e9; padding: 15px; border-radius: 8px; text-align: center;">
                <span class="label" style="display: block; font-size: 0.9em; color: #555;">Total Paquetes</span>
                <span class="valor" style="display: block; font-size: 1.8em; font-weight: bold; color: #2e7d32;">
                    <?= $resumen['total_paquetes'] ?>
                </span>
            </div>
            
            <div class="resumen-item activo" style="background: #fff3e0; padding: 15px; border-radius: 8px; text-align: center;">
                <span class="label" style="display: block; font-size: 0.9em; color: #555;">Total Activo</span>
                <span class="valor" style="display: block; font-size: 1.8em; font-weight: bold; color: #f57c00;">
                    $<?= number_format($resumen['valor_activo'], 0, ',', '.') ?>
                </span>
            </div>
            
            <div class="resumen-item cancelado" style="background: #ffebee; padding: 15px; border-radius: 8px; text-align: center;">
                <span class="label" style="display: block; font-size: 0.9em; color: #555;">Total Cancelado</span>
                <span class="valor" style="display: block; font-size: 1.8em; font-weight: bold; color: #c62828;">
                    $<?= number_format($resumen['valor_cancelado'], 0, ',', '.') ?>
                </span>
            </div>
        </div>
    </div>
    <?php } ?>

<!-- ============================================= -->
<!-- TABLA DE RESULTADOS CON CHECKBOXES -->
<!-- ============================================= -->
<?php if (!empty($resultados)) { ?>
<div class="tabla-container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
        <h3 style="margin: 0;">📋 Resultados de Búsqueda</h3>
        <div>
            <button type="button" onclick="seleccionarTodas()" class="btn btn-secondary" style="margin-right: 5px;">✓ Todas</button>
            <button type="button" onclick="deseleccionarTodas()" class="btn btn-secondary" style="margin-right: 5px;">✗ Ninguna</button>
            <span style="margin-left: 10px; font-size: 13px; color: #666;" id="contadorSeleccionadas">0 seleccionadas</span>
        </div>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="tabla-guias" id="tablaResultados" style="min-width: 100%; font-size: 14px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="width: 30px; font-size: 14px; padding: 10px 5px; text-align: center;">
                        <input type="checkbox" id="checkTodo" onclick="toggleTodo()" style="transform: scale(1.2);">
                    </th>
                    <th style="font-size: 14px; padding: 10px 8px;">Guía</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Suc.</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Destinatario</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Estado</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Pago</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Valor</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Paq.</th>
                    <th style="font-size: 14px; padding: 10px 8px;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultados as $g) { ?>
                <tr>
                    <td style="text-align: center; padding: 8px 5px;">
                        <input type="checkbox" class="seleccionar-guia" value="<?= htmlspecialchars($g['numero_guia']) ?>" style="transform: scale(1.2);">
                    </td>
                    <td style="font-family: monospace; font-size: 13px; padding: 8px 8px; white-space: nowrap;"><?= htmlspecialchars($g['numero_guia']) ?></td>
                    <td style="text-align: center; font-size: 13px; padding: 8px 8px;"><?= htmlspecialchars(substr($g['sucursal'], 0, 8)) ?></td>
                    <td style="max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-size: 13px; padding: 8px 8px;" title="<?= htmlspecialchars($g['destinatario']) ?>">
                        <?= htmlspecialchars($g['destinatario']) ?>
                    </td>
                    <td style="text-align: center; font-size: 13px; padding: 8px 8px;">
                        <span class="estado <?= strtolower(htmlspecialchars($g['estado'])) ?>" style="font-size: 12px; padding: 4px 8px;">
                            <?= substr(htmlspecialchars($g['estado']), 0, 3) ?>
                        </span>
                    </td>
                    <td style="text-align: center; font-size: 13px; padding: 8px 8px;"><?= substr(htmlspecialchars($g['tipo_pago']), 0, 3) ?></td>
                    <td style="text-align: right; font-size: 13px; padding: 8px 8px; white-space: nowrap; font-weight: 500;">
                        <?= $g['valor_cobro'] > 0 ? '$'.number_format($g['valor_cobro'], 0) : '' ?>
                    </td>
                    <td style="text-align: center; font-size: 13px; padding: 8px 8px;"><?= htmlspecialchars($g['numero_paquetes'] ?? 1) ?></td>
                    <td style="font-size: 12px; padding: 8px 8px; white-space: nowrap;"><?= date('d/m/Y', strtotime($g['fecha_creacion'])) ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>
</div>

<!-- JavaScript files -->
<script src="js/mostrar_ocultar.js"></script>
<script src="js/auto_tipo_pago.js"></script>
<script src="js/imprimir.js"></script>
<script src="js/actualizar_preview.js"></script>
<script src="js/lightbox.js"></script>



</body>
</html>