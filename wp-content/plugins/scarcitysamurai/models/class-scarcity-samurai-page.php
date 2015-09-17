<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Page extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;
	private static $banners = array();

	public function __construct() {
		global $wpdb;

		$this->short_name = 'page';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_pages';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'campaign_id' => array(
				'type' => 'integer'
			),
			'type_id' => array(
				'type' => 'integer'
			),
			'position' => array(
				'type' => 'integer'
			),
			'available_from' => array(
				'type' => 'json'
			),
			'available_until' => array(
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
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Page' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL,
				campaign_id BIGINT(20) UNSIGNED,
				type_id BIGINT(20) UNSIGNED,
				position SMALLINT UNSIGNED DEFAULT 0,
				available_from LONGTEXT,
				available_until LONGTEXT,
				data LONGTEXT,
				PRIMARY KEY (id),
				FOREIGN KEY (type_id) REFERENCES " . Scarcity_Samurai_Model::get( 'Page_Type' )->table_name . "(id)
			) $scarcity_samurai_charset_collate";

		$wpdb->query( self::$sql );
	}

	public function delete( $data ) {
		// Firstly, remove all the banners from the pages we want to delete.
		$pages = Scarcity_Samurai_Model::get( 'Page' )->all( null, $data );

		if ( ! empty( $pages ) ) {
			$page_ids = Scarcity_Samurai_Helper::pluck( $pages ,'id' );

			if ( Scarcity_Samurai_Model::get( 'Pages_Banners' )->delete( array( 'page_id' => $page_ids ) ) === false ) {
				return false;
			}
		}

		// Then, delete the pages themselves.
		return parent::delete( $data );
	}

	public static function url_requires_token( $id ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		return ( ( $page['available_from']['enabled'] &&
		           ( $page['available_from']['type'] === 'opt_in' ) ) ||
		         ( $page['available_until']['enabled'] &&
		           ( $page['available_until']['type'] === 'opt_in' ) ) );
	}

	// Returns true if and only if visiting this page requires setting a cookie.
	public static function requires_cookie( $id ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		return ( ( $page['available_until'] !== null ) &&
		         ( $page['available_until']['type'] === 'page_load' ) );
	}

	public static function start_time( $page = null ) {
		if ( $page === null ) {
			$page = Scarcity_Samurai_Helper::current_page();
		}

		switch ( $page['available_from']['type'] ) {
			case 'opt_in':
				$subscription_time = Scarcity_Samurai_User::subscription_time();

				return ( $subscription_time === null ? null : $subscription_time + $page['available_from']['value'] );

			case 'fixed':
				return $page['available_from']['value'];
		}

		return null;
	}

	public static function end_time( $page = null ) {
		if ( $page === null ) {
			$page = Scarcity_Samurai_Helper::current_page();
		}

		switch ( $page['available_until']['type'] ) {
			case 'page_load':
				$cookie_name = Scarcity_Samurai_Security::page_load_cookie_name( $page );
				$legacy_cookie_name = Scarcity_Samurai_Security::page_load_legacy_cookie_name( $page );

				if ( current_user_can( 'edit_post', $page['id'] ) ) {
					$page_load_time = time();
				} else if ( Scarcity_Samurai_Security::cookie_exists( $cookie_name ) ) {
					$page_load_time = intval( Scarcity_Samurai_Security::get_cookie( $cookie_name ) );
				} else if ( Scarcity_Samurai_Security::cookie_exists( $legacy_cookie_name ) ) {
					$page_load_time = intval( Scarcity_Samurai_Security::get_cookie( $legacy_cookie_name ) );
				} else {
					$page_load_time = time();
				}

				return $page_load_time + $page['available_until']['value'];

			case 'opt_in':
				if ( current_user_can( 'edit_post', $page['id'] ) ) {
					$subscription_time = time();
				} else {
					$subscription_time = Scarcity_Samurai_User::subscription_time();
				}

				return ( $subscription_time === null ? null : $subscription_time + $page['available_until']['value'] );

			case 'fixed':
				return $page['available_until']['value'];
		}

		return null;
	}

	public static function contains_opt_in_form( $id ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		return isset( $page['data']['contains_opt_in_form'] ) && $page['data']['contains_opt_in_form'];
	}

	// Checks whether the given page (with id = $id) has opt-in reference to
	// $reference_page_id.
	// If $reference_page_id is not specified, checks for any opt-in references
	// (both specific page and 'any campaign page').
	// If at least one opt-in reference is found, returns true.
	// Otherwise, returns false.
	public static function has_opt_in_references( $id, $reference_page_id = null ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		foreach ( array( $page['available_from'],
		                 $page['available_until'] ) as $available_from_until ) {
			if ( isset( $available_from_until ) &&
			     $available_from_until['enabled'] &&
			     ( $available_from_until['type'] === 'opt_in' ) &&
			     ( ( $reference_page_id === null ) ||
			       isset( $available_from_until['page_id'] ) &&
			       ( $available_from_until['page_id'] === $reference_page_id ) ) )
			{
				return true;
			}
		}

		return false;
	}

	public static function has_redirect( $id ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		return isset( $page['available_until']['enabled'] ) &&
		       $page['available_until']['enabled'] &&
		       isset( $page['available_until']['too_late_action']['redirect'] );
	}

	private static function get_banner_attributes_by_position( $id, $position ) {
		$record = Scarcity_Samurai_Model::get( 'Pages_Banners' )->find_by( array(
			'page_id' => $id,
			'position' => $position
		) );

		if ( $record === null ) {
			return null;
		}

		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find( $record['banner_id'] );

		return array(
			'id' => $banner['id'],
			'name' => $banner['name'],
			'enabled' => $record['enabled'],
			'data' => $record['data']
		);
	}

	public static function header_banner_attributes( $id ) {
		return self::get_banner_attributes_by_position( $id, 'fixed_top' );
	}

	public static function footer_banner_attributes( $id ) {
		return self::get_banner_attributes_by_position( $id, 'fixed_bottom' );
	}

	// Returns all the banners in the current page.
	// The result is in the following format:
	//   array(
	//     array(
	//       'position' => 'fixed_top',
	//       'banner' => < Scarcity_Samurai_Banner model >
	//     ),
	//     array(
	//       'position' => 'fixed_bottom',
	//       'banner' => < Scarcity_Samurai_Banner model >
	//     ),
	//     array(
	//       'position' => 'inline',
	//       'banner' => < Scarcity_Samurai_Banner model >
	//     )
	//   )
	public static function banners( $args = array() ) {
		$page_id = Scarcity_Samurai_Helper::current_page_id();

		extract( wp_parse_args( $args, array(
			'include_fixed_banners' => true,
			'include_inline_banners' => true
		) ), EXTR_SKIP );

		$cache_key = join( ':', array( $page_id, $include_fixed_banners, $include_inline_banners ) );

		// Return the cached result, if exists.
		if ( array_key_exists( $cache_key, self::$banners ) ) {
			return self::$banners[$cache_key];
		}

		$result = array();

		if ( $include_fixed_banners ) {
			$page = Scarcity_Samurai_Helper::current_page();

			if ( ( $page !== null ) && ( $page['campaign_id'] !== null ) ) {
				$pages_banners = new Scarcity_Samurai_Pages_Banners();
				$records = $pages_banners->all( null, array( 'page_id' => $page_id ) );

				foreach ( $records as $record ) {
					$result[] = array(
						'position' => $record['position'],
						'banner' => Scarcity_Samurai_Model::get( 'Banner' )->find( $record['banner_id'] )
					);
				}
			}
		}

		if ( $include_inline_banners ) {
			$page_content = Scarcity_Samurai_Helper::get_page_content_by_id( $page_id );

			preg_match_all( Scarcity_Samurai_Inline_Banners::$html_comment_regex, $page_content, $matches );

			foreach ( $matches[1] as $attributes_string ) {
				$attributes = Scarcity_Samurai_Helper::parse_xml_attributes( $attributes_string );

				if ( isset( $attributes['id'] ) ) {
					$result[] = array(
						'position' => 'inline',
						'banner' => Scarcity_Samurai_Model::get( 'Banner' )->find( (int)( $attributes['id'] ) )
					);
				}
			}
		}

		self::$banners[$cache_key] = $result;

		return $result;
	}

	public static function has_inline_banners() {
		$banners = self::banners( array(
			'include_fixed_banners' => false
		) );

		return ! empty( $banners );
	}

	public static function has_inline_timers() {
		$page_id = Scarcity_Samurai_Helper::current_page_id();
		$page_content = Scarcity_Samurai_Helper::get_page_content_by_id( $page_id );

		preg_match_all( Scarcity_Samurai_Inline_Timers::$html_comment_regex, $page_content, $matches );

		return ! empty( $matches[1] );
	}

	public static function is_published( $id ) {
		return ( get_post_status( $id ) === 'publish' );
	}

	public static function autoresponder( $id, $settings = array() ) {
    $page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

    if ( isset( $page['data']['auto_responder'] ) ) {
      return $page['data']['auto_responder'];
    }

		$auto_responders = array_unique( array_filter( Scarcity_Samurai_Auto_Responder_Integrator::get_page_forms_auto_responders( $id ) ) );

    if ( empty( $auto_responders ) ) {
			return null;
		}

		if ( count( $auto_responders ) > 1 ) {
			Scarcity_Samurai_Helper::error( 'Multiple forms with different auto responders is not supported: ' . join( ', ', $auto_responders ) );
		}

		if ( isset( $settings['short_name'] ) && ( $settings['short_name'] === true ) ) {
			return Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_short_name( $auto_responders[0] );
		}

		return $auto_responders[0];
	}

	public static function publish( $id ) {
		$page = array(
			'ID' => $id,
			'post_status' => 'publish'
		);

		$page_id = wp_update_post( $page );

		return ( $page_id !== 0 );
	}

	public static function page_ids_that_belong_to_campaign() {
		$page_ids = array();

		foreach ( Scarcity_Samurai_Model::get( 'Page' )->all() as $page ) {
			if ( $page[ 'campaign_id' ] !== null ) {
				$page_ids[] = $page[ 'id' ];
			}
		}

		return $page_ids;
	}

	public static function campaign_id( $id ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		return $page['campaign_id'];
	}

	public static function belongs_to_campaign( $id ) {
	  return ( self::campaign_id( $id ) !== null );
	}

	public static function has_unavailable_functionality( $id ) {
		$page = Scarcity_Samurai_Model::get( 'Page' )->get_model( $id );

		return ! Scarcity_Samurai_Access::all_features_are_available( $page );
	}

}
