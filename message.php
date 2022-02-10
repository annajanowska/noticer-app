<?php

    include("connect.php");

    if(isset($_SESSION['id'])) {   

        $idOtherUser = 0;
        $conversations = [];
        $stmt = $dbh->prepare("SELECT DISTINCT idPost, IF(idUserSender = :idUser, idUserReceiver, idUserSender) as buyer, MAX(lastMsg) as lastMsg FROM (
            SELECT idPost, idUserSender, idUserReceiver, MAX(createdTimeMessage) as lastMsg FROM UserPostMessage WHERE idUserSender = :idUser OR idUserReceiver = :idUser
            GROUP BY idPost, idUserSender, idUserReceiver
            ORDER BY lastMsg DESC
        ) AS test
        GROUP BY idPost, IF(idUserSender = :idUser, idUserReceiver, idUserSender);");

        $stmt->execute([':idUser' => $_SESSION['id']]);
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

            $stmt_1 = $dbh->prepare("SELECT title FROM Posts WHERE idPost = :idPost");
            $stmt_1->execute([':idPost' => $row['idPost']]);
            $row['postTitle'] = $stmt_1->fetchColumn();

            $stmt_2 = $dbh->prepare("SELECT login FROM Users WHERE idUser = :idUser");
            $stmt_2->execute([':idUser' => $row['buyer']]);
            $row['otherUserName'] = $stmt_2->fetchColumn();

            // $stmt_1 = $dbh->prepare("SELECT login FROM Users WHERE idUser = :idUser");
            // if ($_SESSION['id'] == intval($row['buyer'])) {
            //     $stmt_1->execute([':idUser' => $row['idUserReceiver']]);
            //     $idOtherUser = $row['idUserReceiver'] ;
            // } else {
            //     $stmt_1->execute([':idUser' => $row['buyer']]);
            //     $idOtherUser = $row['idUserSender'];
            // }

            // $row['idOtherUser'] = $idOtherUser;
            // $row['otherUserName'] = $stmt_1->fetchColumn();

            $conversations[] = $row;
        }

        echo $twig->render('message.html.twig', ['data' => $date, 'session' => $_SESSION, 'conversations' => $conversations]);
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }