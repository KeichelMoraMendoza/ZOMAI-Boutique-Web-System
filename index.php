
<?php
ob_start(); 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'config/conexion.php';
require_once 'controllers/ProductoController.php';
require_once 'controllers/CarritoController.php';

// 1. FUNCIONALIDAD DEL VIEJO: Instancia de controladores
$controller = new ProductoController($conn);
$carritoCtrl = new CarritoController();

// 2. FUNCIONALIDAD DEL VIEJO: Lógica del carrito
if(isset($_GET['agregar_carrito'])) {
    $item = $controller->obtenerPorId($_GET['agregar_carrito']);
    if ($item) {
        $carritoCtrl->agregar($_GET['agregar_carrito'], $item);
    }
    header("Location: index.php?modulo=carrito");
    exit();
}

if(isset($_GET['eliminar_carrito'])) {
    $carritoCtrl->eliminar($_GET['eliminar_carrito']);
    header("Location: index.php?modulo=carrito");
    exit();
}

// 3. FUNCIONALIDAD DEL VIEJO: Lógica de Login/Logout
$error_login = "";
if (isset($_POST['ingresar'])) {
    $user = trim(mysqli_real_escape_string($conn, $_POST['usuario']));
    $pass = trim($_POST['password']);
    $sql = "SELECT * FROM usuarios WHERE usuario = '$user'";
    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if ($pass == $row['password'] || password_verify($pass, $row['password'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_user'] = $row['usuario'];
            header("Location: index.php?modulo=admin_panel");
            exit();
        } else { $error_login = "Contraseña incorrecta"; }
    } else { $error_login = "Usuario no encontrado"; }
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: index.php");
    exit();
}

$modulo = $_GET['modulo'] ?? 'inicio';
$categoria_get = $_GET['cat'] ?? '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ZOMAI | Boutique</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    <style>
        /* --- ESTÉTICA DEL NUEVO --- */
        body { margin: 0; font-family: 'Poppins', sans-serif; background: #fff; overflow-x: hidden; color: #1a1a1a; }
        .hidden { display: none; }
.logo {
    text-align: center;
    /* --- IMPONENCIA --- */
    font-size: 5.5rem; /* Un tamaño majestuoso que domina la vista */
    letter-spacing: 25px; /* Espaciado extra ancho para un lujo etéreo */
    margin: 0; /* Quitamos el margen para usar padding */
    padding: 100px 0; /* Mucho aire arriba y abajo para que respire */
    
    /* --- IDENTIDAD --- */
    font-family: 'Playfair Display', serif;
    font-weight: 200; /* Fino y elegante, para que el tamaño no canse */
    text-transform: uppercase;
    color: #1a1a1a; /* Un negro muy suave, no puro */
    
    /* --- RELIEVE --- */
    text-shadow: 1px 1px 1px #fff, /* Brillo blanco justo detrás */
                 3px 3px 5px #f4f1ea; /* Sombra beige lino suave que da relieve */
                 
    /* --- FONDO SUTIL (OPCIONAL pero recomendado) --- */
    background: linear-gradient(to bottom, #fcfcfc 0%, #fff 100%); /* Fondo casi blanco que le da una textura suave */
    border-bottom: 1px solid #f9f9f9; /* Línea de base ultra fina */
} 
        .hamburguesa { position: fixed; top: 45px; left: 45px; width: 30px; height: 18px; display: flex; flex-direction: column; justify-content: space-between; cursor: pointer; z-index: 1300; }
        .hamburguesa span { width: 100%; height: 1px; background: #000; transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1); }

        .sidebar { position: fixed; top: 0; left: -320px; width: 280px; height: 100%; background: #fff; z-index: 1200; transition: 0.5s cubic-bezier(0.7, 0, 0.3, 1); border-right: 1px solid #f9f9f9; padding: 120px 40px; box-shadow: 15px 0 40px rgba(0,0,0,0.02); }
        #menu-check:checked ~ .sidebar { left: 0; }
        #menu-check:checked ~ .hamburguesa span:nth-child(1) { transform: translateY(8.5px) rotate(45deg); }
        #menu-check:checked ~ .hamburguesa span:nth-child(2) { opacity: 0; }
        #menu-check:checked ~ .hamburguesa span:nth-child(3) { transform: translateY(-8.5px) rotate(-45deg); }

        .nav-link { display: block; text-decoration: none; color: #1a1a1a; font-size: 11px; letter-spacing: 4px; margin-bottom: 30px; text-transform: uppercase; font-weight: 300; transition: 0.3s; }
        .nav-link:hover { color: #888; padding-left: 5px; }

        .contenedor-horizontal { display: flex; flex-wrap: nowrap; overflow-x: auto; gap: 25px; padding: 40px 20px; scrollbar-width: none; }
        .contenedor-horizontal::-webkit-scrollbar { display: none; }
        .card-cat { flex: 0 0 auto; width: 280px; transition: 0.4s; text-decoration: none; }
        .img-cat { width: 100%; height: 400px; background-size: cover; background-position: center; border-radius: 2px; filter: grayscale(20%); transition: 0.5s; }
        .card-cat:hover .img-cat { filter: grayscale(0%); transform: scale(1.02); }
        
        .admin-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; max-width: 1000px; margin: 40px auto; }
        .admin-card { background: #fff; border: 1px solid #eee; padding: 60px 20px; text-align: center; text-decoration: none; color: #1a1a1a; transition: 0.4s; display: flex; flex-direction: column; align-items: center; }
        .admin-card:hover { transform: translateY(-5px); border-color: #000; }
        
        .btn-negro { background: #000; color: #fff; border: 1px solid #000; padding: 18px 35px; letter-spacing: 3px; cursor: pointer; text-decoration: none; font-size: 10px; display: inline-block; text-transform: uppercase; transition: 0.3s; text-align: center; }
        .btn-negro:hover { background: #fff; color: #000; }

        .btn-volver { display: inline-flex; align-items: center; text-decoration: none; color: #bbb; font-size: 10px; letter-spacing: 3px; text-transform: uppercase; transition: 0.3s; margin-bottom: 20px; }
        .btn-volver:hover { color: #000; transform: translateX(-5px); }
    </style>
</head>
<body>

    <input type="checkbox" id="menu-check" class="hidden">
    <label for="menu-check" class="hamburguesa"><span></span><span></span><span></span></label>

    <div class="sidebar">
        <a href="index.php" class="nav-link" style="font-weight: 500;">INICIO</a>
        <p style="font-size: 10px; color: #ccc; letter-spacing: 2px; margin: 20px 0 15px 0; text-transform: uppercase;">Colecciones</p>
        <?php
            $menuCategorias = $controller->obtenerCategorias();
            while($mCat = $menuCategorias->fetch_assoc()):
        ?>
            <a href="index.php?modulo=catalogo&cat=<?php echo urlencode($mCat['nombre']); ?>" class="nav-link" style="font-size: 10px; margin-bottom: 15px;">
                <?php echo strtoupper($mCat['nombre']); ?>
            </a>
        <?php endwhile; ?>

        <hr style="border:0; border-top:1px solid #eee; margin: 25px 0;">
        <a href="index.php?modulo=carrito" class="nav-link" style="font-weight: 500;">MI SELECCIÓN</a>
        
        <?php if (!isset($_SESSION['admin_id'])): ?>
            <a href="index.php?modulo=login" class="nav-link" style="font-weight: 500;">INICIAR SESIÓN</a>
        <?php else: ?>
            <p style="font-size: 10px; color: #ccc; letter-spacing: 2px; margin-top: 30px; margin-bottom: 20px;">ADMINISTRACIÓN</p>
            <a href="index.php?modulo=admin_panel" class="nav-link" style="font-weight: 500;">PANEL DE GESTIÓN</a>
            <a href="index.php?action=logout" class="nav-link" style="color: #c00 !important; font-weight: 600;">CERRAR SESIÓN</a>
        <?php endif; ?>
    </div>

    <h1 class="logo">ZOMAI</h1>

    <div class="container" style="padding: 0 40px;">
        <?php if ($modulo != 'inicio'): ?>
            <div style="max-width: 1000px; margin: 0 auto; padding: 10px 0;">
                <a href="javascript:history.back()" class="btn-volver"><span>←</span> VOLVER</a>
            </div>
        <?php endif; ?>

        <?php if ($modulo == 'inicio'): ?>
            <?php $busqueda = $_GET['buscar'] ?? ''; ?>
            <div style="text-align: center; margin: 20px 0 50px 0;">
                <form action="index.php" method="GET" style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                    <input type="hidden" name="modulo" value="inicio">
                    <input type="text" name="buscar" placeholder="¿QUÉ ESTÁS BUSCANDO?" value="<?= $busqueda ?>"
                           style="padding: 12px; border: none; border-bottom: 1px solid #000; width: 350px; outline: none; font-family: 'Poppins'; font-size: 11px; letter-spacing: 3px; text-align: center;">
                    <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 18px;">🔍</button>
                </form>
            </div>

            <h2 style="text-align:center; font-weight:300; color:#888; letter-spacing:3px; text-transform:uppercase; font-size:14px;">Nuestras Categorías</h2>
            <div class="contenedor-horizontal">
                <?php
                $categoriasDB = $controller->obtenerCategorias();
                $img_mapeo = [
                    'TOPS Y BLUSAS' => 'cat-tops-blusas.jpg', 'PANTALONES Y FALDAS' => 'cat-pantalones-faldas.jpg',
                    'VESTIDOS' => 'cat-vestidos.jpg', 'CHAQUETAS Y BLAZERS' => 'cat-chaquetas-blazers.jpg',
                    'ACCESORIOS' => 'cat-accesorios.jpg', 'PRENDAS DE PUNTO' => 'cat-prendas-de-punto.jpg'
                ];
                while($cat = $categoriasDB->fetch_assoc()):
                    $imagen_cat = $img_mapeo[strtoupper($cat['nombre'])] ?? 'cat-default.jpg';
                ?>
                    <a href="index.php?modulo=catalogo&cat=<?php echo urlencode($cat['nombre']); ?>" class="card-cat">
                        <div class="img-cat" style="background-color:#f4f4f4; background-image: url('assets/img/<?php echo $imagen_cat; ?>');"></div>
                    </a>
                <?php endwhile; ?>
            </div>

            <hr style="border:0; border-top:1px solid #eee; margin: 60px 10%;">

            <?php $productos = $controller->buscarProductos('', $busqueda); ?>
            <div style="text-align: center; margin-bottom: 50px;">
                <h2 style="letter-spacing: 8px; font-weight: 200; text-transform: uppercase;">Colección Completa</h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 40px; padding-bottom: 60px;">
                <?php while($p = $productos->fetch_assoc()): ?>
                    <div style="text-align: center;">
                        <div style="height: 400px; background: #f9f9f9; margin-bottom: 15px; overflow: hidden; border-radius: 2px;">
                            <img src="assets/img/productos/<?= $p['imagen'] ?: 'placeholder.jpg' ?>" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <h4 style="margin: 5px 0; font-weight: 300; letter-spacing: 2px; font-size: 13px;"><?= strtoupper($p['nombre']) ?></h4>
                        <p style="color: #666; font-size: 13px; margin-bottom: 10px;">₡<?= number_format($p['precio'], 0) ?></p>
                        <a href="index.php?modulo=detalle&id=<?= $p['id'] ?>" style="font-size: 10px; color: #000; letter-spacing: 2px; text-decoration: none; border-bottom: 1px solid #000;">VER DETALLE</a>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php elseif ($modulo == 'catalogo'): ?>
            <?php 
                $cat_filtro = $_GET['cat'] ?? '';
                $productos = $controller->buscarProductos($cat_filtro, ''); 
            ?>
            <div style="text-align: center; margin: 40px 0;">
                <h2 style="letter-spacing: 5px; font-weight: 200; text-transform: uppercase;"><?= $cat_filtro ?></h2>
            </div>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px; padding-bottom: 60px;">
                <?php while($p = $productos->fetch_assoc()): ?>
                    <div style="text-align: center;">
                        <div style="height: 350px; background: #f9f9f9; margin-bottom: 15px; overflow: hidden;">
                            <img src="assets/img/productos/<?= $p['imagen'] ?>" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                        <h4 style="margin:0; font-weight:400;"><?= strtoupper($p['nombre']) ?></h4>
                        <p style="color:#999; font-size:12px;">₡<?= number_format($p['precio'], 0) ?></p>
                        <a href="index.php?modulo=detalle&id=<?= $p['id'] ?>" style="font-size:10px; color:#000; letter-spacing:2px; text-decoration:none; border-bottom:1px solid #000;">VER MÁS</a>
                    </div>
                <?php endwhile; ?>
            </div>

        <?php elseif ($modulo == 'detalle'): ?>
            <?php 
                // CORRECCIÓN: Se asume que obtenerPorId ya devuelve el array del producto.
                $p = $controller->obtenerPorId($_GET['id']); 
            ?>
            <div style="max-width: 1000px; margin: 60px auto; display: flex; gap: 60px; flex-wrap: wrap;">
                <div style="flex: 1.2; min-width: 350px;">
                    <img src="assets/img/productos/<?= $p['imagen'] ?>" style="width:100%; border: 1px solid #f0f0f0; padding: 10px;">
                </div>
                <div style="flex: 1; min-width: 300px;">
                    <span style="font-size: 10px; letter-spacing: 4px; color: #bbb; text-transform: uppercase;">Nueva Colección</span>
                    <h1 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; letter-spacing: 2px; margin: 10px 0; font-weight: 400;"><?= strtoupper($p['nombre']) ?></h1>
                    <p style="font-size: 20px; color: #000; margin-bottom: 30px;">₡<?= number_format($p['precio'], 0) ?></p>
                    <p style="line-height: 1.8; color: #666; font-size: 14px; margin-bottom: 40px; font-weight: 200; text-align: justify;"><?= $p['descripcion'] ?></p>
                    <div style="background: #fafafa; padding: 30px; border: 1px solid #eee;">
                        <p style="font-size: 11px; color: #999; margin-bottom: 20px;">STOCK: <?= $p['stock'] ?> UNIDADES</p>
                        <a href="index.php?agregar_carrito=<?= $p['id'] ?>" class="btn-negro" style="width: 100%; box-sizing: border-box;">Añadir a mi Selección</a>
                    </div>
                </div>
            </div>

        <?php elseif ($modulo == 'login'): ?>
            <div style="max-width: 350px; margin: 80px auto; text-align: center;">
                <h2 style="letter-spacing: 5px; font-weight: 200;">LOGIN</h2>
                <?php if($error_login) echo "<p style='color:red; font-size:12px;'>$error_login</p>"; ?>
                <form method="POST" action="index.php?modulo=login" style="display: flex; flex-direction: column; gap: 20px;">
                    <input type="text" name="usuario" placeholder="USUARIO" required style="padding:15px; border:none; border-bottom:1px solid #000; outline:none; text-align:center;">
                    <input type="password" name="password" placeholder="CONTRASEÑA" required style="padding:15px; border:none; border-bottom:1px solid #000; outline:none; text-align:center;">
                    <button type="submit" name="ingresar" class="btn-negro">ENTRAR</button>
                </form>
            </div>

        <?php elseif ($modulo == 'admin_panel'): ?>
            <?php if(!isset($_SESSION['admin_id'])) { header("Location: index.php?modulo=login"); exit(); } ?>
            <div style="text-align: center; margin-top: 50px;">
                <h2 style="font-family: 'Playfair Display', serif; font-size: 2.5rem; font-weight: 400;">Gestión Boutique</h2>
                <div style="height: 1px; background: #000; width: 40px; margin: 20px auto;"></div>
            </div>
            <div class="admin-grid">
                <a href="index.php?modulo=productos" class="admin-card"><h3>Productos</h3><p>Inventario</p></a>
                <a href="index.php?modulo=categorias" class="admin-card"><h3>Categorías</h3><p>Editar secciones</p></a>
                <a href="index.php?modulo=preorden" class="admin-card"><h3>Pre-Orden</h3><p>Ver reservas</p></a>
            </div>

        <?php elseif ($modulo == 'categorias'): ?>
            <?php 
            // Lógica para guardar
            if (isset($_POST['guardar_categoria'])) {
                $nombre = $_POST['nombre_cat'];
                // Aquí llamarías a una función en tu controlador, ej:
                // $controller->crearCategoria($nombre);
                header("Location: index.php?modulo=categorias");
                exit();
            }

            // Lógica para eliminar
            if (isset($_GET['eliminar'])) {
                // $controller->eliminarCategoria($_GET['eliminar']);
                header("Location: index.php?modulo=categorias");
                exit();
            }

            include 'views/categorias.php'; 
        ?>

        <?php elseif ($modulo == 'productos'): ?>
            <?php 
                // Manejo de ELIMINAR (Debe ir antes de cargar la lista)
                if (isset($_GET['eliminar'])) {
                    $controller->eliminar($_GET['eliminar']);
                    echo "<script>window.location.href='index.php?modulo=productos';</script>";
                    exit();
                }

                // Manejo de GUARDAR (Nuevo o Editar)
                if (isset($_POST['guardar'])) { 
                    $controller->guardar($_POST); 
                    echo "<script>window.location.href='index.php?modulo=productos';</script>";
                    exit(); 
                }

                // Manejo de EDITAR (Para llenar el formulario)
                $productoEditar = null;
                if (isset($_GET['editar'])) {
                    $productoEditar = $controller->obtenerPorId($_GET['editar']);
                }

                include 'views/producto.php'; 
            ?>

        <?php elseif ($modulo == 'preorden'): ?>
    <?php 
        // Definimos las variables que el archivo views/Preordenes.php necesita
        $prodCtrl = $controller; 
        $clieCtrl = new ClienteController(); // La instanciamos aquí mismo para asegurar que exista
        $preordenCtrl = new PreordenController();

        // Lógica de acciones
        if (isset($_POST['guardar_preorden'])) { 
            $preordenCtrl->guardar($_POST); 
            header("Location: index.php?modulo=preorden"); 
            exit(); 
        }
        if (isset($_GET['eliminar'])) { 
            $preordenCtrl->eliminar($_GET['eliminar']); 
            header("Location: index.php?modulo=preorden"); 
            exit(); 
        }

        include 'views/Preordenes.php'; 
    ?>

        <?php elseif ($modulo == 'carrito'): ?>
            <?php include 'views/Carrito.php'; ?>
        <?php elseif ($modulo == 'FinalizarCompra'): ?>
            <?php include 'views/FinalizarCompra.php'; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
ob_end_flush(); 
?>