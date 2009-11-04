<?php
/**
 * CSSTemplateEngine Class File
 *
 * Contains the class definition for the CSSTemplateEngine, a subclass of 
 * the TemplateEngine class.
 *
 * Copyright 2008 eGloo, LLC
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Template
 * @version 1.0
 */

/**
 * CSSTemplateEngine
 * 
 * Provides a class definition for a CSS template engine subclass of
 * the TemplateEngine class.
 *
 * @package Template
 */
class CSSTemplateEngine extends TemplateEngine {

    public function __construct( $interfacebundle, $local = 'US', $language = 'en' ) {
		$this->Smarty();
		$this->left_delimiter = '/*<!--{';
		$this->right_delimiter = '}-->*/';

		$this->template_dir = '../Templates/Applications/eGloo/InterfaceBundles/' . $interfacebundle . '/CSS/';
		$this->config_dir   = '../Configuration/Smarty';
		$this->compile_dir	= eGlooConfiguration::getCachePath() . '/CompiledTemplates/' . $local . '/' . $language;
		$this->cache_dir	= eGlooConfiguration::getCachePath() . '/SmartyCache' . $local . '/' . $language;

        $this->caching      = false;
    }

}

?>