<div class="wrap ss-wizard-page">
	<div id="ss-logo-small" class="icon32"></div>
	<h2>Fixed Date (Single page) Setup</h2>

	<br />
	<div class="menu-edit">
		<?php wp_nonce_field('fixed-date-single-wizard', 'security_token'); ?>

		<!-- Progress Bar -->
		<div id="nav-menu-header">
			<ul class="scarcity-samurai-wizard-progress-bar">
				<li>Campaign Name</li>
				<li>Page Selection</li>
				<li>Page Expiry</li>
				<li>Visualize Scarcity</li>
			</ul>
			<div class="clear"></div>
		</div>

		<!-- Wizard pages -->
		<div id="post-body" class="scarcity-samurai-wizard-content">
			<!--
				Campaign Name
			-->
			<div>
				<h3>Campaign Name</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					<strong>What should the campaign be called?</strong>
				</p>
				<p class="ss-sub">
					<input class="ss-wizard-campaign-name ss-text-input" type="text" name="ss-wizard-fixed-date-single-campaign-name" />
				</p>
			</div>

			<!--
				Page Selection
			-->
			<div>
				<h3>Page Selection</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					<strong>Select the page you would like to add scarcity to:</strong>
				</p>
				<p class="ss-sub">
					<?php
						Scarcity_Samurai_Helper::page_select( array(
							'name' => 'ss-wizard-fixed-date-single-page-id',
							'in_campaign' => false
						) );

						Scarcity_Samurai_Dialogs::create_new_page_link();
					?>
				</p>
			</div>

			<!--
				Page Expiry
			-->
			<div>
				<h3>Set Page Expiry</h3>
				<div class="ss-error ss-wizard-fixed-date-single-time-discrepancy-warning">
					We have detected that your local time is running
					<span class="ss-time-discrepancy-type"></span> than your server
					time by <span class="ss-time-discrepancy-minutes"></span> minutes.
				</div>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					You must set an expiry time for Scarcity Samurai to close your offer automatically.
				</p>
				<br />
				<p>
					<strong>When should your page expire?</strong>
				</p>
				<p class="ss-sub">
					<?php
						$timezone = Scarcity_Samurai_Helper::user_timezone();
						extract(Scarcity_Samurai_Helper::parse_fixed_time(time(), $timezone));
					?>
					<select class="ss-select" name="ss-wizard-fixed-date-single-month">
						<?php
							foreach (Scarcity_Samurai_Helper::$months as $month_number => $m) {
								$selected = selected($month_number, $month, false);
								echo "<option value='$month_number' $selected>$m</option>";
							}
						?>
					</select>
					<input class="ss-days-input ss-text-input" name="ss-wizard-fixed-date-single-day" type="number" min="1" max="31" size="2" value="<?php echo $day; ?>">,
					<input class="ss-year-input ss-text-input" name="ss-wizard-fixed-date-single-year" type="number" min="2012" size="4" value="<?php echo $year; ?>"> @
					<input class="ss-hours-input ss-text-input" name="ss-wizard-fixed-date-single-hour" type="number" min="0" max="23" size="2" value="<?php echo $hour < 10 ? "0$hour" : $hour; ?>"> :
					<input class="ss-minutes-input ss-text-input" name="ss-wizard-fixed-date-single-minute" type="number" min="0" max="59" size="2" value="<?php echo $minute < 10 ? "0$minute" : $minute; ?>">
					<?php
						Scarcity_Samurai_Helper::timezone_select( array(
							'name' => 'ss-wizard-fixed-date-single-timezone',
							'selected' => $timezone
						) );
					?>
				</p>
				<br />
				<p>
					<strong>What should happen when your timer reaches zero?</strong>
				</p>
				<ul class="ul-radio">
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-single-timer-expired" value='do_nothing' checked="checked">
							do nothing
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-single-timer-expired" value='redirect_to_page'>
							redirect to page
						</label>
						<div class="ss-opts">
							<p>
								<?php
									Scarcity_Samurai_Helper::page_select( array(
										'name' => 'ss-wizard-fixed-date-single-timer-expired-redirect-page-id'
									) );

									Scarcity_Samurai_Dialogs::create_new_page_link();
								?>
							</p>
						</div>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-single-timer-expired" value='redirect_to_url' />
							redirect to URL
						</label>
						<div class="ss-opts">
							<p>
								<input class="ss-text-input" type="text" size="40" name="ss-wizard-fixed-date-single-timer-expired-redirect-url" />
							</p>
						</div>
					</li>
				</ul>
			</div>

			<!--
				Visualize Scarcity
			-->
			<div>
				<h3>Visualize Your Scarcity Banner</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					Scarcity is MOST effective when it’s kept in your visitors field of
					view at all times. Here you can specify how you would like your
					scarcity banner to look on your page...
				</p>
				<br />
				<p>
					<strong>Where would you like to place your banner on your page?</strong>
				</p>
				<ul class="ul-radio">
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-single-banner-position" value="header" checked="checked" />
							in the header
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-single-banner-position" value="footer" />
							in the footer
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-single-banner-position" value="none" />
							no banner
						</label>
					</li>
				</ul>

				<div class="ss-banner-pos-opts header footer">
					<div>
						<br />
						<p>
							<strong>What would you like your banner to look like?</strong>
						</p>
						<p class="ss-sub">
							<?php
							Scarcity_Samurai_Banner_Editor::banner_select( array(
								'name' => 'ss-wizard-fixed-date-single-banner-id',
								'selected_banner_id' => Scarcity_Samurai_Banner::default_fixed_banner_id()
							) );
							?>
						</p>

						<br />
						<p>
							<strong>When would you like your banner to appear?</strong>
						</p>
						<p class="ss-sub">
							<ul class="ul-radio">
								<li>
									<input id="ss-wizard-fixed-date-single-banner-show-immediately" type="radio" name="ss-wizard-fixed-date-single-banner-show-type" value="immediately" checked="checked" />
									<label for="ss-wizard-fixed-date-single-banner-show-immediately">immediately</label>
								</li>
								<li>
									<input id="ss-wizard-fixed-date-single-banner-show-page-load" type="radio" name="ss-wizard-fixed-date-single-banner-show-type" value="page_load" />
									<input class="ss-show-value-input ss-text-input" type="number" name="ss-wizard-fixed-date-single-banner-show-value" min="0" value="0" />
									<label for="ss-wizard-fixed-date-single-banner-show-page-load">seconds after page load</label>
								</li>
							</ul>
						</p>
					</div>

					<div class="ss-wizard-fixed-date-single-banner-click-action-wrapper">
						<br />
						<p>
							<strong>
								What would you like to do when the banner is clicked?
							</strong>
						</p>
						<ul class="ul-radio">
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-fixed-date-single-banner-action" value="do_nothing" checked="checked" />
									do nothing
								</label>
							</li>
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-fixed-date-single-banner-action" value="redirect_to_page" />
									go to page
								</label>
								<div class="ss-opts">
									<p>
										<?php
											Scarcity_Samurai_Helper::page_select(array(
												'name' => 'ss-wizard-fixed-date-single-banner-action-redirect-page-id'
											) );

											Scarcity_Samurai_Dialogs::create_new_page_link();
										?>
									</p>
								</div>
							</li>
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-fixed-date-single-banner-action" value="redirect_to_url" />
									go to URL
								</label>
								<div class="ss-opts">
									<p>
										<input class="ss-text-input" type="text" size="40" name="ss-wizard-fixed-date-single-banner-action-redirect-url" />
									</p>
								</div>
							</li>
						</ul>
					</div>
				</div>

				<div class="ss-banner-pos-opts content">
					<div>
						<br />
						<p>
							<strong>
								Where in your page would you like to place your banner?
							</strong>
						</p>
						<p>
							To insert your banner, place your mouse cursor in your content
							area where you would like it to go, and then click the
							"Add Inline Banner" button.
						</p>
						<p class="description">
							Note: You can add (or remove) banners later on via the Edit Page
							screen as well.
						</p>
						<br />
						<?php Scarcity_Samurai_Helper::page_editor(); ?>
					</div>
				</div>
			</div>

			<!--
				Finished
			-->
			<div>
				<h3>Finished</h3>

				<p>
					Congratulations! You have now successfully set up your scarcity campaign.
				</p>

				<div class="ss-error ss-wizard-fixed-date-single-unpublished-page-warning">
					The following page need to be published for this campaign to function correctly.
					&nbsp;
					<a class="button">Publish This Page Now</a>
					<ul class="ul-square"></ul>
				</div>

				<br />

				<div class="ss-wizard-fixed-date-single-page-link">
					<h3>Page Link</h3>
					<p>
						To send users to your page, use the following link:
					</p>
					<div class="ss-sub">
						<ul class="ul-checklist">
							<li class="incomplete">
								<strong class="ss-wizard-fixed-date-single-page-title"></strong> &mdash;
								<a class="ss-wizard-fixed-date-single-page-edit-link">Edit</a> |
								<a class="ss-wizard-fixed-date-single-page-view-link">View</a>
								<p>
									Link:
									<input class="ss-wizard-fixed-date-single-page-email-url ss-page-url ss-text-input" type="text" readonly="readonly" />
									&nbsp;
									<a class="button ss-wizard-fixed-date-single-copy-to-clipboard-button">Copy To Clipboard</a>
									&nbsp;
									<span class="ss-copy-to-clipboard-confirmation-message">Copied</span>
								</p>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<!-- Buttons -->
		<div id="nav-menu-footer" class="scarcity-samurai-wizard-buttons">
			<a class="back button left">&laquo; Back</a>
			<a class="continue button-primary right">Continue &raquo;</a>
			<a class="finish button-primary right">Finish</a>
			<a class="view_campaign button-primary right">View Campaign</a>
			<div class="clear"></div>
		</div>
	</div>
</div>
