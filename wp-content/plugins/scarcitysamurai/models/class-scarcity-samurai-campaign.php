<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Campaign extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'campaign';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_campaigns';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'name' => array(
				'type' => 'string'
			),
			'active' => array(
				'type' => 'boolean'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Campaign' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(200) NOT NULL,
				active BIT NOT NULL DEFAULT 1,
				PRIMARY KEY (id),
				UNIQUE (name)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

	protected function sanitize_before_write( $data ) {
		if ( array_key_exists( 'name', $data ) ) {
			$data['name'] = trim( $data['name'] );
		}

		return $data;
	}

	public function delete( $data ) {
		// Firstly, delete all the records of users that subscribed to pages
		// in the specified campaigns.
		$pages = Scarcity_Samurai_Model::get( 'Page' )->all( null, array( 'campaign_id' => $data['id'] ) );

		if ( ! empty( $pages ) ) {
			$page_ids = Scarcity_Samurai_Helper::pluck( $pages, 'id' );

			if ( Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->delete( array( 'page_id' => $page_ids ) ) === false ) {
				return false;
			}
		}

		// Secondly, delete all the pages in the specified campaigns.
		if ( Scarcity_Samurai_Model::get( 'Page' )->delete( array( 'campaign_id' => $data['id'] ) ) === false ) {
			return false;
		}

		// Finally, delete the campaigns themselves.
		return parent::delete( array( 'id' => $data['id'] ) );
	}

	public static function is_active( $id ) {
		$campaign = Scarcity_Samurai_Model::get( 'Campaign' )->get_model( $id );

		return $campaign['active'];
	}

	public static function toggle_activation( $ids, $active ) {
		$update_data = array(
			'active' => $active
		);

		$where = array(
			'id' => $ids
		);

		return Scarcity_Samurai_Model::get( 'Campaign' )->update( $update_data, $where ) ;
	}

	// Returns all the pages with the specified campaign id.
	public static function pages( $id ) {
		return Scarcity_Samurai_Model::get( 'Page' )->all( 'position ASC', array( 'campaign_id' => $id ) );
	}

	// Returns the number of pages in the specified campaign id.
	public static function pages_count( $id ) {
		return Scarcity_Samurai_Model::get( 'Page' )->count( array( 'campaign_id' => $id ) );
	}

	public static function campaign_name_exists( $campaign_name ) {
		global $wpdb;

		self::$sql = $wpdb->prepare("
			SELECT COUNT(*) FROM " . Scarcity_Samurai_Model::get( 'Campaign' )->table_name . "
			WHERE name = %s
		", trim( $campaign_name ) );

		return $wpdb->get_var( self::$sql ) !== '0';
	}

	public static function publish_all_pages( $id ) {
		$pages = self::pages( $id );

		foreach ( $pages as $page ) {
			if ( ! Scarcity_Samurai_Page::publish( $page['id'] ) ) {
				return false;
			}
		}

		return true;
	}

	public static function contains_opt_in_form( $id, $exclude_page_ids = array() ) {
		$pages = self::pages( $id );

		foreach ( $pages as $page ) {
			if ( in_array( $page['id'], $exclude_page_ids ) ) {
				continue;
			}

			if ( Scarcity_Samurai_Page::contains_opt_in_form( $page ) ) {
				return true;
			}
		}

		return false;
	}

	public static function has_opt_in_references( $id, $exclude_page_ids = array() ) {
		$pages = self::pages( $id );

		foreach ( $pages as $page ) {
			if ( in_array( $page['id'], $exclude_page_ids ) ) {
				continue;
			}

			if ( Scarcity_Samurai_Page::has_opt_in_references( $page, null ) ) {
				return true;
			}
		}

		return false;
	}

	public static function autoresponder( $id ) {
		$pages = self::pages( $id );

		foreach ( $pages as $page ) {
			$auto_responder = Scarcity_Samurai_Page::autoresponder( $page['id'] );

			if ( $auto_responder !== null ) {
				return $auto_responder;
			}
		}

		return null;
	}

	public static function squeeze_page( $id ) {
		$pages = self::pages( $id );

		foreach ( $pages as $page ) {
			if ( Scarcity_Samurai_Page::contains_opt_in_form( $page ) ) {
				return $page;
			}
		}

		return null;
	}

	public static function has_unavailable_functionality( $id ) {
		$pages = self::pages( $id );

		foreach ( $pages as $page ) {
			if ( Scarcity_Samurai_Page::has_unavailable_functionality( $page ) ) {
				return true;
			}
		}

		return false;
	}

}
