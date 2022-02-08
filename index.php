<?php

	include("connect.php");

	$userFeedback = "";
	$ile_photos = 0;
	$last_post = 0;
	$comment = "";
	$currentDate = date("Y-m-d H:i:s");

	$posts = [];
	$stmt = $dbh->prepare("SELECT p.*, u.login FROM a30_Post p INNER JOIN a30_Users u ON p.userID = u.id ORDER BY createdTime DESC LIMIT 72");
	$stmt->execute();

	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

		$stmt_1 = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE PostID = :PostID");
		$stmt_1->execute([':PostID' => $row['id']]);

		$comments = [];
		$stmt_2 = $dbh->prepare("SELECT c.*, u.login FROM a30_User_Post_Comment c INNER JOIN a30_Users u ON c.userID = u.id WHERE postID = :PostID ORDER BY createdTimeComment ASC");
		$stmt_2->execute([':PostID' => $row['id']]);
		while ($row_1 = $stmt_2->fetch(PDO::FETCH_ASSOC)) {
			$comments[] = $row_1;
		}
		$row['comments'] = $comments;
		$likes = intval($stmt_1->fetchColumn());
		$row['likes'] = $likes;
        $posts[] = $row;
    }

	if (empty($_POST)) {
    }
	elseif (isset($_POST['logout'])) {
		$userFeedback = "Wylogowałeś się!";
		unset($_SESSION['id']);
		unset($_SESSION['email']);
	}
	else {
		if(isset($_POST['login']) && isset($_POST['password'])) {

			$login = $_POST['login'];
			$password = $_POST['password'];

			$stmt = $dbh->prepare("SELECT * FROM a30_Users WHERE login = :login");
			$stmt->execute([':login' => $login]);
			$user = $stmt->fetch(PDO::FETCH_ASSOC);

			if($user) {
				if(password_verify($password, $user['password'])) {
					$_SESSION['id'] = $user['id'];
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

		$stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_Post WHERE userID = :userID");
		$stmt->execute([':userID' => $_SESSION['id']]);
		$ile_photos = intval($stmt->fetchColumn());

		$stmt = $dbh->prepare("SELECT * FROM a30_Post WHERE userid = :userid ORDER BY createdTime DESC LIMIT 1");
    	$stmt->execute([':userid' => $_SESSION['id']]);
    	$last_post = $stmt->fetch(PDO::FETCH_ASSOC);
	}

	if(isset($_GET['like']) && isset($_SESSION['id'])){

			$id = intval($_GET['like']);

			$stmt = $dbh->prepare("SELECT COUNT(*) FROM a30_User_Post_Like WHERE userID = :userID AND PostID = :PostID");
			$stmt->execute([':userID' => $_SESSION['id'], ':PostID' => $id]);
			$how_many_likes = intval($stmt->fetchColumn());	
	
			if ($how_many_likes == 0) {
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
				$userFeedback = "Lubisz zdjęcie!";
				header('Location: /main');
				exit();
			}
			else {
				header('Location: /main');
				exit();
			}
	}
	
	if (isset($_GET['comments']) && isset($_SESSION['id']) && isset($_POST['comment']) ) {

		$id = intval($_GET['comments']);
		$comment = $_POST['comment'];
		
		if (mb_strlen($comment) >= 2 && mb_strlen($comment) <= 200) {
			try {
				$stmt = $dbh->prepare ("INSERT INTO a30_User_Post_Comment (
				id, userID, postID, comment, createdTimeComment) 
				VALUES (
					null, :userID, :postID, :comment, '$currentDate') 
				");
	
			$stmt->execute([':userID' => $_SESSION['id'], ':postID' => $id, ':comment'=> $comment]);
			} 
			catch (PDOException $e) {
			}
			header('Location: /main');
			exit();
		}	
	}

	echo $twig->render('index.html.twig', ['data' => $date, 'session' => $_SESSION, 'test' => $userFeedback, 'posts' => $posts, 'ile_photos' => $ile_photos, 'last_post' => $last_post, 'comment'=> $comment]);