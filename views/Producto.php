<?php
// Aseguramos que los datos estén disponibles
$listaCategorias = $controller->obtenerCategorias();
$listaProductos = $controller->obtenerTodos();

$editando = false;
$prod_edit = null;

// Cambiamos 'accion' por el parámetro que viene de tu tabla
if (isset($_GET['accion']) && $_GET['accion'] == 'editar' && isset($_GET['id'])) {
    $editando = true;
    $prod_edit = $controller->obtenerPorId($_GET['id']);
}
?>

<div class="gestion-container" style="max-width: 1000px; margin: 0 auto; padding: 20px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <h2 style="font-weight: 200; letter-spacing: 5px; text-transform: uppercase; margin: 0;">
            Inventario Actual
        </h2>
        <button onclick="document.getElementById('form-inventario').classList.toggle('hidden')" 
                style="background: #000; color: #fff; border: none; padding: 12px 25px; letter-spacing: 2px; cursor: pointer; font-size: 11px; text-transform: uppercase;">
            <?= $editando ? "Cerrar Edición" : "+ Nueva Prenda" ?>
        </button>
    </div>

    <div id="form-inventario" class="<?= $editando ? '' : 'hidden' ?>">
        <h3 style="text-align: center; font-weight: 200; letter-spacing: 3px; margin-bottom: 30px; text-transform: uppercase; font-size: 14px;">
            <?= $editando ? "Modificar Datos de la Prenda" : "Ingresar Nueva Prenda" ?>
        </h3>

        <form method="POST" action="index.php?modulo=productos" 
              style="background: #fafafa; padding: 40px; border: 1px solid #eee; margin-bottom: 60px;">
            
            <input type="hidden" name="id" value="<?= $editando ? $prod_edit['id'] : '' ?>">
            
            <div style="display: flex; gap: 40px; flex-wrap: wrap;">
                
                <div style="flex: 1; min-width: 280px;">
                    <label style="font-size: 10px; letter-spacing: 2px; color: #999;">NOMBRE DE LA PRENDA</label>
                    <input type="text" name="nombre" value="<?= $editando ? $prod_edit['nombre'] : '' ?>" required 
                           style="width: 100%; padding: 10px 0; border: none; border-bottom: 1px solid #000; background: transparent; margin-bottom: 25px; outline: none;">

                    <label style="font-size: 10px; letter-spacing: 2px; color: #999;">DESCRIPCIÓN</label>
                    <textarea name="description" rows="3" required 
                              style="width: 100%; padding: 10px; border: 1px solid #eee; background: #fff; margin-top: 10px; outline: none; font-family: inherit;"><?= $editando ? $prod_edit['descripcion'] : '' ?></textarea>
                    
                    <div style="margin-top: 25px;">
                        <label style="font-size: 10px; letter-spacing: 2px; color: #999;">NOMBRE DEL ARCHIVO DE IMAGEN (EJ: BLAZER.JPG)</label>
                        <input type="text" name="imagen" value="<?= $editando ? $prod_edit['imagen'] : '' ?>" 
                               placeholder="ejemplo.jpg"
                               style="width: 100%; padding: 10px 0; border: none; border-bottom: 1px solid #000; background: transparent; outline: none;">
                    </div>
                </div>
                
                <div style="flex: 1; min-width: 280px;">
                    <div style="display: flex; gap: 20px;">
                        <div style="flex: 1;">
                            <label style="font-size: 10px; letter-spacing: 2px; color: #999;">PRECIO (₡)</label>
                            <input type="number" name="precio" value="<?= $editando ? $prod_edit['precio'] : '' ?>" required 
                                   style="width: 100%; padding: 10px 0; border: none; border-bottom: 1px solid #000; background: transparent; margin-bottom: 25px; outline: none;">
                        </div> 
                        <div style="flex: 1;">
                            <label style="font-size: 10px; letter-spacing: 2px; color: #999;">STOCK</label>
                            <input type="number" name="stock" value="<?= $editando ? $prod_edit['stock'] : '0' ?>" min="0" required 
                                   style="width: 100%; padding: 10px 0; border: none; border-bottom: 1px solid #000; background: transparent; margin-bottom: 25px; outline: none;">
                        </div>
                    </div>

                    <label style="font-size: 10px; letter-spacing: 2px; color: #999;">CATEGORÍA</label>
                    <select name="categoria_id" required style="width: 100%; padding: 10px; border: 1px solid #eee; margin-top: 10px; background: #fff;">
                        <?php 
                        $listaCategorias->data_seek(0);
                        while($cat = $listaCategorias->fetch_assoc()): 
                        ?>
                            <option value="<?= $cat['id'] ?>" <?= ($editando && $cat['id'] == $prod_edit['categoria_id']) ? 'selected' : '' ?>>
                                <?= strtoupper($cat['nombre']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div style="text-align: center; margin-top: 40px;">
                <button type="submit" name="guardar" 
                        style="background: #000; color: #fff; border: none; padding: 15px 40px; letter-spacing: 3px; cursor: pointer; font-size: 11px; text-transform: uppercase;">
                    <?= $editando ? 'Actualizar Producto' : 'Guardar en Inventario' ?>
                </button>
                <?php if ($editando): ?>
                    <a href="index.php?modulo=productos" style="display: block; margin-top: 15px; color: #999; text-decoration: none; font-size: 10px; letter-spacing: 1px;">CANCELAR EDICIÓN</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr style="text-align: left; border-bottom: 2px solid #000; text-transform: uppercase; font-size: 10px; letter-spacing: 2px;">
                <th style="padding: 15px 5px;">ID</th>
                <th>Foto</th>
                <th>Prenda</th>
                <th>Precio</th>
                <th>Stock</th>
                <th>Categoría</th>
                <th style="text-align: center;">Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php while($prod = $listaProductos->fetch_assoc()): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding: 15px 5px; color: #ccc;"><?= $prod['id'] ?></td>
                <td>
                    <div style="width: 50px; height: 50px; background: #f4f4f4; overflow: hidden; border-radius: 4px;">
                        <img src="assets/img/productos/<?= $prod['imagen'] ?: 'placeholder.jpg' ?>" 
                             style="width: 100%; height: 100%; object-fit: cover;"
                             onerror="this.src='https://via.placeholder.com/50?text=S/I'">
                    </div>
                </td>
                <td>
                    <strong style="letter-spacing: 1px;"><?= strtoupper($prod['nombre']) ?></strong><br>
                    <small style="color: #999; font-weight: 200;"><?= $prod['descripcion'] ?></small>
                </td>
                <td style="font-weight: 400;">₡<?= number_format($prod['precio'], 0, '.', ',') ?></td>
                <td>
                    <span style="padding: 3px 8px; border-radius: 20px; <?= $prod['stock'] <= 3 ? 'background: #fff0f0; color: #d00; font-weight: bold;' : '' ?>">
                        <?= $prod['stock'] ?> uds.
                    </span>
                </td>
                <td style="color: #666; font-size: 11px;"><?= $prod['categoria'] ?></td>
                <td style="text-align: center;">
                    <a href="index.php?modulo=productos&accion=editar&id=<?= $prod['id'] ?>" 
                       style="text-decoration: none; color: #000; border: 1px solid #000; padding: 5px 10px; margin-right: 5px; font-size: 10px;">EDITAR</a>
                    
                    <a href="index.php?modulo=productos&eliminar=<?= $prod['id'] ?>" 
                       style="text-decoration: none; color: #d00; border: 1px solid #d00; padding: 5px 10px; font-size: 10px;"
                       onclick="return confirm('¿Borrar esta prenda?')">BORRAR</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<style>
    .hidden { display: none; }
</style>