<?php
require_once('Session.php');
require_once('Db.php');
require_once('Member.php');
require_once('Logger.php');
require_once('Friend.php');

// Various redirects used to bring people back to the page
// they originally processed the command on
function processRedirect($m = null) {
    
    $ref = $_SERVER['HTTP_REFERER'];
    if(strpos($ref, 'admin.php') > 0) {
        header('Location: admin.php');
        exit();
    }
    // Return the user to the search page if they were searching
    if (isset($_GET['search'])) {
        header('Location: search.php?s=' . urldecode($_GET['search']));
        exit();
    }
    if($m != null) {
        header('Location: user.php?u='.$m->uid);
        exit();
    }
    header('Location: /groupa/');
    exit();

}

// If the user is not logged in, boot them
$s = new Session();
if (!$s->isLoggedIn()) {
    header('Location: login.php');
    exit();
}

require_once('header.php');

// Start the DB connection
$db = new Db();
$log = new Logger();
$err = array();

// Link error array
$lerr = array();

// Get the uid we're viewing the profile for
// by default, we'll view our own profile.
if (isset($_GET['u'])) {
    $uid = $_GET['u'];
} else
    $uid = $s->getUid();


/**
 * INTERESTS
 * =========
 */
// Gather a list of all interests for table display
$query = 'SELECT IID,INAME,IICON FROM INTEREST_TYPE';
$res = $db->select($query);
$interests = array();
if ($res == null)
    $res = array();
foreach ($res as $val) {
    array_push($interests, $val);
}
// Create string list for iteration on user interests
$allints = '';
foreach ($interests as $i) {
    $allints .= strtoupper($i['INAME']) . ',';
}


// Gather list of user's selected interests
$query = 'select INAME,IICON from MEMBER_INTEREST join INTEREST_TYPE on MEMBER_INTEREST.IID = INTEREST_TYPE.IID where UID=' . $db->scrub($uid) . ' order by INAME';
$res = $db->select($query);
$userinterests = array();
if ($res == null)
    $res = array();
foreach ($res as $val) {
    array_push($userinterests, $val);
}

// Create string list for checking boxes on your own page
$uidint = '';
foreach ($userinterests as $i) {
    $uidint .= strtoupper($i['INAME']) . ',';
}

// Get the approprate display name for general use on page
if ($uid == $s->getUid()) {
    $m = new Member();
    $name = $m->getDisplayName();
} else {
    // If we're looking at someone else's profile, we'll need their
    // display name
    $m = new Member($uid);
    $name = $m->getDisplayName();
}


