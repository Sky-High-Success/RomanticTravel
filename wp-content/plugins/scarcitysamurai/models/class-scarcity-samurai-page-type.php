<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Page_Type extends Scarcity_Samurai_Model {

	public $short_name, $table_name, $defaults;
	protected $fields;
	private static $squeeze_page_type_name = 'Sign-up (squeeze) page',
	               $content_page_type_name = 'Content page',
	               $offer_page_type_name = 'Offer (sales) page',
	               $other_page_type_name = 'Other';
	private static $squeeze_page_type_id = null,
	               $content_page_type_id = null,
	               $offer_page_type_id = null,
	               $other_page_type_id = null;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'page_type';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_page_types';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'name' => array(
				'type' => 'string'
			)
		);

		$this->defaults = array(
			array(
				'name' => self::$squeeze_page_type_name
			),
			array(
				'name' => self::$content_page_type_name
			),
			array(
				'name' => self::$offer_page_type_name
			),
			array(
				'name' => self::$other_page_type_name
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Page_Type' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(100) NOT NULL,
				PRIMARY KEY (id)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

	private static function page_type_id($type) {
		$type_id = "${type}_page_type_id";
		$type_name = "${type}_page_type_name";

		// Return the cached result, if exists.
		if (self::$$type_id !== null) {
			return self::$$type_id;
		}

		$page_type = Scarcity_Samurai_Model::get( 'Page_Type' )->find_by(array('name' => self::$$type_name));
		self::$$type_id = $page_type['id'];

		return self::$$type_id;
	}

	public static function squeeze_page_type_id() {
		return self::page_type_id('squeeze');
	}

	public static function content_page_type_id() {
		return self::page_type_id('content');
	}

	public static function offer_page_type_id() {
		return self::page_type_id('offer');
	}

	public static function other_page_type_id() {
		return self::page_type_id('other');
	}

	public static function default_page_type_id() {
		return Scarcity_Samurai_Access::$d === 'trial_expired' ?
		       self::offer_page_type_id() :
		       self::other_page_type_id();
	}

}
