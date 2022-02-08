<?php

    include("connect.php");

    $currentDate = date("Y-m-d H:i:s");

    //print_R($_SERVER['REQUEST_URI']);
    $postid = explode("/", $_SERVER['REQUEST_URI']);
    //print_R($postid[3]);

    if(isset($_SESSION['id'])){
    if (isset($_GET['show']) && intval($_GET['show']) > 0) {

        $id = intval($_GET['show']);

        $stmt = $dbh->prepare("SELECT * FROM a30_Post WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
	
        $stmt = $dbh->prepare("SELECT u.* FROM a30_Post p INNER JOIN a30_Users u ON p.userID = u.id WHERE p.id = :postid");
        $stmt->execute([':postid' => $postid[3]]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        //print_R($user);
        //print_R($user['login']);
        $stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_Post p INNER JOIN a30_Users u ON p.userID = u.id WHERE u.id = :userid");
        $stmt->execute([':userid' => $user['id']]);
        $how_many_photos = intval($stmt->fetchColumn());

        $stmt = $dbh->prepare("SELECT * FROM a30_Post p INNER JOIN a30_Users u ON p.userID = u.id WHERE u.id = :userid ORDER BY createdTime DESC LIMIT 1");
        $stmt->execute([':userid' => $user['id']]);
        $last_post = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE PostID = :PostID");
		$stmt->execute([':PostID' => $id]);
        $likes = intval($stmt->fetchColumn());

        $tab_comments[]="";

        $stmt = $dbh->prepare("SELECT c.*, u.login FROM a30_User_Post_Comment c INNER JOIN a30_Users u ON c.userID = u.id WHERE postID = :PostID");
		$stmt->execute([':PostID' => $id]);
		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$tab_comments[] = $row;
		}
    }
    else {       

        if (isset($_GET['comments']) && isset($_SESSION['id']) && isset($_POST['comment'])) {

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
            }
        }
        echo $twig->render('post.html.twig', ['data' => $date, 'post' => $_POST, 'get' => $_GET, 'session' => $_SESSION, 'post' => $post, 'user' => $user, 'how_many_photos' => $how_many_photos, 'last_post' => $last_post, 'likes' => $likes, 'tab_comments' => $tab_comments]);
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }