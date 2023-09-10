<?php

function formaterOptions(array $options):array | null
{
    $newOptions = [];

    foreach ($options as $key => $option){
        switch ($key) {
            case 'word':
               $newOptions[$key] = [
                    "value" => $option,
                    "type" => 'collate'
                ];
            break;
            case 'category':
                $newOptions[$key] = [
                    "value" => $option,
                    "type" => 'equal'
                ];
            break;
            case 'equipments':
                if($option == 'true'){
                    $newOptions[$key] = 1;
                }
            break;
        }
    }

    if(count($newOptions) == 0){
        return null;
    }

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