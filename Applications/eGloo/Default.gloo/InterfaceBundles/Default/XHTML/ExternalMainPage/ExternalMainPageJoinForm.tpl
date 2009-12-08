<!--{* @Name:   ExternalMainPageJoinForm
     * @Author: George Cooper (george.cooper@egloo.com)
     *
     * @Description: This template represents the default structural format for the
     * eGloo account registration form.  It is meant to be plugged into a larger
     * template structure and requires external CSS for styling and screen positioning.
     * Effort is made to make this file XHTML compliant for at least XHTML version
     * 1.0 Transitional.
     *
     * @Standalone: No
     * @Provides: Structural Markup, Unique Page Elements, CSS classes
     * @Caching: Yes
     * @Compatibility: XHTML 1.0 Transitional (Tested)
     *
     * @Token username (string) username of the account that this profile belongs to
     *}-->     

<div id="contactInformationBox" class="contactInformationBox">
    <form name="signup" id="signup" method="POST" action="/account/registerNewAccount/">
        <fieldset id="signup_fieldset">
            <LEGEND ACCESSKEY=I>Sign Up Form</LEGEND>
            <div id="contactInformationDiv" class="licenseText">
	                <div class="labelcell">First Name:</div>
	                <input id="firstNameInput" type="text" name="firstNameInput" />
	                <p class="error" id="firstNameInputError">this is an error</p>
	                <div class="labelcell">Last Name:</div>
	                <input id="lastNameInput" type="text" name="lastNameInput" />
	                <p class="error" id="lastNameInputError">this is an error</p>
	                <span class="labelcell">Birthday:</span>
	                <!--{html_select_date prefix="userBirth" start_year="-13" end_year="-106" reverse_years=true
	                	time="--" day_value_format="%02d" day_format="%02d" month_extra="style=\"width:100px;\"" 
						day_extra="style=\"width:45px;\"" year_extra="style=\"width:60px;\""}-->
	                <span class="labelcell">Gender:</span>
	                <select id="userGender" name="userGender">
	                    <option value="Male">Male&nbsp;</option>
	                    <option value="Female" >Female&nbsp;</option>
	                </select>
	                <span class="labelcell">Email:</span>
	                <input id="userEmail" type="text" name="userEmail"/>
	                <p class="error" id="userEmailError">this is an error</p>                    
            </div>
            <div id="contactInformationDiv2" class="licenseText">
	                <div class="labelcell2">Preferred Username:</div>
	                <input id="userPreferredAccountName" type="text" name="userPreferredAccountName"/>
	                <p class="error" id="userPreferredAccountNameError">this is an error</p>
	                <div class="labelcell2">Account Password:</div>
	                <input id="userRequestedAccountPassword" type="password" name="userRequestedAccountPassword"/>
	                <p class="error" id="userRequestedAccountPasswordError">this is an error</p>
	                <div class="labelcell2">Confirm Password:</div>
	                <input id="userConfirmedAccountPassword" type="password" name="userConfirmedAccountPassword"/>
	                <p class="error" id="userConfirmedAccountPasswordError">this is an error</p>
            </div>
	        <div id="confirmSignupDiv">
	                By clicking the "Sign Up" button below or using the web site in any manner:
	            <div id="anon_p1">
	                <ol>
	                    <li>
	                        You represent that you have read and understand the eGloo Beta License Agreement and have 
	                        the capacity and authority to enter into it; and
	                    </li>
	                    <li>
	                        You agree to be bound by all the terms and conditions of this license agreement.
	                    </li>
	                </ol>
	            </div>
	            <div id="anon_p2">
	                <input id="userAcceptsLicense" type="checkbox" name="userAcceptsLicense" value="false"/>
	                <label id="userLicenseAgreementCheckBoxLabel" for="userLicenseAgreement">
	                    I have read and agree to the eGloo Beta License Agreement.
	                </label>		                
	            </div>
	            <div id="referralCodeLabel">Referral:</div>
				<input id="referralCodeInput" type="text" name="userReferralCode" value="" />
				<p class="error" id="referralCodeInputError">this is an error</p>
	            <input id="signupbutton" type="submit" name="signupbutton" value="Sign Up" class="bbutton" />
	        </div>

        </fieldset>
    </form>
</div>

<div id="licenseInformationBox">
    <form name="signup" id="signup2" method="get" action="#">
        <fieldset id="signup2_fieldset">
            <LEGEND ACCESSKEY=I>Beta License Agreement</LEGEND>
            <div id="licenseTextWrapper" class="licenseText">&nbsp;
	            <div id="licenseText">
					<!--{include file="Core/eGloo/XHTML/License/Registration/Beta_Version_1.0.tpl"}-->
	            </div>
            </div>
        </fieldset>
    </form>
</div>
<div id="LicenseScrollArrowContainer">
    <span id="licenseUpScroll">▲</span>
    <span id="licenseDownScroll">▼</span>
</div>