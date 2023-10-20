<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    # Importar variable PDO del archivo global (index.php)
    global $PDO;

    #----- BD CONNECTION
    if(!$PDO){
        require 'config/database.php';
        $database = new Database();
        $PDO = $database->connect();
    }
    #--------------------------------------------------------------------

    class CategoriesModel{ 

        static function getAll()
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Creamos la query con los parametros recibidos
            $sql = 'SELECT * FROM category';
            
            try{
                # Preparamos la query con el string generado
                $query = $PDO->query($sql);
                
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Retornamos el valor para usarlo en proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

        static function getAllByProvider($id)
        {
            # llamamos a la variable global $PDO
            global $PDO;

            # Creamos la query con los parametros recibidos
            $sql = 'SELECT DISTINCT c.categoryID,c.name FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            JOIN equipment e ON e.equipmentID = pe.equipmentID
            JOIN category c ON e.categoryID = c.categoryID
            WHERE pe.providerID = :id';
            
            try{
                # Preparamos la query con el string generado
                $query = $PDO->prepare($sql);

                # Definimos los valores de los parametros
                $query->bindParam(':id', $id);

                # Ejecutamos  la cnsulta
                $query->execute();
                # Obtenemos un array con los datos recibidos
                $data = $query->fetchAll(PDO::FETCH_ASSOC);

                # Retornamos el valor para usarlo en proveedores.model.php
                return $data;
            }
            catch(PDOException $error){
                DefineError('#-001', $error->getMessage());
            }
        }

    }