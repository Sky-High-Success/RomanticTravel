<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Token extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'token';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_tokens';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'user_id' => array(
				'type' => 'integer'
			),
			'token' => array(
				'type' => 'string'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Token' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				user_id BIGINT(20) UNSIGNED NOT NULL,
				token VARCHAR(32) NOT NULL DEFAULT '',
				FOREIGN KEY (user_id) REFERENCES " . Scarcity_Samurai_Model::get( 'User' )->table_name . "(id),
				PRIMARY KEY (id),
				UNIQUE (token)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

}
