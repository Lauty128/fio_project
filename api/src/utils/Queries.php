<?php

namespace App\Util;

class Queries{
    
    static function getWhere(array $options) : string
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
                # Si la posicion es igual a 0, estonces un "WHERE" se agrega.
                if($index == 0){ $where .= "WHERE "; }
                
                $where .= $option['table']." ".$option['equal']." ".$option['value'];
                
                # Si esta posicion no es la ultuma, entonces un "AND" es agregado para continuar al siguiente WHERE
                if($index < (count($options) - 1)){ $where .= " AND "; }  
                
                # A $index le sumamos 1
                $index++;
        }
    
        return $where;
    }   
    
    static function defineQueryByOptionsForProviders(array $options, string $refer = ''):string
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
    
    static function defineOrder(string $order, string $table){
        # Los distintos tipos de ordenes para cada tabla (algunos son bastantes parecidos)
        $orderTypes = [
            'provider' => [
                'ID-ASC' => 'ORDER BY p.providerID ASC',
                'ID-DESC' => 'ORDER BY p.providerID DESC',
                'N-ASC' => 'ORDER BY p.name ASC',
                'N-DESC' => 'ORDER BY p.name DESC',
                'default' => 'ORDER BY p.name ASC'
            ],
            'equipment' => [
                'ID-ASC' => 'ORDER BY e.equipmentID ASC',
                'ID-DESC' => 'ORDER BY e.equipmentID DESC',
                'N-ASC' => 'ORDER BY e.name ASC',
                'N-DESC' => 'ORDER BY e.name DESC',
                'default' => 'ORDER BY e.name ASC'
            ],
        ];
    
        # Si no existe alguna de las tablas retornamos un string vacio
        if(!isset($orderTypes[$table])){ return ''; }
        
        # Si existe la tabla retornamos el string indicado dentro de la tabla y la key del orden
        return $orderTypes[$table][$order];
    }
}