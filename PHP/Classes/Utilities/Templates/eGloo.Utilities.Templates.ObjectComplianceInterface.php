<?php
namespace eGloo\Utilities\Templates;

/**
 * 
 * Provides a method, by which an implementing class could provide
 * a template "compliant" form of itself; eventually object apperance
 * (passing to) within templates will be entirely deprecated
 * @author petflowdeveloper
 *
 */
interface ObjectComplianceInterface { 
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function toTemplateCompliant();
}