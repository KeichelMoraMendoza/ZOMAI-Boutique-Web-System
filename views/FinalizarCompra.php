<?php
// 1. Verificamos que el carrito tenga productos
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    echo "<script>alert('Tu selección está vacía. Elige algunas prendas primero.'); window.location.href='index.php';</script>";
    exit();
}

// 2. Lógica de Cálculos para la vista y el mensaje
$subtotal = 0;
$resumen_prendas_ws = "";

foreach ($_SESSION['carrito'] as $item) {
    $precio_item = $item['precio'];
    $cantidad_item = $item['cantidad'] ?? 1;
    $subtotal += ($precio_item * $cantidad_item);
    // Formateamos para WhatsApp
    $resumen_prendas_ws .= "• " . strtoupper($item['nombre']) . " (x" . $cantidad_item . ") - ₡" . number_format($precio_item, 0) . "%0A";
}

$envio_costo = 3500;
$total_estimado = $subtotal + $envio_costo;

// 3. Vaciado del carrito después de la redirección
if (isset($_GET['finalizado']) && $_GET['finalizado'] == 'true') {
    unset($_SESSION['carrito']);
    echo "<script>window.location.href='index.php?modulo=inicio&status=success';</script>";
    exit();
}
?>

<div style="max-width: 1100px; margin: 80px auto; background: #fff; padding: 60px; font-family: 'Poppins', sans-serif; color: #1a1a1a; box-shadow: 0 10px 40px rgba(0,0,0,0.03); border-radius: 4px;">
    
    <div style="text-align: center; margin-bottom: 70px;">
        <span style="font-size: 10px; letter-spacing: 5px; color: #bbb; text-transform: uppercase;">Exclusividad en cada pieza</span>
        <h1 style="font-family: 'Playfair Display', serif; font-size: 3rem; font-weight: 400; letter-spacing: 2px; margin: 15px 0;">Finalizar Reserva</h1>
        <div style="height: 1px; background: #eee; width: 100px; margin: 30px auto;"></div>
    </div>

    <div style="display: flex; gap: 80px; flex-wrap: wrap;">
        <div style="flex: 1.5; min-width: 350px;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 400; letter-spacing: 1px; margin-bottom: 40px; color: #333;">Información de Entrega</h2>

            <form id="formReserva" onsubmit="enviarAWhatsApp(event)" style="display: flex; flex-direction: column; gap: 35px;">
                
                <div class="input-group">
                    <label style="display: block; font-size: 11px; letter-spacing: 2px; color: #999; text-transform: uppercase; margin-bottom: 10px;">Nombre Completo</label>
                    <input type="text" id="nombre_cliente" required
                           style="width: 100%; background: transparent; border: none; border-bottom: 1px solid #e0e0e0; color: #1a1a1a; padding: 12px 0; outline: none; font-size: 15px; font-weight: 300;">
                </div>

                <div class="input-group">
                    <label style="display: block; font-size: 11px; letter-spacing: 2px; color: #999; text-transform: uppercase; margin-bottom: 10px;">Provincia</label>
                    <select id="provincia" required
                            style="width: 100%; background: #fdfdfd; border: 1px solid #e0e0e0; color: #1a1a1a; padding: 15px; font-size: 14px; font-weight: 300; border-radius: 2px;">
                        <option value="">Selecciona una provincia</option>
                        <option value="San José">San José</option>
                        <option value="Alajuela">Alajuela</option>
                        <option value="Cartago">Cartago</option>
                        <option value="Heredia">Heredia</option>
                        <option value="Guanacaste">Guanacaste</option>
                        <option value="Puntarenas">Puntarenas</option>
                        <option value="Limón">Limón</option>
                    </select>
                </div>

                <div class="input-group">
                    <label style="display: block; font-size: 11px; letter-spacing: 2px; color: #999; text-transform: uppercase; margin-bottom: 15px;">Método de Entrega</label>
                    <div style="display: flex; gap: 20px;">
                        <label style="flex: 1; display: flex; align-items: center; gap: 10px; background: #fbfbfb; border: 1px solid #e0e0e0; padding: 20px; cursor: pointer; border-radius: 2px; font-size: 14px; font-weight: 300;">
                            <input type="radio" name="metodo" value="Envío a domicilio" checked style="accent-color: #000;"> Envío Express
                        </label>
                        <label style="flex: 1; display: flex; align-items: center; gap: 10px; background: #fbfbfb; border: 1px solid #e0e0e0; padding: 20px; cursor: pointer; border-radius: 2px; font-size: 14px; font-weight: 300;">
                            <input type="radio" name="metodo" value="Retiro en Boutique" style="accent-color: #000;"> Retiro en Boutique
                        </label>
                    </div>
                </div>

                <div class="input-group">
                    <label style="display: block; font-size: 11px; letter-spacing: 2px; color: #999; text-transform: uppercase; margin-bottom: 10px;">Dirección Exacta</label>
                    <textarea id="direccion" required
                              style="width: 100%; background: transparent; border: none; border-bottom: 1px solid #e0e0e0; color: #1a1a1a; padding: 12px 0; outline: none; height: 80px; font-family: inherit; font-size: 14px; font-weight: 300; resize: none;"></textarea>
                </div>

                <div style="margin-top: 30px;">
                    <button type="submit" 
                            style="background: #000; color: #fff; border: none; padding: 20px 50px; cursor: pointer; font-weight: 400; text-transform: uppercase; letter-spacing: 4px; font-size: 12px; width: 100%; border-radius: 2px; transition: 0.3s; border: 1px solid #000;">
                        Confirmar y Contactar Boutique
                    </button>
                </div>
            </form>
        </div>

        <div style="flex: 1; min-width: 300px; border-left: 1px solid #eee; padding-left: 60px;">
            <h2 style="font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 400; letter-spacing: 1px; margin-bottom: 40px; color: #333;">Resumen de Piezas</h2>
            
            <div style="max-height: 400px; overflow-y: auto; padding-right: 10px;">
                <?php foreach ($_SESSION['carrito'] as $item): ?>
                    <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 25px; border-bottom: 1px solid #f9f9f9; padding-bottom: 15px;">
                        <div style="width: 70px; height: 90px; background: #fbfbfb; border: 1px solid #eee; overflow: hidden; border-radius: 2px;">
                            <img src="assets/img/productos/<?= $item['imagen'] ?: 'placeholder.jpg' ?>"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div>
                            <p style="font-family: 'Playfair Display', serif; font-size: 1.1rem; margin: 0 0 5px 0; color: #1a1a1a;"><?= strtoupper($item['nombre']) ?></p>
                            <p style="font-size: 14px; color: #000; font-weight: 300;">₡<?= number_format($item['precio'], 0) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 30px; border-top: 1px solid #eee; padding-top: 30px; display: flex; flex-direction: column; gap: 15px; font-size: 14px; font-weight: 300; color: #666;">
                <p>Subtotal <span style="float: right; color: #000;">₡<?= number_format($subtotal, 0) ?></span></p>
                <p>Envío Estimado <span style="float: right; color: #000;">₡<?= number_format($envio_costo, 0) ?></span></p>
                <div style="height: 1px; background: #eee; margin: 10px 0;"></div>
                <h3 style="font-family: 'Playfair Display', serif; font-size: 1.8rem; font-weight: 400; color: #1a1a1a; margin: 0;">Total Estimado <span style="float: right; color: #000;">₡<?= number_format($total_estimado, 0) ?></span></h3>
            </div>
        </div>
    </div>
