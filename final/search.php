<?php 

    require_once('Session.php');
    
    
    // If the user is not logged in, boot them
    $s = new Session();
    if(!$s->isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
    
    
    require_once('Searcher.php');
    require_once('Member.php');
    $m = new Member();
    $s = new Searcher();
    $term = $_GET['s'];
	if(!isset($_GET['t'])) {
        $search_type = 'all';
    } else $search_type = $_GET['t'];
	if(!isset($_GET['sort_user'])) {
		$sort_user = 'date_desc';
	} else $sort_user = $_GET['sort_user'];
	if(!isset($_GET['sort_link'])) {
		$sort_link = 'date_desc';
	} else $sort_link = $_GET['sort_link'];

	$search_type = strtoupper($search_type);
	
	
	// Gather the search results based on search type
    switch ($search_type) {
        case 'USERS':
            $users = $s->userSearch($term,$sort_user);
            break;
        case 'LINKS':
            $links = $s->linkSearch($term,$sort_link);
            break;            
        default:
            $links = $s->linkSearch($term,$sort_link);
            $users = $s->userSearch($term,$sort_user);           
    }
    
    
    
    require_once('header.php');
    
?>
<div id="page">
    <div id="content_text">
        <div id="searchresult">
            <h1>Search Results</h1>
            <form method="GET">
                <label for="searchtxt">&nbsp;</label>
                <input type="text" id="searchtxt" class="searchbox" onblur="restoreGhostText(this)" onfocus="removeGhostText(this)" value="Search..." name="s" />                
                <input type="submit" id="submit" class="hide"/>
            </form>

        </div>
        <p>You searched for <a class="orangelink" href="?s=<?= $term ?>"><?= $term ?></a></p>
		
		   <?php if(strpos($search_type,'USERS') === 0 || $search_type == 'ALL'): ?>
            <h2 class="title"><img src="images/user.png" height="32" width="32" alt="users">Users</h2>        
            <hr style="margin: 0;"/>
            <div id="userlist">
                <table>
                    <thead>
                        <tr>
<?php
	if ($sort_user =='name_desc'){
		print ('<th><a href="search.php?s='.$term.'&sort_user=name_asc&t='.$search_type.'">');
	}
	else {
		print ('<th><a href="search.php?s='.$term.'&sort_user=name_desc&t='.$search_type.'">');
	}
print ('Name</a></th>');

	if ($sort_user =='date_desc'){
		print ('<th><a href="search.php?s='.$term.'&sort_user=date_asc&t='.$search_type.'">');
	}
	else {
		print ('<th><a href="search.php?s='.$term.'&sort_user=date_desc&t='.$search_type.'">');
	}
print ('Signup Date</a></th>');
?>
                            
                                               
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(sizeof($users) == 0) {
                                ?><tr class="odd"><td colspan="3" class="textcenter">No users were found</td><?php
                            }

                            $mod = 0;
							
                            foreach($users as $user) {
                                // Row Highlighting
                                print('<tr'.(($mod++ % 2)==0?" class=\"odd\"":"").'>');

                                // Display full name or first part of email      
                                $displayName = $user['FNAME'].' '.$user['LNAME'];
                                if(strlen($displayName) <= 1) {
                                    $email = explode('@',$user['EMAIL']);                                                                    
                                    $displayName = $email[0];
                                }
                                print('<td><a class="userprofile" href="user.php?context=search&u='.$user['UID'].'">'.$displayName.'</a></td>');

                                // Signup Date
								//Change date from MYSQL format to Prefered Date/Time format
								$datetime = strtotime($user['SIGNUP_DATE']);
								$sub_date = date("m/d/Y", $datetime);
								print('<td>'.$sub_date.'</td>');
								
                                // Actions
                                print('<td><a class="userprofile" href="user.php?context=search&u='.$user['UID'].'"><img src="images/32magnify.png" class="imgicon" alt="Profile" />View Profile</a>&nbsp;');
                                
                                if($s->getUid() != $user['UID'] && !($m->isFriend($s->getUid(),$user['UID']))) {                                    
                                    print('<a href="user.php?u='.$user['UID'].'&f=add&search='.urlencode($term.'&t='.$search_type).'"><img src="images/user_add.png" class="imgicon" alt="Follow" />Add Friend</a>');
                                }
                                
                                if($s->getUid() != $user['UID'] && ($m->isFriend($s->getUid(),$user['UID']))) {
                                    print('<a href="user.php?u='.$user['UID'].'&f=remove&search='.urlencode($term.'&t='.$search_type).'"><img src="images/user_del.png" class="imgicon" alt="Follow" />Remove Friend</a>');
                                }
                                
                                
                                
                                //Check to see if admin. If so display Ban Action
                                if($s->isAdmin()){
                                    print('<a href="user.php?u='.$user['UID'].'&ban=true&search='.urlencode($term.'&t='.$search_type).'"><img src="images/'.(isset($user['ISBANNED'])?"checkuser.ico":"ban-user.png").'" class="imgicon" alt="Ban" />');
                                    print((isset($user['ISBANNED'])?"Unb":"B").'an User</a>');
                                    
                                }//end of if
                                
                                print('</td></tr>');
                            }

                        ?>
                    </tbody>
                    <tfoot>&nbsp;</tfoot>
                </table>         
            </div>
       <?php endif; ?>
		
        <?php if((strpos($search_type,'LINKS') === 0) || ($search_type == 'ALL')): ?>

            <h2 class="title"><img src="images/link.gif" height="32" width="32" alt="link">Links</h2>        
            <hr style="margin: 0;"/>
            <div id="recentlinks">
                <table>
                    <thead>
                        <tr>
<?php
	if ($sort_link =='name_desc'){
		print ('<th><a href="search.php?s='.$term.'&sort_link=name_asc&t='.$search_type.'">');
	}
	else {
		print ('<th><a href="search.php?s='.$term.'&sort_link=name_desc&t='.$search_type.'">');
	}
	print ('Name</a></th>');

	if ($sort_link =='name_desc'){
		print ('<th><a href="search.php?s='.$term.'&sort_link=desc_asc&t='.$search_type.'">');
	}
	else {
		print ('<th><a href="search.php?s='.$term.'&sort_link=desc_desc&t='.$search_type.'">');
	}
	print ('Description</a></th>');
	print ('<th>Link</th>');
	if ($sort_link =='date_desc'){
		print ('<th><a href="search.php?s='.$term.'&sort_link=date_asc&t='.$search_type.'">');
	}
	else {
		print ('<th><a href="search.php?s='.$term.'&sort_link=date_desc&t='.$search_type.'">');
	}
	print ('Submission Date</th>');
	?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $mod = 0;
                            if(sizeof($links) == 0) {
                                ?><tr class="odd"><td colspan="5" class="textcenter">No links were founds</td></tr><?php
                            }
                            foreach($links as $link) {

                                // Row Highlighting
                                print('<tr'.(($mod++ % 2)==0?" class=\"odd\"":"").'>');

                                // Output the displayname
                                $displayName = $link['FNAME'].' '.$link['LNAME'];
                                if(strlen($displayName) <= 1) {
                                    $displayName = explode('@',$link['EMAIL']);                                
                                }
                                print('<td><a href="user.php?u='.$link['UID'].'">'.$displayName.'</a></td>');

                                // Description
                                $href = $link['DESCRIPTION'];
                                print('<td><a href="'.$link['URL'].'" target="_blank">'.$href.'</td>');
                                // Link
                                $href_short = explode('/',$link['URL']);
                                print('<td><a href="'.$link['URL'].'" target="_blank">'.$href_short[2].'</a></td>');

                                // Submission Date
								//Change date from MYSQL format to Prefered Date/Time format
								$datetime = strtotime($link['POST_DATE']);
								$sub_date = date("m/d/Y", $datetime);
                                
                                print('<td>'.$sub_date.'</td>');
                                print('<td><a href="'.$link['URL'].'" target="_blank"><img src="images/32magnify.png" alt="View" />&nbsp;View</a>&nbsp;');
								if($s->isAdmin()){
									print('<a href="user.php?lid='.$link['LID'].'&uid='.$link['UID'].'&remove=true"><img src="images/redx.png" class="imgicon" alt="Remove" />Remove</a>');
									
								}//end of if
								print('</td></tr>');
                            }
                        ?>
                    </tbody>
                    <tfoot></tfoot>
                </table>
            </div>        
        <?php endif; ?>
     
    </div>
</div>
<?php require_once('footer.php'); ?>

