<?php

/* LEGAL COPYRIGHT NOTICE

Copyright (c) Noble Samurai Pty Ltd, 2008-2013. All Rights Reserved.

This software is proprietary to and embodies the confidential technology of Noble Samurai Pty Ltd.
Possession, use, dissemination or copying of this software and media is authorised only pursuant
to a valid written license from Noble Samurai Pty Ltd. Questions or requests regarding permission may
be sent by email to legal@noblesamurai.com or by post to PO Box 477, Blackburn Victoria 3130, Australia.

*/

add_action( 'init', array( 'Scarcity_Samurai_Banner_Editor', 'add_banner_preview_query_vars' ) );
add_action( 'admin_init', array( 'Scarcity_Samurai_Banner_Editor', 'add_ajax' ) );

/**
 *
 */
class Scarcity_Samurai_Banner_Editor
{
	/**
	 * args?
	 * - selected_banner_id
	 * - page_id
	 * - id (select element id)
	 * - name (select element name)
	 * - inline (whether to show inline banners or not inline banners)
	 */
	public static function banner_select( $args ) {
		extract( wp_parse_args( $args, array(
			'selected_banner_id' => null,
			'page_id' => null,
			'id' => null,
			'name' => null,
			'inline' => false
		) ), EXTR_SKIP );

		self::enqueue_banner_select();

		if ( ! $inline ) {
			// This is required in order to show the iframe with the page preview.
			$page = ( $page_id === null ?
			          site_url() :
			          Scarcity_Samurai_Helper::get_page_url_by_id( $page_id ) );
			$page .= '?ss-no-admin-bar&ss-no-scarcity';
		}

		$banners_count = Scarcity_Samurai_Banner::count_banners( compact( 'inline' ) );

		if ( $banners_count === 0 ) {
			$email = SS_SUPPORT_EMAIL;
			echo "<span class='ss-warning-message'>
			        There are no " . ( $inline ? 'inline' : 'fixed' ) . " banners.
			        Please contact <a href='mailto:$email'>$email</a>
			      </span>";
		} else {
			echo Scarcity_Samurai_Helper::build_html_element( 'select', array(
				'id' => $id,
				'class' => 'ss-banner-select',
				'name' => $name,
				'data-inline' => ( $inline ? 'true' : 'false' ),
				'data-value' => $selected_banner_id,
				'data-page' => ( $inline ? null : $page )
			) );
		}
	}

	public static function enqueue_banner_select() {
		self::enqueue_banner_editor();
		wp_enqueue_script( 'ss-banner-select', plugins_url( 'scripts/js/banner-select.js', __FILE__ ), array( 'ss-select2', 'ss-banner-editor', 'ss-banner-models', 'ss-backbone-utils' ) );
	}

	public static function enqueue_banner_editor() {
		wp_enqueue_style( 'wp-color-picker' ); // color pickers
		wp_enqueue_style( 'editor-buttons' );

		wp_enqueue_media(); // setup media editor

		wp_enqueue_style( 'ss-banner-editor', Scarcity_Samurai_Helper::url( 'stylesheets/css/banner-editor.css' ) );
		wp_register_script( 'backbone-nested', Scarcity_Samurai_Helper::url( 'vendor/backbone-nested/backbone-nested.js' ), array( 'jquery', 'backbone' ) );
		wp_enqueue_script( 'ss-banner-models', Scarcity_Samurai_Helper::url( 'scripts/js/banner-models.js' ), array( 'backbone-nested' ) );

		global $wp_version;
		// one lot of JS for <WP3.9 where we are dealing with TinyMCE 3
		if ( version_compare( $wp_version, '3.9', '<' ) ) {
			wp_enqueue_script( 'ss-banner-editor', Scarcity_Samurai_Helper::url( 'scripts/js/banner-editor-pre3.9.js' ), array( 'ss-banner-models', 'wp-color-picker', 'editor', 'media-editor', 'jquery-ui-sortable', 'jquery-ui-resizable', 'ss-backbone-utils' ) );
		// another one for TinyMCE 4
		} else {
			wp_enqueue_script( 'ss-banner-editor', Scarcity_Samurai_Helper::url( 'scripts/js/banner-editor.js' ), array( 'ss-banner-models', 'wp-color-picker', 'editor', 'media-editor', 'jquery-ui-sortable', 'jquery-ui-resizable', 'ss-backbone-utils' ) );
		}

		add_action( 'admin_footer', array( __CLASS__, 'print_editor_media_templates' ) );
		add_action( 'admin_footer', array( __CLASS__, 'force_load_editor' ), 50 );
	}