</div>

<script>
function enviarAWhatsApp(event) {
    event.preventDefault();

    const nombre = document.getElementById('nombre_cliente').value;
    const provincia = document.getElementById('provincia').value;
    const metodo = document.querySelector('input[name="metodo"]:checked').value;
    const direccion = document.getElementById('direccion').value;

    // Mensaje con tono sofisticado
    let mensaje = `*ZOMAI BOUTIQUE | SOLICITUD DE RESERVA*%0A`;
    mensaje += `%0A__________________________________________%0A%0A`;
    mensaje += `*DETALLES DEL CLIENTE*%0A`;
    mensaje += `*Nombre:* ${nombre}%0A`;
    mensaje += `*Ubicación:* ${provincia}, Costa Rica%0A`;
    mensaje += `*Entrega:* ${metodo}%0A`;
    mensaje += `*Dirección:* ${direccion}%0A%0A`;
    
    mensaje += `*DETALLES DEL PEDIDO*%0A`;
    mensaje += `<?= $resumen_prendas_ws ?>`;
    
    mensaje += `%0A*TOTAL:* ₡<?= number_format($total_estimado, 0) ?>%0A`;
    mensaje += `%0A_________________________________________%0A%0A`;
    
    // Mensajito final delicado
    mensaje += `Quedo a la espera de su confirmación para proceder con el envío de mis prendas. Muchas gracias.`;

    const telefono = "50689178313";
    const url = `https://wa.me/${telefono}?text=${mensaje}`;

    window.open(url, '_blank');
    
    window.location.href = 'index.php?modulo=FinalizarCompra&finalizado=true';
}
</script>