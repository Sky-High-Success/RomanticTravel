<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Settings extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'settings';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_settings';

		$this->fields = array(
			'id' => array(
				'type' => 'integer'
			),
			'name' => array(
				'type' => 'string'
			),
			'value' => array(
				'type' => 'json'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Settings' )->table_name . " (
				id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				name VARCHAR(50) NOT NULL,
				value LONGTEXT,
				PRIMARY KEY (id),
				UNIQUE (name)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

	// Returns setting's value by the specified setting name.
	// If such setting doesn't exist, returns null.
	public static function get($name) {
		$setting = Scarcity_Samurai_Model::get( 'Settings' )->find_by(array('name' => $name));

		if ($setting === null) {
			return null;
		}

		return $setting['value'];
	}

	public static function set($name, $value) {
		Scarcity_Samurai_Model::get( 'Settings' )->update( compact( 'value' ), compact( 'name' ) );
	}

}
