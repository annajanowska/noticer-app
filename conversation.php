<?php

    include("connect.php");

    if(isset($_SESSION['id']) && isset($_GET['post']) && isset($_GET['person']) ) { 
        
        $idPost = $_GET['post'];
        $idOtherUser = $_GET['person'];

        $messages = [];
        $stmt = $dbh->prepare("SELECT um.message, u.login, um.createdTimeMessage, um.idUserSender
            FROM UserPostMessage um INNER JOIN Users u ON um.idUserSender = u.idUser 
                WHERE um.idPost = :idPost AND ((um.idUserSender = :idOtherUser AND um.idUserReceiver = :idMeUser) 
                    OR (um.idUserSender = :idMeUser AND um.idUserReceiver = :idOtherUser)) ORDER BY createdTimeMessage");
        $stmt->execute([':idPost' => $idPost, ':idOtherUser' => $idOtherUser, ':idMeUser' => $_SESSION['id']]);
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $messages[] = $row;
        }

        echo $twig->render('conversation.html.twig', ['data' => $date, 'session' => $_SESSION, 'messages' => $messages]);
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }