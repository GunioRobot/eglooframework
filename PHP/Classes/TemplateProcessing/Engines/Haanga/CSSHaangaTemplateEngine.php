<?php
/**
 * CSSHaangaTemplateEngine Class File
 *
 * Contains the class definition for the CSSHaangaTemplateEngine, a subclass of 
 * the TemplateEngine class.
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
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 * @version 1.0
 */

/**
 * CSSHaangaTemplateEngine
 * 
 * Provides a class definition for a CSS template engine subclass of
 * the TemplateEngine class.
 *
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 */
class CSSHaangaTemplateEngine extends HaangaTemplateEngine implements TemplateEngineInterface {

	protected $packagePrefix = 'CSS';
	protected $_custom_left_delimiter = '/*<!--{';
	protected $_custom_right_delimiter = '}-->*/';

}
