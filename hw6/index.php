<?php

/**
 * CIS444 - HW6 - By Jeremy Villegas
 * Fall 2012
 */


define('DB_HOSTNAME','localhost');
define('DB_USERNAME','ville017');
define('DB_SCHEMA','ville017');
define('DB_PASSWORD','419aa88a0c8502efb99e23506fb46785');
define('DB_TABLE','teams');

$id_error = null;

function db_select($id = null) {
    
    // Connect to DB
    $db = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_SCHEMA);
    if(!$db) {
        die('Unable to connect to database');
    }
    
    // Build the query based on passed parameters
    $query = 'SELECT * FROM '.DB_TABLE;
    if($id) $query .= ' WHERE teamID = "'.$id.'"';

    // Execute the query
    $res = mysqli_query($db, $query.';');
    
    // If we got no results, just run for all
    if(!$res || mysqli_num_rows($res) == 0) {
        $query = 'SELECT * FROM '.DB_TABLE;
        $res = mysqli_query($db, $query.';');
        print('<p class="error">Your filter did not return any results</p>');
    }
        
    // Stick the results into an array
    $ret = array();
    while($r = mysqli_fetch_assoc($res)) {
        array_push($ret,$r);
    }
         
    // Close the database and return the array
    mysqli_close($db);
    return($ret);
    
}

    $id = 0;

    // Check if they submitted a teamid
    if(isset($_POST['teamid'])) {
        if(is_numeric($_POST['teamid'])) {
            $id = $_POST['teamid']; 
        } else if(!empty($_POST['teamid']) && $_POST['teamid'] != 'TeamID...') {
            $id_error = '<p class="error">An invalid TeamID was entered.</p>';
        }
         
    } 
    
    // Call the function to select the teams
    $teams = db_select($id);
    
    
?>
<!DOCTYPE html>
<html lang="en-us">    
    <head>
        <meta charset="UTF-8" />
        <title>ville017 HW6 - NFL Teams</title>
        <link rel="stylesheet" type="text/css" href="style.css" />        
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <script type="text/javascript" src="handlers.js"></script>
    </head>
    <body onLoad="">  
        <?php if($id_error) print($id_error); ?>
        <p>Enter a TeamID to filter the list</p>
        <form id="fFilter" name="fFilter" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST" onsubmit="return validate()" >
            <label for="teamid">&nbsp;</label>
            <input type="text" name="teamid" id="teamid" value="<?= ($id==0?"TeamID...":$id) ?>" onclick="clearGhostText(this)" onblur="checkGhostText(this,'TeamID...')" />                                    
            <input type="submit" id="submit" />
            <input type="button" id="reset" onclick="location.href = '/ville017/hw6/index.php';" value="Clear Filter" />
        </form>      
        <br />
        <span>Font size </span>
        <input type="button" class="fontChange" id="fontUp" onclick="fontChange(1)" value="+"/>
        <input type="button" class="fontChange" id="fontDown" onclick="fontChange(-1)" value="-" />                      
        <table id="teams">
            <caption>&nbsp;</caption>
            <thead>
                <tr>
                    <th colspan="3">Team</th>
                    <th colspan="4">Personnel</th>
                    <th colspan="5">Contact Info</th>
                    <th colspan="2">Record</th>
                </tr>
                <tr>
                    <th>ID</th>
                    <th>Name</th>        
                    <th>Year</th>
                    <th>Owner</th>
                    <th>GM</th>
                    <th>Head Coach</th>
                    <th>Starting QB</th>
                    <th>Address</th>
                    <th>City</th>
                    <th>State</th>
                    <th>Zip</th>
                    <th>Phone</th>
                    <th>Overall</th>
                    <th>Divisional</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    for($i=0;$i<sizeof($teams);$i++) {
                        print('<tr'.(($i%2==0)?' class="altrow"':'').'>');
                        print('<td>'.$teams[$i]['teamID'].'</td>');
                        print('<td>'.$teams[$i]['teamName'].'</td>');
                        print('<td>'.$teams[$i]['startYear'].'</td>');
                        print('<td>'.$teams[$i]['ownerName'].'</td>');
                        print('<td>'.$teams[$i]['GMName'].'</td>');
                        print('<td>'.$teams[$i]['coachName'].'</td>');
                        print('<td>'.$teams[$i]['startQB'].'</td>');
                        print('<td>'.$teams[$i]['officeAddress'].'</td>');
                        print('<td>'.$teams[$i]['city'].'</td>');
                        print('<td>'.$teams[$i]['state'].'</td>');
                        print('<td>'.$teams[$i]['zipcode'].'</td>');
                        print('<td>'.$teams[$i]['phone'].'</td>');
                        print('<td>'.$teams[$i]['overallRecord'].'</td>');
                        print('<td>'.$teams[$i]['confRecord'].'</td>');
                        print('</tr>');
                    }
                ?>
            </tbody>
        </table>
    </body>
</html>
