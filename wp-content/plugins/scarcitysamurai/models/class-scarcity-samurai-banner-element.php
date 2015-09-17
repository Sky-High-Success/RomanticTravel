<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Banner_Element extends Scarcity_Samurai_Model {

	public $short_name, $table_name, $defaults;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'banner_element';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_banner_elements';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'banner_id' => array(
				'type' => 'integer'
			),
			'position' => array(
				'type' => 'integer'
			),
			'type' => array(
				'type' => 'string'
			),
			'style' => array(
				'type' => 'json'
			),
			'data' => array(
				'type' => 'json'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Banner_Element' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				banner_id BIGINT(20) UNSIGNED,
				position SMALLINT UNSIGNED,
				type VARCHAR(200) NOT NULL,
				style LONGTEXT,
				data LONGTEXT,
				PRIMARY KEY (id),
				FOREIGN KEY (banner_id) REFERENCES " . Scarcity_Samurai_Model::get( 'Banner' )->table_name . "(id)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}


	/**
	 * Find the file associated with this timer style and return a parser object for it.
	 */
	public static function get_timer_style_css_parser( $style_id ) {
		$filename = plugin_dir_path( SS_PLUGIN_FILE ) . "stylesheets/css/timers/{$style_id}.css";
		if ( ! file_exists( $filename ) ) return;

		return new Scarcity_Samurai_CSS_Parser( $filename );
	}

	/**
	 * Get the basic banner element style shell html
	 *
	 * @param string $style_id
	 * @return string
	 */
	public static function get_timer_style_html( $style_id ) {
		$filename = plugin_dir_path( SS_PLUGIN_FILE ) . "html/timers/{$style_id}.php";
		if ( ! file_exists( $filename ) ) return '';

		// get banner html
		$html = Scarcity_Samurai_Helper::load_html( $filename );
		return "<div class='ss-banner-timer-wrapper'>{$html}</div>";
	}

	/**
	 * Get page specific banner element html
	 *
	 * @param array $element
	 * @param integer $page_id
	 * @param array $args
	 * @return string
	 */
	public static function get_html($element, $page_id, $banner_data) {
		global $scarcity_samurai_dir;

		$element_id = $element['id'];
		$element_style_id = isset( $element['style']['id'] ) ? $element['style']['id'] : null;
		$data = $element['data'];

		switch ($element['type']) {
			case 'text':
				return '<span class="ss-banner-element-wrapper ss-banner-text-wrapper">' . stripslashes( $data['text'] ) . '</span>';

			case 'image':
				$src = esc_attr( $data['src'] );
				$style = ( isset( $data['width'] ) ? "width:{$data['width']}px;" : '' ) .
				         ( isset( $data['height'] ) ? "height:{$data['height']}px" : '' );
				return "<div class='ss-banner-element-wrapper ss-banner-image-wrapper'><img src='{$src}' style='{$style}'></div>";

			case 'timer':
				$classes = "ss-timer-$element_id ss-banner-element-wrapper ss-banner-timer-wrapper";

				$timer_style_id = isset( $banner_data['timer_id' ] ) ? $banner_data['timer_id'] : null;

				if ( $timer_style_id === null ) {
					$container = 'span';
					$timer_content = '';
				} else {
					$container = 'div';
					$classes .= " ss-styled-timer ss-timer-style-$timer_style_id";
					$timer_html_filename = $scarcity_samurai_dir . "html/timers/{$timer_style_id}.php";
					$timer_content = Scarcity_Samurai_Helper::load_html( $timer_html_filename );
				}

				return "<$container class='$classes'>$timer_content</$container>";
		}

		return '';
	}

	public static function get_js_data( $element ) {
		$element_id = $element['id'];
		$element_type = $element['type'];

		switch ( $element_type ) {
			case 'timer':
				return array(
					'element_type' => 'timer',
					'element_id' => $element_id,
					'deadline' => Scarcity_Samurai_Page::end_time()
				);
		}

		return null;
	}

}
