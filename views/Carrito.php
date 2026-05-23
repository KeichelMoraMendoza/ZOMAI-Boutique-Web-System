<div style="max-width: 900px; margin: 60px auto; padding: 40px; font-family: 'Poppins';">
    <h1 style="font-family: 'Playfair Display'; text-align: center; font-weight: 400; letter-spacing: 3px;">Tu Selección</h1>
    <div style="height: 1px; background: #eee; width: 60px; margin: 20px auto 50px;"></div>

    <?php if(!empty($_SESSION['carrito'])): ?>
        <table style="width: 100%; border-collapse: collapse; font-weight: 300;">
            <?php $subtotal = 0; ?>
            <?php foreach($_SESSION['carrito'] as $id => $item): ?>
                <?php $subtotal += ($item['precio'] * $item['cantidad']); ?>
                <tr style="border-bottom: 1px solid #f5f5f5;">
                    <td style="padding: 20px 0; width: 100px;">
                        <img src="assets/img/productos/<?= $item['imagen'] ?>" style="width: 80px; height: 100px; object-fit: cover;">
                    </td>
                    <td style="padding: 20px;">
                        <p style="margin: 0; letter-spacing: 1px;"><?= strtoupper($item['nombre']) ?></p>
                        <a href="index.php?eliminar_carrito=<?= $id ?>" style="font-size: 10px; color: #999; text-decoration: none;">Eliminar</a>
                    </td>
                    <td style="text-align: right; font-size: 14px;">₡<?= number_format($item['precio'], 0) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div style="text-align: right; margin-top: 40px;">
            <p style="color: #999; font-size: 12px; letter-spacing: 2px;">SUBTOTAL</p>
            <h2 style="font-family: 'Playfair Display'; font-weight: 400;">₡<?= number_format($subtotal, 0) ?></h2>
            <br>
            <a href="index.php?modulo=FinalizarCompra" class="btn-negro" style="padding: 20px 60px;">PROCEDER AL CHECKOUT</a>
        </div>
    <?php else: ?>
        <p style="text-align: center; color: #ccc; letter-spacing: 2px; margin: 100px 0;">Tu selección está vacía.</p>
        <div style="text-align: center;"><a href="index.php" class="btn-negro">VOLVER A LA COLECCIÓN</a></div>
    <?php endif; ?>
</div>