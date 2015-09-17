<div id="ss-page-timer-tab-panel" class="wp-tab-panel ss-page-tab-panel">
	<h2>Count Down Timer</h2>
	<p>
		This option allows you to set time limits on a user's access to a page.
	</p>
	<p>
		<label>
			<?php
				$checked = $page['available_until']['enabled'];
			?>
			<input class="ss-ui-toggle" type="checkbox" name="ss-page-timer-enable"
			       data-ss-ui-toggle-element-class="ss-count-down-timer-configuration"
			       <?php checked($checked); ?>>
			Enable Count Down Timer
		</label>
	</p>
	<div class="ss-count-down-timer-configuration ss-sub">
		<p>
			<strong>Count down relative to...</strong>
		</p>
		<ul class="ul-radio">
			<li>
				<?php
					$checked = ($page['available_until']['type'] === null) || ($page['available_until']['type'] === 'page_load');
				?>
				<input id="ss-page-timer-from-page-load"
				       class="ul-radio ss-ui-toggle"
				       data-ss-ui-toggle-element-class="ss-page-timer-from-page-load-configuration"
				       type="radio"
				       name="ss-page-timer-from"
				       value="page_load"
				       <?php checked($checked); ?> />
				<label for="ss-page-timer-from-page-load">
					current page load
				</label>
				<div class="ss-page-timer-from-page-load-configuration">
					<p>
						Time from page load:
						<?php
							if ($page['available_until']['type'] === 'page_load') {
								$value = $page['available_until']['value'];
							} else {
								$value = 0;
							}

							extract(Scarcity_Samurai_Helper::parse_time_period($value));
						?>
						<input class="ss-days-input ss-text-input" type="number" name="ss-page-timer-from-page-load-days" min="0" size="2" value="<?php echo $days; ?>"> days,
						<input class="ss-hours-input ss-text-input" type="number" name="ss-page-timer-from-page-load-hours" min="0" max="23" size="2" value="<?php echo $hours; ?>"> hours,
						<input class="ss-minutes-input ss-text-input" type="number" name="ss-page-timer-from-page-load-minutes" min="0" max="59" size="2" value="<?php echo $minutes; ?>"> mins,
						<input class="ss-seconds-input ss-text-input" type="number" name="ss-page-timer-from-page-load-seconds" min="0" max="59" size="2" value="<?php echo $seconds; ?>"> secs
					</p>
				</div>
			</li>
			<li>
				<?php
					$checked = ( $page['available_until']['type'] === 'opt_in' );
				?>
				<input id="ss-page-timer-from-opt-in"
				       class="ul-radio ss-ui-toggle"
				       data-ss-ui-toggle-element-class="ss-page-timer-from-opt-in-configuration"
				       type="radio"
				       name="ss-page-timer-from"
				       value="opt_in"
				       <?php checked($checked); ?>
				       <?php disabled( $trial_has_expired ); ?> />
				<label for="ss-page-timer-from-opt-in">
				  opt-in on page
			  </label>
				<?php Scarcity_Samurai_Access::upgrade_to_use_message(); ?>
				<div class="ss-page-timer-from-opt-in-configuration">
  				<br />
  				<?php
  					$available_until_page_id =
  						isset( $page['available_until']['page_id'] ) ? $page['available_until']['page_id'] : null;

  					Scarcity_Samurai_Helper::page_select( array(
  						'contains_opt_in_form' => true,
  						'name' => 'ss-page-timer-from-optin-page-id',
  						'selected' => $available_until_page_id,
  						'first_option' => '<option value="">any campaign page</option>',
  						'campaign_id' => $page['campaign_id'],
  						'exclude_page_ids' => array( $page['id'] ) // Don't include the page we are currently on
  					) );
  				?>
  				<span class="ss-warning-message ss-no-pages-with-opt-in-form">
  					<br />
  					<?php _e( "Note: This campaign doesn't have pages with opt-in form." ); ?>
  				</span>
					<p>
						Time from opt-in:
						<?php
							if ( $page['available_until']['type'] === 'opt_in' ) {
								$value = $page['available_until']['value'];
							} else {
								$value = 0;
							}

							extract( Scarcity_Samurai_Helper::parse_time_period( $value ) );
						?>
						<input class="ss-days-input ss-text-input" type="number" name="ss-page-timer-from-optin-days" min="0" size="2" value="<?php echo $days; ?>"> days,
						<input class="ss-hours-input ss-text-input" type="number" name="ss-page-timer-from-optin-hours" min="0" max="23" size="2" value="<?php echo $hours; ?>"> hours,
						<input class="ss-minutes-input ss-text-input" type="number" name="ss-page-timer-from-optin-minutes" min="0" max="59" size="2" value="<?php echo $minutes; ?>"> mins,
						<input class="ss-seconds-input ss-text-input" type="number" name="ss-page-timer-from-optin-seconds" min="0" max="59" size="2" value="<?php echo $seconds; ?>"> secs
					</p>
					<div id="ss-page-timer-optin-notice" class="ss-notice">
						<p class="description">
							This page is only accessible to only those visitors who have opted-in. This restriction can be adjusted
							by visiting the "Access Restriction" tab.
						</p>
					</div>
					<div id="ss-page-timer-optin-warning" class="ss-warning">
						<a id="ss-restrict-to-opt-ins-only" class="button">Restrict to opt-ins only</a>
						<p class="description">
							This page is accessible to anyone. If your intention is to restrict this page to only those visitors
							who have opted-in, this option can be enabled and configured on the "Access Restriction" tab.
						</p>
					</div>
				</div>
			</li>
			<li>
				<?php
					$checked = ($page['available_until']['type'] === 'fixed');
				?>
				<input id="ss-page-timer-from-fixed"
				       class="ul-radio ss-ui-toggle"
				       data-ss-ui-toggle-element-class="ss-page-timer-from-fixed-configuration"
				       type="radio"
				       name="ss-page-timer-from"
				       value="fixed"
				       <?php checked($checked); ?>
				       <?php disabled( $trial_has_expired ); ?> />
				<label for="ss-page-timer-from-fixed">
				  fixed time
				</label>
				<?php Scarcity_Samurai_Access::upgrade_to_use_message(); ?>
				<div class="ss-page-timer-from-fixed-configuration">
					<p>
						<?php
							if ($page['available_until']['type'] === 'fixed') {
								$timezone = $page['available_until']['timezone'];
								$value = $page['available_until']['value'];
							} else {
								$timezone = 'UTC';
								$value = time();
							}

							extract(Scarcity_Samurai_Helper::parse_fixed_time($value, $timezone));
						?>
						Time:
						<select class="ss-select" name="ss-page-timer-from-fixed-month">
							<?php
								foreach (Scarcity_Samurai_Helper::$months as $month_number => $m) {
									$selected = selected($month_number, $month, false);
									echo "<option value='$month_number' $selected>$m</option>";
								}
							?>
						</select>
						<input class="ss-text-input" name="ss-page-timer-from-fixed-day" type="number" min="1" max="31" size="2" value="<?php echo $day; ?>">,
						<input class="ss-text-input" name="ss-page-timer-from-fixed-year" type="number" min="2012" size="4" value="<?php echo $year; ?>"> @
						<input class="ss-text-input" name="ss-page-timer-from-fixed-hour" type="number" min="0" max="23" size="2" value="<?php echo $hour < 10 ? "0$hour" : $hour; ?>"> :
						<input class="ss-text-input" name="ss-page-timer-from-fixed-minute" type="number" min="0" max="59" size="2" value="<?php echo $minute < 10 ? "0$minute" : $minute; ?>">
						<?php
							Scarcity_Samurai_Helper::timezone_select( array(
								'name' => 'ss-page-timer-from-fixed-timezone',
								'selected' => $timezone
							) );
						?>
					</p>
				</div>
			</li>
		</ul>
		<p>
			<strong>When the timer reaches zero...</strong>
		</p>
		<ul class="ul-radio">
			<li>
				<label>
					<?php
						$too_late_action = $page['available_until']['too_late_action'];
						$checked = ($too_late_action === null) || array_key_exists('do_nothing', $too_late_action);
					?>
					<input class="ul-radio" type="radio" name="ss-page-timer-expired" value="do_nothing" <?php checked($checked); ?>>
					do nothing
				</label>
			</li>
			<li>
				<label>
					<?php
						$redirect_page_id = (($too_late_action !== null) &&
						                     array_key_exists('redirect', $too_late_action) &&
						                     array_key_exists('page_id', $too_late_action['redirect']) ?
						                     $too_late_action['redirect']['page_id'] :
						                     null);
						$checked = ($redirect_page_id !== null);
					?>
					<input class="ul-radio" type="radio" name="ss-page-timer-expired" value="redirect_to_page" <?php checked($checked); ?>>
					redirect to page
				</label>
				<div class="ss-opts">
					<p>
						<?php
							Scarcity_Samurai_Helper::page_select( array(
								'name' => 'ss-page-timer-expired-redirect-page-id',
								'selected' => $redirect_page_id
							) );

							Scarcity_Samurai_Dialogs::create_new_page_link();
						?>
					</p>
				</div>
			</li>
			<li>
				<label>
					<?php
						$redirect_url = (($too_late_action !== null) &&
						                 array_key_exists('redirect', $too_late_action) &&
						                 array_key_exists('url', $too_late_action['redirect']) ?
						                 $too_late_action['redirect']['url'] :
						                 null);
						$checked = ($redirect_url !== null);
					?>
					<input class="ul-radio" type="radio" name="ss-page-timer-expired" value="redirect_to_url" <?php checked($checked); ?>>
					redirect to URL
				</label>
				<div class="ss-opts">
					<p>
						<input class="ss-text-input" type="text" size="40" name="ss-page-timer-expired-redirect-url" value="<?php echo $redirect_url; ?>">
					</p>
				</div>
			</li>
		</ul>
	</div>
</div>
