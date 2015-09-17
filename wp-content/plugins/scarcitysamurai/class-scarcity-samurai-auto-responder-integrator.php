<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'admin_init', array( 'Scarcity_Samurai_Auto_Responder_Integrator', 'init' ) );

class Scarcity_Samurai_Auto_Responder_Integrator {

	private static $auto_responders = array(
		'aweber' => array(
			// Auto responder's name that is used in output messages. Should be
			// exactly the same like it appears on the auto responder's web site.
			'name' => 'AWeber',
			// Auto responder's web site URL. Can be String, Array, or null.
			// See examples of each below.
			'site_url' => '//aweber.com',
			// Regular expression that is used to identify the auto responder.
			'action_regex' => '/aweber\.com/',
			// Auto responder specific string for the token. The auto responder will
			// replace this string with the real token value when sending an email.
			'token_placeholder' => '{!custom token}',
			// Link to Knowledgebase article that explains the necessary configuration
			// that needs to be done in the auto responder's web site to use Scarcity
			// Samurai.
			'configuration_instructions_url' => '//noblesamurai.zendesk.com/entries/21509854-Setting-up-Aweber-to-work-with-Scarcity-Samurai'
		),
		'emailsamurai' => array(
			'name' => 'Email Samurai',
			'site_url' => null,
			'action_regex' => '/lists\.noblesamurai\.com/',
			'token_placeholder' => '{!custom token}',
			'configuration_instructions_url' => null
		),
		'getresponse' => array(
			'name' => 'GetResponse',
			'site_url' => '//getresponse.com',
			'action_regex' => '/\.getresponse\.com/',
			'token_placeholder' => '[[cus token]]',
			'configuration_instructions_url' => '//noblesamurai.zendesk.com/entries/21509359-Setting-up-GetResponse-to-work-with-Scarcity-Samurai'
		),
		'infusionsoft' => array(
			'name' => 'Infusionsoft',
			'site_url' => '//infusionsoft.com',
			'action_regex' => '/infusionsoft\.com/',
			'token_placeholder' => array( __CLASS__, 'get_infusionsoft_token_placeholder' ),
			'configuration_instructions_url' => '//noblesamurai.zendesk.com/entries/21509864-Setting-up-InfusionSoft-to-work-with-Scarcity-Samurai'
		),
		'mailchimp' => array(
			'name' => 'MailChimp',
			'site_url' => '//mailchimp.com',
			'action_regex' => '/\.list-manage.*\.com/',
			'token_placeholder' => '*|TOKEN|*',
			'configuration_instructions_url' => '//noblesamurai.zendesk.com/entries/21512065-Setting-up-MailChimp-to-work-with-Scarcity-Samurai'
		),
		'sendpepper' => array(
			'name' => 'SendPepper/OfficeAutoPilot',
			'site_url' => array(
				'SendPepper' => '//sendpepper.com',
				'OfficeAutoPilot' => '//officeautopilot.com'
			),
			'action_regex' => '/moon-ray\.com/',
			'token_placeholder' => '[token]',
			'configuration_instructions_url' => '//noblesamurai.zendesk.com/entries/21680760-Setting-up-SendPepper-or-Office-Auto-Pilot-to-work-with-Scarcity-Samurai'
		),
	);

	public static function init() {
		if ( ! defined( 'NS_EMAIL_SAMURAI' ) ) {
			unset( self::$auto_responders['emailsamurai'] );
		}

		// AJAX hooks
		add_action( 'wp_ajax_ss_subscribe_user', array( __CLASS__, 'subscribe_user' ) ); // For logged in users
		add_action( 'wp_ajax_nopriv_ss_subscribe_user', array( __CLASS__, 'subscribe_user' ) ); // For not logged in users
	}

	public static function supported_auto_responders() {
		return array_keys( self::$auto_responders );
	}

	private static function sort_by_text( $first, $second ) {
		return strcasecmp( $first['text'], $second['text'] );
	}

	public static function auto_responders_select( $settings = array() ) {
	  // Prepare data for <option>s creation
	  $options_data = array();

	  foreach ( self::$auto_responders as $auto_responder => $auto_responder_settings ) {
      $options_data[] = array(
        'value' => $auto_responder,
        'text' => $auto_responder_settings['name']
      );
    }

		usort( $options_data, array( __CLASS__, 'sort_by_text' ) );

    // Create the select with options
		$result = '<select class="ss-auto-responder-select" name="ss-auto-responder-select">';

		// <option></option> is needed for select2 'placeholder' to work.
		// See select2 docs: http://ivaynberg.github.io/select2/#documentation
		$result .= '<option></option>';

		foreach ( $options_data as $option_data ) {
			if ( isset( $settings['selected'] ) ) {
		  	$selected = selected( $option_data['value'], $settings['selected'], false );
			} else {
				$selected = '';
			}

		  $result .= "<option value='" . $option_data['value'] . "' $selected>" . $option_data['text'] . "</option>";
	  }

	  $result .= '</select>';

		echo $result;
  }

	private static function sort_by_name( $first, $second ) {
		return strcasecmp( $first['name'], $second['name'] );
	}

