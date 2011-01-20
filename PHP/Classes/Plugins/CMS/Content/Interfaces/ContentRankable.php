<?php
/**
 * ContentRankable Class File
 *
 * Contains the class definition for the ContentRankable
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * ContentRankable
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
interface ContentRankable {

	public function getRank();
	
	public function addUserView( $userView );
	public function addUserRating( $userRating );
	public function addUserComment( $userComment );
	
	public function getNumberUserViews();
	
	public function getAverageUserRating();
	public function getMeanUserRating();
	public function getMedianUserRating();

	public function getSystemRanking();
	public function getSystemFreshness();
	public function getSystemWeight();
	

}

