<?php require_once('header.php'); 
	if(!($s->isAdmin())) {
		header('Location: index.php');
		exit();
	}
	require_once('Db.php');
	$db = new Db();
	
//Check to see if sort_user is being passed. If not set it to date_desc
if(!isset($_GET['sort_user'])) {
	$_GET['sort_user'] = 'date_desc';
}

//Set what the query will order by and if Ascending or Descending.
switch($_GET['sort_user']) {
	case 'uid_asc':
		$sql_orderUserBy = 'UID ASC';
		break;
	case 'uid_desc':
		$sql_orderUserBy = 'UID DESC';
		break;
	case 'name_asc':
		$sql_orderUserBy = 'FNAME ASC';
		break;
	case 'name_desc':
		$sql_orderUserBy = 'FNAME DESC';
		break;
   	case 'email_asc':
		$sql_orderUserBy = 'EMAIL ASC';
		break;
	case 'email_desc':
		$sql_orderUserBy = 'EMAIL DESC';
		break;
	case 'date_asc':
		$sql_orderUserBy = 'SIGNUP_DATE ASC';
		break;
    default:
        $sql_orderUserBy = 'SIGNUP_DATE DESC';
}//end of switch

//Gather list of the last 15 users that signed up by default.
//If sort_user is being passed then set the query to the column to be sorted by and ascending or descending
$query = "SELECT UID,FNAME,LNAME,EMAIL,SIGNUP_DATE,ISBANNED FROM MEMBER ORDER BY ".$db->scrub($sql_orderUserBy)." LIMIT 15 ";
$res = $db->select($query);
$latestuser = array();
if ($res == null)
    $res = array();
foreach ($res as $val) {
    array_push($latestuser, $val);
}

//Check to see if sort_link is being passed. If not set it to date_desc
if(!isset($_GET['sort_link'])) {
	$_GET['sort_link'] = 'date_desc';
}

//Set what the query will order by and if Ascending or Descending.
switch($_GET['sort_link']) {
	case 'uid_asc':
		$sql_orderLinkBy = 'UID ASC';
		break;
	case 'uid_desc':
		$sql_orderLinkBy = 'UID DESC';
		break;
	case 'name_asc':
		$sql_orderLinkBy = 'FNAME ASC';
		break;
	case 'name_desc':
		$sql_orderLinkBy = 'FNAME DESC';
		break;
   	case 'title_asc':
		$sql_orderLinkBy = 'DESCRIPTION ASC';
		break;
	case 'title_desc':
		$sql_orderLinkBy = 'DESCRIPTION DESC';
		break;
	case 'date_asc':
		$sql_orderLinkBy = 'POST_DATE ASC';
		break;
    default:
        $sql_orderLinkBy = 'POST_DATE DESC';
}//end of switch

//Gather list of the last 15 links that have been submitted by default
//If sort_user is being passed then set the query to the column to be sorted by and ascending or descending
$query = "SELECT LINK.UID,LID,FNAME,LNAME,EMAIL,DESCRIPTION,URL,POST_DATE FROM LINK JOIN MEMBER ON LINK.UID = MEMBER.UID ORDER BY ".$db->scrub($sql_orderLinkBy)." LIMIT 15 ";
$res = $db->select($query);
$latestlink = array();
if ($res == null)
    $res = array();
foreach ($res as $val) {
    array_push($latestlink, $val);
}
?>

<div id="page">
    <h1 class="textcenter">Administrator View</h1>
    <div class="admheading"><img src="images/signup.png" alt="Signup"/><span>Recent Signups</span></div>
    <div id="userlist">
        <table>
            <caption>&nbsp;</caption>
            <thead>
                <tr>
<?php
	if ($sql_orderUserBy =='UID DESC'){
		print ('<th class="uid"><a href="admin.php?sort_user=uid_asc">');
	}
	else {
		print ('<th class="uid"><a href="admin.php?sort_user=uid_desc">');
	}
	print ('User ID</a></th></div>');
	if ($sql_orderUserBy =='FNAME DESC'){
		print ('<th class="name"><a href="admin.php?sort_user=name_asc">');
	}
	else {
		print ('<th class="name"><a href="admin.php?sort_user=name_desc">');
	}
	print ('Name</a></th>');
	if ($sql_orderUserBy =='EMAIL DESC'){
		print ('<th class="email"><a href="admin.php?sort_user=email_asc">');
	}
	else {
		print ('<th class="email"><a href="admin.php?sort_user=email_desc">');
	}
	print ('E-Mail</a></th>');
	if ($sql_orderUserBy =='SIGNUP_DATE DESC'){
		print ('<th class="signup"><a href="admin.php?sort_user=date_asc">');
	}
	else {
		print ('<th class="signup"><a href="admin.php?sort_user=date_desc">');
	}
	print ('Signup Date</a></th>');
?>
                    <th class="actions">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
