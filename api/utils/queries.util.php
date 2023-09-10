<?php

function defineQueryByOptionsForEquipments(array $options, string $refer = ''):string
{
    # Le agregamos el punto para ingresar a las propiedades del elemento referido
    if($refer != ''){ $refer .= '.'; }

    if(isset($options['word']))
    {
        $word = $options['word'];
        $sql = "WHERE ".$refer."nombre COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'";
    }

    if(isset($options['category']))
    {
        $category = $options['category'];
        $sql = "WHERE ".$refer."cod_categoria = ".$category['value']."
            GROUP BY ".$refer."cod_equipo";
    }

    if(isset($options['category'], $options['word']))
    {
        $sql = "WHERE ".$refer."cod_categoria = ".$category['value']." 
            AND ".$refer."nombre COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'
            GROUP BY ".$refer."cod_equipo";
    }

    return $sql;
}

function defineQueryByOptionsForProviders(array $options, string $refer = ''):string
{
    # Le agregamos el punto para ingresar a las propiedades del elemento referido
    if($refer != ''){ $refer .= '.'; }

    # Si existe category o equipments agregamos la conexion con la tabla <proveedor_equipo>
    $originalSql = (isset($options['equipments']) || isset($options['category'])) ? 'JOIN proveedor_equipo pe ON pe.cod_proveedor = '.$refer.'cod_proveedor ' : '';

    #-------------- WORD OPTION
    if(isset($options['word']))
    {
        $word = $options['word'];
        $sql = "WHERE ".$refer."nombre COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'";
    }

    #-------------- CATEGORY OPTION
    if(isset($options['category']))
    {
        $category = $options['category'];
        $sql = "JOIN equipo e ON pe.cod_equipo = e.cod_equipo 
            WHERE e.cod_categoria = ".$category['value'];
    }

    #-------------- WORD AND CATEGORY OPTION
    if(isset($options['category'], $options['word']))
    {
        # Si los dos existen las variables $category y $word ya estaran definidas dentro de los if anteriores 

        $sql = "JOIN equipo e ON pe.cod_equipo = e.cod_equipo 
            WHERE e.cod_categoria = ".$category['value']."
            AND ".$refer."nombre COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'";
    }

    # Unimos la consulta inicial con alguna de las correspondientes;
    $originalSql .= $sql;

    # Los datos seran agrupados si existe el filtro categoria o equipos, con el fin de no repetir los registros
    if(isset($options['equipments']) || isset($options['category'])){
        $originalSql .= " GROUP BY ".$refer."cod_proveedor";
    }

    return $originalSql;
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