<div style="max-width: 900px; margin: 40px auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; border-bottom: 1px solid #eee; padding-bottom: 20px;">
        <h2 style="font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 400; margin: 0;">Categorías</h2>
        <button onclick="document.getElementById('form-categoria').classList.toggle('hidden')" class="btn-negro" style="padding: 12px 25px;">+ Nueva Categoría</button>
    </div>

    <div id="form-categoria" class="hidden" style="background: #fafafa; padding: 30px; border: 1px solid #eee; margin-bottom: 40px;">
        <form method="POST" action="index.php?modulo=categorias" style="display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: end;">
            <div>
                <label style="font-size: 10px; letter-spacing: 2px; color: #999; text-transform: uppercase; display: block; margin-bottom: 10px;">Nombre de la Categoría</label>
                <input type="text" name="nombre_cat" required placeholder="Ej. VESTIDOS" 
                       style="width: 100%; padding: 12px; border: none; border-bottom: 1px solid #000; background: transparent; outline: none; font-family: 'Poppins';">
            </div>
            <button type="submit" name="guardar_categoria" class="btn-negro">Guardar</button>
        </form>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead>
            <tr style="border-bottom: 1px solid #000; text-align: left;">
                <th style="padding: 15px 10px; font-weight: 500; letter-spacing: 2px;">ID</th>
                <th style="padding: 15px 10px; font-weight: 500; letter-spacing: 2px;">NOMBRE</th>
                <th style="padding: 15px 10px; font-weight: 500; letter-spacing: 2px; text-align: right;">ACCIONES</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $cats = $controller->obtenerCategorias(); 
            while($c = $cats->fetch_assoc()): 
            ?>
            <tr style="border-bottom: 1px solid #f2f2f2; transition: 0.3s;">
                <td style="padding: 20px 10px; color: #bbb;">#<?php echo $c['id']; ?></td>
                <td style="padding: 20px 10px; letter-spacing: 1px;"><?php echo strtoupper($c['nombre']); ?></td>
                <td style="padding: 20px 10px; text-align: right;">
                    <a href="index.php?modulo=categorias&eliminar=<?php echo $c['id']; ?>" 
                       style="color: #c00; text-decoration: none; font-size: 10px; letter-spacing: 1px; border: 1px solid #c00; padding: 5px 12px; border-radius: 2px;"
                       onclick="return confirm('¿Eliminar esta categoría?')">ELIMINAR</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>