// Handle any actions that may be as a result of a postback
if (isset($_POST['action'])) {

    // Hold the values for updating data
    $data = array();

    // User wants to update their profile
    if (strtoupper($_POST['action']) == 'UPDATE') {

        // If they want to set their password then we'll error check that
        if (!empty($_POST['password'])) {
            // Check for errors on password
            if ($_POST['password'] != $_POST['password2']) {
                array_push($err, 'Passwords do not match.');
            }

            if (strlen($_POST['password']) < 6) {
                array_push($err, 'Password must be at least 6 characters');
            }
            // If no errors, queue it up
            if (sizeof($err) == 0) {
                $data['PASSWORD'] = $_POST['password'];
            }
        }

        if(strlen($_POST['email']) == 0) {
            array_push($err, 'E-mail address cannot be blank');
        }
        
        if (!preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $_POST['email'])) {
            array_push($err, 'Invalid e-mail address');
        }
                
        if($m->accountExists($_POST['email']) && $_POST['email'] != $s->getEmail()) {
            array_push($err, 'That e-mail address already exists on file');
        }

        // Make sure there are no errors and either you're updating yourself
        // or you're an admin
        if (sizeof($err) === 0 &&
                ($uid == $s->getUid() || $s->isAdmin())) {
                    
            $data['FNAME'] = $_POST['fname'];
            $data['LNAME'] = $_POST['lname'];
            $data['EMAIL'] = $_POST['email'];
            $data['UID'] = $s->getUid();
            
            // If you're an admin updating someone else's profile, do not
            // log them in, just update the data
            $m->updateAccount($data);
            header('Location: user.php?u='.$m->uid);
            exit();
        }
    }

    // User wants to update their interests
    if (strtoupper($_POST['action']) == 'INTERESTS') {
        // Make sure you're playing with your own interests or
        // or you're an admin
        if ($uid == $s->getUid() || $s->isAdmin()) {
            // First, drop all their interests
            $query = 'DELETE FROM MEMBER_INTEREST WHERE UID=' . $db->scrub($uid);
            $db->select($query);
            $uidint = '';
            // Now add the new interests         
            foreach ($_POST as $k => $v) {
                // If POST has other garbage, ignore it
                if (strpos($allints, strtoupper($k)) !== false) {
                    $query = 'INSERT INTO MEMBER_INTEREST VALUES (\'' . $uid . '\',\'' . $v . '\')';
                    $res = $db->select($query);
                    $uidint .= strtoupper($k) .',';
                }
            }

        }
    }
    
    // User wants to submit a new link
    if (strtoupper($_POST['action']) == 'LINK') {
        // Make sure you are only updating your own link
        if($uid == $s->getUid()) {
            
            // Validate the passed values
            $lurl = $_POST['url'];
            $ldesc = $_POST['desc'];
            $ltype = $_POST['type'];
            
            // Make sure URL is valid
            if(!preg_match("/^http/", $lurl)) {
                array_push($lerr,'Please include http:// or https:// in your link');
            }
            
            // Make sure the description has something
            if(strlen($ldesc) <= 5) {
                array_push($lerr,'Your link description is too short');
            }
            
            // Make sure they made a description
            if($ldesc == 'Link Description') {
                array_push($lerr,'You must provide a link description');
            }
            
            // Make sure they choose a link type
            if($ltype == 'None') {
                array_push($lerr,'You must choose a link type');
            }
            
            // Make the change
            if(sizeof($lerr) == 0) {
                $query = "INSERT INTO LINK VALUES (0,".$ltype.",".$s->getUid().",now(),'".$db->scrub($ldesc)."','".$db->scrub($lurl)."')";
                $res = $db->select($query);
                $log->log($s->getUid().' created a new link: '.$ldesc.' <'.$lurl.'>',$_SERVER['REMOTE_ADDR'],'INFO');
                header('Location: user.php?u='.$s->getUid());
                exit();
            }
        }
    }
}

// Handle any GET actions
if(isset($_GET)) {
    // User wants to add a friend
    if(isset($_GET['f'])) {
        $friend = new Friend();
        // Add a friend
        if(strtoupper($_GET['f']) == 'ADD') {
            $result = $friend->addFriend($s->getUid(),$uid);
            processRedirect($m);
        }       
        
        // Remove a friend
        if(strtoupper($_GET['f']) == 'REMOVE') {
           $result = $friend->delFriend($s->getUid(),$uid);           
           processRedirect($m);
        }
    }
    
    // Toggle the ban status of a user
    // Make sure the user is an admin and are not banning themselves
    if(isset($_GET['ban']) && $s->isAdmin() && $uid != $s->getUid()) {
        $ref = $_SERVER['HTTP_REFERER'];
        
        $result = $m->toggleBanStatus($s->getUid());
        
        // Redirect to appropriate source
        processRedirect($m);
        
    }
    
    // User wants to remove a link
    if(isset($_GET['remove'])) {
        
        // Must be an admin or own the link
        if($s->isAdmin() || $uid == $s->getUid()) {
            $linkid = $_GET['lid'];            
            $ref = $_SERVER['HTTP_REFERER'];
                        
            $query = 'DELETE FROM LINK WHERE LID = '.$db->scrub($linkid);
            $db->select($query);
            $log->log(($s->isAdmin()?"Admin ":"User ").$s->getUid().' deleted link: '.$linkid,$_SERVER['REMOTE_ADDR'],'INFO');

            processRedirect($m);
                    
            }
    }
}


// Gather a list of your friends
$f = new Friend();
$res = $f->getFriends($uid);
$friends = array();
if ($res == null)
    $res = array();
foreach ($res as $val) {
    array_push($friends, $val);    
}


//Check to see if sort is being passed. If not set it to date_desc
if(!isset($_GET['sort'])) {
	$_GET['sort'] = 'date_desc';
}

