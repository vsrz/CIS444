<?php

/**
 * Handles various search functions and returns result sets for specific
 * pages
 * 
 */
require_once('Db.php');

class Searcher {

    private static $db;

    public function __construct() {

        // Start the DB connection
        $this->db = new Db();
    }

    /**
     * Basic URL and LINK Description search. Returns an empty array if
     * no results are found.
     * 
     * @param string $searchTerm
     * @return array
     */
    public function linkSearch($searchTerm = null,$sortTerm = null) {

        if ($searchTerm == null) {
            return(array());
        }
        $searchTerm = $this->db->scrub($searchTerm);        
        $query = "select LID,LINK.UID,FNAME,LNAME,DESCRIPTION,POST_DATE,URL from LINK";
        $query .= " join MEMBER on MEMBER.UID = LINK.UID where FNAME like '%";
        $query .= $searchTerm . "%'";
        $query .= " OR LNAME like '%";
        $query .= $searchTerm . "%'";
        $query .= " OR DESCRIPTION like '%" . $searchTerm . "%";

		if (!$sortTerm==null){
		//Set what the query will order by and if Ascending or Descending.
		switch($sortTerm) {
			case 'name_asc':
				$query .="' ORDER BY FNAME, LNAME ASC";
				break;
			case 'name_desc':
				$query .="' ORDER BY FNAME, LNAME DESC";
				break;
			case 'desc_asc':
				$query .="' ORDER BY DESCRIPTION DESC";
				break;
			case 'desc_desc':
				$query .="' ORDER BY DESCRIPTION DESC";
				break;
			case 'date_asc':
				$query .="' ORDER BY POST_DATE ASC";
				break;
			case 'date_desc':
				$query .="' ORDER BY POST_DATE DESC";
				break;
			default:
			}//end of switch
		}//end of if
		
		$result = $this->db->select($query);

        return($result);
    }

    /**
     * Basic user search. Returns an empty array if no results were found.
     * @param string $searchTerm
     * @return array
     */
    public function userSearch($searchTerm = null,$sortTerm = null) {
        if ($searchTerm == null) {
            return(array());
        }
        $searchTerm = $this->db->scrub($searchTerm);        
	
        $query = "select UID,FNAME,LNAME,SIGNUP_DATE,EMAIL,ISBANNED from MEMBER";
        $query .= " where FNAME like '%";
        $query .= $searchTerm . "%' OR LNAME like '%";
        $query .= $searchTerm . "%' OR EMAIL like '%";
        $query .= $searchTerm . "%";

		if (!$sortTerm==null){
		//Set what the query will order by and if Ascending or Descending.
		switch($sortTerm) {
			case 'name_asc':
				$query .="' ORDER BY FNAME, LNAME ASC";
				break;
			case 'name_desc':
				$query .="' ORDER BY FNAME, LNAME DESC";
				break;
			case 'date_asc':
				$query .="' ORDER BY SIGNUP_DATE ASC";
				break;
			case 'date_desc':
				$query .="' ORDER BY SIGNUP_DATE DESC";
				break;
			default:
			}//end of switch
		}//end of if
		
		$result = $this->db->select($query);

        return($result);
    }

    /**
     * Simple utility function that gets the correct display name for a member
     * when providing the name and email address.
     * 
     * @param array $member
     * @return string
     */
    public function getDisplayName($member) {
        $displayName = $member['FNAME'] . ' ' . $member['LNAME'];
        if (strlen($displayName) <= 1) {
            $displayName = explode('@', $member['EMAIL']);
            return($displayName);
        }
        return($displayName);
    }

}
