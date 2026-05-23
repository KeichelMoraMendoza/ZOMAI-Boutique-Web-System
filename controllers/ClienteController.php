<?php
require_once __DIR__ . '/../config/conexion.php';

class ClienteController {

    // Cambiamos el nombre a 'guardar' para que coincida con FinalizarCompra.php
    // Y permitimos que reciba un array para ser más flexible
    public function guardar($datos) {
        global $conn;
        
        // Extraemos los datos del array
        $nombre = $datos['nombre'];
        $correo = $datos['correo'] ?? '';
        $telefono = $datos['telefono'] ?? '';

        $sql = "INSERT INTO clientes (nombre, correo, telefono)
                VALUES ('$nombre', '$correo', '$telefono')";
        
        if ($conn->query($sql)) {
            // ¡IMPORTANTE!: Retornamos el ID autogenerado para la preorden
            return $conn->insert_id; 
        }
        return false;
    }

    // Mantenemos tus otros métodos para el panel administrativo
    public function obtenerTodos() {
        global $conn;
        return $conn->query("SELECT * FROM clientes ORDER BY id DESC");
    }

    public function obtenerPorId($id) {
        global $conn;
        $resultado = $conn->query("SELECT * FROM clientes WHERE id = '$id'");
        return $resultado->fetch_assoc();
    }

    public function actualizar($id, $nombre, $correo, $telefono) {
        global $conn;
        $sql = "UPDATE clientes SET 
                nombre = '$nombre', 
                correo = '$correo', 
                telefono = '$telefono' 
                WHERE id = '$id'";
        return $conn->query($sql);
    }

    public function eliminar($id) {
        global $conn;
        return $conn->query("DELETE FROM clientes WHERE id = '$id'");
    }
}
?>