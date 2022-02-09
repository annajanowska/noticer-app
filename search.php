<?php

	include("connect.php");

    $currentDate = date("Y-m-d H:i:s");
	$userFeedback = "";
	$ile_photos = 0;
	$last_post = 0;
	$comment = "";

    if (empty($_POST)) {
    }
	elseif (isset($_POST['logout'])){
		$userFeedback = "Wylogowałeś się!";
		unset($_SESSION['id']);
		unset($_SESSION['email']);
	}
	else{
		if(isset($_POST['login']) && isset($_POST['password'])) {

			$login = $_POST['login'];
			$password = $_POST['password'];

			$stmt = $dbh->prepare("SELECT * FROM Users WHERE login = :login");
			$stmt->execute([':login' => $login]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if($user) {
				if(password_verify($password, $user['password'])) {
					$_SESSION['id'] = $user['idUser'];
					$_SESSION['login'] = $user['login'];
					$_SESSION['email'] = $user['email'];
					$userFeedback = "";
				}
				else {
					$userFeedback = "Nie poprawne hasło!";
				}
			}
			else {
				$userFeedback = "Nie ma takiego użytkownika!";
			}
		}
	}

	if (isset($_SESSION['id'])) {

		$stmt = $dbh->prepare("SELECT COUNT(*) FROM Posts WHERE idUser = :userID");
		$stmt->execute([':userID' => $_SESSION['id']]);
		$ile_photos = intval($stmt->fetchColumn());

		$stmt = $dbh->prepare("SELECT * FROM Posts WHERE idUser = :userid ORDER BY createdTime DESC LIMIT 1");
    	$stmt->execute([':userid' => $_SESSION['id']]);
    	$last_post = $stmt->fetch(PDO::FETCH_ASSOC);
	}
    if (isset($_GET['category'])) {
        $category = $_GET['category'];
    }
	// else {
    //     $localization = $_GET['miejsce'];
    // }


		$stmt_howmuch = $dbh->prepare("SELECT COUNT(*) FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE idCategory = 
			(SELECT idCategory FROM Categories WHERE categoryName = '$category')");
		//$stmt_howmuch = $dbh->prepare("SELECT idCategory FROM Categories WHERE categoryName = '$category'");
		$stmt_howmuch->execute();
		$how_many_post = intval($stmt_howmuch->fetchColumn());	
		// print_r('to jest');
		// print_r($how_many_post);

		if ($how_many_post == 0) {

			print_r("Nie ma ogłoszeń w tej kategorii");
			$posts = [];

			// $posts = [];
			// $stmt = $dbh->prepare("SELECT p.*, u.login FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE p.id =108");
			// $stmt->execute();

			// while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			// 		$stmt_1 = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE PostID = :PostID");
			// 		$stmt_1->execute([':PostID' => $row['id']]);
			
			
			// 		$comments = [];
			// 		$stmt_2 = $dbh->prepare("SELECT c.*, u.login FROM a30_User_Post_Comment c INNER JOIN a30_Users u ON c.userID = u.id WHERE postID = :PostID ORDER BY createdTimeComment ASC");
			// 		$stmt_2->execute([':PostID' => $row['id']]);
			// 		while ($row_1 = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
			// 			$comments[] = $row_1;
			// 		}
			// 		$row['comments'] = $comments;
			// 		$likes = intval($stmt_1->fetchColumn());
			// 		$row['likes'] = $likes;
			// 		$posts[] = $row;
			// }
		}
		else {
				$posts = [];
				$stmt = $dbh->prepare("SELECT p.*, u.login FROM Posts p INNER JOIN Users u ON p.idUser = u.idUser WHERE idCategory = 
					(SELECT idCategory FROM Categories WHERE categoryName = '$category') ORDER BY createdTime DESC");
				$stmt->execute();

				while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
					// $stmt_1 = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE PostID = :PostID");
					// $stmt_1->execute([':PostID' => $row['id']]);
			
			
					// $comments = [];
					// $stmt_2 = $dbh->prepare("SELECT c.*, u.login FROM a30_User_Post_Comment c INNER JOIN a30_Users u ON c.userID = u.id WHERE postID = :PostID ORDER BY createdTimeComment ASC");
					// $stmt_2->execute([':PostID' => $row['id']]);
					// while ($row_1 = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
					// 	$comments[] = $row_1;
					// }
					// $row['comments'] = $comments;
					// $likes = intval($stmt_1->fetchColumn());
					// $row['likes'] = $likes;
					$stmt_1 = $dbh->prepare("SELECT categoryName FROM Categories WHERE idCategory = :idCategory");
					$stmt_1->execute([':idCategory' => $row['idCategory']]);
					$row['name'] = $stmt_1->fetch(PDO::FETCH_ASSOC);
					$posts[] = $row;
					}
			//echo $twig->render('search.html.twig', ['data' => $date, 'session' => $_SESSION, 'test' => $userFeedback, 'posts' => $posts, 'ile_photos' => $ile_photos, 'last_post' => $last_post, 'comment'=> $comment, 'localization' => $localization]);
		}

	// if (isset($_GET['lajk']) && isset($_SESSION['id'])) {

	// 	$id = intval($_GET['lajk']);

	// 	$stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE userID = :userID AND PostID = :PostID");
	// 	$stmt->execute([':userID' => $_SESSION['id'], ':PostID' => $id]);
	// 	$how_many_likes = intval($stmt->fetchColumn());	

	// 	if($how_many_likes == 0){
	// 		try {
	// 			$stmt = $dbh->prepare ("INSERT INTO a30_User_Post_Like (
	// 			id, userID, PostID) 
	// 			VALUES (
	// 				null, :userID, :PostID) 
	// 			");

	// 		$stmt->execute([':userID' => $_SESSION['id'], ':PostID' => $id]);
	// 		} 
	// 		catch (PDOException $e) {
	// 		}
	// 		$userFeedback = "Lubisz zdjęcie!";
	// 		header('Location: /search.php?localization='.$localization.'');
	// 		exit();
	// 	}else{
	// 		header('Location: /search.php?localization='.$localization.'');
	// 		exit();
	// 	}
	// }

	// if (isset($_GET['koment']) && isset($_SESSION['id']) && isset($_POST['commentss']) ) {

	// 	$id = intval($_GET['koment']);
	// 	$comment = $_POST['commentss'];
		
	// 	if (mb_strlen($comment) >= 2 && mb_strlen($comment) <= 200) {
	// 		try {
	// 			$stmt = $dbh->prepare ("INSERT INTO a30_User_Post_Comment (
	// 			id, userID, postID, comment, createdTimeComment) 
	// 			VALUES (
	// 				null, :userID, :postID, :comment, '$currentDate') 
	// 			");
	
	// 		$stmt->execute([':userID' => $_SESSION['id'], ':postID' => $id, ':comment'=> $comment]);
	// 		} 
	// 		catch (PDOException $e) {
	// 		}
	// 		header('Location: /search.php?localization='.$localization.'');
	// 		exit();
		
	// 	}
	// }

    echo $twig->render('search.html.twig', ['data' => $date, 'session' => $_SESSION, 'test' => $userFeedback, 'posts' => $posts, 'ile_photos' => $ile_photos, 'last_post' => $last_post, 'comment'=> $comment, 'category' => $category]);
