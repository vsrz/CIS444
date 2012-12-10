<?php

require_once('Session.php');
require_once('Db.php');
require_once('Logger.php');
class Member {

    public static $uid;
    public static $email;
    public static $pass;
    public static $fname;
    public static $lname;
    public static $gender;
    public static $signup_date;
    public static $isadmin;
    public static $isbanned;
    private static $db;
    private static $sess;
    private static $log;

    public function __construct($uid = null) {        
        // If UID is null, we'll instantiate a blank object
        // unless the current session is logged in of course
        if($uid == null) {
            $this->sess = new Session();

            // If this session is already logged in, populate this object
            if ($this->sess->isLoggedIn()) {
                $this->sessionLogin();
            }
        } else {
            // Get information based on the UID given for the member object
            $this->load($uid);
        }
    }

    public function __destruct() {
        
    }
    
    // If the user is already logged in, we're gonna trust the
    // session parameters rather than hitting the DB on every page call 
    private function sessionLogin() {
        $this->uid = $this->sess->getUid();
        $this->email = $this->sess->getEmail();
        $this->fname = $this->sess->getFname();
        $this->lname = $this->sess->getLname();
        $this->isadmin = $this->sess->isAdmin();
    }

    // Assign member to session (this logs them in as the current member)
    private function assignSession($r) {
        if(isset($this->sess)) {
            // Save useful information to the session
            $this->sess->setLoggedIn(true);
            $this->sess->setUid($r['UID']);
            $this->sess->setIsAdmin($r['ISADMIN']);
            $this->sess->setFname($r['FNAME']);
            $this->sess->setLname($r['LNAME']);
            $this->sess->setEmail($r['EMAIL']);
            return(true);
        }
        return(false);
    }
    
    // Takes values from a database query and assign the values to the class
    private function assignDbValues($r) {
        $this->uid = $r['UID'];
        $this->email = $r['EMAIL'];
        $this->fname = $r['FNAME'];
        $this->lname = $r['LNAME'];
        $this->signup_date = $r['SIGNUP_DATE'];
        $this->isadmin = $r['ISADMIN'];
        $this->isbanned = $r['ISBANNED'];
        return(true);
    }

    // Is this member friends with the target uid?
    public function isFriend($uid = null, $fid = null) {
        if($uid==null||$fid==null)return(false);
        $this->db = new Db();
        $this->db->scrub($uid);
        $this->db->scrub($fid);
        $query = "SELECT COUNT(*) FROM FRIEND WHERE UID = ".$uid." AND FID = ".$fid;
        $res = $this->db->select($query);
        $count = $res[0]['COUNT(*)'];
        if($count>0)return(true);
        return(false);
    }
        
    public function load($uid = null) {
        if($uid==null) {
            return(false);
        }
        
        $this->db = new Db();
        $this->db->scrub($uid);
        $query = "SELECT UID,EMAIL,FNAME,LNAME,SIGNUP_DATE,ISADMIN,ISBANNED FROM MEMBER WHERE UID = '";
        $query .= $uid;
        $query .= "' LIMIT 1;";

        // This will return a numeric array of rows containing an associative
        // array of key/value pairs
        $results = $this->db->select($query);
        
        $this->assignDbValues($results[0]);

        return(true);
        
        
    }
    
    /**
     * Toggles the ban status of this member
     * 
     * @return bool
     */
    public function toggleBanStatus($adminuid = 'Unknown') {

        $query = 'UPDATE MEMBER SET ISBANNED = '.(!empty($this->isbanned)?"NULL":"1").' WHERE UID = '.$this->uid;

        $this->db = new Db();
        $this->db->select($query);
        
        $log = new Logger();
        $log->log('Admin '.$adminuid.' toggled the ban status for '.$this->uid, $_SERVER['REMOTE_ADDR'], 'INFO');

        return(true);
    }
    
    
    /**
     *  Attempts to log in with the username and password provided
     *  
     * @param string $email
     * @param string $pass
     * @return bool
     */
    public function login($email = null, $pass = null) {

        if (($email == null) || ($pass == null)) {
            return($this);
        }
        
        $this->db = new Db();
        $log = new Logger();
        $email = $this->db->scrub($email);
        $pass = $this->db->scrub($pass);
        $query = "SELECT UID,EMAIL,FNAME,LNAME,SIGNUP_DATE,ISADMIN,ISBANNED FROM MEMBER WHERE EMAIL = '";
        $query .= $email;
        $query .= "' AND PASSWORD = '";
        $query .= $pass;
        $query .= "' AND ISBANNED IS NULL LIMIT 1;";

        // This will return a numeric array of rows containing an associative
        // array of key/value pairs
        $results = $this->db->select($query);

        // Populate the private variables in this class
        if (!empty($results[0])) {
            $this->assignDbValues($results[0]);
            $this->assignSession($results[0]);
            $log->log('User '.$this->uid.' logged in', $_SERVER['REMOTE_ADDR'],'INFO');
            
        }

        // Return null if the credentials were invalid 
        return(!empty($results[0]));
    }

