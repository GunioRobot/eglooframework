<?php
/**
 * XHTMLTwigTemplateEngine Class File
 *
 * Contains the class definition for the TemplateEngine, a subclass of
 * the Smarty template engine class.
 *
 * Copyright 2011 eGloo, LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * XHTMLTwigTemplateEngine
 *
 * Provides a class definition for a generic template engine subclass of
 * the Smarty template engine class.
 *
 * $long_description
 *
 * @package TemplateProcessing
 * @subpackage TemplateEngines
 */
class XHTMLTwigTemplateEngine extends TwigTemplateEngine implements TemplateEngineInterface {

	protected $packagePrefix = 'XHTML';
	protected $_custom_left_delimiter = '<!--{';
	protected $_custom_right_delimiter = '}-->';

}
