<?php 
    #--------------------------- ESTE CODIGO DEBE ESTAR EN CADA ARCHIVO .MODEL.PHP
    
    #----- BD CONNECTION
    use Config\Database;
    Database::connect();

    #--------------------------------------------------------------------

    class CategoriesModel{ 

        static function getAll()
        {
            # Creamos la query con los parametros recibidos
            $sql = 'SELECT * FROM category';
            
            try{
                # Preparamos la query con el string generado
                $query = Database::$connection->query($sql);
                
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
            # Creamos la query con los parametros recibidos
            $sql = 'SELECT DISTINCT c.categoryID,c.name FROM provider p
            JOIN provider_equipment pe ON p.providerID = pe.providerID
            JOIN equipment e ON e.equipmentID = pe.equipmentID
            JOIN category c ON e.categoryID = c.categoryID
            WHERE pe.providerID = :id';
            
            try{
                # Preparamos la query con el string generado
                $query = Database::$connection->prepare($sql);

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