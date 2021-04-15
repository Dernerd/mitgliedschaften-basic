<?php

class MS_Gateway_Authorize_View_Settings extends MS_View {

	public function to_html() {
		$fields = $this->prepare_fields();
		$gateway = $this->data['model'];

		ob_start();
		// Render tabbed interface.
		?>
		<form class="ms-gateway-settings-form ms-form">
			<?php
			MS_Helper_Html::settings_box_header( '', '' );
			foreach ( $fields as $field ) {
				MS_Helper_Html::html_element( $field );
			}
			MS_Helper_Html::settings_box_footer();
			?>
		</form>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	protected function prepare_fields() {
		$gateway = $this->data['model'];
		$action = MS_Controller_Gateway::AJAX_ACTION_UPDATE_GATEWAY;
		$nonce = wp_create_nonce( $action );

		$fields = array(
			'mode' => array(
				'id' 			=> 'mode',
				'title' 		=> __( 'Modus', 'membership2' ),
				'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'value' 		=> $gateway->mode,
				'field_options' => $gateway->get_mode_types(),
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array( 1 ),
			),

			'api_login_id' => array(
				'id' 		=> 'api_login_id',
				'title' 	=> __( 'API Login ID', 'membership2' ),
				'type' 		=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'value' 	=> $gateway->api_login_id,
				'class' 	=> 'ms-text-large',
				'ajax_data' => array( 1 ),
			),

			'api_transaction_key' => array(
				'id' 		=> 'api_transaction_key',
				'title' 	=> __( 'API-Transaktionsschlüssel', 'membership2' ),
				'type' 		=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'value' 	=> $gateway->api_transaction_key,
				'class' 	=> 'ms-text-large',
				'ajax_data' => array( 1 ),
			),

			'pay_button_url' => array(
				'id' 		=> 'pay_button_url',
				'title' 	=> apply_filters(
					'ms_translation_flag',
					__( 'Etikett oder URL der Zahlungsschaltfläche', 'membership2' ),
					'gateway-button' . $gateway->id
				),
				'type' 		=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'value' 	=> $gateway->pay_button_url,
				'class' 	=> 'ms-text-large',
				'ajax_data' => array( 1 ),
			),

			'secure_cc' => array(
				'id' 		=> 'secure_cc',
				'title' 	=> __( 'Sichere Zahlungen', 'membership2' ),
				'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
				'value' 	=> mslib3()->is_true( $gateway->secure_cc ),
				'ajax_data' => array( 1 ),
			),

			'secure_cc_off' => array(
				'id' 		=> 'secure_cc_off',
				'type' 		=> MS_Helper_Html::TYPE_HTML_TEXT,
				'value' 	=> __( 'Standardzahlungsoption: Kreditkartendaten müssen einmal eingegeben werden und können ohne erneute Eingabe der CVC-Nummer wiederverwendet werden.', 'membership2' ),
				'class' 	=> 'hidden secure_cc_off',
			),

			'secure_cc_on' => array(
				'id' 		=> 'secure_cc_on',
				'type' 		=> MS_Helper_Html::TYPE_HTML_TEXT,
				'value' 	=> __( 'Sichere Zahlungsoption: Der Benutzer muss für jede Transaktion die CVV-Nummer der Kreditkarte eingeben - auch für gespeicherte Kreditkarten und jede wiederkehrende Zahlung.', 'membership2' ),
				'class' 	=> 'hidden secure_cc_on',
			),
		);

		// Process the fields and add missing default attributes.
		foreach ( $fields as $key => $field ) {
			if ( ! empty( $field['ajax_data'] ) ) {
				$fields[ $key ]['ajax_data']['field'] 		= $fields[ $key ]['id'];
				$fields[ $key ]['ajax_data']['_wpnonce'] 	= $nonce;
				$fields[ $key ]['ajax_data']['action'] 		= $action;
				$fields[ $key ]['ajax_data']['gateway_id'] 	= $gateway->id;
			}
		}

		return $fields;
	}

}