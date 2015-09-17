<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

class Scarcity_Samurai_Users_Subscriptions extends Scarcity_Samurai_Model {

	public $short_name, $table_name;
	protected $fields;

	public function __construct() {
		global $wpdb;

		$this->short_name = 'users_subscriptions';
		$this->table_name = $wpdb->prefix . 'scarcity_samurai_users_subscriptions';

		$this->fields = array(
			'user_id' => array(
				'type' => 'integer'
			),
			'page_id' => array(
				'type' => 'integer'
			),
			'campaign_id' => array(   // We need it because user can move pages from
				'type' => 'integer'     // one campaign to another.
			),
			'subscription_time' => array(
				'type' => 'datetime'
			)
		);
	}

	public static function on_activate() {
		global $wpdb, $scarcity_samurai_charset_collate;

		self::$sql = "
			CREATE TABLE IF NOT EXISTS " . Scarcity_Samurai_Model::get( 'Users_Subscriptions' )->table_name . " (
				user_id BIGINT(20) UNSIGNED NOT NULL,
				page_id BIGINT(20) UNSIGNED NOT NULL,
				campaign_id BIGINT(20) UNSIGNED NOT NULL,
				subscription_time DATETIME NOT NULL,
				PRIMARY KEY (user_id, page_id, campaign_id),
				FOREIGN KEY (user_id) REFERENCES " . Scarcity_Samurai_Model::get( 'User' )->table_name . "(id),
				FOREIGN KEY (page_id) REFERENCES " . Scarcity_Samurai_Model::get( 'Page' )->table_name . "(id),
				FOREIGN KEY (campaign_id) REFERENCES " . Scarcity_Samurai_Model::get( 'Campaign' )->table_name . "(id)
			) $scarcity_samurai_charset_collate";

		$wpdb->query(self::$sql);
	}

}
