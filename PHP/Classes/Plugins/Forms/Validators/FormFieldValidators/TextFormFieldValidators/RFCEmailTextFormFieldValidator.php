<?php
/**
 * RFCEmailTextFormFieldValidator Class File
 *
 * $file_block_description
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
 * @category Plugins
 * @package Forms
 * @subpackage Validators
 * @version 1.0
 */

/**
 * RFCEmailTextFormFieldValidator
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Validators
 */
class RFCEmailTextFormFieldValidator extends TextFormFieldValidator {

	public function validate( $emailTextFormField ) {
		$retVal = false;

		$retVal = parent::validate( $emailTextFormField );

		if ( $retVal )	{
			$retVal = $this->validateRFCEmail( $emailTextFormField );
		}

		return $retVal;
	}

	private function validateRFCEmail( $emailTextFormField ) {
		$retVal = false;

		$retVal = $this->isValidEmailAddress( $emailTextFormField->getValue() );

		return $retVal;
	}

	/**
	 * Following two methods adapted from RFC 822/2822/5322 Email Parser
	 * 
	 * By Cal Henderson <cal@iamcal.com>
	 * 
	 * This section of code is dual licensed:
	 * CC Attribution-ShareAlike 2.5 - http://creativecommons.org/licenses/by-sa/2.5/
	 * GPLv3 - http://www.gnu.org/copyleft/gpl.html
	 *
	 * Remainder of this class remains Copyright eGloo LLC and is released under the terms of the Apache 
	 * License, Version 2.0, with the interpretation that this inclusion is compatible under the
	 * CC Attribution-ShareAlike 2.5 licensing of Cal Henderson's email validator.	No assignment or interpretation
	 * of licensing for any other part of the eGloo Framework nor its included 3rd-party libraries shall be taken
	 * from the inclusion of this code.
	 */
	private function isValidEmailAddress( $email, $options=array() ) {
		#
		# you can pass a few different named options as a second argument,
		# but the defaults are usually a good choice.
		#

		$defaults = array(
			'allow_comments'	=> true,
			'public_internet'	=> true, # turn this off for 'strict' mode
		);

		$opts = array();
		foreach ($defaults as $k => $v) $opts[$k] = isset($options[$k]) ? $options[$k] : $v;
		$options = $opts;
		


		####################################################################################
		#
		# NO-WS-CTL		  =		  %d1-8 /		  ; US-ASCII control characters
		#						  %d11 /		  ;	 that do not include the
		#						  %d12 /		  ;	 carriage return, line feed,
		#						  %d14-31 /		  ;	 and white space characters
		#						  %d127
		# ALPHA			 =	%x41-5A / %x61-7A	; A-Z / a-z
		# DIGIT			 =	%x30-39

		$no_ws_ctl	= "[\\x01-\\x08\\x0b\\x0c\\x0e-\\x1f\\x7f]";
		$alpha		= "[\\x41-\\x5a\\x61-\\x7a]";
		$digit		= "[\\x30-\\x39]";
		$cr		= "\\x0d";
		$lf		= "\\x0a";
		$crlf		= "(?:$cr$lf)";


		####################################################################################
		#
		# obs-char		  =		  %d0-9 / %d11 /		  ; %d0-127 except CR and
		#						  %d12 / %d14-127		  ;	 LF
		# obs-text		  =		  *LF *CR *(obs-char *LF *CR)
		# text			  =		  %d1-9 /		  ; Characters excluding CR and LF
		#						  %d11 /
		#						  %d12 /
		#						  %d14-127 /
		#						  obs-text
		# obs-qp		  =		  "\" (%d0-127)
		# quoted-pair	  =		  ("\" text) / obs-qp

		$obs_char	= "[\\x00-\\x09\\x0b\\x0c\\x0e-\\x7f]";
		$obs_text	= "(?:$lf*$cr*(?:$obs_char$lf*$cr*)*)";
		$text		= "(?:[\\x01-\\x09\\x0b\\x0c\\x0e-\\x7f]|$obs_text)";

		#
		# there's an issue with the definition of 'text', since 'obs_text' can
		# be blank and that allows qp's with no character after the slash. we're
		# treating that as bad, so this just checks we have at least one
		# (non-CRLF) character
		#

		$text		= "(?:$lf*$cr*$obs_char$lf*$cr*)";
		$obs_qp		= "(?:\\x5c[\\x00-\\x7f])";
		$quoted_pair	= "(?:\\x5c$text|$obs_qp)";


		####################################################################################
		#
		# obs-FWS		  =		  1*WSP *(CRLF 1*WSP)
		# FWS			  =		  ([*WSP CRLF] 1*WSP) /	  ; Folding white space
		#						  obs-FWS
		# ctext			  =		  NO-WS-CTL /	  ; Non white space controls
		#						  %d33-39 /		  ; The rest of the US-ASCII
		#						  %d42-91 /		  ;	 characters not including "(",
		#						  %d93-126		  ;	 ")", or "\"
		# ccontent		  =		  ctext / quoted-pair / comment
		# comment		  =		  "(" *([FWS] ccontent) [FWS] ")"
		# CFWS			  =		  *([FWS] comment) (([FWS] comment) / FWS)

		#
		# note: we translate ccontent only partially to avoid an infinite loop
		# instead, we'll recursively strip *nested* comments before processing
		# the input. that will leave 'plain old comments' to be matched during
		# the main parse.
		#

		$wsp		= "[\\x20\\x09]";
		$obs_fws	= "(?:$wsp+(?:$crlf$wsp+)*)";
		$fws		= "(?:(?:(?:$wsp*$crlf)?$wsp+)|$obs_fws)";
		$ctext		= "(?:$no_ws_ctl|[\\x21-\\x27\\x2A-\\x5b\\x5d-\\x7e])";
		$ccontent	= "(?:$ctext|$quoted_pair)";
		$comment	= "(?:\\x28(?:$fws?$ccontent)*$fws?\\x29)";
		$cfws		= "(?:(?:$fws?$comment)*(?:$fws?$comment|$fws))";


		#
		# these are the rules for removing *nested* comments. we'll just detect
		# outer comment and replace it with an empty comment, and recurse until
		# we stop.
		#

		$outer_ccontent_dull	= "(?:$fws?$ctext|$quoted_pair)";
		$outer_ccontent_nest	= "(?:$fws?$comment)";
		$outer_comment		= "(?:\\x28$outer_ccontent_dull*(?:$outer_ccontent_nest$outer_ccontent_dull*)+$fws?\\x29)";


		####################################################################################
		#
		# atext			  =		  ALPHA / DIGIT / ; Any character except controls,
		#						  "!" / "#" /	  ;	 SP, and specials.
		#						  "$" / "%" /	  ;	 Used for atoms
		#						  "&" / "'" /
		#						  "*" / "+" /
		#						  "-" / "/" /
		#						  "=" / "?" /
		#						  "^" / "_" /
		#						  "`" / "{" /
		#						  "|" / "}" /
		#						  "~"
		# atom			  =		  [CFWS] 1*atext [CFWS]

		$atext		= "(?:$alpha|$digit|[\\x21\\x23-\\x27\\x2a\\x2b\\x2d\\x2f\\x3d\\x3f\\x5e\\x5f\\x60\\x7b-\\x7e])";
		$atom		= "(?:$cfws?(?:$atext)+$cfws?)";


		####################################################################################
		#
		# qtext			  =		  NO-WS-CTL /	  ; Non white space controls
		#						  %d33 /		  ; The rest of the US-ASCII
		#						  %d35-91 /		  ;	 characters not including "\"
		#						  %d93-126		  ;	 or the quote character
		# qcontent		  =		  qtext / quoted-pair
		# quoted-string	  =		  [CFWS]
		#						  DQUOTE *([FWS] qcontent) [FWS] DQUOTE
		#						  [CFWS]
		# word			  =		  atom / quoted-string

		$qtext		= "(?:$no_ws_ctl|[\\x21\\x23-\\x5b\\x5d-\\x7e])";
		$qcontent	= "(?:$qtext|$quoted_pair)";
		$quoted_string	= "(?:$cfws?\\x22(?:$fws?$qcontent)*$fws?\\x22$cfws?)";

		#
		# changed the '*' to a '+' to require that quoted strings are not empty
		#

		$quoted_string	= "(?:$cfws?\\x22(?:$fws?$qcontent)+$fws?\\x22$cfws?)";
		$word		= "(?:$atom|$quoted_string)";


		####################################################################################
		#
		# obs-local-part  =		  word *("." word)
		# obs-domain	  =		  atom *("." atom)

		$obs_local_part = "(?:$word(?:\\x2e$word)*)";
		$obs_domain = "(?:$atom(?:\\x2e$atom)*)";


		####################################################################################
		#
		# dot-atom-text	  =		  1*atext *("." 1*atext)
		# dot-atom		  =		  [CFWS] dot-atom-text [CFWS]

		$dot_atom_text	= "(?:$atext+(?:\\x2e$atext+)*)";
		$dot_atom	= "(?:$cfws?$dot_atom_text$cfws?)";


		####################################################################################
		#
		# domain-literal  =		  [CFWS] "[" *([FWS] dcontent) [FWS] "]" [CFWS]
		# dcontent		  =		  dtext / quoted-pair
		# dtext			  =		  NO-WS-CTL /	  ; Non white space controls
		# 
		#						  %d33-90 /		  ; The rest of the US-ASCII
		#						  %d94-126		  ;	 characters not including "[",
		#										  ;	 "]", or "\"

		$dtext		= "(?:$no_ws_ctl|[\\x21-\\x5a\\x5e-\\x7e])";
		$dcontent	= "(?:$dtext|$quoted_pair)";
		$domain_literal = "(?:$cfws?\\x5b(?:$fws?$dcontent)*$fws?\\x5d$cfws?)";


		####################################################################################
		#
		# local-part	  =		  dot-atom / quoted-string / obs-local-part
		# domain		  =		  dot-atom / domain-literal / obs-domain
		# addr-spec		  =		  local-part "@" domain

		$local_part = "(($dot_atom)|($quoted_string)|($obs_local_part))";
		$domain		= "(($dot_atom)|($domain_literal)|($obs_domain))";
		$addr_spec	= "$local_part\\x40$domain";



		#
		# this was previously 256 based on RFC3696, but dominic's errata was accepted.
		#

		if (strlen($email) > 254) return false;


		#
		# we need to strip nested comments first - we replace them with a simple comment
		#

		if ($options['allow_comments']){

			$email = $this->stripEmailComments($outer_comment, $email, "(x)");
		}


		#
		# now match what's left
		#

		if (!preg_match("!^$addr_spec$!", $email, $m)){

			return false;
		}

		$bits = array(
			'local'			=> isset($m[1]) ? $m[1] : '',
			'local-atom'		=> isset($m[2]) ? $m[2] : '',
			'local-quoted'		=> isset($m[3]) ? $m[3] : '',
			'local-obs'		=> isset($m[4]) ? $m[4] : '',
			'domain'		=> isset($m[5]) ? $m[5] : '',
			'domain-atom'		=> isset($m[6]) ? $m[6] : '',
			'domain-literal'	=> isset($m[7]) ? $m[7] : '',
			'domain-obs'		=> isset($m[8]) ? $m[8] : '',
		);


		#
		# we need to now strip comments from $bits[local] and $bits[domain],
		# since we know they're in the right place and we want them out of the
		# way for checking IPs, label sizes, etc
		#

		if ($options['allow_comments']){
			$bits['local']	= $this->stripEmailComments($comment, $bits['local']);
			$bits['domain'] = $this->stripEmailComments($comment, $bits['domain']);
		}


		#
		# length limits on segments
		#

		if (strlen($bits['local']) > 64) return false;
		if (strlen($bits['domain']) > 255) return false;


		#
		# restrictions on domain-literals from RFC2821 section 4.1.3
		#
		# RFC4291 changed the meaning of :: in IPv6 addresses - i can mean one or
		# more zero groups (updated from 2 or more).
		#

		if (strlen($bits['domain-literal'])){

			$Snum			= "(\d{1,3})";
			$IPv4_address_literal	= "$Snum\.$Snum\.$Snum\.$Snum";

			$IPv6_hex		= "(?:[0-9a-fA-F]{1,4})";

			$IPv6_full		= "IPv6\:$IPv6_hex(?:\:$IPv6_hex){7}";

			$IPv6_comp_part		= "(?:$IPv6_hex(?:\:$IPv6_hex){0,7})?";
			$IPv6_comp		= "IPv6\:($IPv6_comp_part\:\:$IPv6_comp_part)";

			$IPv6v4_full		= "IPv6\:$IPv6_hex(?:\:$IPv6_hex){5}\:$IPv4_address_literal";

			$IPv6v4_comp_part	= "$IPv6_hex(?:\:$IPv6_hex){0,5}";
			$IPv6v4_comp		= "IPv6\:((?:$IPv6v4_comp_part)?\:\:(?:$IPv6v4_comp_part\:)?)$IPv4_address_literal";

			#
			# IPv4 is simple
			#

			if ( preg_match("!^\[$IPv4_address_literal\]$!", $bits['domain'], $m) ) {

				if (intval($m[1]) > 255) return false;
				if (intval($m[2]) > 255) return false;
				if (intval($m[3]) > 255) return false;
				if (intval($m[4]) > 255) return false;

			} else {

				#
				# this should be IPv6 - a bunch of tests are needed here :)
				#

				while (true) {

					if ( preg_match("!^\[$IPv6_full\]$!", $bits['domain']) ) {
						break;
					}

					if ( preg_match("!^\[$IPv6_comp\]$!", $bits['domain'], $m) ) {
						list($a, $b) = explode('::', $m[1]);
						$folded = (strlen($a) && strlen($b)) ? "$a:$b" : "$a$b";
						$groups = explode(':', $folded);
						if (count($groups) > 7) return false;
						break;
					}

					if ( preg_match("!^\[$IPv6v4_full\]$!", $bits['domain'], $m) ) {

						if (intval($m[1]) > 255) return false;
						if (intval($m[2]) > 255) return false;
						if (intval($m[3]) > 255) return false;
						if (intval($m[4]) > 255) return false;
						break;
					}

					if ( preg_match("!^\[$IPv6v4_comp\]$!", $bits['domain'], $m) ) {
						list($a, $b) = explode('::', $m[1]);
						$b = substr($b, 0, -1); # remove the trailing colon before the IPv4 address
						$folded = (strlen($a) && strlen($b)) ? "$a:$b" : "$a$b";
						$groups = explode(':', $folded);
						if (count($groups) > 5) return false;
						break;
					}

					return false;
				}
			}
		} else {

			#
			# the domain is either dot-atom or obs-domain - either way, it's
			# made up of simple labels and we split on dots
			#

			$labels = explode('.', $bits['domain']);


			#
			# this is allowed by both dot-atom and obs-domain, but is un-routeable on the
			# public internet, so we'll fail it (e.g. user@localhost)
			#

			if ($options['public_internet']){
				if (count($labels) == 1) return false;
			}


			#
			# checks on each label
			#

			foreach ($labels as $label){
				if (strlen($label) > 63) return false;
				if (substr($label, 0, 1) == '-') return false;
				if (substr($label, -1) == '-') return false;
			}


			#
			# Petflow's arbitrary TLD check.
			#

			//excluded_tlds: 'cm'
			$tlds = array(
			  'aero','asia','biz','cat','com','coop','edu','gov','info','int','jobs',
			  'mil','mobi','museum','name','net','org','pro','tel','travel','ac','ad',
			  'ae','af','ag','ai','al','am','an','ao','aq','ar','as','at','au','aw','ax',
			  'az','ba','bb','bd','be','bf','bg','bh','bi','bj','bm','bn','bo','br','bs',
			  'bt','bv','bw','by','bz','ca','cc','cd','cf','cg','ch','ci','ck','cl','cn',
			  'co','cr','cu','cv','cx','cy','cz','de','dj','dk','dm','do','dz','ec','ee',
			  'eg','er','es','et','eu','fi','fj','fk','fm','fo','fr','ga','gb','gd','ge',
			  'gf','gg','gh','gi','gl','gm','gn','gp','gq','gr','gs','gt','gu','gw','gy',
			  'hk','hm','hn','hr','ht','hu','id','ie','il','im','in','io','iq','ir','is',
			  'it','je','jm','jo','jp','ke','kg','kh','ki','km','kn','kp','kr','kw','ky',
			  'kz','la','lb','lc','li','lk','lr','ls','lt','lu','lv','ly','ma','mc','md',
			  'me','mg','mh','mk','ml','mm','mn','mo','mp','mq','mr','ms','mt','mu','mv',
			  'mw','mx','my','mz','na','nc','ne','nf','ng','ni','nl','no','np','nr','nu',
			  'nz','om','pa','pe','pf','pg','ph','pk','pl','pm','pn','pr','ps','pt','pw',
			  'py','qa','re','ro','rs','ru','rw','sa','sb','sc','sd','se','sg','sh','si',
			  'sj','sk','sl','sm','sn','so','sr','st','su','sv','sy','sz','tc','td','tf',
			  'tg','th','tj','tk','tl','tm','tn','to','tp','tr','tt','tv','tw','tz','ua',
			  'ug','uk','us','uy','uz','va','vc','ve','vg','vi','vn','vu','wf','ws','ye',
			  'yt','za','zm','zw',
			);
			$last_label = array_pop($labels);
			if (!in_array($last_label, $tlds)){
				return false;
			}
			// put the label back so we don't have to mess with code this originally had (to make merges easier later).
			$labels[] = $last_label;


			#
			# last label can't be all numeric
			#

			if ($options['public_internet']){
				if (preg_match('!^[0-9]+$!', array_pop($labels))) return false;
			}
		}


		return true;
	}

	private function stripEmailComments($comment, $email, $replace=''){
		while(true) {
			$new = preg_replace("!$comment!", $replace, $email);
			if (strlen($new) == strlen($email)){
				return $email;
			}
			$email = $new;
		}
	}

}

