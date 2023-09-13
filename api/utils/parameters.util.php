<?php

function formaterOptionsForEquipments(array $options):array | null
{
    if(count($options) == 0){ return null; }

    $newOptions = [];

    foreach($options as $key => $value){
        switch ($key){
            case 'word':
                $newOptions[$key] = [
                    'table' => "e.nombre",
                    'value' => "'%".$value."%'",
                    'equal' => 'COLLATE utf8mb4_unicode_ci LIKE'
                ];
            break;
            case 'category':
                $newOptions[$key] = [
                    'table' => 'e.cod_categoria',
                    'value' => $value,
                    'equal' => '='
                ];
            break;
        }
    }

    if(count($newOptions) == 0){ return null; }
    return $newOptions;
}

function formaterOptionsForProviders(array $options):array | null
{
    if(count($options) == 0){ return null; }

    $newOptions = [];

    foreach($options as $key => $value){
        switch ($key){
            case 'word':
                $newOptions[$key] = [
                    'table' => "p.nombre",
                    'value' => "'%".$value."%'",
                    'equal' => 'COLLATE utf8mb4_unicode_ci LIKE'
                ];
            break;
            case 'equipments':
                if($value == 'true'){
                    $newOptions[$key] = true;
                }
            break;
            case 'category':
                $newOptions[$key] = [
                    'table' => 'e.cod_categoria',
                    'value' => $value,
                    'equal' => '='
                ];
            break;
        }
    }

    if(count($newOptions) == 0){ return null; }
    return $newOptions;
}

function formaterOrder(string $order):string
{
    # Los tipos de ordanamiento que pueden existir
    $orderTypes = constant('ORDER_TYPES');

    # Controlar que el tipo de orden exista en el array
    if(!in_array($order,$orderTypes)){ $order = 'default'; }

    # Devolver el formato del tipo de orden
    return $order;
}