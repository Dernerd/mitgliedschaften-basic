<?php
/**
 * View.
 * @package Membership2
 */

/**
 * Renders Help and Documentation Page.
 *
 * Extends MS_View for rendering methods and magic methods.
 *
 * @since  1.0.0
 *
 * @return object
 */
class MS_View_Help extends MS_View {

	/**
	 * Overrides parent's to_html() method.
	 *
	 * Creates an output buffer, outputs the HTML and grabs the buffer content before releasing it.
	 * Creates a wrapper 'ms-wrap' HTML element to contain content and navigation. The content inside
	 * the navigation gets loaded with dynamic method calls.
	 * e.g. if key is 'settings' then render_settings() gets called, if 'bob' then render_bob().
	 *
	 * @since  1.0.0
	 *
	 * @return object
	 */
	public function to_html() {
		$this->check_simulation();

		// Setup navigation tabs.
		$tabs = $this->data['tabs'];

		ob_start();
		// Render tabbed interface.
		?>
		<div class="ms-wrap wrap">
			<?php
			MS_Helper_Html::settings_header(
				array(
					'title' => __( 'Hilfe und Dokumentation', 'membership2' ),
					'title_icon_class' => 'wpmui-fa wpmui-fa-info-circle',
				)
			);
			$active_tab = MS_Helper_Html::html_admin_vertical_tabs( $tabs );

			// Call the appropriate form to render.
			$callback_name = 'render_tab_' . str_replace( '-', '_', $active_tab );
			$render_callback = apply_filters(
				'ms_view_help_render_callback',
				array( $this, $callback_name ),
				$active_tab,
				$this->data
			);
			?>
			<div class="ms-settings ms-help-content">
				<?php
				$html = call_user_func( $render_callback );
				$html = apply_filters( 'ms_view_help_' . $callback_name, $html );
				echo $html;
				?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the General help contents
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function render_tab_general() {
		ob_start();
		?>
		<h2>
			<?php _e( 'Du bist unglaublich :)', 'membership2' ); ?><br />
		</h2>
		<p>
			<em><?php _e( 'Vielen Dank, dass Du Mitgliedschaften nutzt', 'membership2' ); ?></em>
			<br/ ><br />
			<?php _ex( 'Hier ist eine kurze Übersicht:', 'help', 'membership2' ); ?>
		</p>
		<div>
		<?php
		printf(
			_x( 'Mitgliedschaften Version: <strong>%s</strong>', 'help', 'membership2' ),
			MS_PLUGIN_VERSION
		);
		if ( MS_IS_PRO ) {
			printf(
				'<br />' .
				_x( 'Hey, dies ist die <strong>PRO-Version</strong> von Mitgliedschaften - vielen Dank für die Unterstützung!', 'help', 'membership2' )
			);
		} else {
			printf(
				'<br />' .
				_x( 'Dies ist die <strong>BASIC-Version</strong> von PS-Mitgliedschaften - Falls Du die erweiterten Funktionen wie Netzwerkschutz und Erweiterungen benötigst, hole Dir die %sPRO-Version%s!', 'help', 'membership2' ),
				'<a href="https://n3rds.work/shop/artikel/psmitgliedschaften-pro/" target="_blank">',
				'</a>'
			);
		}
		if ( is_multisite() ) {
			if ( MS_Plugin::is_network_wide() ) {
				printf(
					'<br />' .
					_x( 'Dein Schutzmodus ist <strong>%s Netzwerk-Weit</strong>.', 'help', 'membership2' ),
					'<i class="wpmui-fa wpmui-fa-globe"></i>'
				);
			} else {
				printf(
					'<br />' .
					_x( 'Dein Schutz deckt <strong>%s nur diese Seite ab</strong>.', 'help', 'membership2' ),
					'<i class="wpmui-fa wpmui-fa-home"></i>'
				);
			}
		}
		$admin_cap = MS_Plugin::instance()->controller->capability;
		if ( $admin_cap ) {
			printf(
				'<br />' .
				_x( 'Alle Benutzer mit Fähigkeiten <strong>%s</strong> sind Mitgliedschaften Admin-Benutzer.', 'help', 'membership2' ),
				$admin_cap
			);
		} else {
			printf(
				'<br />' .
				_x( 'Nur der <strong>Netzwerk-Admin</strong> kann Mitgliedschaften verwalten.', 'help', 'membership2' )
			);
		}
		if ( defined( 'MS_STOP_EMAILS' ) && MS_STOP_EMAILS ) {
			printf(
				'<br />' .
				_x( 'Derzeit ist Mitgliedschaften so konfiguriert, dass <strong>keine E-Mails gesendet</strong> gesendet werden.', 'help', 'membership2' )
			);
		}
		if ( defined( 'MS_LOCK_SUBSCRIPTIONS' ) && MS_LOCK_SUBSCRIPTIONS ) {
			printf(
				'<br />' .
				_x( 'Derzeit ist Mitgliedschaften so konfiguriert, dass <strong>kein Abonnementstatus abläuft/geändert </strong> wird.', 'help', 'membership2' )
			);
		}
		if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
			printf(
				'<br />' .
				_x( 'Warnung: DISABLE_WP_CRON ist <strong>aktiviert</strong>! Mitgliedschaften sendet nicht alle E-Mails oder ändert den Abonnementstatus, wenn das Ablaufdatum erreicht ist!', 'help', 'membership2' )
			);
		}
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			printf(
				'<br />' .
				_x( 'WP_DEBUG ist <strong>aktiviert</strong> auf dieser Webseite.', 'help', 'membership2' )
			);
		} else {
			printf(
				'<br />' .
				_x( 'WP_DEBUG ist <strong>deaktiviert</strong> auf dieser Webseite.', 'help', 'membership2' )
			);
		}
		?>
		</div>
		<?php MS_Helper_Html::html_separator(); ?>
		<h2>
			<?php _ex( 'Erweiterungen Menü', 'help', 'membership2' ); ?>
		</h2>
		<table cellspacing="0" cellpadding="4" border="0" width="100%">
			<tr>
				<td>
					<span class="top-menu">
					<div class="menu-image dashicons dashicons-lock"></div>
					<?php _e( 'Mitgliedschaften', 'membership2' ); ?>
					</span>
				</td>
				<td></td>
			</tr>
			<tr class="alternate">
				<td><span><?php _e( 'Mitgliedschaften', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Erstelle und verwalte Mitgliedschaftspläne, für die sich Benutzer anmelden können', 'help', 'membership2' ); ?></td>
			</tr>
			<tr>
			<td><span><?php _e( 'Schutzregeln', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Lege die Schutzoptionen fest, d. H. welche Seiten durch welche Mitgliedschaft geschützt sind', 'help', 'membership2' ); ?></td>
			</tr>
			<tr class="alternate">
				<td><span><?php _e( 'Alle Mitglieder', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Listet alle WordPress-Benutzer auf und ermöglicht die Verwaltung ihrer Mitgliedschaften', 'help', 'membership2' ); ?></td>
			</tr>
			<tr>
				<td><span><?php _e( 'Mitglied hinzufügen', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Erstelle einen neuen WP-Benutzer oder bearbeitee Abonnements eines vorhandenen Benutzers', 'help', 'membership2' ); ?></td>
			</tr>
			<tr class="alternate">
				<td><span><?php _e( 'Abrechnung', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Verwalte gesendete Rechnungen, einschließlich Details wie dem Zahlungsstatus. <em>Nur sichtbar, wenn Du mindestens eine bezahlte Mitgliedschaft hast</em>', 'help', 'membership2' ); ?></td>
			</tr>
			<tr>
				<td><span><?php _e( 'Gutscheine', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Verwalte Rabattgutscheine. <em>Benötigt Erweiterung "Gutscheine"</em>', 'help', 'membership2' ); ?></td>
			</tr>
			<tr class="alternate">
				<td><span><?php _e( 'Einladungscodes', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Verwalte Einladungscodes. <em>Benötigt Erweiterung "Einladungscodes"</em>', 'help', 'membership2' ); ?></td>
			</tr>
			<tr>
				<td><span><?php _e( 'Erweiterungen', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Erweiterungen aktivieren', 'help', 'membership2' ); ?></td>
			</tr>
			<tr class="alternate">
				<td><span><?php _e( 'Einstellungen', 'membership2' ); ?></span></td>
				<td><?php _ex( 'Globale Plugin-Optionen wie Mitgliederseiten, Zahlungsoptionen und E-Mail-Vorlagen', 'help', 'membership2' ); ?></td>
			</tr>
		</table>
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the Shortcode help contents
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function render_tab_shortcodes() {
		ob_start();
		?>

		<?php
		/*********
		**********   ms-protect-content   **************************************
		*********/
		?>
		<h2><?php _ex( 'Häufige Shortcodes', 'help', 'membership2' ); ?></h2>

		<div id="ms-protect-content" class="ms-help-box">
			<h3><code>[ms-protect-content]</code></h3>

			<?php _ex( 'Setze dies um alle Inhalte, um sie für/vor bestimmten Mitgliedern zu schützen (basierend auf ihrer Mitgliedschaftsstufe).', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>id</code>
						<?php _ex( '(ID list)', 'help', 'membership2' ); ?>
						<strong><?php _ex( 'Benötigt', 'help', 'membership2' ); ?></strong>.
						<?php _ex( 'Eine oder mehrere Mitglieds-IDs. Der Shortcode wird ausgelöst, wenn der Benutzer mindestens einer dieser Mitgliedschaften angehört', 'help', 'membership2' ); ?>
					</li>
					<li>
						<code>access</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Definiert, ob Mitglieder der Mitgliedschaften den Inhalt sehen können oder nicht', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>silent</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Durch den stillen Schutz werden Inhalte entfernt, ohne dass dem Benutzer eine Nachricht angezeigt wird', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							no
						</span>
					</li>
					<li>
						<code>msg</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Gib eine benutzerdefinierte Schutznachricht an. <em>Dies wird nur angezeigt, wenn Stiller Schutz nicht wahr ist</em>', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p>
					<code>[ms-protect-content id="1"]</code>
					<?php _ex( 'Dies können nur Mitglieder mit Mitgliedschaft 1 sehen!', 'help', 'membership2' ); ?>
					<code>[/ms-protect-content]</code>
				</p>
				<p>
					<code>[ms-protect-content id="2,3" access="no" silent="yes"]</code>
					<?php _ex( 'Jeder außer Mitgliedern der Mitgliedschaften 2 oder 3 usw. kann dies sehen!', 'help', 'membership2' ); ?>
					<code>[/ms-protect-content]</code>
				</p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-user   *************************************************
		*********/
		?>

		<div id="ms-user" class="ms-help-box">
			<h3><code>[ms-user]</code></h3>

			<?php _ex( 'Zeigt den Inhalt nur bestimmten Benutzern an (ohne Berücksichtigung der Mitgliedschaftsstufe)', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>type</code>
						<?php _ex( '(all|loggedin|guest|admin|non-admin)', 'help', 'membership2' ); ?>
						<?php _ex( 'Entscheide, welcher Benutzertyp die Nachricht sehen soll', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"loggedin"
						</span>
					</li>
					<li>
						<code>msg</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Stelle eine benutzerdefinierte Schutzmeldung bereit, die Benutzern angezeigt wird, die keinen Zugriff auf den Inhalt haben', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p>
					<code>[ms-user]</code>
					<?php _ex( 'Du bist eingeloggt', 'help', 'membership2' ); ?>
					<code>[/ms-user]</code>
				</p>
				<p>
					<code>[ms-user type="guest"]</code>
					<?php printf( htmlspecialchars( _x( '<a href="">Jetzt registrieren</a>! <a href="">Du hast bereits ein Konto</a>?', 'help', 'membership2' ) ) ); ?>
					<code>[/ms-user]</code>
				</p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-membership-register-user   *****************************
		*********/
		?>

		<div id="ms-membership-register-user" class="ms-help-box">
			<h3><code>[ms-membership-register-user]</code></h3>

			<?php _ex( 'Zeigt ein Registrierungsformular an. Besucher können mit diesem Formular ein WordPress-Benutzerkonto erstellen', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>title</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Registerformulars', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Ein Konto erstellen', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>first_name</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Anfangswert für Vorname', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>last_name</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Anfangswert für den Nachnamen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Anfangswert für Benutzername', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>email</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Anfangswert für die E-Mail-Adresse', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>membership_id</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Mitglieds-ID, die dem neuen Benutzer zugewiesen werden soll. Dieses Feld ist ausgeblendet und kann bei der Registrierung nicht geändert werden. <em>Hinweis: Wenn für diese Mitgliedschaft eine Zahlung erforderlich ist, wird der Benutzer nach der Registrierung zum Zahlungsgateway weitergeleitet</em>', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>loginlink</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige einen Login-Link unter dem Formular an', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"yes"
						</span>
					</li>
				</ul>

				<h4><?php _e( 'Feldbezeichnungen', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>label_first_name</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Vorname', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_last_name</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Familienname', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Wähle einen Benutzernamen', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_email</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Email Addresse', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_password</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Passwort', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_password2</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Bestätige das Passwort', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_register</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							"<?php _e( 'Registriere mein Konto', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>hint_first_name</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Platzhalter im Feld', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							""
						</span>
					</li>
					<li>
						<code>hint_last_name</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Platzhalter im Feld', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							""
						</span>
					</li>
					<li>
						<code>hint_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Platzhalter im Feld', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							""
						</span>
					</li>
					<li>
						<code>hint_email</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Platzhalter im Feld', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							""
						</span>
					</li>
					<li>
						<code>hint_password</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Platzhalter im Feld', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							""
						</span>
					</li>
					<li>
						<code>hint_password2</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Platzhalter im Feld', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							""
						</span>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-register-user]</code></p>
				<p><code>[ms-membership-register-user title="" hint_email="john@email.com" label_password2="Repeat"]</code></p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-membership-signup   ************************************
		*********/
		?>

		<div id="ms-membership-signup" class="ms-help-box">
			<h3><code>[ms-membership-signup]</code></h3>

			<?php _ex( 'Zeigt eine Liste aller Mitgliedschaften an, für die sich der aktuelle Benutzer anmelden kann', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<h4><?php _ex( 'Allgemeine Optionen', 'help', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code><?php echo esc_html( MS_Helper_Membership::MEMBERSHIP_ACTION_SIGNUP ); ?>_text</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Tastenbeschriftung', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Anmelden', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code><?php echo esc_html( MS_Helper_Membership::MEMBERSHIP_ACTION_MOVE ); ?>_text</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Tastenbeschriftung', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Ändern', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code><?php echo esc_html( MS_Helper_Membership::MEMBERSHIP_ACTION_CANCEL ); ?>_text</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Tastenbeschriftung', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Abbrechen', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code><?php echo esc_html( MS_Helper_Membership::MEMBERSHIP_ACTION_RENEW ); ?>_text</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Tastenbeschriftung', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Erneuern', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code><?php echo esc_html( MS_Helper_Membership::MEMBERSHIP_ACTION_PAY ); ?>_text</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Tastenbeschriftung', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Zur vollständigen Bezahlung', 'membership2' ); ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-signup]</code></p>
			</div>
		</div>



		<?php
		/*********
		**********   ms-membership-login   *************************************
		*********/
		?>

		<div id="ms-membership-login" class="ms-help-box">
			<h3><code>[ms-membership-login]</code></h3>

			<?php _ex( 'Zeigt das Anmelde-/Passwortverlustformular oder für angemeldete Benutzer einen Abmeldelink an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<h4><?php _ex( 'Allgemeine Optionen', 'help', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>title</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Der Titel über dem Anmeldeformular', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>show_labels</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Stelle auf "yes" ein, um die Beschriftungen für Benutzername und Passwort vor den Eingabefeldern anzuzeigen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							no
						</span>
					</li>
					<li>
						<code>redirect_login</code>
						<?php _ex( '(URL)', 'help', 'membership2' ); ?>
						<?php _ex( 'Die Seite, die angezeigt werden soll, nachdem der Benutzer angemeldet wurde', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php echo MS_Model_Pages::get_url_after_login(); ?>"
						</span>
					</li>
					<li>
						<code>redirect_logout</code>
						<?php _ex( '(URL)', 'help', 'membership2' ); ?>
						<?php _ex( 'Die Seite, die angezeigt werden soll, nachdem der Benutzer abgemeldet wurde', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php echo MS_Model_Pages::get_url_after_logout(); ?>"
						</span>
					</li>
					<li>
						<code>header</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>register</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>autofocus</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Konzentriere das Anmeldeformular auf das Laden der Seite', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
				</ul>

				<h4><?php _ex( 'Mehr Optionen', 'help', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>holder</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"div"
						</span>
					</li>
					<li>
						<code>holderclass</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"ms-login-form"
						</span>
					</li>
					<li>
						<code>item</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>itemclass</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>prefix</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>postfix</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>wrapwith</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>wrapwithclass</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>form</code>
						<?php _ex( '(login|lost|logout)', 'help', 'membership2' ); ?>
						<?php _ex( 'Definiert, welches Formular angezeigt werden soll. Ein leerer Wert ermöglicht es dem Plugin, automatisch zwischen Anmelden/Abmelden zu wählen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>nav_pos</code>
						<?php _ex( '(top|bottom)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"top"
						</span>
					</li>
				</ul>

				<h4><?php
				printf(
					__( 'Optionen nur für <code>%s</code>', 'membership2' ),
					'form="login"'
				);
				?></h4>
				<ul>
					<li>
						<code>show_note</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige über dem Anmeldeformular den Hinweis "Du bist nicht angemeldet" an', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>label_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Benutzername' ); ?>"
						</span>
					</li>
					<li>
						<code>label_password</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Passwort' ); ?>"
						</span>
					</li>
					<li>
						<code>label_remember</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Behalte mich in Erinnerung' ); ?>"
						</span>
					</li>
					<li>
						<code>label_log_in</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Anmeldung' ); ?>"
						</span>
					</li>
					<li>
						<code>id_login_form</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"loginform"
						</span>
					</li>
					<li>
						<code>id_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"user_login"
						</span>
					</li>
					<li>
						<code>id_password</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"user_pass"
						</span>
					</li>
					<li>
						<code>id_remember</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"rememberme"
						</span>
					</li>
					<li>
						<code>id_login</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"wp-submit"
						</span>
					</li>
					<li>
						<code>show_remember</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>value_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>value_remember</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Setze dies auf "yes", um das Kontrollkästchen "Angemeldet bleiben" standardmäßig zu aktivieren', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							no
						</span>
					</li>
				</ul>

				<h4><?php
				printf(
					__( 'Optionen nur für <code>%s</code>', 'membership2' ),
					'form="lost"'
				);
				?></h4>
				<ul>
					<li>
						<code>label_lost_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Benutzername oder E-Mail-Adresse', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>label_lostpass</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Passwort zurücksetzen', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>id_lost_form</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"lostpasswordform"
						</span>
					</li>
					<li>
						<code>id_lost_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"user_login"
						</span>
					</li>
					<li>
						<code>id_lostpass</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"wp-submit"
						</span>
					</li>
					<li>
						<code>value_username</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-login]</code></p>
				<p>
					<code>[ms-membership-login form="logout"]</code>
					<?php _ex( 'is identical to', 'help', 'membership2' ); ?>
					<code>[ms-membership-logout]</code>
				</p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-note   *************************************************
		*********/
		?>

		<div id="ms-note" class="ms-help-box">
			<h3><code>[ms-note]</code></h3>

			<?php _ex( 'Zeigt dem Benutzer eine Info-/Erfolgsmeldung an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>type</code>
						(info|warning)
						<?php _ex( 'Die Art der Mitteilung. Info ist grün und Warnung rot', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"info"
						</span>
					</li>
					<li>
						<code>class</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Eine zusätzliche CSS-Klasse, die dem Hinweis hinzugefügt werden sollte', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p>
					<code>[ms-note type="info"]</code>
					<?php _ex( 'Vielen Dank, dass Du Dich unserer Premium-Mitgliedschaft angeschlossen hast!', 'help', 'membership2' ); ?>
					<code>[/ms-note]</code>
				</p>
				<p>
					<code>[ms-note type="warning"]</code>
					<?php _ex( 'Bitte melde Dich an, um auf diese Seite zuzugreifen!', 'help', 'membership2' ); ?>
					<code>[/ms-note]</code>
				</p>
			</div>
		</div>

		<?php
		/*********
		**********   ms-member-info   ******************************************
		*********/
		?>

		<div id="ms-member-info" class="ms-help-box">
			<h3><code>[ms-member-info]</code></h3>

			<?php _ex( 'Zeigt Details zum aktuellen Mitglied an, z. B. den Vornamen des Mitglieds oder eine Liste der Mitgliedschaften, die er abonniert hat', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>value</code>
						(email|firstname|lastname|fullname|memberships|custom)
						<?php _ex( 'Definiert, welcher Wert angezeigt werden soll. <br> Über die API kann ein benutzerdefiniertes Feld festgelegt werden (die API-Dokumente finden Sie auf der Registerkarte Erweiterte Einstellungen).', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"fullname"
						</span>
					</li>
					<li>
						<code>default</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Standardwert, der angezeigt wird, wenn das definierte Feld leer ist', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>before</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige dieses Text vor dem Feldwert an. Wird nur verwendet, wenn das Feld nicht leer ist', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"&lt;span&gt;"
						</span>
					</li>
					<li>
						<code>after</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige diesen Text nach dem Feldwert an. Wird nur verwendet, wenn das Feld nicht leer ist', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"&lt;/span&gt;"
						</span>
					</li>
					<li>
						<code>custom_field</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Nur relevant für den Wert <code>custom</code>. Dies ist der Name des benutzerdefinierten Felds, das abgerufen werden soll', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>list_separator</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wird verwendet, wenn der Feldwert eine Liste ist (d. H. Eine Mitgliederliste oder der Inhalt eines benutzerdefinierten Felds).', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							", "
						</span>
					</li>
					<li>
						<code>list_before</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wird verwendet, wenn der Feldwert eine Liste ist (d. H. Eine Mitgliederliste oder der Inhalt eines benutzerdefinierten Felds).', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>list_after</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wird verwendet, wenn der Feldwert eine Liste ist (d. H. Eine Mitgliederliste oder der Inhalt eines benutzerdefinierten Felds).', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							""
						</span>
					</li>
					<li>
						<code>user</code>
						<?php _ex( '(User-ID)', 'help', 'membership2' ); ?>
						<?php _ex( 'Verwende diese Option, um Daten eines beliebigen Benutzers anzuzeigen. Wenn nicht angegeben, wird der aktuelle Benutzer angezeigt', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							0
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Erweiterung:', 'help', 'membership2' ); ?></em></p>
				<p>
					<code>[ms-member-info value="fullname" default="(Guest)"]</code>
				</p>
				<p>
					<code>[ms-member-info value="memberships" default="Sign up now!" list_separator=" | " before="Deine Mitgliedschaften: "]</code>
				</p>
			</div>
		</div>

		<?php
		/**
		 * Allow Add-ons to add their own shortcode documentation.
		 *
		 * @since  1.0.1.0
		 */
		do_action( 'ms_view_help_shortcodes-common' );
		?>



		<hr />

		<h2><?php _ex( 'Mitgliedschaft Shortcodes', 'help', 'membership2' ); ?></h2>


		<?php
		/*********
		**********   ms-membership-title   *************************************
		*********/
		?>

		<div id="ms-membership-title" class="ms-help-box">
			<h3><code>[ms-membership-title]</code></h3>

			<?php _ex( 'Zeigt den Namen einer bestimmten Mitgliedschaft an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>id</code>
						<?php _ex( '(Single ID)', 'help', 'membership2' ); ?>
						<strong><?php _ex( 'Erforderlich', 'help', 'membership2' ); ?></strong>.
						<?php _ex( 'Die Mitgliedschafts-ID', 'help', 'membership2' ); ?>
					</li>
					<li>
						<code>label</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wird vor dem Titel angezeigt', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Titel der Mitgliedschaft:', 'membership2' ) ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-title id="5" label=""]</code></p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-membership-price   *************************************
		*********/
		?>

		<div id="ms-membership-price" class="ms-help-box">
			<h3><code>[ms-membership-price]</code></h3>

			<?php _ex( 'Zeigt den Preis einer bestimmten Mitgliedschaft an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>id</code>
						<?php _ex( '(Single ID)', 'help', 'membership2' ); ?>
						<strong><?php _ex( 'Erforderlich', 'help', 'membership2' ); ?></strong>.
						<?php _ex( 'Die Mitgliedschafts-ID', 'help', 'membership2' ); ?>
					</li>
					<li>
						<code>currency</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>label</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wird vor dem Preis angezeigt', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Mitgliedschaftspreis:', 'membership2' ) ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-price id="5" currency="no" label="Only today:"]</code> $</p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-membership-details   ***********************************
		*********/
		?>

		<div id="ms-membership-details" class="ms-help-box">
			<h3><code>[ms-membership-details]</code></h3>

			<?php _ex( 'Zeigt die Beschreibung einer bestimmten Mitgliedschaft an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>id</code>
						<?php _ex( '(Einzel ID)', 'help', 'membership2' ); ?>
						<strong><?php _ex( 'Benötigt', 'help', 'membership2' ); ?></strong>.
						<?php _ex( 'Mitgliedschaft ID', 'help', 'membership2' ); ?>
					</li>
					<li>
						<code>label</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wird vor der Beschreibung angezeigt', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Details zur Mitgliedschaft:', 'membership2' ) ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiele:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-details id="5"]</code></p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-membership-buy   *************************************
		*********/
		?>

		<div id="ms-membership-buy" class="ms-help-box">
			<h3><code>[ms-membership-buy]</code></h3>

			<?php _ex( 'Zeigt eine Schaltfläche zum Kaufen/Anmelden für die angegebene Mitgliedschaft an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>id</code>
						<?php _ex( '(Einzel ID)', 'help', 'membership2' ); ?>
						<strong><?php _ex( 'Benötigt', 'help', 'membership2' ); ?></strong>.
						<?php _ex( 'Mitgliedschaft ID', 'help', 'membership2' ); ?>
					</li>
					<li>
						<code>label</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Schaltflächenbezeichnung', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Anmelden', 'membership2' ); ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-buy id="5" label="Buy now!"]</code></p>
			</div>
		</div>

		<?php
		/**
		 * Allow Add-ons to add their own shortcode documentation.
		 *
		 * @since  1.0.1.0
		 */
		do_action( 'ms_view_help_shortcodes-membership' );
		?>


		<hr />

		<h2><?php _ex( 'Weniger gebräuchliche Shortcodes', 'help', 'membership2' ); ?></h2>


		<?php
		/*********
		**********   ms-membership-logout   ************************************
		*********/
		?>

		<div id="ms-membership-logout" class="ms-help-box">
			<h3><code>[ms-membership-logout]</code></h3>

			<?php _ex( 'Zeigt einen Abmeldelink an. Wenn der Benutzer nicht angemeldet ist, gibt der Shortcode eine leere Zeichenfolge zurück', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<h4><?php _ex( 'Allgemeine Optionen', 'help', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>redirect</code>
						<?php _ex( '(URL)', 'help', 'membership2' ); ?>
						<?php _ex( 'Die Seite, die angezeigt werden soll, nachdem der Benutzer abgemeldet wurde', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php echo MS_Model_Pages::get_url_after_logout(); ?>"
						</span>
					</li>
				</ul>

				<h4><?php _ex( 'Mehr Optionen', 'help', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>holder</code>
						<?php _ex( 'Wrapper-Element (div, span, p)', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"div"
						</span>
					</li>
					<li>
						<code>holder_class</code>
						<?php _ex( 'Klasse für den Wrapper', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"ms-logout-form"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiele:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-logout]</code></p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-membership-account-link   ******************************
		*********/
		?>

		<div id="ms-membership-account-link" class="ms-help-box">
			<h3><code>[ms-membership-account-link]</code></h3>

			<?php _ex( 'Fügt einen einfachen Link zur Kontoseite ein', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>label</code>
						<?php _ex( '(Text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Der Inhalt des Links', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Besuche Deine Kontoseite für weitere Informationen', 'membership2' ) ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiele:', 'help', 'membership2' ); ?></em></p>
				<p>
					<?php _ex( 'Abonnements verwalten in', 'help', 'membership2' ); ?>
					<code>[ms-membership-account-link label="<?php _ex( 'deinen Konto', 'help', 'membership2' ); ?>"]!</code>
				</p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-protection-message   ***********************************
		*********/
		?>

		<div id="ms-protection-message" class="ms-help-box">
			<h3><code>[ms-protection-message]</code></h3>

			<?php _ex( 'Zeigt die Schutzmeldung auf Seiten an, auf die der Benutzer nicht zugreifen kann. Dieser Shortcode sollte nur auf der Mitgliederseite "Mitgliedschaften" verwendet werden', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li><em><?php _ex( 'keine Argumente', 'help', 'membership2' ); ?></em></li>
				</ul>

				<p>
					<?php _ex( 'Tipp: Wenn der Benutzer nicht angemeldet ist, wird in diesem Shortcode auch das Standard-Anmeldeformular angezeigt. <em> Wenn Du ein eigenes Anmeldeformular über den Shortcode [ms-membership-login] bereitstellst, fügt dieser Shortcode kein zweites Anmeldeformular hinzu.</em>', 'help', 'membership2' ); ?>
				</p>

				<p><em><?php _ex( 'Beispiele:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-protection-message]</code></p>
			</div>
		</div>

		<?php
		/*********
		**********   ms-membership-account   ***********************************
		*********/
		?>

		<div id="ms-membership-account" class="ms-help-box">
			<h3><code>[ms-membership-account]</code></h3>

			<?php _ex( 'Zeigt die Seite "Mein Konto" des aktuell angemeldeten Benutzers an', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<h4><?php _e( 'Mitgliedschaftsbereich', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>show_membership</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Gibt an, ob die aktuellen Mitgliedschaften des Benutzers angezeigt werden sollen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>membership_title</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des aktuellen Mitgliedschaftsabschnitts', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Deine Mitgliedschaft', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>show_membership_change</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige den Link an, um andere Mitgliedschaften zu abonnieren', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>membership_change_label</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Links', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Wechseln', 'membership2' ); ?>"
						</span>
					</li>
				</ul>

				<h4><?php _e( 'Profilabschnitt', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>show_profile</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Gibt an, ob die Profildetails des Benutzers angezeigt werden sollen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>profile_title</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Benutzerprofilabschnitts', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Persönliche Daten', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>show_profile_change</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige den Link zum Bearbeiten des Benutzerprofils an', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>profile_change_label</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Links', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Bearbeiten', 'membership2' ); ?>"
						</span>
					</li>
				</ul>

				<h4><?php _e( 'Abschnitt Rechnungen', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>show_invoices</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Gibt an, ob der Abschnitt mit den letzten Rechnungen angezeigt werden soll', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>invoices_title</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Rechnungsabschnitts', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Rechnungen', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>limit_invoices</code>
						<?php _ex( '(nummer)', 'help', 'membership2' ); ?>
						<?php _ex( 'Anzahl der Rechnungen, die in der Liste der letzten Rechnungen angezeigt werden sollen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							10
						</span>
					</li>
					<li>
						<code>show_all_invoices</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige den Link zur vollständigen Liste der Benutzerrechnungen an', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>invoices_details_label</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Links', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Alle ansehen', 'membership2' ); ?>"
						</span>
					</li>
				</ul>

				<h4><?php _e( 'Bereich Aktivitäten', 'membership2' ); ?></h4>
				<ul>
					<li>
						<code>show_activity</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Gibt an, ob der Abschnitt mit den letzten Aktivitäten des Benutzers angezeigt werden soll', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>activity_title</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Abschnitts Aktivitäten', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Aktivitäten', 'membership2' ); ?>"
						</span>
					</li>
					<li>
						<code>limit_activities</code>
						<?php _ex( '(nummer)', 'help', 'membership2' ); ?>
						<?php _ex( 'Anzahl der Elemente, die in der Liste der letzten Aktivitäten angezeigt werden sollen', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							10
						</span>
					</li>
					<li>
						<code>show_all_activities</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Zeige den Link zur vollständigen Liste der Benutzeraktivitäten an', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
					<li>
						<code>activity_details_label</code>
						<?php _ex( '(text)', 'help', 'membership2' ); ?>
						<?php _ex( 'Titel des Links', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							"<?php _e( 'Alle ansehen', 'membership2' ); ?>"
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-membership-account]</code></p>
				<p><code>[ms-membership-account show_profile_change="no" show_activity="no" limit_activities="3" activity_title="Last 3 activities"]</code></p>
			</div>
		</div>


		<?php
		/*********
		**********   ms-invoice   **********************************************
		*********/
		?>

		<div id="ms-invoice" class="ms-help-box">
			<h3><code>[ms-invoice]</code></h3>

			<?php _ex( 'Zeige dem Benutzer eine Rechnung an. In den meisten Fällen nicht sehr nützlich, da die Rechnung nur vom Rechnungsempfänger eingesehen werden kann', 'help', 'membership2' ); ?>
			<div class="ms-help-toggle"><?php _ex( 'Erweitern', 'help', 'membership2' ); ?></div>
			<div class="ms-help-details" style="display:none">
				<ul>
					<li>
						<code>id</code>
						<?php _ex( '(Einzel ID)', 'help', 'membership2' ); ?>
						<strong><?php _ex( 'Benötigt', 'help', 'membership2' ); ?></strong>.
						<?php _ex( 'Rechnungs ID', 'help', 'membership2' ); ?>
					</li>
					<li>
						<code>pay_button</code>
						<?php _ex( '(yes|no)', 'help', 'membership2' ); ?>
						<?php _ex( 'Wenn die Rechnung eine Schaltfläche "Bezahlen" enthalten soll', 'help', 'membership2' ); ?>
						<span class="ms-help-default">
							<?php _ex( 'Standard:', 'help', 'membership2' ); ?>
							yes
						</span>
					</li>
				</ul>

				<p><em><?php _ex( 'Beispiel:', 'help', 'membership2' ); ?></em></p>
				<p><code>[ms-invoice id="123"]</code></p>
			</div>
		</div>

		<?php
		/**
		 * Allow Add-ons to add their own shortcode documentation.
		 *
		 * @since  1.0.1.0
		 */
		do_action( 'ms_view_help_shortcodes-other' );
		?>

		<hr />
		<?php
		$html = ob_get_clean();

		return apply_filters(
			'ms_view_help_shortcodes',
			$html
		);
	}

	/**
	 * Renders the Network-Wide Protection help contents
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function render_tab_network() {
		ob_start();
		?>
		<h2><?php _ex( 'Netzwerkweiter Schutz', 'help', 'membership2' ); ?></h2>
		<?php if ( MS_IS_PRO ) : ?>
		<p>
			<strong><?php _ex( 'Aktiviere den netzwerkweiten Modus', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Füge in der wp-config.php die Zeile <code>define ("MS_PROTECT_NETWORK", true); </code> hinzu, um den netzwerkweiten Schutz zu aktivieren. Wichtig: Die Einstellungen für den netzwerkweiten Modus werden anders als die normalen (standortweiten) Einstellungen gespeichert. Nachdem Du das erste Mal in den netzwerkweiten Modus gewechselt hast, musst Du das Plugin erneut einrichten.<br />Hinweis: Das Plugin aktiviert sich automatisch netzwerkweit. Du musst nur die obige Option hinzufügen.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Deaktiviere den netzwerkweiten Modus', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Entferne einfach die Zeile <code>define( "MS_PROTECT_NETWORK", true );</code> aus der wp-config.php um zum normalen Seite für Seite Schutz zurückzukehren. Alle vorherigen Mitgliedschaften bleiben bestehen (wenn Du vor der Aktivierung des netzwerkweiten Modus standortweite Mitgliedschaften erstellt hast)<br />Hinweis: Nach dieser Änderung wird das Plugin weiterhin netzwerkweit aktiviert. Gehe zu Netzwerkadministrator> Plugins und deaktiviere es, wenn Du nur bestimmte Seiten in Deinem Netzwerk schützen möchtest.', 'help', 'membership2' ); ?>
		</p>
		<?php else : ?>
		<p>
			<?php
			printf(
				_x( 'Der netzwerkweite Schutz ist eine Pro-Funktion. %sHier mehr über die Pro-Version%s!', 'help', 'membership2' ),
				'<a href="https://n3rds.work/shop/artikel/ps-mitgliedschaften-pro/" target="_blank">',
				'</a>'
			);
			?>
		</p>
		<?php endif; ?>
		<hr />
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the Advanced settings help contents
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function render_tab_advanced() {
		ob_start();
		?>
		<h2><?php _ex( 'Erweiterte Einstellungen', 'help', 'membership2' ); ?></h2>
		<p>
			<strong><?php _ex( 'Zurücksetzen', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Öffne die Seite Einstellungen und füge der URL <code>&reset=1</code> hinzu. Es wird eine Eingabeaufforderung angezeigt, mit der alle PS Mitgliedschaften-Einstellungen zurückgesetzt werden können. Verwende diese Option, um alle Spuren nach dem Testen des Plugins zu bereinigen.', 'help', 'membership2' ); ?>
		</p>
        <p>
            <strong><?php _ex( 'Abonnements reparieren', 'help', 'membership2' ); ?></strong><br />
            <?php _ex( 'Öffne die Seite Einstellungen und füge der URL <code>&fixsub=1</code> hinzu. Es wird eine Eingabeaufforderung angezeigt, mit der PS Mitgliedschaften-Abonnements repariert werden können. Verwende diese Option, um Abonnements zu reparieren, die nicht mit Stripe synchron sind.', 'help', 'membership2' ); ?>
        </p>
		<p>
			<strong><?php _ex( 'E-Mails stoppen', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'In der wp-config.php die Zeile <code>define( "MS_STOP_EMAILS", true );</code> hinzufügen um geschützten Inhalt zu zwingen, <em>keine</me> Emails an Mitglieder zu senden. Dies kann beim Testen verwendet werden, um zu verhindern, dass Deine Benutzer Email-Benachrichtigungen erhalten.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Emails reduzieren', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Standardmäßig erhalten Mitglieder für jedes behandelte Ereignis eine Email (siehe Abschnitt "Einstellungen> Automatisierte Email-Antworten"). Du kannst jedoch die an Deine Benutzer gesendeten E-Mails reduzieren, indem Du die folgende Zeile zur wp-config.php hinzufügst <code>define( "MS_DUPLICATE_EMAIL_HOURS", 24 );</code>. Dadurch wird verhindert, dass dieselbe Email mehr als einmal alle 24 Stunden gesendet wird.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Abonnementstatus sperren', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Füge der wp-config.php die Zeile <code>define( "MS_LOCK_SUBSCRIPTIONS", true );</code> hinzu um die automatische Statusprüfung von Abonnements zu deaktivieren. Eine Registrierung ist weiterhin möglich, danach ändert sich der Abonnementstatus nicht mehr. Abonnements verfallen nicht mehr.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Keine Admin-Shortcode-Vorschau', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Standardmäßig werden dem Benutzer zusätzliche Informationen auf der Seite angezeigt, wenn sie den Shortcode <code>[ms-protect-content]</code> verwendet. Um diese zusätzliche Ausgabe zu deaktivieren, fügen die Zeile <code>define( "MS_NO_SHORTCODE_PREVIEW", true );</code> in der wp-config.php hinzu.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Definiere Admin-Benutzer für PS Mitgliedschaften', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Standardmäßig gelten alle Benutzer mit der Funktion <code>manage_options</code> als Administratorbenutzer von PS Mitgliedschaften und haben uneingeschränkten Zugriff auf die gesamte Webseite (einschließlich geschützter Inhalte). Um die erforderliche Funktion zu ändern, füge die Zeile <code>define( "MS_ADMIN_CAPABILITY", "manage_options" );</code> in der wp-config.php hinzu. Wenn Du den Wert auf <code>false</code> setzt, hat nur der Superadmin vollen Zugriff auf die Seite.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Debuggen eines falschen Seitenzugriffs', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'In PS Mitgliedschaften ist ein kleines Debugging-Tool integriert, mit dem Du Zugriffsprobleme für den aktuellen Benutzer analysieren kannst. Um dieses Tool verwenden zu können, musst Du auf Deiner Seite <code>define( "WP_DEBUG", true );</code> festlegen. Öffne als Nächstes die Seite, die Du analysieren möchtest, und fügen <code>?explain=access</code> zur Seiten-URL hinzu. Infolgedessen siehst Du nicht den normalen Seiteninhalt, sondern viele nützliche Details zu den Zugriffsberechtigungen.', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Führe ein Protokoll aller ausgehenden Emails', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Wenn Du alle Emails verfolgen möchtest, die PS Mitgliedschaften an Deine Mitglieder sendet, füge Deiner wp-config.php die Zeile <code>define( "MS_LOG_EMAILS", true );</code> hinzu. Ein neuer Navigationslink wird hier auf der Hilfeseite angezeigt, um den Email-Verlauf zu überprüfen.', 'help', 'membership2' ); ?>
		</p>
                <p>
			<strong><?php _ex( 'Erstelle ein Testabonnement, wenn Du Paypal verwendest', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Wenn Du während des Testzeitraums ein Abonnement erstellen möchtest, verwende <code>define( "MS_PAYPAL_TRIAL_SUBSCRIPTION", true );</code> in der Datei wp-config.php. Bitte beachte, dass dies nur funktioniert, wenn Du Paypal verwendest.', 'help', 'membership2' ); ?>
		</p>
        <p>
			<strong><?php _ex( 'Deaktiviere die Standard-Email-Adresse bei der Registrierung', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( 'Verwende <code>define( "MS_DISABLE_WP_NEW_USER_NOTIFICATION", true );</code> in der Datei wp-config.php, um die WP-Standard-Email bei der Registrierung im Back-End zu deaktivieren.', 'help', 'membership2' ); ?>
		</p>
		<hr />
		<?php
		return ob_get_clean();
	}

	/**
	 * Renders the Customize Mitgliedschaft help contents
	 *
	 * @since  1.0.1.2
	 * @return string
	 */
	public function render_tab_branding() {
		ob_start();
		?>
		<h2><?php _ex( 'Vorlagenhierarchie', 'help', 'membership2' ); ?></h2>
		<p>
			<?php
			printf(
				_x( 'Standardmäßig rendert PS Mitgliedschaften den in Deinen %sPS Mitgliedschaften-Seiten%s definierten Seiteninhalt unter Verwendung der Standardvorlage für einzelne Seiten. Du kannst dies jedoch sehr einfach anpassen, indem Du spezielle %sTemplate-Dateien%s im Design erstellst.', 'help', 'membership2' ),
				'<a href="' . MS_Controller_Plugin::get_admin_url( 'settings' ) . '">',
				'</a>',
				'<a href="https://developer.wordpress.org/themes/basics/template-files/" target="_blank">',
				'</a>'
			);
			?>
		</p>
		<p>
			<strong><?php _ex( 'Kontoseite', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( '1. <tt>m2-account.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '2. Standard single-page Vorlage', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Seite mit der Mitgliedschaftenliste', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( '1. <tt>m2-memberships-100.php</tt> (Nicht die Liste, sondern nur die Kasse für Mitgliedschaft 100)', 'help', 'membership2' ); ?><br />
			<?php _ex( '2. <tt>m2-memberships.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '3. Standard single-page Vorlage', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Registrierungsseite', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( '1. <tt>m2-register-100.php</tt> (Nicht die Liste, sondern nur die Kasse für Mitgliedschaft 100)', 'help', 'membership2' ); ?><br />
			<?php _ex( '2. <tt>m2-register.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '3. Standard single-page Vorlage', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Danke Seite', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( '1. <tt>m2-registration-complete-100.php</tt> (Nach dem Abonnieren der Mitgliedschaft 100)', 'help', 'membership2' ); ?><br />
			<?php _ex( '2. <tt>m2-registration-complete.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '3. Standard single-page Vorlage', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Geschützte Inhaltsseite', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( '1. <tt>m2-protected-content-100.php</tt> (Die Seite ist durch die Mitgliedschaft 100 geschützt)', 'help', 'membership2' ); ?><br />
			<?php _ex( '2. <tt>m2-protected-content.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '3. Standard single-page Vorlage', 'help', 'membership2' ); ?>
		</p>
		<p>
			<strong><?php _ex( 'Rechnungslayout', 'help', 'membership2' ); ?></strong><br />
			<?php _ex( '1. <tt>m2-invoice-100.php</tt> (Wird von allen Rechnungen für die Mitgliedschaft 100 verwendet)', 'help', 'membership2' ); ?><br />
			<?php _ex( '2. <tt>m2-invoice.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '3. <tt>single-ms_invoice.php</tt>', 'help', 'membership2' ); ?><br />
			<?php _ex( '4. Standardrechnungsvorlage für PS Mitgliedschaften', 'help', 'membership2' ); ?>
		</p>
		<hr />
		<?php
		return ob_get_clean();
	}

	/**
	 * Render the email history list.
	 *
	 * @since  1.0.2.7
	 * @return string
	 */
	public function render_tab_emails() {
		$listview = MS_Factory::create( 'MS_Helper_ListTable_CommunicationLog' );
		$listview->prepare_items();

		ob_start();
		?>
		<div class="wrap ms-wrap ms-communicationlog">
			<?php
			$listview->views();
			?>
			<form action="" method="post">
				<?php $listview->display(); ?>
			</form>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}



	/**
	 * Render the rest api documentation
	 *
	 * @since  1.1.3
	 * @return string
	 */
	public function render_tab_restapi() {
		ob_start();
		?>
		<h2><?php _ex( 'Rest API-Dokumentation', 'help', 'membership2' ); ?></h2>
		<p>
			<strong><u><?php _ex( 'Gültige API-Anfragen', 'help', 'membership2' ); ?></u></strong><br/>
			<?php _ex( sprintf( __( 'Alle API-Anforderungen müssen den Anforderungsparameter %s enthalten, der in den Add-On-Optionen konfiguriert ist. Zum Beispiel %s', 'membership2' ), '<strong>pass_key</strong>', '<strong><i>/wp-json/psmitgliedschaften/v1/members?pass_key=123456</i></strong>' ), 'help', 'membership2' ); ?>
			<br/>
			<u><?php _ex( 'Ungültige Anforderungsantwort', 'help', 'membership2' ); ?></u><br/>
			<code>
				{
					"code"		: "rest_user_cannot_view",
					"message"	: "Invalid request, you are not allowed to make this request",
					"data"		: {
						"status": 401
					}
				}
			</code>
		</p>
		<div id="member-route" class="ms-help-box">
			<h3 class="ms-help-toggle"><?php _ex( sprintf( __( 'Mitgliederroute %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/member</i></strong>' ), 'help', 'membership2' ); ?></h3>
			<div class="ms-help-details" style="display:none">
				<p>
					<strong><u><?php _ex( sprintf( __( '1. Mitglieder auflisten %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/member/list</i></strong>', '[GET]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<u><?php _ex( 'Parameter', 'help', 'membership2' ); ?></u>
					<ul>
						<li><?php _ex( sprintf( __( '- %s : Ergebnisse pro Seite. Der Standardwert ist 10 (optional)', 'membership2' ), 'per_page' ), 'help', 'membership2' ); ?></li>
						<li><?php _ex( sprintf( __( '- %s : Aktuelle Seite. Beginnt mit 1 (erforderlich)', 'membership2' ), 'page' ), 'help', 'membership2' ); ?></li>
						<li><?php _ex( sprintf( __( '- %s : Mitgliedschaftsstatus. ZB ausstehend, wartend, aktiv, Testversion, abgebrochen, Testversion abgelaufen, abgelaufen, deaktiviert (optional)', 'membership2' ), 'member_status' ), 'help', 'membership2' ); ?></li>
					</ul>
					<?php _ex( 'Antwort ist eine Liste von Mitgliedsobjekten', 'help', 'membership2' ); ?><br/><br/>

					<strong><u><?php _ex( sprintf( __( '2. Mitglieder zählen %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/member/count</i></strong>', '[GET]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<u><?php _ex( 'Parameter', 'help', 'membership2' ); ?></u>
					<ul>
						<li><?php _ex( sprintf( __( '- %s : Mitgliedschaftsstatus. ZB ausstehend, wartend, aktiv, Testversion, abgebrochen, Testversion abgelaufen, abgelaufen, deaktiviert (optional)', 'membership2' ), 'member_status' ), 'help', 'membership2' ); ?></li>
					</ul>
					<?php _ex( 'Response is the total members per status', 'help', 'membership2' ); ?><br/><br/>

					<strong><u><?php _ex( sprintf( __( '3. Mitglied werden %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/member/get</i></strong>', '[GET]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<u><?php _ex( 'Parameter', 'help', 'membership2' ); ?></u>
					<ul>
						<li><?php _ex( sprintf( __( '- %s : Die Benutzer-ID (erforderlich)', 'membership2' ), 'user_id' ), 'help', 'membership2' ); ?></li>
					</ul>
					<?php _ex( 'Die Antwort ist ein Mietglieder-Objekt', 'help', 'membership2' ); ?><br/><br/>

					<strong><u><?php _ex( sprintf( __( '4. Mitglied abonnieren %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/member/subscription</i></strong>', '[POST]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<u><?php _ex( 'Parameter', 'help', 'membership2' ); ?></u>
					<ul>
						<li><?php _ex( sprintf( __( '- %s : Die Mitgliedschafts-ID (erforderlich)', 'membership2' ), 'membership_id' ), 'help', 'membership2' ); ?></li>
						<li><?php _ex( sprintf( __( '- %s : Die Benutzer-ID (erforderlich', 'membership2' ), 'user_id' ), 'help', 'membership2' ); ?></li>
					</ul>
					<?php _ex( 'Antwort ist ein Abonnementobjekt', 'help', 'membership2' ); ?><br/><br/>

					<strong><u><?php _ex( sprintf( __( '5. Kontrolliere ob Benutzer Mitglied der Mitgliedschaft ist %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/member/subscription</i></strong>', '[GET]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<u><?php _ex( 'Parameter', 'help', 'membership2' ); ?></u>
					<ul>
						<li><?php _ex( sprintf( __( '- %s : Die Mitgliedschafts-ID (erforderlich)', 'membership2' ), 'membership_id' ), 'help', 'membership2' ); ?></li>
						<li><?php _ex( sprintf( __( '- %s : Die Benutzer-ID (erforderlich)', 'membership2' ), 'user_id' ), 'help', 'membership2' ); ?></li>
					</ul>
					<?php _ex( 'Antwort ist ein Abonnementobjekt', 'help', 'membership2' ); ?>

				</p>
			</div>
		</div>
		<div id="membership-route" class="ms-help-box">
			<h3 class="ms-help-toggle"><?php _ex( sprintf( __( 'Mitgliedschaftsroute %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/membership</i></strong>' ), 'help', 'membership2' ); ?></h3>
			<div class="ms-help-details" style="display:none">
				<p>
					<strong><u><?php _ex( sprintf( __( '1. Liste Mitgliedschaften auf %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/membership/list</i></strong>', '[GET]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<?php _ex( 'Antwort ist eine Liste von Mitgliedschaftsobjekten', 'help', 'membership2' ); ?><br/><br/>

					<strong><u><?php _ex( sprintf( __( '2. Mitgliedschaft erhalten %s %s' ), '<strong><i>/wp-json/psmitgliedschaften/v1/membership/get</i></strong>', '[GET]' ), 'help', 'membership2' ); ?></u></strong><br/>
					<ul>
						<li><?php _ex( sprintf( __( '- %s : Die Mitglieds-ID oder der Name oder Slug (erforderlich)', 'membership2' ), 'param' ), 'help', 'membership2' ); ?></li>
					</ul>
					<?php _ex( 'Antwort ist ein Mitgliedschaftsobjekt', 'help', 'membership2' ); ?><br/><br/>
				</p>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

}