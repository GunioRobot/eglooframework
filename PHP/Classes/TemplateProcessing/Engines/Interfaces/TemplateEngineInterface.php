<?php
/**
 * TemplateEngineInterface Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 * @version 1.0
 */

/**
 * TemplateEngineInterface
 *
 * $short_description
 *
 * $long_description
 *
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 */
interface TemplateEngineInterface {

    public function __construct( $interfacebundle, $local = 'US', $language = 'en' );

	public function useApplicationTemplates( $useApplicationTemplates = true, $interfaceBundle = null );

	public function useApplicationCommonTemplates( $useApplicationCommonTemplates );

	public function useFrameworkTemplates( $useFrameworkTemplates = true, $scope = null, $package = null );

	public function getTemplatePaths();

}

