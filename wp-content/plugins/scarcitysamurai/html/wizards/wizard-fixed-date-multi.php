<div class="wrap ss-wizard-page">
	<div id="ss-logo-small" class="icon32"></div>
	<h2>Fixed Date Scarcity (Multi-page campaign) Setup</h2>

	<br />
	<div class="menu-edit">
		<?php wp_nonce_field('fixed-date-multi-wizard', 'security_token'); ?>

		<!-- Progress Bar -->
		<div id="nav-menu-header">
			<ul class="scarcity-samurai-wizard-progress-bar">
				<li>Campaign Name</li>
				<li>Page Selection</li>
				<li>Offer Page Expiry</li>
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
					<strong>What should your campaign be called?</strong>
				</p>
				<p class="ss-sub">
					<input class="ss-wizard-campaign-name ss-text-input" type="text" name="ss-wizard-fixed-date-multi-campaign-name" />
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
					Select the pages that make up your campaign.
				</p>
				<br />

				<!-- Squeeze page -->
				<p>
					<strong>Select your signup (squeeze) page</strong>
				</p>
				<p class="ss-sub">
					<?php
						Scarcity_Samurai_Helper::page_select( array(
							'name' => 'ss-wizard-fixed-date-multi-squeeze-page-id',
							'in_campaign' => false
						) );

						Scarcity_Samurai_Dialogs::create_new_page_link();
					?>
				</p>
				<div class="ss-auto-responder-wrapper ss-sub" style="display: none">
					<span style="line-height: 28px">Auto Responder:</span>
					<?php
						Scarcity_Samurai_Auto_Responder_Integrator::auto_responders_select();
					?>
					<div class="ss-auto-responder-not-selected-message ss-description">
						<br />
						<?php	Scarcity_Samurai_Auto_Responder_Integrator::supported_auto_responders_message(); ?>
					</div>
				</div>
				<br />

				<!-- Content pages -->
				<p>
					<strong>Select your content page(s)</strong> (optional)
				</p>
				<div class="ss-sub scarcity-samurai-wizard-content-pages-wrapper">
					<p class="ss-page-select-template ss-hidden">
						<?php
							Scarcity_Samurai_Helper::page_select( array(
								'name' => 'ss-wizard-fixed-date-multi-content-page-id',
								'in_campaign' => false
							) );

							Scarcity_Samurai_Dialogs::create_new_page_link();
						?>

						<span class='ss-wizard-fixed-date-multi-remove-content-page'>
							&nbsp;
							<a href='#'>Remove</a>
						</span>
					</p>
					<p class="ss-page-select-wrapper">
						<?php
							Scarcity_Samurai_Helper::page_select( array(
								'name' => 'ss-wizard-fixed-date-multi-content-page-id',
								'in_campaign' => false
							) );

							Scarcity_Samurai_Dialogs::create_new_page_link();
						?>

						<span class='ss-wizard-fixed-date-multi-remove-content-page'>
							&nbsp;
							<a href='#'>Remove</a>
						</span>
					</p>
				</div>
				<br />

				<!-- Offer page -->
				<p>
					<strong>Select your offer (sales) page</strong>
				</p>
				<p class="ss-sub">
					<?php
						Scarcity_Samurai_Helper::page_select( array(
							'name' => 'ss-wizard-fixed-date-multi-offer-page-id',
							'in_campaign' => false
						) );

						Scarcity_Samurai_Dialogs::create_new_page_link();
					?>
				</p>
			</div>

			<!--
				Offer Page Expiry
			-->
			<div>
				<h3>Set Offer Page Expiry</h3>
				<div class="ss-error ss-wizard-fixed-date-multi-time-discrepancy-warning">
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
					<strong>When should your offer page expire?</strong>
				</p>
				<p class="ss-sub">
					<?php
						$timezone = Scarcity_Samurai_Helper::user_timezone();
						extract(Scarcity_Samurai_Helper::parse_fixed_time(time(), $timezone));
					?>
					<select class="ss-select" name="ss-wizard-fixed-date-multi-month">
						<?php
							foreach (Scarcity_Samurai_Helper::$months as $month_number => $m) {
								$selected = selected($month_number, $month, false);
								echo "<option value='$month_number' $selected>$m</option>";
							}
						?>
					</select>
					<input class="ss-days-input ss-text-input" name="ss-wizard-fixed-date-multi-day" type="number" min="1" max="31" size="2" value="<?php echo $day; ?>">,
					<input class="ss-year-input ss-text-input" name="ss-wizard-fixed-date-multi-year" type="number" min="2012" size="4" value="<?php echo $year; ?>"> @
					<input class="ss-hours-input ss-text-input" name="ss-wizard-fixed-date-multi-hour" type="number" min="0" max="23" size="2" value="<?php echo $hour < 10 ? "0$hour" : $hour; ?>"> :
					<input class="ss-minutes-input ss-text-input" name="ss-wizard-fixed-date-multi-minute" type="number" min="0" max="59" size="2" value="<?php echo $minute < 10 ? "0$minute" : $minute; ?>">
					<?php
						Scarcity_Samurai_Helper::timezone_select( array(
							'name' => 'ss-wizard-fixed-date-multi-timezone',
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
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-multi-timer-expired" value='do_nothing' checked="checked">
							do nothing
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-multi-timer-expired" value='redirect_to_page'>
							redirect to page
						</label>
						<div class="ss-opts">
							<p>
								<?php
									Scarcity_Samurai_Helper::page_select( array(
										'name' => 'ss-wizard-fixed-date-multi-timer-expired-redirect-page-id'
									) );

									Scarcity_Samurai_Dialogs::create_new_page_link();
								?>
							</p>
						</div>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-multi-timer-expired" value='redirect_to_url' />
							redirect to URL
						</label>
						<div class="ss-opts">
							<p>
								<input class="ss-text-input" type="text" size="40" name="ss-wizard-fixed-date-multi-timer-expired-redirect-url" />
							</p>
						</div>
					</li>
				</ul>
			</div>

			<!--
				Visualize Scarcity
			-->
			<div>
				<h3>Visualize Scarcity</h3>
				<div class="ss-error ss-wizard-validation-errors">
					<p>
						Please fix the following errors:
					</p>
					<ul class="ul-square"></ul>
				</div>
				<p>
					Scarcity is MOST effective when it’s kept in your visitors field of
					view at all times. Here you can specify how you would like your
					scarcity banner to look on your offer page...
				</p>
				<br />
				<p>
					<strong>Where would you like to place your banner on your page?</strong>
				</p>
				<ul class="ul-radio">
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-multi-banner-position" value="header" checked="checked" />
							in the header
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-multi-banner-position" value="footer" />
							in the footer
						</label>
					</li>
					<li>
						<label>
							<input type="radio" class="ul-radio" name="ss-wizard-fixed-date-multi-banner-position" value="none" />
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
								'name' => 'ss-wizard-fixed-date-multi-banner-id',
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
									<input id="ss-wizard-fixed-date-multi-banner-show-immediately" type="radio" name="ss-wizard-fixed-date-multi-banner-show-type" value="immediately" checked="checked" />
									<label for="ss-wizard-fixed-date-multi-banner-show-immediately">immediately</label>
								</li>
								<li>
									<input id="ss-wizard-fixed-date-multi-banner-show-page-load" type="radio" name="ss-wizard-fixed-date-multi-banner-show-type" value="page_load" />
									<input class="ss-show-value-input ss-text-input" type="number" name="ss-wizard-fixed-date-multi-banner-show-value" min="0" value="0" />
									<label for="ss-wizard-fixed-date-multi-banner-show-page-load">seconds after page load</label>
								</li>
							</ul>
						</p>
					</div>

					<div class="ss-wizard-fixed-date-multi-banner-click-action-wrapper">
						<br />
						<p>
							<strong>
								What would you like to do when the banner is clicked?
							</strong>
						</p>
						<ul class="ul-radio">
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-fixed-date-multi-banner-action" value="do_nothing" checked="checked" />
									do nothing
								</label>
							</li>
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-fixed-date-multi-banner-action" value="redirect_to_page" />
									go to page
								</label>
								<div class="ss-opts">
									<p>
										<?php
											Scarcity_Samurai_Helper::page_select( array(
												'name' => 'ss-wizard-fixed-date-multi-banner-action-redirect-page-id'
											) );

											Scarcity_Samurai_Dialogs::create_new_page_link();
										?>
									</p>
								</div>
							</li>
							<li>
								<label>
									<input class="ul-radio" type="radio" name="ss-wizard-fixed-date-multi-banner-action" value="redirect_to_url" />
									go to URL
								</label>
								<div class="ss-opts">
									<p>
										<input class="ss-text-input" type="text" size="40" name="ss-wizard-fixed-date-multi-banner-action-redirect-url" />
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
				<h3>You're almost finished...</h3>
				<p>
					There are just a few minor steps left to complete:
				</p>

				<div class="ss-notice ss-wizard-fixed-date-multi-token-setup-instructions">
					<p>
						For your campaign to function properly you just need to configure
						<span class="auto_responder_short_name"></span> to work with Scarcity Samurai.
						This only takes a couple of minutes and you can see step-by-step
						instructions on how to do this here:
						<?php
							foreach ( Scarcity_Samurai_Auto_Responder_Integrator::supported_auto_responders() as $auto_responder ) {
								echo "
									<span class='instructions-link $auto_responder'>" .
										Scarcity_Samurai_Auto_Responder_Integrator::get_auto_responder_configuration_instructions_link( $auto_responder ) . "
									</span>
								";
							}
						?>
					</p>
					<p>
						<span class="description">
							Note: Your 'dynamic email' links for this campaign are listed below.
						</span>
					</p>
				</div>

				<div class="ss-error ss-wizard-fixed-date-multi-unpublished-pages-warning">
					<p>
						<span class="single">
							The following page need to be published for this campaign to function
							correctly. &nbsp;	<a class="button">Publish This Page Now</a>
						</span>
						<span class="multiple">
							The following pages need to be published for this campaign to function
							correctly. &nbsp;	<a class="button">Publish These Pages Now</a>
						</span>
						<ul class="ul-square"></ul>
					</p>
				</div>

				<div class="ss-wizard-fixed-date-multi-page-links">
					<h3>Page Links</h3>
					<p>
						To send users to pages in this campaign using your email system,
						use the following links:
					</p>

					<h4>Squeeze Page</h4>
					<div class="ss-sub">
						<ul class="ul-checklist">
							<li class="incomplete">
								<strong class="ss-wizard-fixed-date-multi-squeeze-page-title"></strong> &mdash;
								<a class="ss-wizard-fixed-date-multi-squeeze-page-edit-link">Edit</a> |
								<a class="ss-wizard-fixed-date-multi-squeeze-page-view-link">View</a>
								<p>
									Link:
									<input class="ss-wizard-fixed-date-multi-squeeze-page-email-url ss-page-url ss-text-input" type="text" readonly="readonly" />
									&nbsp;
									<a class="button ss-wizard-fixed-date-multi-copy-to-clipboard-button">Copy To Clipboard</a>
									&nbsp;
									<span class="ss-copy-to-clipboard-confirmation-message">Copied</span>
								</p>
							</li>
						</ul>
					</div>

					<div class="ss-wizard-fixed-date-multi-content-pages">
						<h4>Content Pages</h4>
						<div class="ss-sub">
							<ul class="ul-checklist">
								<li class="incomplete">
									<strong class="ss-wizard-fixed-date-multi-content-page-title"></strong> &mdash;
									<a class="ss-wizard-fixed-date-multi-content-page-edit-link">Edit</a> |
									<a class="ss-wizard-fixed-date-multi-content-page-view-link">View</a>
									<p>
										Link:
										<input class="ss-wizard-fixed-date-multi-content-page-email-url ss-page-url ss-text-input" type="text" readonly="readonly" />
										&nbsp;
										<a class="button ss-wizard-fixed-date-multi-copy-to-clipboard-button">Copy To Clipboard</a>
										&nbsp;
										<span class="ss-copy-to-clipboard-confirmation-message">Copied</span>
									</p>
								</li>
							</ul>
						</div>
					</div>

					<h4>Offer Page</h4>
					<div class="ss-sub">
						<ul class="ul-checklist">
							<li class="incomplete">
								<strong class="ss-wizard-fixed-date-multi-offer-page-title"></strong> &mdash;
								<a class="ss-wizard-fixed-date-multi-offer-page-edit-link">Edit</a> |
								<a class="ss-wizard-fixed-date-multi-offer-page-view-link">View</a>
								<p>
									Link:
									<input class="ss-wizard-fixed-date-multi-offer-page-email-url ss-page-url ss-text-input" type="text" readonly="readonly" />
									&nbsp;
									<a class="button ss-wizard-fixed-date-multi-copy-to-clipboard-button">Copy To Clipboard</a>
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
