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

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script type="text/javascript" src="js/handlers.js"></script>
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics_debug.js','ga');

            ga('create', 'UA-35755533-1', {
                'cookieDomain' : 'none'
            });

            $(function() {
                function getURLParameter(url, name) {
                    return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
                }

                $('a.userpost').on('click', function() {
                    var url = $(this).attr('href');
                    var uval = $(this).data('index');
                    ga('send', 'event', 'userpost', 'click', uval.toString());
                });

                $('a.userprofile').on('click', function() {
                    var url = $(this).attr('href');
                    var uval = getURLParameter(url, 'u');
                    var context = getURLParameter(url, 'context');
                    ga('send', 'event', 'userprofile', 'click', uval.toString());
                });
            });
        </script>
    </head>
    <body>
        <div id="wrapper">
            <div id="header-wrapper" class="container">
                <div id="header" class="container">
                    <div id="logo">
                        <h1><a href="/?context=banner"><img src="images/checkmark.png" alt="checkmark" >check it out! </a></h1>
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
	print ('<li>'.$a.' href="/?context=header">Home</a></li>');
	print ('<li>'.$a.' href="contact.php?context=header">Contact</a></li>');
    print ('<li>'.$a.' href="about.php?context=header">About</a></li>');
	print ('<li>'.$a.' href="help.php?context=header">Help</a></li>');
	if ($s->isAdmin()){
		print ('<li>'.$a.' href="admin.php?context=header">Admin</a></li>');
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