	public static function force_load_editor() {
		// make sure tinymce is loaded
		if ( ! class_exists( '_WP_Editors' ) )
			require_once( ABSPATH . WPINC . '/class-wp-editor.php' );

		_WP_Editors::enqueue_scripts();

		$editor_id = 'ss-banner-editor-dummy';
		$set = array(
			'tinymce' => true,
			'quicktags' => false
		);
		$set = _WP_Editors::parse_settings( $editor_id, $set );
		_WP_Editors::editor_settings( $editor_id, $set );
		wp_localize_script( 'ss-banner-editor', 'ssBannerEditor', array( 'language' => _WP_Editors::$mce_locale ) );
	}

	public static function add_ajax() {
		add_action( 'wp_ajax_ss_banners_read', array( __CLASS__, 'banners_read' ) );
		add_action( 'wp_ajax_ss_banner_read', array( __CLASS__, 'banner_read' ) );
		add_action( 'wp_ajax_ss_banner_save', array( __CLASS__, 'banner_save' ) );
		add_action( 'wp_ajax_ss_banner_delete', array( __CLASS__, 'banner_delete' ) );
		add_action( 'wp_ajax_ss_banner_styles_read', array( __CLASS__, 'banner_styles_read' ) );
		add_action( 'wp_ajax_ss_banner_timer_styles_read', array( __CLASS__, 'banner_timer_styles_read' ) );
	}

	public static function add_banner_preview_query_vars() {
		global $show_admin_bar;

		if ( isset( $_REQUEST['ss-no-admin-bar'] ) ) {
			$show_admin_bar = false;
		}
	}

	public static function banners_read() {
		$banners = Scarcity_Samurai_Model::get( 'Banner' )->all();

		foreach ( $banners as &$banner ) {
			$elements = Scarcity_Samurai_Banner::get_elements( $banner['id'] );
			$banner['elements'] = $elements;
			unset( $banner );
		}

		wp_send_json_success( $banners );
	}

	public static function banner_read() {
		$params = json_decode( Scarcity_Samurai_Helper::get_request( 'params' ), true );
		$banner_id = $params['id'];

		$banner = Scarcity_Samurai_Model::get( 'Banner' )->find( $banner_id );
		$elements = Scarcity_Samurai_Banner::get_elements( $banner_id );
		$banner['elements'] = $elements;

		wp_send_json_success( $banner );
	}

	public static function banner_delete() {
		$params = json_decode( Scarcity_Samurai_Helper::get_request( 'params' ), true );
		$banner_id = $params['id'];

		// check to see if there are any pages with this banner (enabled).
		$banner_page = Scarcity_Samurai_Model::get( 'Pages_Banners' )->find_by( array(
			'banner_id' => $banner_id,
			'enabled' => true
		) );

		if ( $banner_page !== null ) {
			wp_send_json_error( 'Cannot delete this banner.  It is currently being used.' );
		}

		// Delete the banner
		Scarcity_Samurai_Model::get( 'Banner' )->delete( array( 'id' => $banner_id ) );

		wp_send_json_success();
	}

	public static function banner_save() {
		$params = json_decode( Scarcity_Samurai_Helper::get_request( 'params' ), true );

		extract( wp_parse_args( $params, array(
			'name' => null,
			'style' => null,
			'data' => array(
				'inline' => false
			),
			'elements' => array()
		) ), EXTR_SKIP );

		// update and existing banner
		if ( isset( $id ) ) {
			Scarcity_Samurai_Model::get( 'Banner' )->update( array(
				'style' => $style,
			), compact( 'id' ) );

			// save or update elements
			$element_ids = array();

			foreach ( $elements as $element ) {
				$element_id = isset( $element['id'] ) ? $element['id'] : null;
				$element['banner_id'] = $id;
				unset( $element['id'] );

				foreach( array( 'style', 'data' ) as $field ) {
					if ( isset( $element[ $field ] ) && empty( $element[ $field ] ) ) {
						$element[ $field ] = null;
					}
				}

				if ( isset( $element_id ) ) {
					Scarcity_Samurai_Model::get( 'Banner_Element' )->update( $element, array( 'id' => $element_id )	);
				} else {
					$element_id = Scarcity_Samurai_Model::get( 'Banner_Element' )->insert( $element );
				}

				$element_ids[] = $element_id;
			}

			// remove elements that aren't in this banner anymore
			$existing_elements = Scarcity_Samurai_Banner::get_elements( $id );

			foreach ( $existing_elements as $element ) {
				if ( ! in_array( $element['id'], $element_ids ) ) {
					Scarcity_Samurai_Model::get( 'Banner_Element' )->delete( array( 'id' => $element['id'] ) );
				}
			}

		// brand new banner
		} else {
			if ( ! isset( $name ) ) {
				wp_send_json_error( 'Banner name cannot be empty.' );
			}

			// check if a banner already exists with that name...
			if ( Scarcity_Samurai_Model::get( 'Banner' )->find_by( array( 'name' => $name ) ) !== null ) {
				wp_send_json_error( 'A banner already exists with this name.' );
			}

			$id = Scarcity_Samurai_Model::get( 'Banner' )->insert( compact( 'name', 'style', 'data' ) );

			foreach ( $elements as $element ) {
				$element['banner_id'] = $id;
				unset( $element['id'] );

				foreach( array( 'style', 'data' ) as $field ) {
					if ( isset( $element[ $field ] ) && empty( $element[ $field ] ) ) {
						unset( $element[ $field ] );
					}
				}

				Scarcity_Samurai_Model::get( 'Banner_Element' )->insert( $element );
			}
		}

		wp_send_json_success( array( 'id' => $id ) );
	}

