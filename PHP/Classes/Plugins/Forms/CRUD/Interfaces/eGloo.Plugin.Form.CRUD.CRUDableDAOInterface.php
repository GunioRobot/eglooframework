<?php
namespace eGloo\Plugin\Form\CRUD;

/**
 * eGloo\Plugin\Form\CRUD\CRUDableDAOInterface Interface File
 *
 * $file_block_description
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
 * @category Plugins
 * @package Forms
 * @subpackage CRUD
 * @version 1.0
 */

/**
 * eGloo\Plugin\Form\CRUD\CRUDableDAOInterface
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage CRUD
 */
interface CRUDableDAOInterface {

	public function CRUDCreate( $formDTO );

	public function CRUDRead( $formDTO );

	public function CRUDUpdate( $formDTO );

	public function CRUDDestroy( $formDTO );

}

