<?php

//Cargamos el fichero XML completo con todos los datos de los platos
$menu_xml = simplexml_load_file('menu.xml');

// Creamos los arrays que hacen de listas vacías para evitar los duplicados
// Aquí guardaremos los títulos de las secciones y los alérgenos para no repetirlos
$categorias = array(); 
$alergenos_usados = array(); 

// Revisamos plato por plato dentro del XML
foreach ($menu_xml->plato as $plato) {

    // Buscamos las categorías
    $tipo = (string) $plato['tipo']; // Leemos el atributo "tipo" del plato
    
    // Si esta categoría NO está en nuestro array, la guardamos
    // Así evitamos que "Para Picar" se repita por cada plato que haya
    if (!in_array($tipo, $categorias)) {
        $categorias[] = $tipo;
    }
    
    // --- B) BUSCAMOS LOS ALÉRGENOS ---
    // Primero comprobamos si el plato tiene la etiqueta de características
    if (isset($plato->caracteristicas->item)) {
        
        // Recorremos cada característica (item) del plato
        foreach ($plato->caracteristicas->item as $item) {
            
            // Limpiamos el texto para que todos sean iguales:
            // trim() quita espacios, strtolower() minúsculas, ucfirst() primera mayúscula.
            // Ejemplo: pasa de " vegano " a "Vegano".
            $alergeno = ucfirst(strtolower(trim((string) $item)));
            
            // Si el alérgeno limpio NO está en nuestra lista final, lo añadimos.
            // Así en la leyenda final solo salen los que realmente existen en la carta.
            if (!in_array($alergeno, $alergenos_usados)) {
                $alergenos_usados[] = $alergeno;
            }
        }
    }
}


    // Función "traductora": Recibe el nombre de un alérgeno/característica y nos devuelve su icono visual (HTML)
    function obtenerIcono($caracteristica) {
    
    // trim() elimina espacios al principio o al fina
    // strtolower() lo pasa todo a minúsculas
    // Así nos aseguramos de que cualquier string sea adeque de manera correcta
    $c = strtolower(trim($caracteristica));
    
    // Diccionario de iconos:
    // Comparamos la palabra limpia y devolvemos el código del icono de FontAwesome correspondiente.
    if ($c == 'vegano') return '<i class="fa-solid fa-leaf text-success"></i>';
    if ($c == 'picante') return '<i class="fa-solid fa-pepper-hot text-danger"></i>';
    if ($c == 'sin gluten') return '<i class="fa-solid fa-wheat-awn-circle-exclamation text-warning"></i>';
    if ($c == 'lacteo') return '<i class="fa-solid fa-cheese text-warning"></i>';
    if ($c == 'carne') return '<i class="fa-solid fa-drumstick-bite text-danger"></i>';
    if ($c == 'huevo') return '<i class="fa-solid fa-egg text-warning"></i>';
    if ($c == 'soja') return '<i class="fa-solid fa-seedling text-success"></i>';
    if ($c == 'frutos secos') return '<i class="fa-solid fa-cookie text-warning"></i>';
    
    //He utilizado una nomenclatura semántica para las clases CSS. Esto significa que los nombres elegidos describen que es el elemento y su función dentro de la carta, en lugar de describir su aspecto visual
     
    // Opción por defecto:
    // Si en el XML se añade una característica nueva que no está en la lista de arriba,
    // la función devolverá este icono genérico de unos cubiertos para que no hayan errores visuales.
    return '<i class="fa-solid fa-utensils text-secondary"></i>';
}
?>
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carta - Mayorito's Hot Dogs</title>
    
    <link rel="icon" type="image/x-icon" href="img/logo-empresa.ico"> <!-- Favicon del logo de empresa -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">  <!-- Boostrap, utilizamos el grid para garantizar que se adecue perfectamente tanto por el sistema de columnas tanto para el diseño Responsive -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">  <!-- Importamos la librería de Fontawesome para los iconos -->
    <link rel="stylesheet" href="styles.css?v=16">

</head>

<body id="inicio">

