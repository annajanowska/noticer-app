<?php

    include("connect.php");
    
   if(isset($_SESSION['id'])){

    $categoriesNames = [];
    $stmt = $dbh->prepare("SELECT categoryName FROM Categories");
    $stmt->execute();
    $test = "test";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categoriesNames[] = $row;
    }
    //print_r($categoriesNames);

   print_r($_POST['nameCat']);
   /* if ($_POST['nameCat']) {
        $query = "SELECT subcategoryName FROM Categories WHERE categoryName=".$_POST['nameCat'];
        $result = $db=>query($query);
        if ($result->num_rows >0) {
            while($row = $result->fetch_assoc()) {
                print_r($row);
            }
        }
    } */

    if(empty($_POST))
    {
        echo $twig->render('add.html.twig',['session' => $_SESSION, 'data' => $date, 'categoriesNames' => $categoriesNames]);
    }
    else {          
        try {
            if (empty($_FILES['image'])) {
                throw new Exception('Image file is missing');
            }
            
            $image = $_FILES['image'];
            if ($image['error'] !== 0) {
                if ($image['error'] === 1) 
                    throw new Exception('Max upload size exceeded');
                    
                throw new Exception('Image uploading error: INI Error');
            }
            if (!file_exists($image['tmp_name']))
                throw new Exception('Image file is missing in the server');
            $maxFileSize = 2 * 10e6; 
            if ($image['size'] > $maxFileSize)
                throw new Exception('Max size limit exceeded'); 
            $imageData = getimagesize($image['tmp_name']);
            if (!$imageData) 
                throw new Exception('Invalid image');
            $mimeType = $imageData['mime'];
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mimeType, $allowedMimeTypes)) 
                throw new Exception('Only JPEG, PNG and GIFs are allowed');
            

            $fileExtention = strtolower(pathinfo($image['name'] ,PATHINFO_EXTENSION));
            $fileName = round(microtime(true)) . mt_rand() . '.' . $fileExtention; 
            $path = '/Images/' . $fileName;
            $destination = $_SERVER['DOCUMENT_ROOT'] . $path;
        
            if (!move_uploaded_file($image['tmp_name'], $destination))
                throw new Exception('Error in moving the uploaded file');
        
            $localization = $_POST['localization'];
            $title = $_POST['title'];
            $price = $_POST['price'];
            $category = $_POST['category'];
            print_r($_GET['category']);
            $subcategory = $_POST['subcategory'];
            $description = $_POST['description'];
            $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
            $domain = $protocol . $_SERVER['SERVER_NAME'];
            $url = $domain . $path;
            $currentDate = date("Y-m-d H:i:s");
           

            $stmt = $dbh->prepare("SELECT * FROM Users WHERE idUser = :id");
            $stmt->execute([':id' => $_SESSION['id']]);
            $userID = $stmt->fetch(PDO::FETCH_ASSOC)['idUser'];

    
            $stmt = $dbh -> prepare("INSERT INTO Posts (idPost, title, description, createdTime, imgSource, localization, price, isSold, idUser, idCategory) 
                VALUES (
                null, '$title', '$description', '$currentDate', '$url', '$localization', $price, 0 , $userID, 1) 
                ");
            $stmt = $stmt->execute();
            
            if ( true) {
                $info = "Przesłano plik!";
                echo $twig->render('add.html.twig', ['post' => $_POST, 'session' =>$_SESSION, 'get' => $_GET, 'test'=> $info, 'data' => $date, 'categoriesNames' => $categoriesNames]);
                
            } else 
                throw new Exception('Error in saving into the database');
            
        } catch (Exception $e) {
            $info = "Plik nie został przesłany.";
            echo $twig->render('add.html.twig', ['post' => $_POST, 'session' =>$_SESSION, 'get' => $_GET, 'test'=>$info, 'data' => $date, 'categoriesNames' => $categoriesNames]);
        }
    }       
   }
   else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
   }