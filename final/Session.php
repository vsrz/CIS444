<?php
/**
 * This class will help you deal with sessions
 * Calling it will immediately start the session.
 */

session_start();

class Session {
    // session vars must be unique since we share this system with other groups

    const SESS_LOGGED_IN = 'groupa_logged_in';
    const SESS_IS_ADMIN = 'groupa_is_admin';
    const SESS_MY_UID = 'groupa_myuid';
    const SESS_MY_EMAIL = 'groupa_email';
    const SESS_MY_FNAME = 'groupa_fname';
    const SESS_MY_LNAME = 'groupa_lname';
    const SESS_MY_GENDER = 'groupa_gender';
    const SESS_MY_DISPLAY_NAME = 'groupa_displayname';

    public function __construct() {
        
    }

    public function resetSession() {
        $_SESSION = array();
        session_destroy();
    }

    public function set($key = null, $val = null) {
        if ((!isset($key) || empty($key)) ||
                (!isset($val) || empty($val))) {
            return(false);
        }

        $_SESSION[$key] = $val;
        return(true);
    }

    public function del($key) {
        if (!isset($key) || empty($key)) {
            return(false);
        }

        unset($_SESSION[$key]);
        return(true);
    }

    public function get($key) {
        if (!isset($_SESSION[$key])) {
            return(false);
        }
        return($_SESSION[$key]);
    }

    // Logged in functions
    public function isLoggedIn() {
        $val = $this->get(Session::SESS_LOGGED_IN);
        return(!empty($val));
    }

    public function setLoggedIn($val = null) {
        if ($val == null)
            return(false);
        $this->set(Session::SESS_LOGGED_IN, $val);
        return(true);
    }

    // Uid functions
    public function getUid() {
        return($this->get(Session::SESS_MY_UID));
    }

    public function setUid($uid = null) {
        if ($uid == null)
            return(false);
        $this->set(Session::SESS_MY_UID, $uid);
        return(true);
    }

    // Admin functions
    public function isAdmin() {
        $val = $this->get(Session::SESS_IS_ADMIN);
        return($val != null);
    }

    public function setIsAdmin($val = null) {
        if ($val == null)
            return(false);
        $this->set(Session::SESS_IS_ADMIN, $val);
        return(true);
    }

    // Fname function
    public function getFname() {
        return($this->get(Session::SESS_MY_FNAME));
    }

    public function setFname($fname = null) {
        if ($fname == null)
            return(false);
        $this->set(Session::SESS_MY_FNAME, $fname);
        return(true);
    }

    // Fname function
    public function getLname() {
        return($this->get(Session::SESS_MY_LNAME));
    }

    public function setLname($lname = null) {
        if ($lname == null)
            return(false);
        $this->set(Session::SESS_MY_LNAME, $lname);
        return(true);
    }

    // Email function
    public function getEmail() {
        return($this->get(Session::SESS_MY_EMAIL));
    }

    public function setEmail($val = null) {
        if ($val == null)
            return(false);
        $this->set(Session::SESS_MY_EMAIL, $val);
        return(true);
    }
    
    // Display name
    public function getDisplayName() {        
        $fname = $this->getFname();
        $lname = $this->getLname();        
        if(strlen($fname.' '.$lname) <= 1) {
            return($this->getEmail());            
        }
        return($fname.' '.$lname);
    }

}