$mod=0;
foreach ($latestuser as $latestusers){
        // Row Highlighting
        print('<tr' . (($mod++ % 2) == 0 ? " class=\"odd\"" : "") . '>');
		//Change date from MYSQL format to Prefered Date/Time format
		$datetime = strtotime($latestusers['SIGNUP_DATE']);
		$sub_date = date("m/d/Y", $datetime);
		//Display UserID, Name, E-Mail, Signup Date
		print ('
					<td><a href="user.php?u='.$latestusers['UID'].'">'.$latestusers['UID'].'</a></td>
					<td><a href="user.php?u='.$latestusers['UID'].'">'.(empty($latestusers['FNAME'])?$latestusers['EMAIL']:$latestusers['FNAME'].' '.$latestusers['LNAME']).'</a></td>
					<td><a href="user.php?u='.$latestusers['UID'].'">'.$latestusers['EMAIL'].'</a></td>
					<td>'.$sub_date.'</td>
					<td>
						<a href="user.php?u='.$latestusers['UID'].'"><img src="images/32magnify.png" class="imgicon" alt="Profile" />View Profile</a>
						<a href="user.php?u='.$latestusers['UID'].'&ban=true">');
                print('<img src="images/');
                print((isset($latestusers['ISBANNED'])?"checkuser.ico":"ban-user.png").'" class="imgicon" alt="Ban" />');
                print((isset($latestusers['ISBANNED'])?"Unb":"B").'an User</a>
					</td>
				</tr>');
}//end of foreach
?>				
            </tbody>
            <tfoot></tfoot>
        </table> 
	
        <div id="usersearch">
                        <form method="get" action="search.php">
                            <label for="search-text">&nbsp;</label><input type="text" name="s" id="search-text" class="searchbox" onfocus="removeGhostText(this)" onblur="restoreGhostText(this,'Find User')" value="Find User" />
                            <input type="submit" id="search-submit" value="GO" class="hide" />
                            <input type="hidden" name="t" value="users" />
            </form>
        </div>
    </div>
    <div class="admheading">
	<a id="links">
        <img src="images/link.gif" alt="link" />
        <span>Recently Submitted Links</span>
    </div>

    <div id="recentlinks">
        <table>
            <caption>&nbsp;</caption>
            <thead>
                <tr>
<?php
	if ($sql_orderLinkBy =='UID DESC'){
		print ('<th class="uid"><a href="admin.php?sort_link=uid_asc#links">');
	}
	else {
		print ('<th class="uid"><a href="admin.php?sort_link=uid_desc#links">');
	}
	print ('User ID</a></th></div>');
	if ($sql_orderLinkBy =='FNAME DESC'){
		print ('<th class="name"><a href="admin.php?sort_link=name_asc#links">');
	}
	else {
		print ('<th class="name"><a href="admin.php?sort_link=name_desc#links">');
	}
	print ('Name</a></th>');
	if ($sql_orderLinkBy =='DESCRIPTION DESC'){
		print ('<th><a href="admin.php?sort_link=title_asc#links">');
	}
	else {
		print ('<th><a href="admin.php?sort_link=title_desc#links">');
	}
	print ('Title</a></th>');
	if ($sql_orderLinkBy =='POST_DATE DESC'){
		print ('<th class="sub"><a href="admin.php?sort_link=date_asc#links">');
	}
	else {
		print ('<th class="sub"><a href="admin.php?sort_link=date_desc#links">');
	}
	print ('Submission Date</a></th>');
?>
					<th class="center">Actions</th>
                </tr>
            </thead>
            <tbody>
<?php
	//Display recently submitted links
	$mod2=0;
	foreach ($latestlink as $latestlinks){

		// Row Highlighting
        print('<tr' . (($mod2++ % 2) == 0 ? " class=\"odd\"" : "") . '>');
		
		//Change date from MYSQL format to Prefered Date/Time format
		$datetime = strtotime($latestlinks['POST_DATE']);
		$sub_date = date("m/d/Y", $datetime);

		//Display UserID, Name, E-Mail, Signup Date
		print('
					<td><a href="user.php?u='.$latestlinks['UID'].'"> '.$latestlinks['UID'].'</a></td>
					<td><a href="user.php?u='.$latestlinks['UID'].'"> '.(empty($latestlinks['FNAME'])?$latestlinks['EMAIL']:$latestlinks['FNAME'].' '.$latestlinks['LNAME']).'</a></td>
					<td><a href='.$latestlinks['URL'].' target="_blank"> '.$latestlinks['DESCRIPTION'].'</a></td>
					<td>'.$sub_date.'</td>
					<td class="center">
						<a href="'.$latestlinks['URL'].'" target="_blank"><img src="images/32magnify.png" alt="View" />View</a>
						<a href="user.php?lid='.$latestlinks['LID'].'&uid='.$latestlinks['UID'].'&remove=true"><img src="images/redx.png" class="imgicon" alt="Remove" />&nbsp;Remove</a>
					</td>
				</tr>
		
									
		');
}//end of foreach
?>
          </tbody>
            <tfoot></tfoot>
        </table>
		
		        <div id="linksearch">
				<form method="get" action="search.php">
				<label for="search-text">&nbsp;</label><input type="text" name="s" id="search-text" class="searchbox" onfocus="removeGhostText(this)" onblur="restoreGhostText(this,'Find Link')" value="Find Link" />
				<input type="submit" id="search-submit" value="GO" class="hide" />
				<input type="hidden" name="t" value="links" />

        </div>
    </div>
    <div class="clear">&nbsp;</div>
<?php require_once('footer.php'); ?>
