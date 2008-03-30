<?php
/*
 * Created on Aug 13, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */


// NON-PRODUCTION STATIC CLASS
// This class is to be used only for creating static methods
// to create and return fake input values for internal testing
// of system components.
//
// This is not a replacement for or redundant to unit tests.
// It is meant for developers to be able to create fake values
// to be used within code blocks that depend on other system
// components that are either not completed OR to create
// fake values that cannot be created in a non-production
// environment.
class TestValueConstructor {

    // TODO write createContentIcingItems() method
    static public function createContentIcingItems() {}
    
    static public function createContentMusicItems() {
        $musicItems = array();
        
        $musicItems[] = Audio::getTestContent( 'bug0', 'A Perfect Circle' );
        $musicItems[] = Audio::getTestContent( 'bug1', '10 Foot Pole' );
        $musicItems[] = Audio::getTestContent( 'bug2', 'Spinetshank' );                
        $musicItems[] = Audio::getTestContent( 'bug3', 'E.S. Posthumus' );
        $musicItems[] = Audio::getTestContent( 'bug4', '40 Below Summer' );
        $musicItems[] = Audio::getTestContent( 'bug5', '(Hed) P.E.' );
        $musicItems[] = Audio::getTestContent( 'bug6', 'Sevendust' );
        $musicItems[] = Audio::getTestContent( 'bug7', 'AFI' );
        $musicItems[] = Audio::getTestContent( 'bug8', 'Dream Theater' );
        $musicItems[] = Audio::getTestContent( 'bug9', 'Devil Driver' );
                        
        $musicItems[] = Audio::getTestContent( 'zug0', 'Nickleback' );
        $musicItems[] = Audio::getTestContent( 'zug1', 'Hive' );
        $musicItems[] = Audio::getTestContent( 'zug2', 'Orgy' );                
        $musicItems[] = Audio::getTestContent( 'zug3', 'Oyster Cult' );
        $musicItems[] = Audio::getTestContent( 'zug4', 'Lit' );
        $musicItems[] = Audio::getTestContent( 'zug5', 'Evanescence' );
        $musicItems[] = Audio::getTestContent( 'zug6', 'Busta Rhymes' );
        $musicItems[] = Audio::getTestContent( 'zug7', 'Flyleaf' );
        $musicItems[] = Audio::getTestContent( 'zug8', 'Deftones' );
        $musicItems[] = Audio::getTestContent( 'zug9', 'Tool' );
                
        $musicItems[] = Audio::getTestContent( 'dug0', 'Styles of Beyond' );
        $musicItems[] = Audio::getTestContent( 'dug1', 'Fort Minor' );
        $musicItems[] = Audio::getTestContent( 'dug2', 'Rihanna' );                
        $musicItems[] = Audio::getTestContent( 'dug3', 'Metallica' );
        $musicItems[] = Audio::getTestContent( 'dug4', 'Headcharge' );
        $musicItems[] = Audio::getTestContent( 'dug5', 'Slipknot' );
        $musicItems[] = Audio::getTestContent( 'dug6', 'Rage' );
        $musicItems[] = Audio::getTestContent( 'dug7', 'Linkin Park' );
        $musicItems[] = Audio::getTestContent( 'dug8', 'SOAD' );
        $musicItems[] = Audio::getTestContent( 'dug9', 'Stone Sour' );
        
        shuffle( $musicItems );
        
        return $musicItems;
    }

