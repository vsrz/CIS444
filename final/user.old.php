<?php require_once('header.php'); ?>
<!-- end #header -->

<div id="page">
    <div id="sidebar">
        <p class="name-large">John Doe
            <img src="images/default_profile.png" class="icon64" alt="Profile">
        </p>
    </div>
    <div class="clear">&nbsp;</div>
    <div class="line">&nbsp;</div>
    <div id="profile_friends">
        <p> My Friends </p>
        <ol>
            <li>Jane Doe</li>
            <li>Bob Smith</li>		
            <li>Betty Blue</li>
            <li>Sonja Red</li>
            <li>Jack Bauer</li>
            <li>John Jameson</li>
        </ol>
        <input type="text" name="friend" id="friend"  onblur="contactCheckValid(this)" value="" />	
        <input type="submit" id="add_friend" value="Add Friend" onclick = "addfriendrow()"/>	
    </div>
    <div class="line">&nbsp;</div>
    <div id="profile_links">
        <p> My Links </p>
        <ol>
            <li id = "link1">Link 1</li>
            <li id = "link2">Link 2</li>
            <li id = "link3">Link 3</li>
            <li id = "link4">Link 4</li>
        </ol>
        <input type="text" name="link" id="link"  onblur="contactCheckValid(this)" value="" />	
        <input type="submit" id="add_link" value="Add Link" onclick = "addlinkrow()" />	
    </div>
    <div class="line">&nbsp;</div>
    <div id="profile_interests">
        <p> My Interests </p>
        <table>
            <tr>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_0">
                    Boating
                </td>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_1">
                    Reading
                </td>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_2">
                    Paintball 
                </td>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_3">
                    Soccer
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_4">
                    Zombies
                </td>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_5">
                    Guns 
                </td>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_6">
                    Cars 
                </td>
                <td>
                    <input type="checkbox" name="Interest" value="checkbox" checked="checked" id="Interest_7">
                    Things that blow up
                </td>
            </tr>
        </table>
        <input type="text" name="interest" id="interest"  onblur="contactCheckValid(this)" value="" />	
        <input type="submit" id="update_interest" value="Update Interests" onclick = "updatelinks(link_array)" />
        <div class="clear">&nbsp;</div>
        <div class="line">&nbsp;</div>
        <div id="profile_user">
            <table>
                <tr>
                    <td>First Name</td>
                    <td id = "UP_firstname">John</td>	
                    <td> 
                        <input type="text" name="submit_firstname" id="submit_firstname" onblur="contactCheckValid(this)" value=""  />							 
                    </td>						
                </tr>
                <tr>
                    <td>Last Name</td>
                    <td id = "UP_lastname">Doe</td>	
                    <td> 
                        <input type="text" name="submit_lastname" id="submit_lastname" onblur="contactCheckValid(this)" value=""  />							 
                    </td>							
                </tr>
                <tr>
                    <td>Email</td>
                    <td id ="UP_email">doe001@cougars.csusm.edu</td>	
                    <td> 
                        <input type="text" name="submit_email" id="submit_email" onblur="contactCheckValid(this)" value=""  />							 
                    </td>
                </tr>
                <tr>
                    <td>Password</td>
                    <td id = "UP_password">Enter Password ----------------------></td>					
                    <td> 
                        <input type="text" name="submit_password" id="submit_password"  onblur="contactCheckValid(this)" value="" />							 
                    </td>
                </tr>
            </table>	
            <input type="submit" id="submit_changes" value="Change Information" onclick = "UP_changeinfo()" />							
        </div>
    </div>	    
    <div class="clear">&nbsp;</div>
    <?php require_once('footer.php'); ?>
