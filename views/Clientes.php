<?php
// Solo necesitamos la lista de clientes. 
// La variable $clienteCtrl viene definida desde el index.php
$listaClientes = $clienteCtrl->obtenerTodos();

$editandoCliente = false;
$cliente_edit = null;

if (isset($_GET['accion']) && $_GET['accion'] == 'editar' && isset($_GET['id'])) {
    $editandoCliente = true;
    $cliente_edit = $clienteCtrl->obtenerPorId($_GET['id']);
}
?>

<h2><?= $editandoCliente ? "Editar Datos de: " . $cliente_edit['nombre'] : "Registrar Nuevo Cliente" ?></h2>

<form method="POST" action="index.php?modulo=clientes<?= $editandoCliente ? '&id='.$cliente_edit['id'] : '' ?>">
    <label>Nombre Completo:</label>
    <input type="text" name="nombre" value="<?= $editandoCliente ? $cliente_edit['nombre'] : '' ?>" required>

    <label>Correo Electrónico:</label>
    <input type="email" name="correo" value="<?= $editandoCliente ? $cliente_edit['correo'] : '' ?>" required>

    <label>Teléfono:</label>
    <input type="text" name="telefono" value="<?= $editandoCliente ? $cliente_edit['telefono'] : '' ?>" required>

    <br><br>
    <button type="submit" name="<?= $editandoCliente ? 'actualizar_cliente' : 'guardar_cliente' ?>" class="btn-guardar">
        <?= $editandoCliente ? 'Actualizar Datos' : 'Registrar Cliente' ?>
    </button>
    
    <?php if ($editandoCliente): ?>
        <a href="index.php?modulo=clientes" style="margin-left:15px; color: #999; text-decoration: none;">Cancelar</a>
    <?php endif; ?>
</form>

<hr>

<h2>Base de Datos de Clientes</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php while($c = $listaClientes->fetch_assoc()): ?>
        <tr>
            <td><?= $c['id'] ?></td>
            <td><strong><?= $c['nombre'] ?></strong></td>
            <td><?= $c['correo'] ?></td>
            <td><?= $c['telefono'] ?></td>
            <td>
                <a href="index.php?modulo=clientes&accion=editar&id=<?= $c['id'] ?>" class="btn-editar">Editar</a>
                <a href="index.php?modulo=clientes&eliminar=<?= $c['id'] ?>" 
                   class="btn-eliminar" 
                   onclick="return confirm('¿Borrar a este cliente?')">
                   Eliminar
                </a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>