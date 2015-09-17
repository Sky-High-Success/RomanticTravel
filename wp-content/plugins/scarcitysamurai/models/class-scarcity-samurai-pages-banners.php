<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Pages_Banners extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'pages_banners';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_pages_banners';

		$this->fields = array(
			'page_id' => array(
				'type' => 'integer'
			),
			'banner_id' => array(
				'type' => 'integer'
			),
			'enabled' => array(
				'type' => 'boolean'
			),
			'position' => array(
				'type' => 'string'
			),
			'data' => array(
				'type' => 'json'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Pages_Banners' )->table_name . " (
				page_id BIGINT(20) UNSIGNED NOT NULL,
				banner_id BIGINT(20) UNSIGNED NOT NULL,
				enabled BIT NOT NULL DEFAULT 0,
				position VARCHAR(50) NOT NULL,
				data LONGTEXT,
				PRIMARY KEY (page_id, banner_id, position),
				FOREIGN KEY (page_id) REFERENCES " . Scarcity_Samurai_Model::get( 'Page' )->table_name . "(id),
				FOREIGN KEY (banner_id) REFERENCES " . Scarcity_Samurai_Model::get( 'Banner' )->table_name . "(id)
			) $scarcity_samurai_charset_collate";

		$wpdb->query( self::$sql );
	}

}