	public static function supported_auto_responders_message() {
		$supported_auto_responders = array();

		foreach ( self::$auto_responders as $auto_responder_settings ) {
			$site_url = $auto_responder_settings['site_url'];

			if ( is_string( $site_url ) ) {
				$supported_auto_responders[] = array(
					'name' => $auto_responder_settings['name'],
					'url' => $site_url
				);
			} else if ( is_array( $site_url ) ) {
				foreach ( $site_url as $name => $url ) {
					$supported_auto_responders[] = array(
						'name' => $name,
						'url' => $url
					);
				}
			}
		}

		usort( $supported_auto_responders, array( __CLASS__, 'sort_by_name' ) );

		$supported_auto_responders_count = count( $supported_auto_responders );

		$message = 'We currently only support ';

		foreach ( $supported_auto_responders as $index => $supported_auto_responder ) {
			$name = $supported_auto_responder['name'];
			$url = $supported_auto_responder['url'];

			$message .= "<a href='$url' target='_blank'>$name</a>";

			if ( $index === $supported_auto_responders_count - 1 ) {
				$message .= '.';
			} else if ( $index === $supported_auto_responders_count - 2 ) {
				$message .= ', and ';
			} else {
				$message .= ', ';
			}
		}

		echo $message;
	}

	public static function get_auto_responder_short_name( $auto_responder ) {
		if ( ( $auto_responder === null ) ||
		     ( ! isset( self::$auto_responders[$auto_responder] ) ) ) {
			return '';
		}

		return self::$auto_responders[$auto_responder]['name'];
	}

	public static function get_auto_responder_token_placeholder( $auto_responder, $page_id = null ) {
		if ( ( $auto_responder === null ) ||
		     ( ! isset( self::$auto_responders[$auto_responder] ) ) ) {
			return null;
		}

		$token_placeholder = self::$auto_responders[$auto_responder]['token_placeholder'];

		if ( is_string( $token_placeholder ) ) {
			return $token_placeholder;
		}

		if ( is_array( $token_placeholder ) &&
		     method_exists( $token_placeholder[0], $token_placeholder[1] ) ) {
			return call_user_func( $token_placeholder, $page_id );
		}

		return null;
	}

	private static function get_infusionsoft_token_placeholder( $page_id ) {
		$default_token_placeholder = '~Contact._token~';

		// We need to find the 'id' of the token field here
		$page = Scarcity_Samurai_Model::get( 'Page' )->find( $page_id );
		$campaign_id = $page['campaign_id'];
		$squeeze_page = Scarcity_Samurai_Model::get( 'Campaign' )->squeeze_page( $campaign_id );
		$forms = self::get_page_forms( $squeeze_page['id'] );

		if ( empty( $forms ) ) {
			return $default_token_placeholder;
		}

		$form = $forms[0];
		
		$token_field = $form->find( '[name^=inf_custom_token]', 0 );

		if ( $token_field->id === false ) {
			return $default_token_placeholder;
		}

		return "~Contact.{$token_field->id}~";
	}

	public static function get_auto_responder_configuration_instructions_link( $auto_responder ) {
		if ( ( $auto_responder === null ) ||
		     ( ! isset( self::$auto_responders[$auto_responder] ) ) ) {
			return '';
		}

		$url = self::$auto_responders[$auto_responder]['configuration_instructions_url'];
		$short_name = self::$auto_responders[$auto_responder]['name'];
		$result = "$short_name Configuration Instructions";

		if ( $url !== null ) {
			$result = "<a href='$url' target='_blank'>$result</a>";
		}

		return $result;
	}

	private static function get_page_forms( $page_id ) {
		$page_forms = array();

		// Check non Optimize Press forms in page content
		$content = Scarcity_Samurai_Helper::get_page_content_by_id( $page_id );

		if ( ! empty( $content ) ) {
			$content = simple_html_1_11_str_get_html( $content );
			$page_forms = $content->find( 'form' );
		}

		// Check Optimize Press form in page meta data
		$current_theme = wp_get_theme();

		if ( $current_theme->Name === 'OptimizePress' ) {
			$optimize_press_form = get_post_meta( $page_id, '_optthemes_webformcodehtml' );

			if ( ! empty( $optimize_press_form ) ) {
				$optimize_press_form = simple_html_1_11_str_get_html( $optimize_press_form[0] );
				$optimize_press_form = $optimize_press_form->find( 'form', 0 );

				if ( $optimize_press_form !== null ) {
					array_push( $page_forms, $optimize_press_form );
				}
			}
		}

		return $page_forms;
	}

	private static function get_auto_responder_by_form_action( $form_action ) {
		foreach ( self::$auto_responders as $auto_responder => $auto_responder_settings )  {
			if ( preg_match( $auto_responder_settings['action_regex'], $form_action ) ) {
				return $auto_responder;
			}
		}

		return null;
	}

	public static function get_page_forms_auto_responders( $page_id ) {
		$auto_responders = array();
		$forms = self::get_page_forms( $page_id );

		foreach ( $forms as $form ) {
			$auto_responder = self::get_auto_responder_by_form_action( $form->action );

			if ( $auto_responder !== null ) {
				$auto_responders[] = $auto_responder;
			}
		}

		return $auto_responders;
	}

	public static function subscribe_user() {
		$args = wp_parse_args( $_REQUEST, array(
			'nonce' => '',
			'page_id' => '',
			'email' => '',
			'token' => ''
		) );

		extract( $args );

		if ( empty( $email ) ) {
			wp_send_json_error( "Couldn't subscribe user. Email required." );
		}

		if ( ! wp_verify_nonce( $nonce, 'ss-subscribe-user' ) ) {
			wp_send_json_error( "You don't have permission to do that." );
		}

		if ( ! Scarcity_Samurai_User::subscribe( $email, $token, $page_id ) ) {
			wp_send_json_error( "Couldn't subscribe user." );
		}

		Scarcity_Samurai_Security::set_cookie( 'scarcity_samurai_token', $token );

		wp_send_json_success();
	}

}
