<?php

class MS_View_Settings_Page_General extends MS_View_Settings_Edit {

	public function to_html() {
		$settings = $this->data['settings'];

		$fields = array(
			'plugin_enabled' => array(
				'id' 		=> 'plugin_enabled',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' 	=> __( 'Inhaltsschutz', 'membership2' ),
				'desc' 		=> __( 'Diese Einstellung schaltet den Inhaltsschutz auf dieser Webseite um.', 'membership2' ),
				'value' 	=> MS_Plugin::is_enabled(),
				'data_ms' 	=> array(
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_SETTINGS,
					'setting' 	=> 'plugin_enabled',
				),
			),

			'hide_admin_bar' => array(
				'id' 		=> 'hide_admin_bar',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' 	=> __( 'Admin-Symbolleiste ausblenden', 'membership2' ),
				'desc' 		=> __( 'Blende die Admin-Symbolleiste für Benutzer ohne Administratorrechte aus.', 'membership2' ),
				'value' 	=> $settings->hide_admin_bar,
				'data_ms' 	=> array(
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_SETTINGS,
					'setting' 	=> 'hide_admin_bar',
				),
			),

			'enable_cron_use' => array(
				'id' 		=> 'enable_cron_use',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' 	=> __( 'Verwende System-Cron zum Senden von E-Mails', 'membership2' ),
				'desc' 		=> __( 'Verarbeite Kommunikations-E-Mails stündlich im Hintergrund. Gut für Webseiten mit viel Verkehr', 'membership2' ),
				'value' 	=> $settings->enable_cron_use,
				'data_ms' 	=> array(
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_SETTINGS,
					'setting' 	=> 'enable_cron_use',
				),
			),

			'enable_query_cache' => array(
				'id' 		=> 'enable_query_cache',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' 	=> __( 'Ergebnisse beschleunigen', 'membership2' ),
				'desc' 		=> __( 'Zwischenspeichere Deine Mitgliedschaftsabfragen, um schnellere Ergebnisse zu erzielen. Wenn Du dies aktivierst, werden die Ergebnisse 12 Stunden lang zwischengespeichert. Gut für Webseiten mit vielen Daten', 'membership2' ),
				'value' 	=> $settings->enable_query_cache,
				'data_ms' 	=> array(
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_SETTINGS,
					'setting' 	=> 'enable_query_cache',
				),
			),

			'force_single_gateway' => array(
				'id' 		=> 'force_single_gateway',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' 	=> __( 'Standard-Gateway erzwingen', 'membership2' ),
				'desc' 		=> __( 'Dadurch werden alle manuell registrierten Mitglieder gezwungen, das standardmäßige einzelne aktive Zahlungsgateway zu verwenden', 'membership2' ),
				'value' 	=> $settings->force_single_gateway,
				'data_ms' 	=> array(
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_SETTINGS,
					'setting' 	=> 'force_single_gateway',
				),
			),

			'force_registration_verification' => array(
				'id' 		=> 'force_registration_verification',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'title' 	=> __( 'Aktiviere die Kontoüberprüfung', 'membership2' ),
				'desc' 		=> __( 'Dadurch werden alle registrierten Konten gezwungen, ihre E-Mails zuerst zu bestätigen, bevor sie sich anmelden', 'membership2' ),
				'value' 	=> $settings->force_registration_verification,
				'data_ms' 	=> array(
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_SETTINGS,
					'setting' 	=> 'force_registration_verification',
				),
			),
			
		);

		$fields = apply_filters( 'ms_view_settings_prepare_general_fields', $fields );
		$setup = MS_Factory::create( 'MS_View_Settings_Page_Setup' );
		$action_url = esc_url_raw( remove_query_arg( array( 'msg' ) ) );

		ob_start();

		MS_Helper_Html::settings_tab_header();
		?>

		<form action="<?php echo esc_url( $action_url ); ?>" method="post" class="cf">
			<div class="cf">
				<div class="ms-half">
					<?php MS_Helper_Html::html_element( $fields['plugin_enabled'] ); ?>
				</div>
				<div class="ms-half">
					<?php MS_Helper_Html::html_element( $fields['hide_admin_bar'] ); ?>
				</div>
			</div>
			<?php
			MS_Helper_Html::html_separator();
			?>
			<div class="cf">
				<div class="ms-half">
					<?php MS_Helper_Html::html_element( $fields['enable_cron_use'] ); ?>
				</div>
				<div class="ms-half">
					<?php MS_Helper_Html::html_element( $fields['enable_query_cache'] ); ?>
				</div>
			</div>
			<?php
			MS_Helper_Html::html_separator();
			?>
			<div class="cf">
				<div class="ms-half">
					<?php MS_Helper_Html::html_element( $fields['force_single_gateway'] ); ?>
				</div>
				<div class="ms-half">
					<?php MS_Helper_Html::html_element( $fields['force_registration_verification'] ); ?>
				</div>
			</div>
			<?php
			MS_Helper_Html::html_separator();
			MS_Helper_Html::html_element( $setup->html_full_form() );
			?>
		</form>
		<?php
		return ob_get_clean();
	}

}