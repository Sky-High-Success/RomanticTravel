<?php
	global $scarcity_samurai_dir;

	wp_nonce_field('page-meta-box', 'security_token');

	function ss_show_when( $condition ) {
		echo 'style="display: ' . ( $condition ? 'block' : 'none' ) . '"';
	}
?>
<a name="ss-campaign"></a>
<?php if ( Scarcity_Samurai_Access::$d === 'trial_expired' ) { ?>
	<div class="ss-trial-message">
		Your Scarcity Samurai trial version has expired!
		<a class="button-primary" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade</a>
	</div>
<?php	} ?>
<?php if ( Scarcity_Samurai_Access::$d === 'trial_not_expired' ) { ?>
	<div class="ss-trial-message">
		Your Scarcity Samurai trial version will expire in
		<?php echo Scarcity_Samurai_Helper::format_time_period( Scarcity_Samurai_Access::trial_remaining_time() ); ?>.
		<a class="button-primary" href="<?php esc_attr_e( $f ); ?>" target="_blank">Upgrade</a>
	</div>
<?php } ?>
<h2>Campaign</h2>
<p>
	Which campaign this page belongs to?
</p>
<p>
	<select class="ss-page-campaign ss-campaign-select" name="ss-page-campaign">
		<option value="">No Campaign</option>
		<?php
			foreach ( $campaigns as $campaign ) {
				$selected = selected( $campaign['id'], $page['campaign_id'], false );
				echo "<option value='" . $campaign['id'] . "' $selected>" . $campaign['name'] . '</option>';
			}
		?>
	</select>
</p>
<div class="ss-page-campaign-page-opts">
	<h2>Page Settings</h2>
	<p>
		Each page in your campaign may have a different purpose. Your users will
		generally enter your campaign through a 'Sign Up' or 'Squeeze' page, view one
		or more pages of 'Content', before viewing the 'Offer' page.
	</p>
	<p>
		Page Purpose:
		<select class="ss-page-type-id" name="ss-page-type-id">
			<?php
				$page_types = Scarcity_Samurai_Model::get( 'Page_Type' )->all();
				$selected_page_type_id =
					isset( $page['type_id'] ) ? $page['type_id'] : Scarcity_Samurai_Page_Type::default_page_type_id();

				foreach ( $page_types as $page_type ) {
					$selected = selected( $page_type['id'], $selected_page_type_id, false );
					echo "<option value='" . $page_type['id'] . "' $selected>" . $page_type['name'] . "</option>";
				}
			?>
		</select>
	</p>

	<p>
		<?php
			$checked = Scarcity_Samurai_Page::contains_opt_in_form( $page );
		?>
		<input id="ss-contains-opt-in-form"
		       class="ss-contains-opt-in-form"
		       type="checkbox"
		       name="ss-contains-opt-in-form"
		       <?php checked( $checked ); ?>
		       <?php disabled( $trial_has_expired ); ?> />
		<label for="ss-contains-opt-in-form">
		  Page contains an opt-in form
		</label>
		<?php Scarcity_Samurai_Access::upgrade_to_use_message(); ?>

		<div class="ss-auto-responder-wrapper ss-sub" <?php ss_show_when( $checked ); ?>>
			<span style="line-height: 28px">Auto Responder:</span>
			<?php
				Scarcity_Samurai_Auto_Responder_Integrator::auto_responders_select( array(
					'selected' => $auto_responder
				) );
			?>
			<br />
			<p class="ss-auto-responder-not-selected-message" <?php ss_show_when( $auto_responder === null ); ?>>
				<?php	Scarcity_Samurai_Auto_Responder_Integrator::supported_auto_responders_message(); ?>
			</p>
			<p class="ss-auto-responder-selected-message ss-description" <?php ss_show_when( $auto_responder !== null ); ?>>
				For your campaign to function properly you just need to configure
				<span class="ss-auto-responder-short-name">
					<?php echo $auto_responder_short_name; ?>
				</span>
				to work with Scarcity Samurai.<br />
				This only takes a couple of minutes and you can see step-by-step
				instructions on how to do this here:
				<span class="ss-auto-responder-configuration-instructions-link">
					<?php echo Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_configuration_instructions_link( $auto_responder ); ?>
				</span>
			</p>
		</div>
	</p>
	<br />

	<div class="ss-page-options-div">
		<ul id="ss-page-tabs">
			<li class="ss-page-lock-tab tabs"><a name="ss-page-lock" href="#ss-page-lock">Access Restriction</a></li>
			<li class="ss-page-timer-tab hide-if-no-js"><a name="ss-page-timer" href="#ss-page-timer">Count Down Timer</a></li>
			<li class="ss-page-banners-tab hide-if-no-js"><a name="ss-page-banners" href="#ss-page-banners">Banners</a></li>
		</ul>

		<?php
			include($scarcity_samurai_dir . 'html/page-meta-box/access-restriction.php');
			include($scarcity_samurai_dir . 'html/page-meta-box/count-down-timer.php');
			include($scarcity_samurai_dir . 'html/page-meta-box/banners.php');
		?>
	</div>
</div>
