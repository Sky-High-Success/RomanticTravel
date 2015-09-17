<div class="wrap">
	<div class="welcome-panel ss-welcome-panel">
		<img class="ss-logo" src="<?php echo Scarcity_Samurai_Helper::url( 'images/scarcity-samurai-logo.png' ); ?>" />
		<div class="welcome-panel-content">
			<div class="ss-welcome-message">
				<h3>Welcome to Scarcity Samurai!</h3>
				<p>
					You're just a few clicks away from supercharging your sales,
					your income, and your lifestyle with the most effective scarcity-based
					marketing system in the world...
				</p>
			</div>
		</div>
	</div>
	<div id="dashboard-widgets-wrap">
		<div id="dashboard-widgets" class="metabox-holder columns-1">
			<div class="postbox-container">
				<div id="postbox-container-1" class="meta-box-sortables">
					<div class="postbox">
						<h3 class="hndle nodrag"><span>Registration</span></h3>
						<div class="inside">
							<p>Please enter in your registration information.  This should have been emailed to you or you can find it by logging into <a href="https://noblesamurai.com/account/">our account system</a>.</p>
							<?php if ( isset( $error ) ) { ?>
							<div class="error"><p><?php esc_html_e( $error ); ?></p></div>
							<?php } ?>
							<form method="POST">
								<?php wp_nonce_field( 'register_scarcity_samurai', 'ss_register_nonce' ); ?>
								<table class="form-table">
									<tbody>
										<tr valign="top">
											<th scope="row">Email Address:</th>
											<td><input type="text" class="regular-text" name="ss_email" value="<?php echo esc_attr( $email ); ?>" /></td>
										</tr>
										<tr valign="top">
											<th scope="row">Registration Key:</th>
											<td><input type="text" class="regular-text" name="ss_samid" value="" /></td>
										</tr>
										<tr>
											<th></th>
											<td>
												<p>
													<label>
														<input type="checkbox" name="ss_eula" value="i_agree" />
														<strong>I have read and accept the End User Licence Agreement.</strong>
													</label>
													<br />
													To read the full terms of the End User License Agreement <a href="http://content.noblesamurai.com/ss-eula/" target="_blank">click here</a>.
												</p>
											</td>
										</tr>
									</tbody>
								</table>
								<input name="register" type="submit" class="button button-primary" value="Register" />
							</form>
						</div>
					</div>
				</div>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
