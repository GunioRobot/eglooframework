<?php
/**
 * AlertListRequestProcessor Class File
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
 * @package RequestProcessing
 * @subpackage Alert
 * @version 1.0
 */

/**
 * AlertListRequestProcessor
 *
 * Needs to be commented
 *
 * @package RequestProcessing
 * @subpackage Alert
 */
class AlertListRequestProcessor extends RequestProcessor {
    // TODO SANITY CHECKs -- Do not assume RequestProcessor wasn't drunk when it sent this to you
    private static $_template = 'blogEntry_default.tpl';

    public function processRequest() {
        $this->_templateEngine = new XHTMLDefaultTemplateEngine( 'dev', 'us' );

        $alertList = TestValueConstructor::getAlertList();

        $this->_templateEngine->assign( 'alertList', $alertList );

        $this->_templateEngine->display( '../Templates/Frameworks/Common/XHTML/Alert/AlertListContainer.tpl' );
    }

}

?>