    /**
     * Checks to see if the account exists
     * 
     * @param type $email Email of the account to check
     * @return bool 
     */
    public function accountExists($email) {
        $this->db = new Db();
        
        $this->db->scrub($email);
        $query = "SELECT UID FROM MEMBER WHERE EMAIL = '".$email."'";
        $results = $this->db->select($query);
        return(sizeof($results)>0);
    }
    
    
    public function isBanned() {
        return(isset($this->isbanned));
    }
    /**
     * 
     * Creates a new email account using the email provided
     * 
     * @param type $email E-mail address to be created
     * @param type $pass Password to create
     * @return type
     */
    public function createNew($email = null, $pass = null) {
        
        // Make sure valid values were passed
        if(($email == null) || ($pass == null)) {
            return(false);
        }
        
        // If the account already exists, just log them in
        if($this->login($email, $pass)) {
            return(true);
        }
        
        // Build the query to create the account
        $this->db = new Db();
        $data = array();
        $data['UID'] = '0';
        $data['EMAIL'] = $this->db->scrub($email);
        $data['PASSWORD'] = $this->db->scrub($pass);
        $data['SIGNUP_DATE'] = date("Y-m-d", time());
        $this->db->insert('MEMBER', $data);

        
        // Create the account and log the user in
        $query = 'SELECT UID FROM MEMBER WHERE EMAIL = \''.$email.'\'';
        $res = $this->db->select($query);
        $data['UID'] = $res[0]['UID'];
        $this->assignSession($data);
        $log = new Logger();
        $log->log('New account created UID: '.$data['UID'].' Email: '.$data['EMAIL'].' ', $_SERVER['REMOTE_ADDR'],'INFO');
        return(true);
        
    }  
    
    /**
     * Determines the publicly available display name and returns it
     * 
     * @return string
     */
    public function getDisplayName() {
        $displayName = $this->fname.' '.$this->lname;
        if(strlen($displayName) <= 1) {
            $displayName = explode('@',$this->email);
            return($displayName[0]);
        }
        return($displayName);
    }
    
    /**
     * Will return the first part of the email address if no name is specified
     * but will return only the first name if given.
     * 
     * @return string
     */
    public function getShortName() {
        $displayName = $this->fname;
        if(strlen($displayName) <= 1) {
            $displayName = explode('@',$this->email);
            return($displayName[0]);
        }
        return($displayName);
    }
    /**
     * Takes an array object and updates the provided parameters in the
     * account then reloads the correct values into the session.
     * 
     * The array object may contain these associations:
     * FNAME = 'first name'
     * LNAME = 'last name'
     * EMAIL = 'email'
     * PASSWORD = 'password'
     * UID = numeric_uid
     * 
     * @param array $values
     * @return bool
     */
    public function updateAccount($val = null) {
        $query = 'UPDATE MEMBER SET';
        $mod = 0;
        foreach ($val as $k => $v) {
            if ($k != 'UID') {
                if ($mod++ > 0)
                    $query .= ', ';
                $query .= ' ' . $k . ' = \'' . $v . '\'';
            }
        }
        $query .= ' WHERE UID = ' . $this->db->scrub($val['UID']);
        
        $this->db = new Db();
        $this->db->select($query);
        $val['ISADMIN'] = null;
        
        // Set the current session up with the new values
        $this->assignSession($val);        
 
        // Log it
        $log = new Logger();
        $log->log('User '.$val['UID'].' updated their profile', $_SERVER['REMOTE_ADDR'], 'INFO');
        return(true);
    }
    
    
    /**
     * Logs the current user out of their session
     * 
     * @return bool
     */
    public function logout() {
        $this->sess->resetSession();
        return(true);
    }
}