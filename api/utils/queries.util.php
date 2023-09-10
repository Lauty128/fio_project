<?php

function defineQueryByOptionsForEquipments(array $options, string $refer = ''):string
{
    # Le agregamos el punto para ingresar a las propiedades del elemento referido
    if($refer != ''){ $refer .= '.'; }

    if(isset($options['word'])){
        $word = $options['word'];
        $sql = "WHERE ".$refer."nombre COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'";
    }

    if(isset($options['category'])){
        $category = $options['category'];
        $sql = "WHERE ".$refer."cod_categoria = ".$category['value']."
            GROUP BY ".$refer."cod_equipo";
    }

    if(isset($options['category'], $options['word'])){
        $sql = "WHERE ".$refer."cod_categoria = ".$category['value']." 
            AND ".$refer."nombre COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'
            GROUP BY ".$refer."cod_equipo";
    }

    return $sql;
}

function defineQueryByOptionsForProviders(array $options, string $refer = ''):string
{
    return '';
}



function defineOrder(string $name):string | null
{
    # Los tipos de ordanamiento que pueden existir
    $orderTypes = [
        'N-ASC' => 'ORDER BY e.nombre ASC',
        'N-DESC' => 'ORDER BY e.nombre DESC',
        'default' => '',
    ];

    # Controlar que el tipo de orden exista en el array
    if(!isset($orderTypes[$name])){ return null; }

    return $orderTypes[$name];
}


# OBTENER PROVEEDORES FILTRADOS POR UNA CATEGORIA MAS EL TOTAL DE PRODUCTOS DE ESA CATEGORIA QUE VENDEN
                //SELECT p.cod_proveedor, p.nombre, COUNT(pe.cod_equipo) as cantidad FROM proveedor p
                //JOIN proveedor_equipo pe ON pe.cod_proveedor = p.cod_proveedor
                //JOIN equipo e ON e.cod_equipo = pe.cod_equipo
                //WHERE e.cod_categoria = 2
                //GROUP BY p.cod_proveedor; 

                # FILTRAR PROVEEDORES POR SU NOMBRE
                //SELECT * FROM proveedor
                //WHERE nombre COLLATE utf8mb4_unicode_ci LIKE '%:word%';