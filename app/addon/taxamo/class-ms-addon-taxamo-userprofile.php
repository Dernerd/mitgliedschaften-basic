<?php

/**
 * The members tax settings editor
 */
class MS_Addon_Taxamo_Userprofile extends MS_View {

	public function to_html() {
		$fields = $this->prepare_fields();

		$classes = array();
		$classes[] = 'ms-tax-' . $fields['country_choice']['value'];

		ob_start();
		?>
		<div class="ms-wrap <?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<div class="modal-header">
				<button type="button" class="close">&times;</button>
				<h4 class="modal-title"><?php _e( 'Steuereinstellungen', 'membership2' ); ?></h4>
			</div>
			<div class="modal-body">

			<?php
			foreach ( $fields as $field ) {
				MS_Helper_Html::html_element( $field );
			}
			?>

			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default close"><?php _e( 'Schliessen', 'membership2' ); ?></button>
				<button type="button" class="btn btn-primary save"><?php _e( 'Speichern', 'membership2' ); ?></button>
			</div>
			<div class="loading-message">
				<?php _e( 'Daten speichern, bitte warten...', 'membership2' ); ?>
			</div>
		</div>
		<div class="body-messages">
			<div class="ms-tax-loading-overlay"></div>
			<div class="ms-tax-loading-message"><?php _e( 'Aktualisiere Seite, bitte warten...', 'membership2' ); ?></div>
		</div>
		<?php
		$html = ob_get_clean();

		return apply_filters(
			'ms_addon_taxamo_userprofile',
			$html
		);
	}

	public function prepare_fields() {
		$fields = array();
		$invoice_id = false;

		if ( isset( $this->data['invoice'] ) ) {
			$invoice 	= $this->data['invoice'];
			$invoice_id = $invoice->id;
		}

		$profile 	= MS_Addon_Taxamo_Api::get_tax_profile();
		$countries 	= MS_Addon_Taxamo_Api::get_country_codes();
		$action 	= MS_Addon_Taxamo::AJAX_SAVE_USERPROFILE;
		$nonce 		= wp_create_nonce( $action );

		$country_options = array(
			'auto' => sprintf(
				__( 'Das erkannte Land %s ist korrekt.', 'membership2' ),
				'<strong>' . $profile->detected_country->name . '</strong>'
			),
			'vat' => __( 'Ich habe eine EU-Umsatzsteuer-Identifikationsnummer und möchte diese für die Steuererklärung verwenden.', 'membership2' ),
			'declared' => __( 'Erkläre mein Wohnsitzland manuell.', 'membership2' ),
		);

		$vat_details = '';
		if ( ! empty( $profile->vat_number ) && $profile->vat_valid ) {
			$vat_details = sprintf(
				__( 'Dies ist eine gültige Umsatzsteuer-Identifikationsnummer von %s. Wenn Du dies verwendest, bist Du jetzt von der Mehrwertsteuer befreit.', 'membership2' ),
				'<strong>' . $profile->vat_country->name . '</strong>'
			);
		} else {
			$vat_details = __( 'Die Umsatzsteuer-Identifikationsnummer ist ungültig.', 'membership2' );
		}
		if ( $profile->use_vat_number ) {
			$tax_message = __( 'Gültige EU-Umsatzsteuer-Identifikationsnummer angegeben: Du bist von der Umsatzsteuer befreit', 'membership2' );
		} else {
			$tax_message = __( 'Das für die Steuerberechnung verwendete Land ist %s', 'membership2' );
		}

		$fields['tax_country_label'] = array(
			'type' => MS_Helper_Html::TYPE_HTML_TEXT,
			'title' => sprintf(
				$tax_message,
				'<strong>' . $profile->tax_country->name . '</strong>'
			),
			'wrapper_class' => 'effective_tax_country',
		);
		$fields['detected_country_label'] = array(
			'type' => MS_Helper_Html::TYPE_HTML_TEXT,
			'title' => sprintf(
				__( 'Wir haben festgestellt, dass sich Dein Computer in %s befindet', 'membership2' ),
				'<strong>' . $profile->detected_country->name . '</strong>'
			),
		);
		$fields['detected_country'] = array(
			'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
			'id' 	=> 'detected_country',
			'value' => $profile->detected_country->code,
		);
		$fields['country_choice'] = array(
			'type' 			=> MS_Helper_Html::INPUT_TYPE_RADIO,
			'id' 			=> 'country_choice',
			'class' 		=> 'country_choice',
			'value' 		=> $profile->country_choice,
			'field_options' => $country_options,
		);
		$fields['declared_country_code'] = array(
			'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
			'id' 			=> 'declared_country',
			'title' 		=> __( 'Mein Wohnsitzland', 'membership2' ),
			'desc' 			=> __( 'Ich bestätige, dass ich niedergelassen bin, meine ständige Adresse habe oder normalerweise im folgenden Land wohne', 'membership2' ),
			'value' 		=> $profile->declared_country->code,
			'field_options' => $countries,
			'wrapper_class' => 'manual_country_field',
		);
		$fields['vat_number'] = array(
			'type' 			=> MS_Helper_Html::INPUT_TYPE_TEXT,
			'id' 			=> 'vat_number',
			'title' 		=> __( 'EU-Steuernummer', 'membership2' ),
			'desc' 			=> __( 'Fülle dieses Feld aus, wenn Du den EU-Mehrwertsteuerzahler vertrittst', 'membership2' ),
			'wrapper_class' => 'vat_number_field',
			'value' 		=> $profile->vat_number,
			'valid_country' => $profile->vat_country->vat_valid,
			'after' 		=> $vat_details,
		);
		$fields['invoice_id'] = array(
			'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
			'id' 	=> 'invoice_id',
			'value' => $invoice_id,
		);
		$fields['action'] = array(
			'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
			'id' 	=> 'action',
			'value' => $action,
		);
		$fields['_wpnonce'] = array(
			'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
			'id' 	=> '_wpnonce',
			'value' => $nonce,
		);

		/*
		 * 1. Checkbox "I confirm that the country of my main residence is in <country>" (in the payment table!)
		 * 4. When VAT is entered the checkbox is disabled and VAT country is used. Checkbox 1 is hidden.
		 */

		return apply_filters(
			'ms_addon_taxamo_userprofile_fields',
			$fields
		);
	}
}