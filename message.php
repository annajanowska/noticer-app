<?php

    include("connect.php");

    if(isset($_SESSION['id'])) {   

        $conversations = [];
        $stmt = $dbh->prepare("SELECT DISTINCT (idPost), idUserSender , idUserReceiver FROM UserPostMessage WHERE idUserSender = :idUserSender OR idUserReceiver = :idUserReceiver");
        $stmt->execute([':idUserSender' => $_SESSION['id'], ':idUserReceiver' => $_SESSION['id']]);
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stmt_1 = $dbh->prepare("SELECT title FROM Posts WHERE idPost = :idPost");
            $stmt_1->execute([':idPost' => $row['idPost']]);
            $row['postTitle'] = $stmt_1->fetchColumn();
            $stmt_1 = $dbh->prepare("SELECT login FROM Users WHERE idUser = :idUser");
            if ($_SESSION['id'] == intval($row['idUserSender'])) {
                $stmt_1->execute([':idUser' => $row['idUserReceiver']]);
            } else {
                $stmt_1->execute([':idUser' => $row['idUserSender']]);
            }
            $row['otherUserName'] = $stmt_1->fetchColumn();
            $conversations[] = $row;
        }

        echo $twig->render('message.html.twig', ['data' => $date, 'session' => $_SESSION, 'conversations' => $conversations]);
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }