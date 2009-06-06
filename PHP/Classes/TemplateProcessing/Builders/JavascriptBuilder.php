<?php
/**
 * JavascriptBuilder Class File
 *
 * Contains the class definition for the JavascriptBuilder, a concrete
 * subclass that inherits from the TemplateBuilder class. 
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

require( '../PHP/Classes/Caching/Smarty/SmartyMemcacheHandler.php' );

/**
 * JavascriptBuilder
 * 
 * Provides a definition for a JavascriptBuilder subclass of the
 * TemplateBuilder abstract class.
 *
 * @package Template
 * @subpackage Template Building
 */
class JavascriptBuilder extends TemplateBuilder {
    
    private $cacheID = null;
    private $hardCacheID = null;
    private $deployment = 'dev';
    private $dispatchPath = null;
    private $requestInfoBean = null;
    private $templateEngine = null;
    private $templateVariables = null;
        
    public function setRequestInfoBean( $requestInfoBean ) {
        $this->requestInfoBean = $requestInfoBean;
    }
    
    public function setTemplateVariables( $templateVariables ) {
        $this->templateVariables = $templateVariables;
        foreach( $templateVariables as $key => $value) $this->templateEngine->assign( $key, $value );
    }
    
    public function setCacheID( $cacheID ) {
        $this->templateEngine->cache_handler_func = 'smarty_cache_memcache';
        $this->templateEngine->caching = true;
        
        $this->cacheID = $cacheID;
    }

    public function setHardCacheID( $dispatchPath, $cacheID ) {
        $this->templateEngine->cache_handler_func = 'smarty_cache_memcache';
        $this->templateEngine->caching = true;
        
        $this->dispatchPath = $dispatchPath;
        $this->hardCacheID = $cacheID;
    }
    
    public function isCached() {
        return $this->templateEngine->is_cached( $this->dispatchPath, $this->cacheID );
    }

    public function isHardCached( $dispatchPath, $cacheID ) {
        return $this->templateEngine->is_cached( $this->dispatchPath, $this->cacheID );
    }
    
    public function setDispatchPath() {
//      $templateDispatcher = JavascriptDispatcher::getInstance( 'eGloo', 'Default' );
        $templateDispatcher = JavascriptDispatcher::getInstance( $this->application, $this->interfaceBundle );

        // TODO this should be moved to setTemplateEngine as part of the director's work
        $this->templateEngine = new JavascriptTemplateEngine( $this->deployment, 'us', $this->interfaceBundle );
        
        $this->dispatchPath = $templateDispatcher->dispatch( $this->requestInfoBean );
    }
    
    public function setTemplateEngine() {
    	$this->templateEngine = new JavascriptTemplateEngine( $this->deployment, 'us', $this->interfaceBundle );
    }
    
    public function run() {
        
        // assign variables
        
        // output        
//        $output = $this->templateEngine->fetch( $this->dispatchPath );
        $output = $this->templateEngine->fetch( $this->dispatchPath, $this->cacheID );
        
        return $output;        
    }
        
}

?>
