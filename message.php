<?php

    include("connect.php");

    if(isset($_SESSION['id'])) {   
            echo $twig->render('message.html.twig', ['data' => $date, 'session' => $_SESSION]);
    }
    else {
        header("Location:https://s401354.labagh.pl/main");
        exit();
    }