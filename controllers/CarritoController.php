<?php
class CarritoController {
    public function agregar($id, $producto) {
        if(!isset($_SESSION['carrito'])) { $_SESSION['carrito'] = []; }
        
        // Si ya está, solo sumamos (opcional en boutique, usualmente es 1 sola pieza)
        if(isset($_SESSION['carrito'][$id])) {
            $_SESSION['carrito'][$id]['cantidad']++;
        } else {
            $_SESSION['carrito'][$id] = [
                'nombre' => $producto['nombre'],
                'precio' => $producto['precio'],
                'imagen' => $producto['imagen'],
                'cantidad' => 1
            ];
        }
    }

    public function eliminar($id) {
        unset($_SESSION['carrito'][$id]);
    }
}