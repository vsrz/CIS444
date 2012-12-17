<?php

    require_once('Session.php');
    require_once('Db.php');
    
    // If the user is not logged in, boot them
    $s = new Session();
    if(!$s->isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
	


    require_once('header.php');
    
    // Start the DB connection
    $db = new Db();    

    // Gather list of your interests
    $query = 'select INAME,IICON from MEMBER_INTEREST join INTEREST_TYPE on MEMBER_INTEREST.IID = INTEREST_TYPE.IID where UID='.$s->getUid().' order by INAME';
    $res = $db->select($query);
    $interests = array();
    if($res == null) $res = array(); 
    foreach($res as $val) {
        array_push($interests, $val);        
    }
    
    // Gather a list of your friends
    $query = 'select MEMBER.EMAIL,MEMBER.FNAME,MEMBER.LNAME,MEMBER.UID from FRIEND join MEMBER on FRIEND.FID=MEMBER.UID where FRIEND.UID='.$s->getUid().' order by MEMBER.LNAME';
    $res = $db->select($query);
    $friends = array();
    if($res == null) $res = array(); 
    
    foreach($res as $val) {
        array_push($friends, $val);
    }
    
    // Gather a list of links
    $query = 'select LINK.UID,DESCRIPTION, POST_DATE, LTNAME,URL,MEMBER.FNAME,MEMBER.LNAME,LINK_TYPE.LICON from LINK join LINK_TYPE on LINK.LTYPE = LINK_TYPE.LTID join MEMBER on LINK.UID = MEMBER.UID join FRIEND on FRIEND.FID = LINK.UID where FRIEND.UID = '.$s->getUid().' order by POST_DATE DESC';
    $res = $db->select($query);
    if($res == null) $res = array(); 
    $links = array();
    foreach($res as $val) {
        array_push($links,$val);
    }
    
$return ="\r";
$tab = "\t";
    
?>
<div id="page">
    <div id="content">
        <?php
            // Output the links of your friends
            if(sizeof($links) == 0) {
                print('<p class="red40">None of your friends have posted any links.</p>');
            } else {
                foreach($links as $link) {
                    $link_icon = (empty($link['LICON'])?"link.gif":$link['LICON']);
                    print('<div class="post">');
                    print('<h2>');
                    print('<a href="'.$link['URL'].'" target="_blank">');
                    print('<img src="images/'.$link_icon.'" height="40" width="40" alt="link">');
                    print('&nbsp;'.$link['DESCRIPTION'].'</a></h2>');
                    print('<p class="meta"><span class="date">'.date("F d, Y",strtotime($link['POST_DATE'])).'</span>');
                    print('<span class="posted">Posted by <a href="user.php?u='.$link['UID'].'">'.$link['FNAME'].' '.$link['LNAME'].'</a></span></p>');
                    print('<div class="clear">&nbsp;</div></div>');
					print($return.$tab.$tab);
                }
            }
        ?>
    </div>
    <div id="sidebar">
        <ul>
            <li>
                <div id="search" >
                    <form method="get" action="search.php">
                        <label for="search-text">&nbsp;</label><input type="text" name="s" id="search-text" class="searchbox" onfocus="removeGhostText(this)" onblur="restoreGhostText(this)" value="Search..." />
                        <input type="submit" id="search-submit" value="GO" class="hide" />
                    </form>
                </div>
                
            </li>
            <div class="clear">&nbsp;</div>
            <li>
                <form method="GET" action="user.php">
				<table>
				<tr>
					<td rowspan="2"><img src="images/default_profile.png" class="icon64" alt="Profile"/></td>
					<td><h2 style="padding-left: 0;">
					<?=
						//Get first name and last name
						(strpos($s->getDisplayName(),'@')===false?$s->getDisplayName():substr($s->getDisplayName(),0,strpos($s->getDisplayName(),'@')));
					?></h2>
					</td>
				</tr>
				<tr>
					<td>           
                    <input type="hidden" name="uid" value="<?= $s->getUid() ?>" /><input type="submit" class="button orange" value="View My Profile" />                                                
					</td>
					</tr>

   
				</table>
            </li>
			<div class="clear">&nbsp;</div>
			<div class="clear">&nbsp;</div>
            <li>
                <h2>Your Interests</h2>
                    <?php
                        // Output the interests set by the user
                        if(sizeof($interests) == 0) {
                            print('<p class="red14">You haven\'t set any interests yet</p>');
                        } else {
                            print('<ul>');
                            foreach($interests as $val) {
                                if(empty($val['IICON'])) {
                                    $img = 'interests.png';
                                } else {
                                    $img = $val['IICON'];
                                }

                                print('<li><img src="images/'.$img.'" alt="'.$val['INAME'].'" class="icon16" />');
                                print('&nbsp;'.$val['INAME'].'</li>');
                            }
                            print('</ul>');
                        }
                        
                    ?>
                
            </li>
            <li>
                <h2>Your Friends</h2>
                <?php
                    // First check if the user has any friends
                    if(sizeof($friends) == 0) {
                        print('<p class="red14">You haven\'t added any friends yet</p>');
                    } else {
                        // Output a list of the friends that was setup above
                        print('<ul>');
                        foreach($friends as $friend) {
                            if(empty($friend['FNAME'])) {
                                $name = explode('@',$friend['EMAIL']);
								$name=$name[0];
                            } else {
                                $name = $friend['FNAME'].' '.$friend['LNAME'];
                            }
                            print('<li><a href="user.php?u='.$friend['UID'].'"><img src="images/friend.png" alt="'.$name.'" class="icon16" />&nbsp;'.$name.'</a></li>');
                            
                        }
                        print('</ul>');
                    }
                ?>
            </li>
        </ul>
    </div>
    <div class="clear">&nbsp;</div>
<?php require_once('footer.php'); ?>