    static public function createContentPeopleItems() {
        $peopleItems = array();
        
        $peopleItems[] = Audio::getTestContent( 'bug0', 'Jay-Z' );
        $peopleItems[] = Audio::getTestContent( 'bug1', 'Lebron James' );
        $peopleItems[] = Audio::getTestContent( 'bug2', 'Kaila Yu' );                
        $peopleItems[] = Audio::getTestContent( 'bug3', 'Eminem' );
        $peopleItems[] = Audio::getTestContent( 'bug4', 'Kevin Spacey' );
        $peopleItems[] = Audio::getTestContent( 'bug5', 'Will Farrell' );
        $peopleItems[] = Audio::getTestContent( 'bug6', 'Jim Carrey' );
        $peopleItems[] = Audio::getTestContent( 'bug7', 'Rachel McAdams' );
        $peopleItems[] = Audio::getTestContent( 'bug8', 'Shaquille O\'Neal' );
        $peopleItems[] = Audio::getTestContent( 'bug9', '50 Cent' );
                        
        $peopleItems[] = Audio::getTestContent( 'zug0', 'George Bush' );
        $peopleItems[] = Audio::getTestContent( 'zug1', 'Angelina Jolie' );
        $peopleItems[] = Audio::getTestContent( 'zug2', 'Brad Pitt' );                
        $peopleItems[] = Audio::getTestContent( 'zug3', 'Tom Cruise' );
        $peopleItems[] = Audio::getTestContent( 'zug4', 'Dr. Dre' );
        $peopleItems[] = Audio::getTestContent( 'zug5', 'Denzel Washington' );
        $peopleItems[] = Audio::getTestContent( 'zug6', 'Dave Chappelle' );
        $peopleItems[] = Audio::getTestContent( 'zug7', 'Jackie Chan' );
        $peopleItems[] = Audio::getTestContent( 'zug8', 'Jet Li' );
        $peopleItems[] = Audio::getTestContent( 'zug9', 'Jon Stewart' );
                
        $peopleItems[] = Audio::getTestContent( 'dug0', 'Stephen Colbert' );
        $peopleItems[] = Audio::getTestContent( 'dug1', 'Steve Carell' );
        $peopleItems[] = Audio::getTestContent( 'dug2', 'Lloyd Banks' );                
        $peopleItems[] = Audio::getTestContent( 'dug3', 'Chris Rock' );
        $peopleItems[] = Audio::getTestContent( 'dug4', 'Owen Wilson' );
        $peopleItems[] = Audio::getTestContent( 'dug5', 'Luke Wilson' );
        $peopleItems[] = Audio::getTestContent( 'dug6', 'Vince Vaughn' );
        $peopleItems[] = Audio::getTestContent( 'dug7', 'Christopher Walken' );
        $peopleItems[] = Audio::getTestContent( 'dug8', 'Rihanna' );
        $peopleItems[] = Audio::getTestContent( 'dug9', 'Kanye West' );
        
        shuffle( $peopleItems );
        
        return $peopleItems;
    }

    static public function createContentPictureItems() {
        $pictureItems = array();
        
        $pictureItems[] = Audio::getTestContent( 'bug0', 'A Perfect Circle' );
        $pictureItems[] = Audio::getTestContent( 'bug1', '10 Foot Pole' );
        $pictureItems[] = Audio::getTestContent( 'bug2', 'Spinetshank' );                
        $pictureItems[] = Audio::getTestContent( 'bug3', 'E.S. Posthumus' );
        $pictureItems[] = Audio::getTestContent( 'bug4', '40 Below Summer' );
        $pictureItems[] = Audio::getTestContent( 'bug5', '(Hed) P.E.' );
        $pictureItems[] = Audio::getTestContent( 'bug6', 'Sevendust' );
        $pictureItems[] = Audio::getTestContent( 'bug7', 'AFI' );
        $pictureItems[] = Audio::getTestContent( 'bug8', 'Dream Theater' );
        $pictureItems[] = Audio::getTestContent( 'bug9', 'Devil Driver' );
                        
        $pictureItems[] = Audio::getTestContent( 'zug0', 'Nickleback' );
        $pictureItems[] = Audio::getTestContent( 'zug1', 'Hive' );
        $pictureItems[] = Audio::getTestContent( 'zug2', 'Orgy' );                
        $pictureItems[] = Audio::getTestContent( 'zug3', 'Oyster Cult' );
        $pictureItems[] = Audio::getTestContent( 'zug4', 'Lit' );
        $pictureItems[] = Audio::getTestContent( 'zug5', 'Evanescence' );
        $pictureItems[] = Audio::getTestContent( 'zug6', 'Busta Rhymes' );
        $pictureItems[] = Audio::getTestContent( 'zug7', 'Flyleaf' );
        $pictureItems[] = Audio::getTestContent( 'zug8', 'Deftones' );
        $pictureItems[] = Audio::getTestContent( 'zug9', 'Tool' );
                
        $pictureItems[] = Audio::getTestContent( 'dug0', 'Styles of Beyond' );
        $pictureItems[] = Audio::getTestContent( 'dug1', 'Fort Minor' );
        $pictureItems[] = Audio::getTestContent( 'dug2', 'Rihanna' );                
        $pictureItems[] = Audio::getTestContent( 'dug3', 'Metallica' );
        $pictureItems[] = Audio::getTestContent( 'dug4', 'Headcharge' );
        $pictureItems[] = Audio::getTestContent( 'dug5', 'Slipknot' );
        $pictureItems[] = Audio::getTestContent( 'dug6', 'Rage' );
        $pictureItems[] = Audio::getTestContent( 'dug7', 'Linkin Park' );
        $pictureItems[] = Audio::getTestContent( 'dug8', 'SOAD' );
        $pictureItems[] = Audio::getTestContent( 'dug9', 'Stone Sour' );
        
        shuffle( $pictureItems );
        
        return $pictureItems;
    }

