<div id="ss-page-lock-tab-panel" class="wp-tab-panel ss-page-tab-panel <?php echo 'ss-' . Scarcity_Samurai_Access::$d . '-access'; ?>">
	<div>
		<h2>Access Restriction</h2>
		<p>
			Specify when your visitors will be able to access this page.
			(e.g. if you were to have a sale this weekend, you may want to restrict
			access to this page until Saturday etc)
		</p>
		<p>
			<?php
				$checked = $page['available_from']['enabled'];
			?>
			<input id="ss-page-lock-enable"
			       class="ss-ui-toggle"
			       type="checkbox"
			       name="ss-page-lock-enable"
			       data-ss-ui-toggle-element-class="ss-access-restriction-configuration"
			       <?php checked($checked); ?>
			       <?php disabled( $trial_has_expired ); ?> />
			<label for="ss-page-lock-enable">
			  Enable Access Restriction
			</label>
			<?php Scarcity_Samurai_Access::upgrade_to_use_message(); ?>
		</p>
	</div>
	<div class="ss-access-restriction-configuration ss-sub">
		<p><strong>Visitors can access this page...</strong></p>
		<ul class="ul-radio">
			<li>
				<?php
					$checked = ( $page['available_from']['type'] === null ) || ( $page['available_from']['type'] === 'opt_in' );
				?>
				<input class="ul-radio" type="radio" name="ss-page-lock-from" value="opt_in" <?php checked($checked); ?>>
				<?php
					if ( $page['available_from']['type'] === 'opt_in' ) {
						$value = $page['available_from']['value'];
					} else {
						$value = 0;
					}

					extract( Scarcity_Samurai_Helper::parse_time_period( $value ) );
				?>
				<input class="ss-days-input ss-text-input" type="number" name="ss-page-lock-from-optin-days" min="0" size="2" value="<?php echo $days; ?>"> days,
				<input class="ss-hours-input ss-text-input" type="number" name="ss-page-lock-from-optin-hours" min="0" max="23" size="2" value="<?php echo $hours; ?>"> hours,
				<input class="ss-minutes-input ss-text-input" type="number" name="ss-page-lock-from-optin-minutes" min="0" max="59" size="2" value="<?php echo $minutes; ?>"> mins,
				<input class="ss-seconds-input ss-text-input" type="number" name="ss-page-lock-from-optin-seconds" min="0" max="59" size="2" value="<?php echo $seconds; ?>"> secs
				&nbsp;&nbsp;&nbsp; after opting in on &nbsp;&nbsp;&nbsp;
				<?php
					$available_until_page_id =
						isset( $page['available_from']['page_id'] ) ? $page['available_from']['page_id'] : null;

					Scarcity_Samurai_Helper::page_select( array(
						'contains_opt_in_form' => true,
						'name' => 'ss-page-lock-from-optin-page-id',
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
			</li>
			<li>
				<?php
					$checked = ( $page['available_from']['type'] === 'fixed' );
				?>
				<input class="ul-radio" type="radio" name="ss-page-lock-from" value="fixed" <?php checked($checked); ?>>
				From
				<?php
					if ($page['available_from']['type'] === 'fixed') {
						$timezone = $page['available_from']['timezone'];
						$value = $page['available_from']['value'];
					} else {
						$timezone = 'UTC';
						$value = time();
					}

					extract(Scarcity_Samurai_Helper::parse_fixed_time($value, $timezone));
				?>
				<select class="ss-select" name="ss-page-lock-from-fixed-month">
					<?php
						foreach (Scarcity_Samurai_Helper::$months as $month_number => $m) {
							$selected = selected($month_number, $month, false);
							echo "<option value='$month_number' $selected>$m</option>";
						}
					?>
				</select>
				<input class="ss-text-input" name="ss-page-lock-from-fixed-day" type="number" min="1" max="31" size="2" value="<?php echo $day; ?>">,
				<input class="ss-text-input" name="ss-page-lock-from-fixed-year" type="number" min="2012" size="4" value="<?php echo $year; ?>"> @
				<input class="ss-text-input" name="ss-page-lock-from-fixed-hour" type="number" min="0" max="23" size="2" value="<?php echo $hour < 10 ? "0$hour" : $hour; ?>"> :
				<input class="ss-text-input" name="ss-page-lock-from-fixed-minute" type="number" min="0" max="59" size="2" value="<?php echo $minute < 10 ? "0$minute" : $minute; ?>">
				<?php
					Scarcity_Samurai_Helper::timezone_select( array(
						'name' => 'ss-page-lock-from-fixed-timezone',
						'selected' => $timezone
					) );
				?>
			</li>
		</ul>
		<p><strong>If a visitor attempts to access this page before it is available...</strong></p>
		<ul class="ul-radio">
			<li>
				<label>
					<?php
						$too_early_action = $page['available_from']['too_early_action'];
						$checked = ($too_early_action === null) ||
						           (isset($too_early_action['error']) && ($too_early_action['error'] === 404));
					?>
					<input class="ul-radio" type="radio" name="ss-page-lock-early" value="page_not_found" <?php checked($checked); ?>>
					return a 404 "page not found" error
				</label>
			</li>
			<li>
				<label>
					<?php
						$redirect_page_id = (($too_early_action !== null) &&
						                     array_key_exists('redirect', $too_early_action) &&
						                     array_key_exists('page_id', $too_early_action['redirect']) ? $too_early_action['redirect']['page_id'] : null);
						$checked = ($redirect_page_id !== null);
					?>
					<input class="ul-radio" type="radio" name="ss-page-lock-early" value="redirect_to_page" <?php checked($checked); ?>>
					redirect to page
				</label>
				<div class="ss-opts">
					<p>
						<?php
							Scarcity_Samurai_Helper::page_select( array(
								'name' => 'ss-page-lock-early-redirect-page-id',
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
						$redirect_page_url = (($too_early_action !== null) &&
						                      array_key_exists('redirect', $too_early_action) &&
						                      array_key_exists('url', $too_early_action['redirect']) ? $too_early_action['redirect']['url'] : null);
						$checked = ($redirect_page_url !== null);
					?>
					<input class="ul-radio" type="radio" name="ss-page-lock-early" value="redirect_to_url" <?php checked($checked); ?>>
					redirect to URL
				</label>
				<div class="ss-opts">
					<p>
						<input class="ss-text-input" type="text" size="40" name="ss-page-lock-early-redirect-url" value="<?php echo $redirect_page_url; ?>">
					</p>
				</div>
			</li>
		</ul>

		<div class="ss-page-lock-optin-only-opts">
			<p>
				<strong>If a visitor has not opted-in...</strong>
			</p>
			<ul class="ul-radio">
				<li>
					<label>
						<?php
							$not_opted_in_action = (($page['available_from'] !== null) &&
							                        array_key_exists('not_opted_in_action', $page['available_from']) ?
							                        $page['available_from']['not_opted_in_action'] :
							                        null);
							$checked = ($not_opted_in_action === null) ||
							           (isset($not_opted_in_action['error']) && ($not_opted_in_action['error'] === 404));
						?>
						<input class="ul-radio" type="radio" name="ss-page-lock-not-opted-in" value="page_not_found" <?php checked($checked); ?>>
						return a 404 "page not found" error
					</label>
				</li>
				<li>
					<label>
						<?php
							$redirect_page_id = (isset($not_opted_in_action['redirect']['page_id']) ? $not_opted_in_action['redirect']['page_id'] : null);
							$checked = ($redirect_page_id !== null);
						?>
						<input class="ul-radio" type="radio" name="ss-page-lock-not-opted-in" value="redirect_to_page" <?php checked($checked); ?>>
						redirect to page
					</label>
					<div class="ss-opts">
						<p>
							<?php
								Scarcity_Samurai_Helper::page_select( array(
									'name' => 'ss-page-lock-not-opted-in-redirect-page-id',
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
							$redirect_url = (isset($not_opted_in_action['redirect']['url']) ? $not_opted_in_action['redirect']['url'] : null);
							$checked = ($redirect_url !== null);
						?>
						<input class="ul-radio" type="radio" name="ss-page-lock-not-opted-in" value="redirect_to_url" <?php checked($checked); ?>>
						redirect to URL
					</label>
					<div class="ss-opts">
						<p>
							<input class="ss-text-input" type="text" size="40" name="ss-page-lock-not-opted-in-redirect-url" value="<?php echo $redirect_url; ?>">
						</p>
					</div>
				</li>
			</ul>
		</div>
	</div>
</div>