<!-- Cabecera principal: Combina utilidades de Bootstrap y clases semánticas propias para estructurar el logo y la descripción de forma responsive -->
    <header class="py-4 cabecera-restaurante shadow-lg">
        <div class="container cabecera-layout">
            <div class="logo-wrapper">
                <img src="img/logo-mayoritos.png" alt="Logo Mayorito's Hot Dogs & Diner" class="logo-principal">
            </div>
        <div class="texto-wrapper">
                <h1 class="titulo-cabecera mb-3">LA AUTÉNTICA EXPERIENCIA AMERICANA EN CADA BOCADO</h1>
                <p class="menu-descripcion m-0">
                    Descubre nuestros perritos calientes artesanos, elaborados con ingredientes de primera calidad. Desde los clásicos americanos hasta opciones veganas y creaciones innovadoras.
                </p>
        </div>
        </div>
    </header>

    <!-- Aquí mezclo clases de utilidad de Bootstrap para darle forma de tarjeta flotante y responsive (sombras, bordes redondeados y márgenes) junto con mi propia clase carta-fondo para aplicarle el estilo visual y el color del menú -->
    <main class="container my-5 carta-fondo p-md-5 p-4 rounded shadow-lg">
        
        <?php foreach ($categorias as $categoria_actual) { ?>
            
        <h2 class="text-center mb-4 mt-5 titulo-categoria"><?php echo $categoria_actual; ?></h2>
        <div class="row">
                
            <?php foreach ($menu_xml->plato as $plato) { ?>
                <?php if ((string) $plato['tipo'] == $categoria_actual) { ?>
                        
                <div class="col-12 col-md-6 mb-4">
                <div class="plato-item p-3 h-100">
                                
                <div class="plato-cabecera">
                    <h4 class="m-0 nombre-plato"><?php echo $plato->nombre; ?></h4>
                        <span class="precio fw-bold fs-5"><?php echo $plato->precio; ?>€</span>
                </div>
                                
                <p class="descripcion text-muted mb-2"><?php echo $plato->descripcion; ?></p>
                                
                <div class="plato-pie">
                <small class="text-secondary">
                    <i class="fa-solid fa-fire text-warning"></i> <?php echo $plato->calorias; ?> kcal
                </small>
                <!-- He utilizado dos bucles anidados. 
                 El primer bucle recorre las categorías únicas y printa los títulos de las secciones
                 Dentro, un segundo bucle recorre todos los platos del XML, aplicando una condición para filtrar y renderizar la estructura HTML solo de los platos que pertenecen a esa categoría
                 Además, el HTML del plato usa el sistema de rejilla de Bootstrap para adaptarse a dos columnas en ordenador y a una en móvil -->
                                    
                <div class="iconos-caracteristicas fs-5">
                <?php 
                // Comprobamos si hay alérgenos y los recorremos abriendo PHP una sola vez
                if (isset($plato->caracteristicas->item)) { 
                foreach ($plato->caracteristicas->item as $item) { 
                ?>
                <span title="<?php echo ucfirst(strtolower(trim((string) $item))); ?>">
                    <?php echo obtenerIcono($item); ?>
                </span>
                <?php 
                } // Cierra el foreach de los items
                } // Cierra el if de seguridad (isset)
                ?>
                </div> </div> </div> </div> <?php 
                } // Cierra el if 
                } // Cierra el foreach 
    ?>

    <!-- Cierra la caja donde hemos agrupado estos platos y dibuja una bonita línea de separación antes de pasar a la siguiente parte del menú -->
    </div> <div class="separador-decorativo"></div>

<?php 
} // Cierra el foreach PRINCIPAL (bucle de las categorías)
?>
        
        <div class="leyenda-final mt-5 text-center">
            <h3 class="mb-4 titulo-categoria" style="font-size: 1.8rem;">Información de Alérgenos</h3>
            <div class="d-flex flex-wrap justify-content-center gap-3">
                
                <?php foreach ($alergenos_usados as $alergeno) { ?>
                    <span class="etiqueta-alergeno">
                        <?php echo obtenerIcono($alergeno); ?> 
                        <?php echo $alergeno; ?>
                    </span>
                <?php } ?>

            </div>
        </div>
        
    </main>

    <a href="#inicio" class="btn-subir shadow" title="Volver arriba">
        <i class="fa-solid fa-chevron-up"></i>
    </a>
<!-- Una vez que el código termina de imprimir todas las secciones de la carta, pone abajo del todo una pequeña leyenda explicando los iconos de los alérgenos 
 y, por último, añade el botón flotante para que el cliente pueda volver rápido al principio de la página -->
</body>
</html>