    static public function createContentVideoItems() {
        $videoItems = array();
        
	    $videoItems[] = Audio::getTestContent( 'bug0', 'Apocalypto' );
	    $videoItems[] = Audio::getTestContent( 'bug1', 'The Guardian' );
	    $videoItems[] = Audio::getTestContent( 'bug2', 'The Protector' );
	    $videoItems[] = Audio::getTestContent( 'bug3', 'Talladega Nights' );
	    $videoItems[] = Audio::getTestContent( 'bug4', 'Transformers' );
	    $videoItems[] = Audio::getTestContent( 'bug5', 'Jackass 2' );
	    $videoItems[] = Audio::getTestContent( 'bug6', 'Miami Vice' );
	    $videoItems[] = Audio::getTestContent( 'bug7', 'Open Season' );
	    $videoItems[] = Audio::getTestContent( 'bug8', 'Borat' );
	    $videoItems[] = Audio::getTestContent( 'bug8', 'The Grudge 2' );
	
	    $videoItems[] = Audio::getTestContent( 'zug0', 'Open Season' );
	    $videoItems[] = Audio::getTestContent( 'zug1', 'The Illusionist' );
	    $videoItems[] = Audio::getTestContent( 'zug2', 'Sleeping Dogs Lie' );
	    $videoItems[] = Audio::getTestContent( 'zug3', 'Lucky You' );
	    $videoItems[] = Audio::getTestContent( 'zug4', 'The Reaping' );
	    $videoItems[] = Audio::getTestContent( 'zug5', 'The Departed Trailer 1' );
	    $videoItems[] = Audio::getTestContent( 'zug6', 'The Santa Clause 3' );
	    $videoItems[] = Audio::getTestContent( 'zug7', 'Fast Food Nation' );
	    $videoItems[] = Audio::getTestContent( 'zug8', 'The Black Dahlia' );
	    $videoItems[] = Audio::getTestContent( 'zug9', 'Employee of the Month (Revised)' );
	    
	    $videoItems[] = Audio::getTestContent( 'dug0', 'Renaissance' );
	    $videoItems[] = Audio::getTestContent( 'dug1', 'The Last King of Scotland' );
	    $videoItems[] = Audio::getTestContent( 'dug2', 'School For Scoundrels' );
	    $videoItems[] = Audio::getTestContent( 'dug3', 'The Departed' );
	    $videoItems[] = Audio::getTestContent( 'dug4', 'Viva Pedro' );
	    $videoItems[] = Audio::getTestContent( 'dug5', 'Babel' );
	    $videoItems[] = Audio::getTestContent( 'dug6', 'The Fountain' );
	    $videoItems[] = Audio::getTestContent( 'dug7', 'The Quiet' );
	    $videoItems[] = Audio::getTestContent( 'dug8', 'American Hardcore' );
	    $videoItems[] = Audio::getTestContent( 'dug9', 'TMNT' );
	        
        shuffle( $videoItems );
        
        return $videoItems;
    }

