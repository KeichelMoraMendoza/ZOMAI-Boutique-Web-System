<?php

// Obtenemos los datos necesarios

$listaProductos = $prodCtrl->obtenerTodos();

$listaClientes = $clieCtrl->obtenerTodos();

$listaPreordenes = $preordenCtrl->obtenerTodas();

?>



<div class="gestion-container" style="max-width: 1000px; margin: 0 auto; padding: 20px;">

   

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 20px;">

        <h2 style="font-weight: 200; letter-spacing: 5px; text-transform: uppercase; margin: 0;">

            Preórdenes

        </h2>

        <button onclick="document.getElementById('form-preorden').classList.toggle('hidden')"

                style="background: #000; color: #fff; border: none; padding: 12px 25px; letter-spacing: 2px; cursor: pointer; font-size: 11px; text-transform: uppercase;">

            + Nueva Preorden

        </button>

    </div>



    <div id="form-preorden" class="hidden" style="background: #fafafa; padding: 40px; border: 1px solid #eee; margin-bottom: 60px;">

        <h3 style="text-align: center; font-weight: 200; letter-spacing: 3px; margin-bottom: 30px; text-transform: uppercase; font-size: 14px;">

            Registrar Pedido Especial

        </h3>



        <form method="POST" action="index.php?modulo=preordenes">

            <div style="display: flex; gap: 40px; flex-wrap: wrap; margin-bottom: 30px;">

                <div style="flex: 1; min-width: 280px;">

                    <label style="font-size: 10px; letter-spacing: 2px; color: #999; display: block; margin-bottom: 10px;">SELECCIONAR CLIENTA</label>

                    <select name="cliente_id" required

                            style="width: 100%; padding: 12px; border: none; border-bottom: 1px solid #000; background: transparent; outline: none; font-family: 'Poppins';">

                        <option value="">-- Elija una clienta --</option>

                        <?php

                        $listaClientes->data_seek(0);

                        while($c = $listaClientes->fetch_assoc()):

                        ?>

                            <option value="<?= $c['id'] ?>"><?= strtoupper($c['nombre']) ?></option>

                        <?php endwhile; ?>

                    </select>

                </div>

               

                <div style="flex: 1; min-width: 280px;">

                    <label style="font-size: 10px; letter-spacing: 2px; color: #999; display: block; margin-bottom: 10px;">SELECCIONAR PRENDA</label>

                    <select name="producto_id" required

                            style="width: 100%; padding: 12px; border: none; border-bottom: 1px solid #000; background: transparent; outline: none; font-family: 'Poppins';">

                        <option value="">-- Elija el producto --</option>

                        <?php

                        $listaProductos->data_seek(0);

                        while($p = $listaProductos->fetch_assoc()):

                        ?>

                            <option value="<?= $p['id'] ?>" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>

                                <?= strtoupper($p['nombre']) ?> (₡<?= number_format($p['precio'], 0) ?>)

                                <?= $p['stock'] <= 0 ? '- AGOTADO' : '- DISPONIBLE: '.$p['stock'] ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

            </div>

            <div style="text-align: center;">

                <button type="submit" name="guardar_preorden"

                        style="background: #000; color: #fff; border: none; padding: 15px 40px; letter-spacing: 3px; cursor: pointer; font-size: 11px; text-transform: uppercase;">

                    Confirmar Preorden

                </button>

            </div>

        </form>

    </div>



    <h3 style="font-weight: 300; letter-spacing: 3px; border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px; font-size: 14px;">PEDIDOS PENDIENTES</h3>

   

    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">

        <thead>

            <tr style="text-align: left; border-bottom: 2px solid #000; text-transform: uppercase; font-size: 10px; letter-spacing: 2px;">

                <th style="padding: 15px 5px;">Ref</th>

                <th>Clienta</th>

                <th>Resumen del Pedido (Prendas)</th>

                <th>Fecha y Hora</th>

                <th style="text-align: center;">Acción</th>

            </tr>

        </thead>

        <tbody>

            <?php while($pre = $listaPreordenes->fetch_assoc()): ?>

            <tr style="border-bottom: 1px solid #eee;">

                <td style="padding: 20px 5px; color: #ccc;">#<?= $pre['id'] ?? '---' ?></td>

                <td>

                    <strong style="letter-spacing: 1px;"><?= strtoupper($pre['cliente']) ?></strong>

                </td>

                <td style="padding: 15px 0;">

                    <div style="color: #444; line-height: 1.6;">

                        <?= str_replace(',', '<br>•', '• ' . strtoupper($pre['productos_lista'])) ?>

                    </div>

                    <div style="font-size: 10px; color: #bbb; margin-top: 5px; text-transform: uppercase;">

                        Total: <?= $pre['total_prendas'] ?> unidades

                    </div>

                </td>

                <td style="color: #999; font-size: 11px;">

                    <?= date('d/m/Y', strtotime($pre['fecha'])) ?> <span style="margin-left: 5px; color: #ddd;">|</span> <?= date('H:i', strtotime($pre['fecha'])) ?>

                </td>

                <td style="text-align: center;">

                    <a href="index.php?modulo=preordenes&eliminar=<?= $pre['id'] ?>"

                    style="text-decoration: none; color: #d00; border: 1px solid #d00; padding: 6px 15px; font-size: 10px; letter-spacing: 1px; text-transform: uppercase;"

                    onclick="return confirm('¿Deseas quitar este registro del historial?')">

                    Quitar

                    </a>

                </td>

            </tr>

            <?php endwhile; ?>

        </tbody>

    </table>

</div>



<style>

    .hidden { display: none; }

    select option:disabled {

        color: #ccc;

        background: #f9f9f9;

    }

</style>