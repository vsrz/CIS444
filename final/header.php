<?php
    require_once('Session.php');
    $s = new Session();
    $loggedIn = $s->isLoggedIn();
?><!DOCTYPE html >
<html lang="en-us">
    <head>
        <meta name="keywords" content="check it out, links" />
        <meta name="description" content="Check It Out is a social hyperlink feed" />
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <title>Check It Out!</title>
        <link href="css/style.css" rel="stylesheet" type="text/css" media="screen" />
        <link rel="shortcut icon" href="http://cis444.cs.csusm.edu/groupa/favicon.ico" />

        <script type="text/javascript" src="js/handlers.js"></script>
    </head>
    <body>
        <div id="wrapper">
            <div id="header-wrapper" class="container">
                <div id="header" class="container">
                    <div id="logo">
                        <h1><a href="/groupa/"><img src="images/checkmark.png" alt="checkmark" >check it out! </a></h1>
                    </div>
                    <div id="menu">
                        <ul>
<?php
	//Check to see if the user is an admin. If so display the admin link and change CSS to smaller padding to allow new link
	if ($s->isAdmin()){
		$a='<a class="admin"';
		}
	else{
		$a='<a ';
	}
	print ('<li>'.$a.' href="/groupa/">Home</a></li>');
	print ('<li>'.$a.' href="contact.php">Contact</a></li>');
    print ('<li>'.$a.' href="about.php">About</a></li>');
	print ('<li>'.$a.' href="help.php">Help</a></li>');
	if ($s->isAdmin()){
		print ('<li>'.$a.' href="admin.php">Admin</a></li>');
	}
	print ('<li>'.$a);
?>	
                            href="login.php<?= (!empty($loggedIn) ? "?action=logout" : "") ?>">
                                    Log <?= (empty($loggedIn) ? "In" : "Out") ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div><img src="images/img03.png" width="1000" height="40" alt="header" />
                </div>
            </div>
            <!-- end header.php -->