    static public function createContentGameItems() {
        $gameItems = array();
        
        $gameItems[] = Audio::getTestContent( 'bug0', 'A Perfect Circle' );
        $gameItems[] = Audio::getTestContent( 'bug1', '10 Foot Pole' );
        $gameItems[] = Audio::getTestContent( 'bug2', 'Spinetshank' );                
        $gameItems[] = Audio::getTestContent( 'bug3', 'E.S. Posthumus' );
        $gameItems[] = Audio::getTestContent( 'bug4', '40 Below Summer' );
        $gameItems[] = Audio::getTestContent( 'bug5', '(Hed) P.E.' );
        $gameItems[] = Audio::getTestContent( 'bug6', 'Sevendust' );
        $gameItems[] = Audio::getTestContent( 'bug7', 'AFI' );
        $gameItems[] = Audio::getTestContent( 'bug8', 'Dream Theater' );
        $gameItems[] = Audio::getTestContent( 'bug9', 'Devil Driver' );
                        
        $gameItems[] = Audio::getTestContent( 'zug0', 'Nickleback' );
        $gameItems[] = Audio::getTestContent( 'zug1', 'Hive' );
        $gameItems[] = Audio::getTestContent( 'zug2', 'Orgy' );                
        $gameItems[] = Audio::getTestContent( 'zug3', 'Oyster Cult' );
        $gameItems[] = Audio::getTestContent( 'zug4', 'Lit' );
        $gameItems[] = Audio::getTestContent( 'zug5', 'Evanescence' );
        $gameItems[] = Audio::getTestContent( 'zug6', 'Busta Rhymes' );
        $gameItems[] = Audio::getTestContent( 'zug7', 'Flyleaf' );
        $gameItems[] = Audio::getTestContent( 'zug8', 'Deftones' );
        $gameItems[] = Audio::getTestContent( 'zug9', 'Tool' );
                
        $gameItems[] = Audio::getTestContent( 'dug0', 'Styles of Beyond' );
        $gameItems[] = Audio::getTestContent( 'dug1', 'Fort Minor' );
        $gameItems[] = Audio::getTestContent( 'dug2', 'Rihanna' );                
        $gameItems[] = Audio::getTestContent( 'dug3', 'Metallica' );
        $gameItems[] = Audio::getTestContent( 'dug4', 'Headcharge' );
        $gameItems[] = Audio::getTestContent( 'dug5', 'Slipknot' );
        $gameItems[] = Audio::getTestContent( 'dug6', 'Rage' );
        $gameItems[] = Audio::getTestContent( 'dug7', 'Linkin Park' );
        $gameItems[] = Audio::getTestContent( 'dug8', 'SOAD' );
        $gameItems[] = Audio::getTestContent( 'dug9', 'Stone Sour' );
        
        shuffle( $gameItems );
        
        return $gameItems;
    }
    
    static public function createDropDownColumns() {
        $dropDownColumns = array();
        
        $dropDownColumns[] = new DropDownColumn( 'Center_Sub_Column4', '' );
        $dropDownColumns[] = new DropDownColumn( 'Center_Sub_Column3', '' );
        $dropDownColumns[] = new DropDownColumn( 'Center_Sub_Column2', '' );
        
        return $dropDownColumns;
    }
 
    static public function createUserProfileCube( $cubeID ) {
        if ( $cubeID === '1' ) {
            $cube = new Cube();
            $cube->setTitle( 'Friends' );
            $cube->setCSSClass( '' );
            $cube->setUniqueID( '' );
            $cube->setContent( 'test' );
        }
        return $cube;
    }
    
    static public function createFridge() {
        $cubeList = array( '0001', '0002', '0003', '0004', '0005', '0006', '0007' );
        return $cubeList;
    }

    static public function getAlertList() {
        $blogList = array();
        $blogList[] = Alert::getTestContent( 'title1', 'alert1' );
        $blogList[] = Alert::getTestContent( 'title2', 'alert2' );
        $blogList[] = Alert::getTestContent( 'title3', 'alert3' );
        $blogList[] = Alert::getTestContent( 'title4', 'alert4' );
        $blogList[] = Alert::getTestContent( 'title5', 'alert5' );
        return $blogList;
    }
    
    static public function getBlogEntryList() {
        $blogList = array();
        $blogList[] = Blog::getTestContent( 'title1', 'summary1' );
        $blogList[] = Blog::getTestContent( 'title2', 'summary2' );
        $blogList[] = Blog::getTestContent( 'title3', 'summary3' );
        $blogList[] = Blog::getTestContent( 'title4', 'summary4' );
        $blogList[] = Blog::getTestContent( 'title5', 'summary5' );
        return $blogList;
    }

    static public function getMessageList() {
        $blogList = array();
        $blogList[] = Message::getTestContent( 'title1', 'message1' );
        $blogList[] = Message::getTestContent( 'title2', 'message2' );
        $blogList[] = Message::getTestContent( 'title3', 'message3' );
        $blogList[] = Message::getTestContent( 'title4', 'message4' );
        $blogList[] = Message::getTestContent( 'title5', 'message5' );
        return $blogList;
    }

    
    static public function getSessionUserBean(){
    	$userBean = new UserDTO();
		$userBean->setUserID("Keith!");
    	return $userBean;
    }
    
    static public function getCubeFromDataBase( $id ){
		$cubeDTO = new CubeDTO();
		$cubeDTO->setPermissionLevel(6);
		$cubeDTO->setID("1123");
		$cubeDTO->setDirectoryLocation("../cubes/F/Friends.gloo");
		return $cubeDTO;
    }

    static public function getCubeIDList() {
        $cubeIDList = array( '1000', '1111', '1123', '1444', '1555', '1893' );
        
        return $cubeIDList;
    }

}

?>
