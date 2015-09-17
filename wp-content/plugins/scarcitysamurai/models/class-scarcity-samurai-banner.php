<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Banner extends Scarcity_Samurai_Model {

	const DEFAULT_BANNER_STYLE_ID = '1';
	const DEFAULT_TIMER_STYLE_ID = '1';

	public $short_name, $table_name, $defaults;
	protected $fields;
	private static $default_fixed_banner_name = 'Red banner with text and timer';
	private static $default_inline_banner_name = 'Red Half Page Banner (300px) - Time Only';

	public function __construct() {
		global $wpdb;

		$this->short_name = 'banner';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_banners';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'name' => array(
				'type' => 'string'
			),
			'style' => array(
				'type' => 'json'
			),
			'data' => array(
				'type' => 'json'
			)
		);

		$this->defaults = array(
			array(
				'name' => self::$default_fixed_banner_name,
				'style' => array(
					'id' => '1',
					'timer_id' => '1',
					'background_color' => '#cf0000'
				),
				'data' => array(
					'inline' => false
				),
				'banner_elements' => array(
					array(
						'position' => 0,
						'type' => 'text',
						'data' => array(
							'text' => 'This offer will expire in: '
						)
					),
					array(
						'position' => 1,
						'type' => 'timer'
					)
				)
			),
		);

		$inline_banner_colors = array(
			'Red' => array( 'background_color' => '#cf0000', 'text_color' => '#ffffff' ),
			'Gold' => array( 'background_color' => '#ffcc00', 'text_color' => '#000000' ),
			'Green' => array( 'background_color' => '#006600', 'text_color' => '#ffffff' ),
			'Blue' => array( 'background_color' => '#000099', 'text_color' => '#ffffff' ),
		);

		foreach ( $inline_banner_colors as $color_name => $color_values ) {
			array_push( $this->defaults,
				array(
					'name' => "{$color_name} Half Page Banner (300px) - Time Only",
					'style' => array_merge( $color_values, array(
						'id' => '1',
						'timer_id' => '1',
						'width' => '300px',
					) ),
					'data' => array(
						'inline' => true,
					),
					'banner_elements' => array(
						array(
							'position' => 0,
							'type' => 'timer',
						),
					),
				),
				array(
					'name' => "{$color_name} Full Banner (468px) - Only 12:34:56:78 to go",
					'style' => array_merge( $color_values, array(
						'id' => '1',
						'timer_id' => '1',
						'width' => '468px',
					) ),
					'data' => array(
						'inline' => true,
					),
					'banner_elements' => array(
						array(
							'position' => 0,
							'type' => 'text',
							'data' => array(
								'text' => 'Only',
							),
						),
						array(
							'position' => 1,
							'type' => 'timer',
						),
						array(
							'position' => 2,
							'type' => 'text',
							'data' => array(
								'text' => 'to go',
							),
						),
					),
				),
				array(
					'name' => "{$color_name} Leaderboard (728px) - Offer expires in: 12:34:56:78 Act NOW!",
					'style' => array_merge( $color_values, array(
						'id' => '1',
						'timer_id' => '1',
						'width' => '728px',
					) ),
					'data' => array(
						'inline' => true,
					),
					'banner_elements' => array(
						array(
							'position' => 0,
							'type' => 'text',
							'data' => array(
								'text' => 'Offer expires in:',
							),
						),
						array(
							'position' => 1,
							'type' => 'timer',
						),
						array(
							'position' => 2,
							'type' => 'text',
							'data' => array(
								'text' => 'Act NOW!',
							),
						),
					),
				)
			);
		}
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Banner' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(100) NOT NULL,
				style LONGTEXT,
				data LONGTEXT,
				PRIMARY KEY (id),
				UNIQUE (name)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

	// check to see if there is an existing banner with this name
	public static function banner_name_exists( $name ) {
		$existing_banners = Scarcity_Samurai_Model::get( 'Banner' )->all();
		foreach ( $existing_banners as $existing_banner ) {
			if ( $existing_banner['name'] === $name ) {
				return true;
			}
		}
		return false;
	}

	public function delete( $data ) {
		// Remove the banner from the pages it appears on
		if ( Scarcity_Samurai_Model::get( 'Pages_Banners' )->delete( array( 'banner_id' => $data['id'] ) ) === false ) {
			return false;
		}

		// Delete all the banner elements in this banner
		if ( Scarcity_Samurai_Model::get( 'Banner_Element' )->delete( array( 'banner_id' => $data['id'] ) ) === false ) {
			return false;
		}

		// Delete the banner
		return parent::delete( array( 'id' => $data['id'] ) );
	}

	/**
	 * Get all elements contained within a specific banner, ordered by their
	 * position in the banner.
	 *
	 * @param integer $id the saved banner's id
	 * @param string $element_type options element type to restrict the
	 *    returned elements.
	 */
	public static function get_elements( $id, $element_type = null ) {
		$data = array( 'banner_id' => $id );

		if ( $element_type !== null ) {
			$data['type'] = $element_type;
		}

		return Scarcity_Samurai_Model::get( 'Banner_Element' )->all( 'position ASC', $data );
	}

	public static function get_style_css_parser( $inline, $style_id ) {
		$banners_css_dir = ( $inline ? SS_INLINE_BANNERS_CSS_DIR : SS_FIXED_BANNERS_CSS_DIR );
		$filename = "{$banners_css_dir}{$style_id}.css";

		return file_exists( $filename ) ? new Scarcity_Samurai_CSS_Parser( $filename ) : null;
	}

	public static function get_css( $banner_id ) {
		$css = '';
		$css_prefix = ".ss-banner-{$banner_id} ";

		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find( $banner_id );
		$inline = $banner['data']['inline'];
		$style_id = ( isset( $banner['style']['id'] ) ? $banner['style']['id'] : self::DEFAULT_BANNER_STYLE_ID );
		$css_parser = self::get_style_css_parser( $inline, $style_id );

		if ( $css_parser !== null ) {
			$css_args = ( isset( $banner['style'] ) ? $banner['style'] : array() );
			$css_vars = $css_parser->get_replacement_vars( $css_args );
			$css .= $css_parser->get_css( $css_prefix, $css_args );
		}

		$elements = self::get_elements( $banner_id );
		$timer_css = '';

		foreach ( $elements as $element ) {
			switch ( $element['type'] ) {
				case 'timer':
					if ( ( $timer_css !== '' ) || ! isset( $banner['style']['timer_id'] ) ) {
						continue;
					}

					$timer_style_id = $banner['style']['timer_id'];
					$timer_css_parser = Scarcity_Samurai_Banner_Element::get_timer_style_css_parser( $timer_style_id );

					if ( $timer_css_parser !== null ) {
						$timer_css_args = array();

						if ( is_array( $css_vars ) && ! empty( $css_vars ) ) {
							foreach ( $css_vars as $key => $value ) {
								$timer_css_args["BANNER_{$key}"] = $value;
							}
						}

						$timer_css = $timer_css_parser->get_css( $css_prefix, $timer_css_args );
					}

					break;
			}
		}

		return $css . $timer_css;
	}

	/**
	 * Get the basic banner style shell html
	 *
	 * @param string $style_id
	 * @param string $content
	 * @param array $args
	 */
	public static function get_style_html( $style_id, $content, $args ) {
		extract( $args, EXTR_SKIP );

		$banners_html_dir = ( $inline ? SS_INLINE_BANNERS_HTML_DIR : SS_FIXED_BANNERS_HTML_DIR );
		$filename = "{$banners_html_dir}{$style_id}.php";

		if ( ! file_exists( $filename ) ) {
			return '';
		}

		// Add banner click link
		if ( isset( $action_redirect_url ) ) {
			$action_redirect_url = esc_attr( $action_redirect_url );
			$content .= "<a class='ss-banner-action-link' href='{$action_redirect_url}'></a>";
		}

		// Add Administration Link
		if ( isset( $administration_view_link ) && ( $administration_view_link === true ) ) {
			$content .= '<a class="ss_banner_administration_view_link">Administration View</a>';
		}

		// Get banner HTML
		$html = Scarcity_Samurai_Helper::load_html( $filename, array(
			'content' => $content
 		) );

		// Wrap banner and add positional information
		switch ( $position ) {
			case 'fixed_top': $class = 'ss-banner-top'; break;
			case 'fixed_bottom': $class = 'ss-banner-bottom'; break;
			default: $class = 'ss-banner-inline';
		}

		$html = "<div class='{$class}'>{$html}</div>";

		return $html;
	}

	/**
	 * Get page specific banner html
	 *
	 * @param integer $banner_id
	 * @param integer $page_id
	 * @param array $args
	 */
	public static function get_html( $banner_id, $page_id, $args = array() ) {
		extract( wp_parse_args( $args, array(
			'position' => null, // header footer banners
			'align' => 'none', // inline banners
		) ), EXTR_SKIP );

		// Should this banner be visible on this page?
		if ( ! self::is_visible( $banner_id, $page_id, $position ) ) {
			return '';
		}

		// Do we need to make the banner clickable?
		if ( isset( $data['action']['redirect'] ) ) {
			$action_redirect_url = Scarcity_Samurai_Helper::get_redirect_url( $data['action']['redirect'] );
		}

		// Get banner style information
		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find( $banner_id );
		$style_id = isset( $banner['style']['id'] ) ? $banner['style']['id'] : self::DEFAULT_BANNER_STYLE_ID;
		$inline = $banner['data']['inline'];

		// Construct banner contents
		$args['timer_id'] = isset( $banner['style']['timer_id'] ) ? $banner['style']['timer_id'] : null;
		$content = self::get_content( $banner['id'], $page_id, $args );

		// Show 'Administration View' link only if the timer counts since page load
		// and user is logged in.
		if ( current_user_can( 'edit_posts' ) ) {
			$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );

			$administration_view_link =
				isset( $page['available_until']['type'] ) &&
				( $page['available_until']['type'] === 'page_load' );
		} else {
			$administration_view_link = false;
		}

		// Get HTML for the banner itself
		$html = self::get_style_html( $style_id, $content, compact(
			'inline',
			'position',
			'action_redirect_url',
			'administration_view_link'
		) );

		// Set inline banner alignment
		$banner_align = ( $inline ? "ss-banner-align-{$align}" : '' );

		// Show banner at specific time?
		$show = $args['data']['show'];

		if ( ( $show['type'] === 'page_load' ) && ( $show['value'] > 0 ) ) {
			$show_after = "style='display: none' data-show-after='{$show['value']}'";
		} else {
			$show_after = '';
		}

		// max width class?
		$banner_width = $inline && isset( $banner['style']['width'] ) && $banner['style']['width'] == 'max'
			? 'ss-banner-width-max' : '';

		// Finally, wrap everything in a specific banner id div
		return "<div class='ss-banner-{$banner_id} {$banner_align} {$banner_width} ss-banner-wrapper' {$show_after}>{$html}</div>";
	}

	/**
	 * Construct banner content
	 */
	private static function get_content( $banner_id, $page_id, $args ) {
		$elements = self::get_elements( $banner_id );

		$contents = array();
		foreach ( $elements as $element ) {
			$contents[] = Scarcity_Samurai_Banner_Element::get_html( $element, $page_id, $args );
		}
		return join( '', $contents );
	}

	public static function get_js_data( $banner, $position ) {
		$page_id = Scarcity_Samurai_Helper::current_page_id();

		if ( ! self::is_visible( $banner['id'], $page_id, $position ) ) {
			return array();
		}

		$banner_js_data = array();
		$elements = self::get_elements( $banner['id'] );

		foreach ( $elements as $element ) {
			$element_js_data = Scarcity_Samurai_Banner_Element::get_js_data( $element );

			if ( $element_js_data === null ) {
				continue;
			}

			$element_type = $element_js_data['element_type'];
			$element_id = $element_js_data['element_id'];
			unset( $element_js_data['element_type'] );
			unset( $element_js_data['element_id'] );

			if ( ! array_key_exists( $element_type, $banner_js_data ) ) {
				$banner_js_data[$element_type] = array();
			}

			$banner_js_data[$element_type][$element_id] = $element_js_data;
		}

 		return $banner_js_data;
	}

	public static function is_inline( $id ) {
		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find( $id );

		return isset( $banner['data']['inline'] ) ? $banner['data']['inline'] : false;
	}

	public static function is_enabled( $id, $page_id, $position ) {
		if ( ! Scarcity_Samurai_Page::belongs_to_campaign( $page_id ) ) {
			return false;
		}

		if ( self::is_inline( $id ) ) {
			return true;
		}

		$record = Scarcity_Samurai_Model::get( 'Pages_Banners' )->find_by( array(
			'page_id' => $page_id,
			'banner_id' => $id,
			'position' => $position
		) );

		if ( $record === null ) {
			return false;
		}

		return $record['enabled'];
	}

	// If the banner contains a timer, the timer must be enabled to see the banner.
	// In other words, to see the banner, the following 2 conditions must hold:
	//   - Banner has a timer => timer enabled
	//     (which is equivalent to: Banner does not have a timer OR timer is enabled)
	//   - The banner itself must be enabled, and the page must belong to a campaign
	public static function is_visible( $id, $page_id, $position = null ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );

		return ( $page['campaign_id'] !== null ) &&
		       self::is_enabled( $id, $page_id, $position ) &&
		       ( ! self::contains_timer( $id ) || $page['available_until']['enabled'] );
	}

	public static function position($banner) {
		$page_id = Scarcity_Samurai_Helper::current_page_id();

		$record = Scarcity_Samurai_Model::get( 'Pages_Banners' )->find_by(array(
			'page_id' => $page_id,
			'banner_id' => $banner['id']
		));

		return ($record === null ? null : $record['position']);
	}

	private static function contains_element( $id, $element_type ) {
		$elements = Scarcity_Samurai_Model::get( 'Banner_Element' )->find_by( array(
			'banner_id' => $id,
			'type' => $element_type
		) );

		return ! empty( $elements );
	}

	public static function contains_timer( $id ) {
		return self::contains_element( $id, 'timer' );
	}

	public static function count_banners( $settings = array() ) {
		$banners = Scarcity_Samurai_Model::get( 'Banner' )->all();

		if ( isset( $settings['inline'] ) ) {
			$count = 0;

			foreach ( $banners as $banner ) {
				if ( self::is_inline( $banner['id'] ) === $settings['inline'] ) {
					$count += 1;
				}
			}

			return $count;
		}

		return count( $banners );
	}

	public static function default_fixed_banner_id() {
		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find_by( array(
		  'name' => self::$default_fixed_banner_name
		) );

    return $banner['id'];
  }

  public static function default_inline_banner_id() {
		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find_by( array(
		  'name' => self::$default_inline_banner_name
		) );

    return $banner['id'];
  }
}
