<?php 
    
    $path = $_SERVER['DOCUMENT_ROOT'];
    include_once('PDOFactory.php');
    include_once('library.php');
    include_once($path . '/classes/user.php');
    include_once($path . '/classes/tournament.php');
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    } 
    $user = User::onlyID($_SESSION['user_id']);
    if(empty((array)$user)){
        header('Location:/landing.php');
    }

?>
<!DOCTYPE html>
<html>

<head>
<title>Smash Rank</title>
    <link href="/rsc/master.css" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon-96x96.png">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
      rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
</head>
<body>
    <div id="main-nav">
        <div id="user-block">

        <div class="user-image">
            <a href="<?php echo $user->getProfileLink() ?>"><img src="<?php echo $user->getProfileImage(60); ?>" /></a>
        </div>
        <?php
        echo    '<p id="username">
                    <a href="' . $user->getProfileLink() . '">' . $user->getDisplayName() . 
                    '</a><span class="role">' . get_role($user->getRole()) . '</span></p>';
        ?>
        </div>
        <ul id="navbar">
            <li><a href="/index.php">Home</a></li>
            <li><a href="/groups/">Community</a></li>
            <!--<li><a href="/ranking/">Ranks</a></li>-->
            <li><a href="/tourney/">Tourneys</a></li>
            <div class="lower-bar">
                <li><a href="/users/settings.php">Settings <i class="material-icons" >settings</i></a></li>
                <li><a href="/landing.php">Logout <i class="material-icons" >exit_to_app</i></a>
            </div>
        </ul>
    </div>