//Set what the query will order by and if Ascending or Descending.
switch($_GET['sort']) {
	case 'name_asc':
		$sql_orderBy = 'DESCRIPTION ASC';
		break;
	case 'name_desc':
		$sql_orderBy = 'DESCRIPTION DESC';
		break;
	case 'date_asc':
		$sql_orderBy = 'POST_DATE ASC';
		break;
    default:
        $sql_orderBy = 'POST_DATE DESC';
}

// Gather a list of my links
$query = 'select DESCRIPTION, POST_DATE, LID,URL from LINK where LINK.UID = ' . $db->scrub($uid) . ' order by '.$db->scrub($sql_orderBy);
$res = $db->select($query);
if ($res == null)
    $res = array();
$links = array();
foreach ($res as $val) {
    array_push($links, $val);
}

// Gather the link types, only if you're looking at your own profile
if($uid == $s->getUid()) {
    $query = 'SELECT * FROM LINK_TYPE';
    $res = $db->select($query);
    if($res == null)
        $res = array();
    $linktypes = array();
    foreach($res as $val) {
        array_push($linktypes,$val);
    }     
}
?>
<div id="page">
    <div id="content">
        <h2 class="title"><img src="images/link.gif" height="32" width="32" alt="link"><?= (($uid == $s->getUid()) ? "&nbsp;Your Links" : "&nbsp;Links by " . $name) ?></h2>        
        <hr style="margin: 0;"/>
        <div id="userrecentlinks">
            <table>
                <thead>
                    <tr>
                    <?php
                            if ($sql_orderBy =='DESCRIPTION DESC'){
                                    print ('<th class="uid"><a href="user.php?uid='.$uid.'&sort=name_asc">');
                            }
                            else {
                                    print ('<th class="uid"><a href="user.php?uid='.$uid.'&sort=name_desc">');
                            }
                            print ('Description</a></th>');
                            if ($sql_orderBy =='POST_DATE DESC'){
                                    print ('<th class="uid"><a href="user.php?uid='.$uid.'&sort=date_asc">');
                            }
                            else {
                                    print ('<th class="uid"><a href="user.php?uid='.$uid.'&sort=date_desc">');
                            }
                        print ('Date</a></th>');
                            print ('<th>Actions</th>');
                    ?> 
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $mod = 0;
                        if (sizeof($links) == 0) {
                            ?><tr class="odd"><td colspan="5" class="textcenter"><?= (($uid == $s->getUid()) ? "You haven't" : $name . " hasn't") ?> submitted any links yet!</td></tr><?php
                        }
                        foreach ($links as $link) {

                            // Row Highlighting
                            print('<tr' . (($mod++ % 2) == 0 ? " class=\"odd\"" : "") . '>');

                            // Description
                            $href = $link['DESCRIPTION'];
                            print('<td><a href="'.$link['URL'].'" target="_blank">' . $href . '</a></td>');

                            // Submission Date
                            // Change date from MYSQL format to Prefered Date/Time format
                            $datetime = strtotime($link['POST_DATE']);
                            $sub_date = date("m/d/Y", $datetime);	
                            print('<td>' . $sub_date . '</td>');
                            print('<td><a href="' . $link['URL'] . '" target="_blank"><img src="images/32magnify.png" alt="View" />&nbsp;View</a>&nbsp;');
                            if ($uid == $s->getUid() || $s->isAdmin()) {
                                print('<a href="?u='.$uid.'&remove=true&lid=' . $link['LID'] . '"><img src="images/redx.png" alt="Delete" />&nbsp;Delete</a>&nbsp;</td>');
                            }
                            print('</tr>');
                        }
                    ?>
                </tbody>
                <tfoot></tfoot>
            </table>
            <?php if(sizeof($lerr) > 0): ?>
                <div class="error">
                    <ul>
                    <?php 
                        foreach($lerr as $v) {
                            print("<li>$v</li>");
                        }
                    ?>
                    </ul>
                </div>                
            <?php endif; ?>
            <?php if($uid == $s->getUid()): ?>
                <h3 style="padding-top:10px;">Add a link</h3>
                <div id="newlink">
                    <div>&nbsp;</div>
                    <form method="POST">
                        <input type="hidden" name="action" value="link" />
                        <label for="linkdesc">&nbsp;</label><input type="text" class="txt" name="desc" onfocus="removeGhostText(this,'Link Description')" onblur="restoreGhostText(this,'Link Description')"  id="linkdesc" value="Link Description" />
                        <label for="linkurl">&nbsp;</label><input type="text" class="txt" name="url" id="linkurl" onfocus="removeGhostText(this,'Link URL')" onblur="restoreGhostText(this,'Link URL')"  value="Link URL" />
                        <label for="linktype">&nbsp;</label>
                        <select name="type" id="type" onfocus="changeColor(this,'#000000');">
                            <option value="None">Choose a link type</option>
                            <?php 
                                foreach($linktypes as $linktype) { 
                                    print('<option value="'.$linktype['LTID'].'">'.$linktype['LTNAME'].'</option>');
                                }
                            ?>
                        </select>
                        <input type="submit" class="button orange" value="Submit Link" />
                    </form>
                </div>
            <?php endif; ?>
        </div>      
        <h2 class="title"><img src="images/interests.png" height="32" width="32" alt="link"><?= ($uid == $s->getUid() ? "&nbsp;Your Interests" : "&nbsp Interests for " . $name) ?></h2>        
        <hr style="margin: 0;"/>
        <div id="userinterests">
            <form method="POST">
                <table>
                    <thead>
                    </thead>
                    <tbody>
                        <?php
                            // Output the interests set by the user
                            if (sizeof($interests) == 0) {
                                print('<tr><td colspan="3"><p>You haven\'t set any interests yet</p></td></tr>');
                            } else {
                                $mod = 0;
                                foreach ($interests as $val) {
                                    // Setup new row if necessary
                                    if ($mod++ % 3 == 0) {
                                        print('</tr><tr>');
                                    }

                                    // Setup the icon for the interest
                                    if (empty($val['IICON'])) {
                                        $img = 'interests.png';
                                    } else {
                                        $img = $val['IICON'];
                                    }

                                    // Output the cell
                                    print('<td');
                                    if($uid != $s->getUid()) {
                                        if(strpos($uidint,strtoupper($val['INAME'])) !== false)                                                
                                            //print(' style="background-color: #D4FFE4"');
                                            print(' class="interested"');
                                        else print(' class="uninterested"');
                                    }
                                    print('>');

                                    // Enable the checkbox option if the user is looking at their own profile
                                    if ($uid == $s->getUid()) {
                                        print('&nbsp;');
                                        print('<input type="checkbox" id="' . $val['INAME'] . '" value="' . $val['IID'] . '" name="' . $val['INAME'] . '" ');
                                        if (strpos($uidint, strtoupper($val['INAME'])) !== false)
                                            print('checked="checked" ');
                                        print('/>');
                                    }

                                    print('&nbsp;&nbsp;<img src="images/' . $img . '" alt="'.$val['INAME'].'" class="icon32" />');
                                    print('&nbsp;' . $val['INAME'] . '</td>');
                                }
                            }
                        ?>

                    </tbody>
                    <tfoot></tfoot>
                </table>
                <div>&nbsp;</div>
                <?php if ($uid == $s->getUid()):// || $s->isAdmin()): ?> 
                    <input type="hidden" name="action" value="interests" />
                    <input type="submit" class="button orange" value="Update Interests" />
                <?php endif; ?>
            </form>
        </div>
            <?php if ($uid == $s->getUid() || $s->isAdmin()): ?>
                <h2 class="title"><img src="images/default_profile.png" height="32" width="32" alt="link">&nbsp;<?= (($uid == $s->getUid()) ? "Your Profile" : "Profile for " . $name) ?></h2>        
                <hr style="margin: 0 0 25px 0;"/>
                <div id="login_form">
                    <?php
                        if(sizeof($err) > 0) {
                            print('<ul class="error">');
                            foreach($err as $e) {
                                print('<li>'.$e.'</li>');
                            }
                            print('</ul>');
                        }

                    ?>    
                    <form method="POST">
                        <label for="fname">First Name</label>
                        <input type="text" class="txt" name="fname" id="fname" value="<?= $m->fname ?>" />
                        <label for="lname">Last Name</label>
                        <input type="text" class="txt" name="lname" id="lname" value="<?= $m->lname ?>" />
                        <label for="email">E-mail</label>
                        <input type="text" class="txt" name="email" id="email" value="<?= $m->email ?>" />
                        <label for="password">Change Password</label>
                        <input type="password" class="txt" name="password" id="password" value="" />
                        <label for="password2">Confirm</label>
                        <input type="password" class="txt" name="password2" id="password2" value="" />                    
                        <input type="hidden" name="action" value="update" />
                        <input type="submit" value="Update Profile" class="button orange" />                    
                    </form>
                    <?php if ($s->isAdmin() && $s->getUid() != $uid): ?>                        
                        <form method="GET">
                            <div>&nbsp;</div>
                            <input type="hidden" value="toggle" name="ban" />
                            <input type="hidden" value="<?= $uid ?>" name="u" />
                            <input type="submit" value="<?= (!empty($m->isbanned)?"Unb":"B") ?>an this user" class="button orange" />
                        </form>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
    </div>
    <div id="sidebar">
        <ul>
            <?php if($s->isAdmin() && $m->isBanned()): ?>
            <li>
                    <span class="banner">This user is banned</span>
            </li>                
            <?php endif; ?>
            <li>  
                <p class="name"><img src="images/default_profile.png" class="icon64" alt="Profile"/><a href="user.php?u=<?= $uid ?>"><?= $name ?></a></p>
            </li>
            <?php if($s->getUid() != $uid && !($m->isFriend($s->getUid(), $uid))): ?>
            <li class="addfriendbutton">
                <form method="GET">
                    <input type="hidden" name="f" value="add" />
                    <input type="hidden" name="u" value="<?= $uid ?>" />
                    <input type="submit" value="Add Friend" class="button blue" />
                </form>
                
            </li>
            <?php endif; ?>
            <?php if($s->getUid() != $uid && ($m->isFriend($s->getUid(), $uid))): ?>
            <li class="addfriendbutton">
                <form method="GET">
                    <input type="hidden" name="f" value="remove" />
                    <input type="hidden" name="u" value="<?= $uid ?>" />
                    <input type="submit" value="Remove Friend" class="button red" />
                </form>                
            </li>
            <?php endif; ?>       
            <?php if ($uid != $s->getUid()): ?>
                <li class="addfriendbutton">
                    <ul>
                        <form method="GET" action="user.php">
                            <input type="hidden" name="uid" value="<?= $s->getUid() ?>" /><input type="submit" class="button orange" value="View My Profile" />                                                
                        </form>
                    </ul>
                </li>
            <?php endif; ?>
            <li>
                <h2><?= ($uid == $s->getUid() ? "Your" : $m->getShortName()."'s") ?> Friends</h2>
                    <?php
                    // First check if the user has any friends
                    if (sizeof($friends) == 0) {
                        print('<p>'.($uid != $s->getUid()?$name." has not":"You haven't").' added any friends yet</p>');
                    } else {
                        // Output a list of the friends that was setup above
                        print('<ul>');
                        foreach ($friends as $friend) {
                            if (empty($friend['FNAME'])) {
                                $name = explode('@',$friend['EMAIL']);
								$name=$name[0];
                            } else {
                                $name = $friend['FNAME'] . ' ' . $friend['LNAME'];
                            }
                            print('<li><a href="?u=' . $friend['UID'] . '"><img src="images/friend.png" alt="' . $name . '" class="icon16" />&nbsp;' . $name . '</a></li>');
                        }
                        print('</ul>');
                    }
                    ?>
            </li>
            <?php if ($uid == $s->getUid()): ?>
                <li>
                    <div id="friendsearch">
                        <form method="get" action="search.php">
                            <label for="search-text">&nbsp;</label><input type="text" name="s" id="search-text" class="searchbox" onfocus="removeGhostText(this)" onblur="restoreFriendText(this)" value="Find friends..." />
                            <input type="submit" id="search-submit" value="GO" class="hide" />
                            <input type="hidden" name="t" value="users" />
                        </form>
                    </div>
                    <div class="clear">&nbsp;</div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="clear">&nbsp;</div>
            <?php require_once('footer.php'); ?>