	public static function banner_styles_read() {
		$styles = self::_banner_styles_read( 'banners' );
		wp_send_json_success( $styles );
	}

	public static function banner_timer_styles_read() {
		$styles = self::_banner_styles_read( 'timers' );
		wp_send_json_success( $styles );
	}

	private static function _banner_styles_read( $type ) {
		switch ( $type ) {
			case 'banners':
				return array_merge(
					self::_banner_styles_read( 'fixed_banners' ),
					self::_banner_styles_read( 'inline_banners' )
				);
			case 'fixed_banners':
				$style_files = glob( SS_FIXED_BANNERS_HTML_DIR . '*.php' );
				break;
			case 'inline_banners':
				$style_files = glob( SS_INLINE_BANNERS_HTML_DIR . '*.php' );
				break;
			case 'timers':
				$style_files = glob( SS_TIMERS_HTML_DIR . '*.php' );
				break;
			default:
				$style_files = array();
		}

		$styles = array();
		foreach ( $style_files as $style_html_file ) {
			$parts = pathinfo( $style_html_file );
			$id = $parts['filename'];

			switch ( $type ) {
				case 'fixed_banners':
				case 'inline_banners':
					$inline = ( $type == 'inline_banners' );
					// load and setup html...
					$html = Scarcity_Samurai_Banner::get_style_html( $id, '', array(
						'inline' => $inline,
						'position' => $inline ? null : 'fixed_top'
					) );

					$backbone_id = $inline ? 'inline_' . $id : $id;

					// load style information...
					$style_css_parser = Scarcity_Samurai_Banner::get_style_css_parser( $inline, $id );
					break;

				case 'timers':
					$html = Scarcity_Samurai_Banner_Element::get_timer_style_html( $id );
					$style_css_parser = Scarcity_Samurai_Banner_Element::get_timer_style_css_parser( $id );
					break;
			}

			if ( $style_css_parser !== null ) {
				$vars = $style_css_parser->get_replacement_vars( array(), false );
				$css = $style_css_parser->get_css(
					"CSS_PREFIX",
					array_combine( array_keys( $vars ), array_keys( $vars ) )
				);
			}

			$styles[] = compact( 'backbone_id', 'id', 'html', 'css', 'vars', 'inline' );

			// clear values for next loop
			unset( $id, $html, $css, $vars );
		}

		return $styles;
	}

