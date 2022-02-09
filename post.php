<?php

    include("connect.php");

    $currentDate = date("Y-m-d H:i:s");

    //print_R($_SERVER['REQUEST_URI']);
    $postid = explode("/", $_SERVER['REQUEST_URI']);
    //print_R($postid[3]);

    if(isset($_SESSION['id'])){
        if (isset($_GET['show']) && intval($_GET['show']) > 0) {
    
            $id = intval($_GET['show']);

            $stmt = $dbh->prepare("SELECT p.*, categoryName FROM Posts p INNER JOIN Categories c ON c.idCategory = p.idCategory WHERE idPost= :id");
            $stmt->execute([':id' => $id]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            // print_r("Post: ");
            // print_r($post);
        
            $stmt = $dbh->prepare("SELECT u.* FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE p.idPost = :postid");
            $stmt->execute([':postid' => $postid[3]]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            //print_r("User: ");
            //print_r($user);
    
            //print_R($user);
            //print_R($user['login']);
            $stmt = $dbh->prepare("SELECT COUNT(*) FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE u.idUser = :userid");
            $stmt->execute([':userid' => $user['idUser']]);
            $how_many_photos = intval($stmt->fetchColumn());
    
            $stmt = $dbh->prepare("SELECT * FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE u.idUser = :userid ORDER BY createdTime DESC LIMIT 1");
            $stmt->execute([':userid' => $user['idUser']]);
            $last_post = $stmt->fetch(PDO::FETCH_ASSOC);
    
            /*$stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE PostID = :PostID");
            $stmt->execute([':PostID' => $id]);
            $likes = intval($stmt->fetchColumn());
    
            $tab_comments[]="";
    
            $stmt = $dbh->prepare("SELECT c.*, u.login FROM a30_User_Post_Comment c INNER JOIN a30_Users u ON c.userID = u.id WHERE postID = :PostID");
            $stmt->execute([':PostID' => $id]);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tab_comments[] = $row;
            }*/
            

        }
        else {       
    
            if (isset($_GET['message']) && isset($_SESSION['id']) && isset($_POST['message']) ) {

                $id = intval($_GET['message']);
                $message = $_POST['message'];
                
                $stmt_1 = $dbh->prepare("SELECT idUser FROM Posts WHERE idPost = $id");
                $stmt_1->execute();
                $idReceiver = intval($stmt_1->fetchColumn());
                print_r($idReceiver);
                
                if (mb_strlen($message) >= 2 && mb_strlen($message) <= 200) {
                    try {
                        $stmt = $dbh->prepare ("INSERT INTO UserPostMessage (
                        idUserPostMessage, message, idUserSender, idUserReceiver, idPost, createdTimeMessage) 
                        VALUES (
                            null, :message, :idUserSender, :idUserReceiver, :postID , '$currentDate') 
                        ");
        
                    $stmt->execute([':message' => $message, ':idUserSender' => $_SESSION['id'], ':idUserReceiver' => $idReceiver, ':postID' => $id]);
                    } 
                    catch (PDOException $e) {
                    }
                    header('Location: post/show/'.$id.'');
                    print_r("powrót");
                    exit();
                }	
            }
           /* if (isset($_GET['comments']) && isset($_SESSION['id']) && isset($_POST['comment'])) {
    
                $id = intval($_GET['comments']);
                $comment = $_POST['comment'];
        
                try {
                    $stmt = $dbh->prepare ("INSERT INTO a30_User_Post_Comment (
                    id, userID, postID, comment, createdTimeComment) 
                    VALUES (
                        null, :userID, :postID, :comment, '$currentDate') 
                    ");
        
                $stmt->execute([':userID' => $_SESSION['id'], ':postID' => $id, ':comment'=> $comment]);
                $info="dodano!";
    
                } 
                catch (PDOException $e) {
                    $info="nie dodano!";
                }
                header('Location: /post/show/'.$id.'');
                exit();
            }
    
            if (isset($_GET['like'])) {
    
                $id = intval($_GET['like']);
        
                $stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE userID = :userID AND PostID = :PostID");
                $stmt->execute([':userID' => $_SESSION['id'], ':PostID' => $id]);
                $how_many_likes = intval($stmt->fetchColumn());	
        
                if($how_many_likes == 0) {
                    try {
                        $stmt = $dbh->prepare ("INSERT INTO a30_User_Post_Like (
                        id, userID, PostID) 
                        VALUES (
                            null, :userID, :PostID) 
                        ");
        
                    $stmt->execute([':userID' => $_SESSION['id'], ':PostID' => $id]);
                    } 
                    catch (PDOException $e) {
                    }
                    $userFeedback = "Lubisz zdjÄ™cie!";
                    header('Location: /post/show/'.$id.'');
                    exit();
                }
                else {
                    header('Location: /post/show/'.$id.'');
                    exit();
                 }
                } */


            }
            echo $twig->render('post.html.twig', ['data' => $date, 'post' => $_POST, 'get' => $_GET, 'session' => $_SESSION, 'post' => $post, 'user' => $user, 'how_many_photos' => $how_many_photos, 'last_post' => $last_post ]);
        }
        else {
            header("Location:https://s105.labagh.pl/main");
            exit();
        }