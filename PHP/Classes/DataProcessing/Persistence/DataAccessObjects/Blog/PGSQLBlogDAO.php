<?php
/**
 * PGSQLBlogDAO Class File
 *
 * Needs to be commented
 * 
 * Copyright 2011 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * PGSQLBlogDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLBlogDAO extends BlogDAO {
    
    /**
     * @return BlogDTO 
     */
    public function createBlogEntry( $userID, $blogEntryTitle, $blogEntryContent ) {
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT createNewBlog($1, $2, $3)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID, $blogEntryTitle, $blogEntryContent));

		$testarray =  pg_fetch_assoc($result);
		
		pg_close( $db_handle );

		$blogDTO = new BlogDTO();
		
		if( $testarray['createnewblog'] === 't' ) {
			$blogDTO->setCreateBlogEntrySuccessful();
		} else {
            $blogDTO->setCreateBlogEntrySuccessful( false );
        }
        
        $blogDTO->setTitle( $blogEntryTitle );
        $blogDTO->setContent( $blogEntryContent );
        return $blogDTO;
    }

    /**
     * @return BlogDTO 
     */
    public function deleteBlogEntry( $userID, $blogEntryID ) {
        //$userID is not really needed here for now, may be later, or there may need to be a check to see who is deleting what . . .
        //Also need to decide whether blogs are to be truely deleted or just removed from viewing.
        //For now they are truely deleted. We need to figure out the legal issues with that.
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT deleteBlog($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($blogEntryID));

//		$testarray =  pg_fetch_assoc($result);
		
		pg_close( $db_handle );
		//Something proving the delete worked . . .        
    }

    /**
     * @return BlogDTO 
     */
    public function editBlogEntry( $userID, $blogEntryID, $blogEntryTitle, $blogEntryContent ) {
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT editBlog($1, $2, $3)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($blogEntryID, $blogEntryTitle, $blogEntryContent));

		$testarray =  pg_fetch_assoc($result);
		
		pg_close( $db_handle );

        $blogDTO = new BlogDTO();
        
		if( $testarray['editblog'] === 't' ) {
			$blogDTO->setCreateBlogEntrySuccessful();
		}
        $blogDTO->setTitle( $blogEntryTitle );
        $blogDTO->setContent( $blogEntryContent );
        return $blogDTO;        
    }

    /**
     * @return BlogDTO 
     */    
    public function viewBlogEntry( $userID, $blogEntryID ) {
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
		//[output_blogwriter]
		//[output_dateblogcreated]
		//[output_dateedited]
		//[output_blogtitle]
		//[output_blogcontent] 

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_BlogWriter, output_DateBlogCreated, output_DateEdited, output_BlogTitle, output_BlogContent FROM viewBlog($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($blogEntryID));

		$testarray =  pg_fetch_assoc($result);
		
		pg_close( $db_handle );

        $blogDTO = new BlogDTO();
        $blogDTO->setOwner($testarray['output_blogwriter']);
        $blogDTO->setCreationDate($testarray['output_dateblogcreated']);
        $blogDTO->setLastModificationDate($testarray['output_dateedited']);
        $blogDTO->setTitle($testarray['output_blogtitle']);
        $blogDTO->setContent($testarray['output_blogcontent']);
        $blogDTO->setCreateBlogEntrySuccessful();
        return $blogDTO;
    }

    /**
     * @return BlogDTO Array 
     */
    public function viewBlogEntryList( $profileID, $loggedInProfileID /* some params go here; not sure what yet */) {
        $retVal = array();

  		//Prepare a query for execution
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
  		
  		$result = pg_prepare($db_handle, "query", 'SELECT Blogs.Blog_ID, BlogWriter, DateBlogCreated, DateEdited, BlogTitle, BlogContent
														FROM Blogs INNER JOIN BlogEntries ON Blogs.Blog_ID=BlogEntries.Blog_ID
														WHERE BlogWriter=(SELECT IndividualProfiles.Profile_ID 
																		FROM Profiles 
																			INNER JOIN IndividualProfiles ON Profiles.Profile_ID=IndividualProfiles.Profile_ID 
																		WHERE Profiles.ProfileCreator=$1 
																			AND MainProfile=TRUE) 
															AND DateEdited IN (SELECT MAX(DateEdited) FROM BlogEntries GROUP BY Blog_ID);');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID));

		$testarray =  pg_fetch_all($result);
		
		pg_close( $db_handle );
        // TODO check if null first
        foreach ($testarray as $row) {
            $blogDTO = new BlogDTO();
            $blogDTO->setBlogID($row['blog_id']);
			$blogDTO->setOwner($row['blogwriter']);
    	    $blogDTO->setCreationDate($row['dateblogcreated']);
        	$blogDTO->setLastModificationDate($row['dateedited']);
        	$blogDTO->setTitle($row['blogtitle']);
        	$blogDTO->setContent($row['blogcontent']);            
        	$retVal[] = $blogDTO;
        }

        return $retVal;
    }

}
