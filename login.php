<?php

require_once('Session.php');
require_once('Member.php');

$m = new Member();
$s = new Session();

$err = array();
$email = "";
$cbo = "";
// Is the user returning to this page after clicking a button?
if (isset($_POST) && !empty($_POST)) {
    
    // User is trying to log in
    if(strtoupper($_POST['action']) == 'LOGIN') {
        $result = $m->login($_POST['email'], $_POST['password']);
        if(!($result)) {
            array_push($err, 'Login failed');
        }
    }

    // User is trying to create an account
    if(strtoupper($_POST['action']) == 'SIGNUP') {
        
        // Make sure the passwords sent match
        $pw = $_POST['password'];
        $pw2 = $_POST['password2'];
        
        /** Check various errors
         *  Validate by e-mail address
         */
        if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST['email'])) {
            array_push($err, 'Invalid e-mail address');
        }
        
        if(strlen($_POST['email']) == 0) {
            array_push($err, 'E-mail address cannot be blank');
        }
        
        if($m->accountExists($_POST['email'])) {
            array_push($err, 'That e-mail address already exists on file');
        }
        
        // Check password
        if(strlen($pw) == 0) {
            array_push($err, 'Password cannot be left blank.');            
        }
        
        if(!($pw == $pw2)) {
            array_push($err, 'Passwords do not match.');            
        }
        
        // Check terms
        if(!isset($_POST['a'])) {
            array_push($err, 'You must agree to the terms and conditions');
        }
        
        // If stuff is valid, let's create the account
        if(sizeof($err) == 0) {
            $m->createNew($_POST['email'], $pw);
            header('Location: index.php');
            exit();
        } else {
            // Some invalid stuff, let's save appropriate values 
            // so they don't have to enter them again
            $email = " value=\"".$_POST['email']."\"";
            if(isset($_POST['a'])) {
                $cbo = " checked=\"checked\"";
            } else $cbo = "";
        }      
    }
}

// Did the user intend to logout?
if (isset($_GET['action']) && $s->isLoggedIn()) {
    if(strtoupper($_GET['action']) == 'LOGOUT') {
        // Log the user out and destroy the session
        $m->logout();
        
    }
}

// If the user is logged in, there's no reason for them to be here. This
// happens if the user hits the back button on the browser after logging in
if($s->isLoggedIn()) {
    header('Location: index.php');
    exit();
}


// Must output this last since we may potentially modify the header
require_once('header.php');


?>
<div id="page">
    <div id="content_text">
        <h1 class="title"><img src="images/okey.png" class="icon64" alt="&nbsp;" />Sign In</h1>
        <div class="clear">&nbsp;</div>
        <div class="line">&nbsp;</div>
        <div class="clear">&nbsp;</div>
        <?php if((sizeof($err) > 0) && strtoupper($_POST['action']) == 'LOGIN'): ?>
            <div class="error">
                <ul>
                <?php 
                    foreach($err as $v) {
                        print("<li>$v</li>");
                    }
                ?>
                </ul>
            </div>
        <?php endif; ?>
        <div id="login_form">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" id="frmLogin" onsubmit="return loginValidate()">
                <label for="name">E-mail</label>
                <input type="text" class="txt" id="username" name="email" onblur="contactCheckValid(this)" value="" />
                <label for="email">Password</label>
                <input type="password" class="txt" id="password" onblur="contactCheckValid(this)" name="password" value="" />
                <input type="hidden" name="action" value="login" />
                <input type="submit" class="button orange" id="submit" value="Log In" />
            </form>            
        </div>
        <p>&nbsp;</p>
        <h1 class="title"><img src="images/clip.png" class="icon64" alt="&nbsp;" />Sign Up</h1>
        <span>&nbsp;</span>
        <div class="line">&nbsp;</div>
        <?php if((sizeof($err) > 0) && strtoupper($_POST['action']) == 'SIGNUP'): ?>
            <div class="error">
                <ul>
                <?php 
                    foreach($err as $v) {
                        print("<li>$v</li>");
                    }
                ?>
                </ul>
            </div>
        <?php endif; ?>
        <div id="signup_form">
            <form action="<?= $_SERVER['PHP_SELF'] ?>#signup_form" method="POST">
                <label for="name">E-mail</label>
                <input type="text" class="txt" id="username" name="email" onblur="contactCheckValid(this)"<?= $email ?> />
                <label for="email">Password</label>
                <input type="password" class="txt" id="password" onblur="contactCheckValid(this)" name="password" value="" />
                <label for="email">Confirm Password</label>
                <input type="password" class="txt" id="password2" onblur="contactCheckValid(this)" name="password2" value="" />
                <input type="checkbox" id="agree" name="a" value="checked"<?= $cbo ?> /><span style="font-size: 14px;">I agree to the <a href="about.php#terms" target="_blank">terms and conditions</a>.</span>
                <input type="hidden" name="action" value="signup" />
                <label for="submit">&nbsp;</label>
                <input type="submit" class="button orange" id="submit" value="Sign Up" />
            </form>
        </div>

    </div>
    <?php require_once('footer.php'); ?>
    