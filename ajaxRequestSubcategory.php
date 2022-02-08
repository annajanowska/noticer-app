<?php
    include("connect.php");
    
    
   
    
    //print_r($name['subcategoryName']);
    //print_r($_POST);
    $resultObj = new stdClass;
    if(!empty($_POST['nameCat'])){
        $catName = $_POST['nameCat'];

        $stmt = $dbh->prepare("SELECT subcategoryName FROM Categories WHERE categoryName= '$catName' ");
            $stmt->execute();
            $name = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!empty($name))
        {
            $resultObj->subCategoryName = $name['subcategoryName'];
            $resultObj->post = $_POST;
            $resultObj->isError = false;
            $resultJSON = json_encode($resultObj);
            echo $resultJSON;
        } else {
            $resultObj->subCategoryName = "null";
            $resultObj->isError = true;
            $resultJSON = json_encode($resultObj);
            echo $resultJSON;
        }
    
    } else {
        $resultObj->subCategoryName = "null";
        $resultObj->isError = true;
        $resultJSON = json_encode($resultObj);
        echo $resultJSON;
    }
   

    /* $stmt = $dbh->prepare("SELECT * FROM a30_Post WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC); */ 
   /* while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categoriesNames[] = $row;
    } */
    
     /* if ($_POST['nameCat']) {
        $query = "SELECT subcategoryName FROM Categories WHERE categoryName=".$_POST['nameCat'];
        $result = $db=>query($query);
        if ($result->num_rows >0) {
            while($row = $result->fetch_assoc()) {
                print_r($row);
            }
        }
    } */

   /* if(empty($_POST))
    {
    }
    else {          
            
    }*/
