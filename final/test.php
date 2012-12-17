<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('Db.php');
        
$db = new Db();

$searchTerm = 'john';
        $query  = "select UID,FNAME,LNAME,SIGNUP_DATE,EMAIL from MEMBER";
        $query .= " where FNAME like '%";
        $query .= $searchTerm."%' OR LNAME like '%";
        $query .= $searchTerm."%' OR EMAIL like '%";
        $query .= $searchTerm."%'";
        
        
$res = $db->select($query);
print_r($res);
print('<br /><br />');
$link = 'http://youtu.be/va9d1jkdkasldkr2cc&link=rel';
$link_desc = explode('/',$link);
print_r($link_desc);
        /*        $data = array();
        $data['UID'] = '0';
        $data['EMAIL'] = 'asdf@asdf.com';
        $data['PASSWORD'] = 'asdf';
        $data['SIGNUP_DATE'] = date("Y-m-d", time());
        $db->insert('MEMBER', $data);
        
  
$db = new Db();
$uid = 'ville017@cougars.csusm.edu';
$pass = '123qwe';
$query = "SELECT UID,EMAIL,FNAME,LNAME,GENDER,SIGNUP_DATE,ISADMIN FROM MEMBER";/* WHERE EMAIL = '";
$query .= $uid;
$query .= "' AND PASSWORD = '";
$query .= $pass;
$query .= "' LIMIT 1;";

$res = $db->select($query);

foreach ($res as $a => $b) {
    foreach ($b as $k => $v) {
        print($k . ' -> ' . $v);
        print('<br />');
    }
    print('<p>End of Element '.$a.'</p><br />');
} */

/*
$email = $_GET['email'];
print(preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email));
*/
?>
