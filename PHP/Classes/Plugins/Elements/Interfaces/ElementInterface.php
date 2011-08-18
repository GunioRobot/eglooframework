<?php
/**
 * ElementInterface Interface File
 *
 * Needs to be commented
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
 * @package Elements
 * @version 1.0
 */

/**
 * ElementInterface
 * 
 * Needs to be commented
 *
 * @package Elements
 */
interface ElementInterface {
    
    public function getElementType();

    public function setElementType( $elementType );

    public function getElementTypeID();

    public function setElementTypeID( $elementTypeID );
    
    public function getElementID();

    public function setElementID( $elementID );
    
    public function getElementInstanceID();

    public function setElementInstanceID( $elementInstanceID );
    
    public function getElementCreatorProfileID();

    public function setElementCreatorProfileID( $elementCreatorID );
    
    public function getElementInstanceCreatorProfileID();

    public function setElementInstanceCreatorProfileID( $elementInstanceCreatorID );

    public function getElementPackage();
    
    public function setElementPackage( $elementPackage );

    public function getElementPackagePath();
    
    public function setElementPackagePath( $elementPackagePath );
        
}
