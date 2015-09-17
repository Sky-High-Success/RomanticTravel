<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_CSS_Parser {

	public static $id_counter = 0;
	public static $parsed_files = array();

	private $id;
	private $filename;

	/**
	 * Constructor.  Load and parse a given css file.
	 *
	 * @params $filename path to the css file.
	 */
	public function __construct( $filename ) {
		if ( ! file_exists( $filename ) ) {
			Scarcity_Samurai_Helper::error( "Cannot parse $filename because such file doesn't exist." );
		}

		$this->id = self::$id_counter++;
		$this->filename = $filename;
		$this->parse_css();
	}

	//------------------------------------------------------------------------
	// Parsing Functions
	//------------------------------------------------------------------------

	const RE_VARIABLES_COMMENT = '!/\*.*?Variables:(.*?)((\n[\*\s]*){2}|\*+/)(.*)!ms';
	const RE_COLOR_KEY_VAL = '!^\s*\**\s*(.*?)\s*:\s*(.*?)\s*;?\s*$!m';
	const RE_FUNCTION = '!^(darken|lighten)\(\s*(.*?),\s*(\d*)\s*\)\s*;?\s*!';
	const RE_COLOR = '!^#[0-9a-f]{6}$!';
	const RE_VARIABLE = '!^[A-Z_]+$!';

	const RE_COMMENT = '!\s*/\*.*?\*/\s*!ms';
	const RE_CSS_RULE_BLOCK = '!({(?:(?>[^{}]+)|(?R))*})!ms';
	const RE_CSS_RULE_KEY = '!((?:^|,)\s*)(\S)!ms';

	/**
	 * Parse the css file.  Stores parsed information in a static variable so
	 * that we only parse each file once, even if called again.
	 *
	 * - Pulls out a list of variables and associated creation / replacement
	 *   rules. @see get_replacements()
	 * - Constructs a new css template with PREFIX before all rules (makes
	 *   inserting of a class or id prefix easier later on).
	 *   @see get_css_prefix_template()
	 */
	private function parse_css() {
		// Don't parse the same file twice
		if (array_key_exists($this->filename, self::$parsed_files)) {
			return;
		}

		$css = file_get_contents($this->filename);

		$replacements = $this->get_replacements($css);
		$css_prefix_template = $this->get_css_prefix_template($css);
		$cache = array();

		self::$parsed_files[$this->filename] = compact('replacements', 'css_prefix_template', 'cache');
	}

	/**
	 * Parses the Variables section in the comment. This section should contain a
	 * list of 'key: value' replacements where key is what to replace, and the
	 * value is one of the following:
	 *    - function, e.g. darken(key, 10)
	 *    - another key, e.g. TEXT_COLOR
	 *    - css value, e.g. '26px', '#444'
	 */
	public function get_replacements($css) {
		$replacements = array();

		if ((preg_match(self::RE_VARIABLES_COMMENT, $css, $comment) === 1) &&
		    (preg_match_all(self::RE_COLOR_KEY_VAL, $comment[1], $key_value_pairs, PREG_SET_ORDER) !== false)) {
			foreach ($key_value_pairs as $key_value) {
				list(, $key, $value) = $key_value;

				if (preg_match(self::RE_FUNCTION, $value, $fun) === 1) { // Check if this looks like a function
					$value = array($fun[1], $fun[2], $fun[3]);
				} else if (preg_match(self::RE_VARIABLE, $value) === 1) { // or a replacement value
					$value = array('variable_replace', $value);
				}

				$replacements[$key] = $value;
			}
		}

		return $replacements;
	}

	/**
	 * Parse the css and place a PREFIX before all css rules.  This allows us
	 * to simply replace PREFIX with any actual class or id prefix we may want
	 * at a later stage.
	 */
	private function get_css_prefix_template($css) {
		// Strip comments
		$css = preg_replace(self::RE_COMMENT, '', $css);

		// Insert PREFIX before all css rules
		$css_bits = preg_split(self::RE_CSS_RULE_BLOCK, $css, -1, PREG_SPLIT_DELIM_CAPTURE);

		foreach ($css_bits as &$bit) {
			if ($bit[0] == '{') {
				continue;
			}

			$bit = preg_replace(self::RE_CSS_RULE_KEY, '$1PREFIX$2', $bit);
		}

		return join('', $css_bits);
	}

	public function get_replacement_vars( $extra_replacements = array(), $resolve = true ) {
		if ( ! array_key_exists( $this->filename, self::$parsed_files ) ) {
			Scarcity_Samurai_Helper::error("Cannot get the CSS of $filename because this file wasn't parsed yet.");
		}
		$extra_replacements = array_change_key_case( $extra_replacements, CASE_UPPER );

		extract( self::$parsed_files[ $this->filename ] );
		if ( isset( $cache[ $this->id ] ) ) {
			$replacements = $cache[ $this->id ];
		}

		// merge in any extra replacement values
		$replacements = array_merge( $replacements, $extra_replacements );
		self::$parsed_files[ $this->filename ]['cache'][ $this->id ] = $replacements;

		if ( $resolve ) {
			foreach ( $replacements as &$replacement ) {
				if ( ! is_array( $replacement ) ) continue;

				$function = array_shift( $replacement );
				if ( method_exists( $this, $function ) ) {
					$replacement = call_user_func_array( array( $this, $function ), $replacement );
				}
			}
		}

		return $replacements;
	}

	public function get_css($prefix = '', $extra_replacements = array()) {
		if (!array_key_exists($this->filename, self::$parsed_files)) {
			Scarcity_Samurai_Helper::error("Cannot get the CSS of $filename because this file wasn't parsed yet.");
		}

		extract( self::$parsed_files[ $this->filename ] );
		if ( isset( $cache[ $this->id ] ) ) {
			$replacements = $cache[ $this->id ];
		}

		// Process replacements
		$replacements = $this->get_replacement_vars( $extra_replacements );
		self::$parsed_files[ $this->filename ]['cache'][ $this->id ] = $replacements;

		// Replace prefix
		$css = str_replace('PREFIX', $prefix, $css_prefix_template);

		// Replace variables
		return str_replace(array_keys($replacements), array_values($replacements), $css);
	}

	private function get_value($key) {
		$replacements = isset( self::$parsed_files[$this->filename]['cache'][ $this->id ] )
			? self::$parsed_files[$this->filename]['cache'][ $this->id ]
			: self::$parsed_files[$this->filename]['replacements'];

		if (array_key_exists($key, $replacements)) {
			return $replacements[$key];
		}

		Scarcity_Samurai_Helper::error("$key is undefined");
	}

	private function variable_replace($key) {
		return $this->get_value($key);
	}

	private function darken($key, $percent) {
		$color = $this->get_value($key);
		$rgb = sscanf($color, '#%2x%2x%2x');
		$new_color = '#';

		foreach ($rgb as $c) {
			$new_color .= sprintf('%02x', max(0, round($c - ($percent * 255 / 100))));
		}

		return $new_color;
	}

	private function lighten($key, $percent) {
		$color = $this->get_value($key);
		$rgb = sscanf($color, '#%2x%2x%2x');
		$new_color = '#';

		foreach ($rgb as $c) {
			// percent * 255 / 100 -- figure out the percentage sice of 255
			// c + (percent * 255 / 100) calculate the new lighter color
			// min( 255, ... ) make sure we have a value of most 255
			// sprintf('%02x' ...) append hex value to generated color
			$new_color .= sprintf('%02x', min(255, round($c + ($percent * 255 / 100))));
		}

		return $new_color;
	}

}
