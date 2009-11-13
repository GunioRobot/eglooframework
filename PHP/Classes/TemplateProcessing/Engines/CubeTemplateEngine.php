<?php
/**
 * CubeTemplateEngine Class File
 *
 * Contains the class definition for the CubeTemplateEngine, a subclass of 
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
 * CubeTemplateEngine
 * 
 * Provides a class definition for a Cube template engine subclass of
 * the TemplateEngine class.
 *
 * @package Template
 */
class CubeTemplateEngine extends TemplateEngine {

    public function __construct( $interfacebundle, $local = 'US', $language = 'en' ) {
		parent::__construct( $interfacebundle, $local = 'US', $language = 'en' );

        $this->left_delimiter = '<!--{'; 
        $this->right_delimiter = '}-->'; 

        $this->template_dir = 'Cubes/';
        $this->config_dir   = 'Configuration/Smarty';

		$this->compile_dir	= eGlooConfiguration::getCachePath() . '/CompiledTemplates/' . $local . '/' . $language;
		$this->cache_dir	= eGlooConfiguration::getCachePath() . '/SmartyCache/' . $local . '/' . $language;

		$this->compile_check = false;
		$this->force_compile = false;

		if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::PRODUCTION) {
			$this->compile_check = false;
			$this->force_compile = false;
			$this->caching = true;
		} else if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::STAGING) {
			$this->compile_check = true;
			$this->force_compile = false;
			$this->caching = true;
		} else if (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::DEVELOPMENT) {
			$this->compile_check = true;
			$this->force_compile = true;
			$this->caching = false;
		} else {
			throw new CubeTemplateEngineException('Unknown Deployment Type Specified');
		}

    }

}

/**
 * Private exception subclass for use by TemplateEngine
 */
final class CubeTemplateEngineException extends Exception {

   /**
    * CubeTemplateEngineException constructor.  Takes a message and a code and invokes
    * the parent (Exception) constructor.  May eventaully contain additional code,
    * but for now acts as a means of determining the exact type of exception thrown
    * so it is possible to track down what threw it.
    *
    * @param $message   the message that this exception will contain
    * @param $code      the optional code of this exception (unused)
    * @returns          a CubeTemplateEngineException
    */
   public function __construct( $message, $code = 0 ) {
       // Call parent constructor
       parent::__construct( $message, $code );
   }

}