	public static function print_editor_media_templates() {
		?>
		<script type="text/html" id="tmpl-ss-banner-editor">
			<div class="ss-banner-editor-dialog ss-banner-modal wp-core-ui">
				<a class="ss-banner-modal-close" href="#" title="<?php esc_attr_e('Close'); ?>"><span class="ss-banner-modal-icon"></span></a>
				<div class="ss-banner-modal-content">
					<div class="ss-banner-editor-title">
						<h1>Banner Editor</h1>
					</div>
					<div class="ss-banner-editor-menu">
						<a href="#" data-content="banner-style" class="ss-banner-editor-menu-item active">Banner Style</a>
						<a href="#" data-content="timer-style" class="ss-banner-editor-menu-item">Timer Style</a>
						<a href="#" data-content="banner-dimensions" class="ss-banner-editor-menu-item ss-banner-editor-menu-item-banner-dimensions">Banner Dimensions</a>
						<a href="#" data-content="banner-options" class="ss-banner-editor-menu-item">Colors and Fonts</a>
					</div>
					<div class="ss-banner-editor-frame-content">
						<div class="ss-banner-editor-content-frame ss-banner-editor-banner-style-frame" style="display:block">
							<ul class="ss-banner-editor-banner-styles"></ul>
						</div>
						<div class="ss-banner-editor-content-frame ss-banner-editor-timer-style-frame">
							<ul class="ss-banner-editor-timer-styles"></ul>
						</div>
						<div class="ss-banner-editor-content-frame ss-banner-editor-banner-dimensions-frame">
							<ul class="ss-banner-editor-options">
								<li class="ss-banner-editor-option">
									<span class="customize-control-title">Width</span>
									<div class="customize-control-content">
										<form>   <!-- Sometimes Firefox doesn't like selects which are not inside a form (they become unclickable) -->
											<select class="ss-banner-editor-banner-width" name="width">
												<option value="auto">Auto (Expand to fit contents)</option>
												<option value="max">Maximum Width</option>
												<option value="300px">300px - Half Page Banner</option>
												<option value="468px">468px - Full Banner</option>
												<option value="728px">728px - Leaderboard</option>
												<option value="custom">Custom</option>
											</select>
										</form>
										<span class="ss-banner-editor-banner-custom-width"><input name="custom_width" type="text" size="3" />px</span>
									</div>
								</li>
							</ul>
						</div>
					</div>
					<!-- HACK: keep this outside of the ss-banner-editor-frame-content block so that the color pickers can overflow properly -->
						<div class="ss-banner-editor-content-frame ss-banner-editor-banner-options-frame">
							<ul class="ss-banner-editor-options">
								<li class="ss-banner-editor-option">
									<span class="customize-control-title">Background Color</span>
									<div class="customize-control-content">
										<input class="color-picker-hex" type="text" name="background_color" maxlength="7" placeholder="Hex Value" data-default-color="{{BACKGROUND_COLOR}}" value="{{BACKGROUND_COLOR}}" />
									</div>
								</li>
								<li class="ss-banner-editor-option">
									<span class="customize-control-title">Text Color</span>
									<div class="customize-control-content">
										<input class="color-picker-hex" type="text" name="text_color" maxlength="7" placeholder="Hex Value" data-default-color="{{TEXT_COLOR}}" value="{{TEXT_COLOR}}" />
									</div>
								</li>
								<li class="ss-banner-editor-option">
									<span class="customize-control-title">Font</span>
									<div class="customize-control-content">
										<!-- TODO: replace this with nice graphical dropdown if we have time -->
										<select name="text_font_family">
											<option value="Arial, sans-serif">Arial</option>
											<option value="Times, serif">Times New Roman</option>
										</select>
									</div>
								</li>
							</ul>
						</div>
					<!-- END HACK -->
					<div class="ss-banner-editor-layout-toolbar wp_themeSkin">
						<div class="right">
							<a class="button ss-button-add-image">Add Image</a>
							<a class="button ss-button-add-text">Add Text</a>
							<a class="button ss-button-add-timer">Add Timer</a>
						</div>
						<div class="ss-banner-editor-mce-toolbar"></div>
					</div>
					<div class="ss-banner-editor-layout">
						<iframe scrolling="no"></iframe>
						<div class="ss-banner-overlay"></div>
					</div>
					<div class="ss-banner-editor-footer">
						<div class="left">
							<a href="#" class="ss-button-delete button-large button button-dangerous">Delete</a>
						</div>
						<a href="#" class="ss-button-save-as button-large button">Save As</a>
						<a href="#" class="ss-button-save button-large button button-primary">Save</a>
					</div>
				</div>
			</div>
			<div class="ss-banner-modal-backdrop"></div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-editor-banner">
			<div class="{{bannerClass}}">
			</div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-editor-timer">
			<div class="{{timerClass}}">
			</div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-editor-banner-style">
			<div class="preview">
				<div class="preview-content">
					<iframe scrolling="no"></iframe>
					<div class="ss-banner-overlay">
					</div>
				</div>
			</div>
			<div class="check"><div class="media-modal-icon"></div></div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-editor-timer-style">
			<div class="preview">
				<div class="preview-content">
					<div class="preview-alignment"></div>
				</div>
			</div>
			<div class="check"><div class="media-modal-icon"></div></div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-editor-banner-element">
			<div class="ss-banner-element"></div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-editor-banner-editable-element">
			<div class="ss-banner-editable-element">
				<div class="ss-banner-element"></div>
				<div class="drag">Drag<a class="remove" href="#">&times;</a></div>
			</div>
		</script>

		<script type="text/html" id="tmpl-ss-banner-select-preview">
			<div class="ss-banner-select">
				<div class="preview">
					<div class="preview-content">
						<div class="ss-banner-overlay"></div>
					</div>
				</div>
				<div class="title-background"></div>
				<div class="title">{{{title}}}</div>
			</div>
		</script>

		<?php
	}
}
