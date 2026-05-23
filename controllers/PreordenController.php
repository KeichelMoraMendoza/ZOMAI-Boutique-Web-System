<?php
require_once __DIR__ . '/../config/conexion.php';

class PreordenController {

    // Ajustamos para recibir un array de datos, facilitando la integración con el checkout
    public function guardar($datos) {
        global $conn;
        
        // Extraemos los IDs que vienen del proceso automático
        // Nota: Asegúrate que los nombres coincidan con los de tu tabla (cliente_id vs id_cliente)
        $cliente_id = $datos['id_cliente'];
        $producto_id = $datos['id_producto'];
        
        $sql = "INSERT INTO preordenes (cliente_id, producto_id, fecha)
                VALUES ('$cliente_id', '$producto_id', NOW())";
        
        return $conn->query($sql);
    }

   public function obtenerTodas() {
    global $conn;
    // Usamos GROUP_CONCAT para juntar los nombres de productos en una sola fila
    $sql = "SELECT 
                MIN(preordenes.id) as id, 
                clientes.nombre AS cliente, 
                GROUP_CONCAT(productos.nombre SEPARATOR ', ') AS productos_lista, 
                COUNT(productos.id) as total_prendas,
                preordenes.fecha
            FROM preordenes
            INNER JOIN clientes ON preordenes.cliente_id = clientes.id
            INNER JOIN productos ON preordenes.producto_id = productos.id
            GROUP BY clientes.id, preordenes.fecha
            ORDER BY preordenes.fecha DESC";
    
    return $conn->query($sql);
}
}
?>