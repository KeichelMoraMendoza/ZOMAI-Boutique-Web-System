<?php
require_once __DIR__ . '/../config/conexion.php';

class ProductoController {

    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // --- ESTA ES LA FUNCIÓN QUE DEBES USAR EN EL INDEX ---
    public function guardar($datos) {
        // Limpiamos los datos
        $nombre = mysqli_real_escape_string($this->conn, $datos['nombre']);
        // Ojo aquí: revisa si en tu HTML el name es 'description' o 'descripcion'
        $descripcion = mysqli_real_escape_string($this->conn, $datos['description'] ?? $datos['descripcion']);
        $precio = (float)$datos['precio'];
        $stock = (int)$datos['stock'];
        $categoria_id = (int)$datos['categoria_id'];
        $imagen = mysqli_real_escape_string($this->conn, $datos['imagen']); 

        // Verificamos si viene un ID para saber si es ACTUALIZAR o CREAR
        if (!empty($datos['id'])) {
            $id = (int)$datos['id'];
            $sql = "UPDATE productos SET 
                    nombre = '$nombre', 
                    descripcion = '$descripcion', 
                    precio = $precio, 
                    categoria_id = $categoria_id,
                    stock = $stock,
                    imagen = '$imagen' 
                    WHERE id = $id";
        } else {
            // Si no hay ID, es un producto NUEVO
            $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen, categoria_id, stock)
                    VALUES ('$nombre', '$descripcion', $precio, '$imagen', $categoria_id, $stock)";
        }
        
        return $this->conn->query($sql);
    }

    // 2. READ (Obtener todos)
    public function obtenerTodos() {
        $sql = "SELECT productos.*, categorias.nombre AS categoria
                FROM productos
                INNER JOIN categorias ON productos.categoria_id = categorias.id
                ORDER BY productos.id DESC";
        return $this->conn->query($sql);
    }

    public function obtenerPorId($id) {
        $id = (int)$id;
        $resultado = $this->conn->query("SELECT * FROM productos WHERE id = $id");
        return $resultado->fetch_assoc();
    }

    // 4. DELETE (Eliminar)
    public function eliminar($id) {
        $id = (int)$id;
        return $this->conn->query("DELETE FROM productos WHERE id = $id");
    }

    public function obtenerCategorias() {
        return $this->conn->query("SELECT * FROM categorias ORDER BY nombre ASC");
    }

    public function buscarProductos($categoria = '', $busqueda = '') {
        $sql = "SELECT productos.*, categorias.nombre AS cat_nombre 
                FROM productos 
                INNER JOIN categorias ON productos.categoria_id = categorias.id 
                WHERE 1=1"; // Quitamos la restricción estricta de stock para el admin

        if ($categoria != '') {
            $categoria = mysqli_real_escape_string($this->conn, $categoria);
            $sql .= " AND categorias.nombre = '$categoria'";
        }

        if ($busqueda != '') {
            $busqueda = mysqli_real_escape_string($this->conn, $busqueda);
            $sql .= " AND (productos.nombre LIKE '%$busqueda%' OR productos.descripcion LIKE '%$busqueda%')";
        }

        $sql .= " ORDER BY productos.id DESC";
        return $this->conn->query($sql);
    }
}
?>