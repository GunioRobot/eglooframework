<?php
/**
 * eGlooInterfaceDirector Class File
 *
 * Contains the class definition for the eGlooInterfaceDirector, a subclass 
 * of the TemplateDirector class.
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
 * eGloo Interface Director
 * 
 * Provides a definition for an eGloo interface director subclass of
 * the TemplateDirector abstract class.
 *
 * @package Template
 */
class eGlooInterfaceDirector extends TemplateDirector {

    private $cacheID = null;
    private $requestInfoBean = null;
    private $templateBuilder = null;

    public function __construct( $requestInfoBean ) {
        $this->requestInfoBean = $requestInfoBean;
    }

    public function preProcessTemplate() {
        $this->templateBuilder->setRequestInfoBean( $this->requestInfoBean );
		$this->templateBuilder->setTemplateEngine();
        $this->templateBuilder->setDispatchPath();
		$this->templateBuilder->resolveTemplateRoot();

        if ( $this->cacheID !== null ) {
            $this->templateBuilder->setCacheID( $this->cacheID, $this->ttl );
        }
    }

    public function processTemplate() {
        return $this->templateBuilder->run();
    }

    public function postProcessTemplate() {
        
    }

    public function setTemplateVariables( $tokenArray ) {
        $this->templateBuilder->setTemplateVariables( $tokenArray );
    }

    public function setContentProcessors( $contentProcessorArray ) {
        $this->templateBuilder->setContentProcessors( $contentProcessorArray );
    }

    public function getTemplateBuilder() {
        
    }
    
    public function isCached() {
        return $this->templateBuilder->isCached();
    }

    public function isHardCached( $dispatchPath, $cacheID ) {
        return $this->templateBuilder->isHardCached( $dispatchPath, $cacheID );
    }
    
    public function setCacheID( $cacheID, $ttl = 3600 ) {
        $this->cacheID = $cacheID;
        $this->ttl = $ttl;
    }
    
    public function setHardCacheID( $dispatchPath, $cacheID, $ttl = 3600 ) {
        $this->templateBuilder->setHardCacheID( $dispatchPath, $cacheID, $ttl );
    }
    
    public function setTemplateBuilder( TemplateBuilder $templateBuilder ) {
        $this->templateBuilder = $templateBuilder;
        // TODO uncomment this as part of refactoring for hard caching
        //$this->templateBuilder->setTemplateEngine();
    }
    
}

?>
