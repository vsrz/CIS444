<?php

require_once('Session.php');

/**
 * Description of Friend
 *
 *  This class will help you deal with references to the Friend table
 * in the dbase.
 *   
 * @author jvillegas
 */
class Friend {

    private static $uid;
    private static $fid;
    private static $db;
    private static $session;
    
    /**
     * Create a new instance of the Friend class
     * 
     * @param int $uid User ID
     * @param int $fid Friend ID
     */
    public function __construct($uid = null, $fid = null) {
        $this->session = new Session();
        if($uid==null)
            $this->uid = $this->session->getUid();
        else
            $this->uid = $uid;
        $this->fid = $fid;
        $this->db = new Db();
    }
    
    /**
     * Add a friend to the $uid with the uid of $fid. Use the inherited
     * values if none are specified.
     * 
     * @param int $uid
     * @param int $fid
     * @return bool
     */
    public function addFriend($uid = null, $fid = null) {
        
        if($fid==null && $this->fid == null)
            return(false);
        else if ($fid == null)
            $fid = $this->fid;
        
        $query = 'INSERT INTO FRIEND VALUES (\''.$this->db->scrub($uid).'\',\''.$this->db->scrub($fid).'\')';
        $this->db->select($query);
        return(true);
        
    }
    
    /**
     * Add a friend to the $uid with the uid of $fid. Use the inherited
     * values if none are specified.
     *
     * @param int $uid
     * @param int $fid
     * @return bool
     */
    public function delFriend($uid = null, $fid = null) {
        if($fid==null && $this->fid == null)
            return(false);
        else if ($fid == null)
            $fid = $this->fid;
        
        $query = 'DELETE FROM FRIEND WHERE FID = '.$this->db->scrub($fid).' and UID = '.$this->db->scrub($uid);
        $this->db->select($query);
        return(true);
    }
    
    /**
     * Is this member friends with the target uid?
     * 
     * @param int $uid
     * @param int $fid
     * @return bool
     */
    public function isFriend($uid = null, $fid = null) {
        if($uid==null||$fid==null)return(false);
        $query = "SELECT COUNT(*) FROM FRIEND WHERE UID = ".$this->db->scrub($uid)." AND FID = ".$this->db->scrub($fid);
        $this->db = new Db();
        $res = $this->db->select($query);
        $count = $res[0]['COUNT(*)'];
        if($count>0)return(true);
        return(false);
    }
    
    /**
     * Gets a list of friends from the DB and returns an array containing them
     * 
     * @param int $uid
     * @return array
     */
    public function getFriends($uid = null) {
        $query = 'select EMAIL,MEMBER.FNAME,MEMBER.LNAME,MEMBER.UID from FRIEND join MEMBER on FRIEND.FID=MEMBER.UID where FRIEND.UID=' . $this->db->scrub($uid) . ' order by MEMBER.LNAME';
        $res = $this->db->select($query);
        return($res);
    }
        
}
