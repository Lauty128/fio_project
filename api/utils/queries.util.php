<?php

function getWhere(array $options) : string
{
    $where = '';

    if(isset($options['equipments'])){
        $options = array_filter($options, function($key){
            $possibleOptions = ['word','category'];
            return in_array($key, $possibleOptions);
        }, ARRAY_FILTER_USE_KEY);
    }
    
    if(count($options) > 0){ $index = 0; }

    foreach ($options as $option) {
            # If this position is equal to 0(zero), then a 'WHERE' is added
            if($index == 0){ $where .= "WHERE "; }
            
            $where .= $option['table']." ".$option['equal']." ".$option['value'];
            
            # If this position isn't the last, then an 'AND' is added
            if($index < (count($options) - 1)){ $where .= " AND "; }  
            
            # Index is equal to index + 1
            $index++;
    }

    //var_dump($where);
    //exit();
    return $where;
}   

function defineQueryByOptionsForProviders(array $options, string $refer = ''):string
{
    # Le agregamos el punto para ingresar a las propiedades del elemento referido
    if($refer != ''){ $refer .= '.'; }

    # Si existe category o equipments agregamos la conexion con la tabla <provider_equipment>
    $originalSql = (isset($options['equipments']) || isset($options['category'])) ? 'JOIN provider_equipment pe ON pe.providerID = '.$refer.'providerID ' : '';

    #-------------- WORD OPTION
    if(isset($options['word']))
    {
        $word = $options['word'];
        $sql = "WHERE ".$refer."name COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'";
    }

    #-------------- CATEGORY OPTION
    if(isset($options['category']))
    {
        $category = $options['category'];
        $sql = "JOIN equipment e ON pe.equipmentID = e.equipmentID 
            WHERE e.categoryID = ".$category['value'];
    }

    #-------------- WORD AND CATEGORY OPTION
    if(isset($options['category'], $options['word']))
    {
        # Si los dos existen las variables $category y $word ya estaran definidas dentro de los if anteriores 

        $sql = "JOIN equipment e ON pe.equipmentID = e.equipmentID 
            WHERE e.categoryID = ".$category['value']."
            AND ".$refer."name COLLATE utf8mb4_unicode_ci LIKE '%".$word['value']."%'";
    }

    # Unimos la consulta inicial con alguna de las correspondientes;
    $originalSql .= $sql;

    # Los datos seran agrupados si existe el filtro categoria o equipos, con el fin de no repetir los registros
    if(isset($options['equipments']) || isset($options['category'])){
        $originalSql .= " GROUP BY ".$refer."providerID";
    }

    return $originalSql;
}

function defineOrder(string $name):string | null
{
    # Los tipos de ordanamiento que pueden existir
    $orderTypes = [
        'N-ASC' => 'ORDER BY e.name ASC',
        'N-DESC' => 'ORDER BY e.name DESC',
        'default' => 'ORDER BY e.name ASC',
    ];

    # Controlar que el tipo de orden exista en el array
    if(!isset($orderTypes[$name])){ return null; }

    return $orderTypes[$name];
}