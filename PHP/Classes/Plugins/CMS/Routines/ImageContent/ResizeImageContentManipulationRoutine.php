<?php
/**
 * ResizeImageContentManipulationRoutine Class File
 *
 * Contains the class definition for the ResizeImageContentManipulationRoutine
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * ResizeImageContentManipulationRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ResizeImageContentManipulationRoutine extends ManipulationRoutine {

	/**
	 * @var integer Maximum Height
	 */
	protected $_height_max = null;

	/**
	 * Returns protected class member $_height_max
	 *
	 * @return integer Maximum Height
	 */
	public function getHeightMax() {
		return $this->_height_max;
	}

	/**
	 * Sets protected class member $_height_max
	 *
	 * @param height_max integer Maximum Height
	 */
	public function setHeightMax( $height_max ) {
		$this->_height_max = $height_max;
	}

	/**
	 * @var integer Minimum Height
	 */
	protected $_height_min = null;

	/**
	 * Returns protected class member $_height_min
	 *
	 * @return integer Minimum Height
	 */
	public function getHeightMinimum() {
		return $this->_height_min;
	}

	/**
	 * Sets protected class member $_height_min
	 *
	 * @param height_minimum integer Minimum Height
	 */
	public function setHeightMinimum( $height_min ) {
		$this->_height_min = $height_min;
	}

	/**
	 * @var integer Maximum Width
	 */
	protected $_width_max = null;

	/**
	 * Returns protected class member $_width_max
	 *
	 * @return integer Maximum Width
	 */
	public function getWidthMax() {
		return $this->_width_max;
	}

	/**
	 * Sets protected class member $_width_max
	 *
	 * @param width_max integer Maximum Width
	 */
	public function setWidthMax( $width_max ) {
		$this->_width_max = $width_max;
	}

	/**
	 * @var integer Minimum Width
	 */
	protected $_width_min = null;

	/**
	 * Returns protected class member $_width_min
	 *
	 * @return integer Minimum Width
	 */
	public function getWidthMin() {
		return $this->_width_min;
	}

	/**
	 * Sets protected class member $_width_min
	 *
	 * @param width_min integer Minimum Width
	 */
	public function setWidthMin( $width_min ) {
		$this->_width_min = $width_min;
	}

	/**
	 * @var float Percentage to scale up or down (over 100% to enlarge, below 100% to shrink)
	 */
	protected $_scale_percentage = null;

	/**
	 * Returns protected class member $_scale_percentage
	 *
	 * @return float Percentage to scale up or down (over 100% to enlarge, below 100% to shrink)
	 */
	public function getScalePercentage() {
		return $this->_scale_percentage;
	}

	/**
	 * Sets protected class member $_scale_percentage
	 *
	 * @param scale_percentage float Percentage to scale up or down (over 100% to enlarge, below 100% to shrink)
	 */
	public function setScalePercentage( $scale_percentage ) {
		$this->_scale_percentage = $scale_percentage;
	}

	/**
	 * @var integer Method of resize
	 */
	protected $_resize_routine = null;

	/**
	 * Returns protected class member $_resize_routine
	 *
	 * @return integer Method of resize
	 */
	public function getResizeRoutine() {
		return $this->_resize_routine;
	}

	/**
	 * Sets protected class member $_resize_routine
	 *
	 * @param resize_routine integer Method of resize
	 */
	public function setResizeRoutine( $resize_routine ) {
		$this->_resize_routine = $resize_routine;
	}

	/**
	 * @var mixed Quality of resize
	 */
	protected $_resize_quality = null;

	/**
	 * Returns protected class member $_resize_quality
	 *
	 * @return mixed Quality of resize
	 */
	public function getResizeQuality() {
		return $this->_resize_quality;
	}

	/**
	 * Sets protected class member $_resize_quality
	 *
	 * @param resize_quality mixed Quality of resize
	 */
	public function setResizeQuality( $resize_quality ) {
		$this->_resize_quality = $resize_quality;
	}

	/**
	 * @var array Key/Value array of parameters for the resize routine
	 */
	protected $_parameters = array();

	/**
	 * Returns protected class member $_parameters
	 *
	 * @return array Key/Value array of parameters for the resize routine
	 */
	public function getParameters() {
		return $this->_parameters;
	}

	/**
	 * Sets protected class member $_parameters
	 *
	 * @param parameters array Key/Value array of parameters for the resize routine
	 */
	public function setParameters( $parameters ) {
		$this->_parameters = $parameters;
	}

	/**
	 * Manipulate and return the given ImageContentDTO (should be non-destructive)
	 *
	 * @param imageDTO the ImageContentDTO to be manipulated
	 */
	public function manipulateContent( $imageDTO ) {
		// Clone the original so we have most of the meta already
		$retVal = clone $imageDTO;

		// Do stuff on the cloned DTO including copying to a working directory, resizing, etc 

		// Return cloned and modified ImageDTO
		return $retVal;
	}

}

