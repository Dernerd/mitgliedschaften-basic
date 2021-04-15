<?php

class MS_View_Membership_Add extends MS_View {

	/**
	 * Create view output.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function to_html() {
		$fields = $this->prepare_fields();
		$cols = count( $fields['type']['field_options'] );
		if ( $cols < 2 ) { $cols = 2; }
		if ( $cols > 3 ) { $cols = 2; }

		ob_start();
		?>
		<div class="ms-wrap">
			<?php
			MS_Helper_Html::settings_header(
				array(
					'title' => __( 'Neue Mitgliedschaft erstellen', 'membership2' ),
					'desc' => __( 'Wähle zunächst einen Namen und einen Typ für Deine Mitgliedschaft.', 'membership2' ),
				)
			);
			?>
			<div class="ms-settings ms-membership-add ms-cols-<?php echo esc_attr( $cols ); ?>">
				<form method="post" id="ms-choose-type-form">
					<div class="ms-settings-row cf">
						<h3><?php _e( 'Wähle einen Mitgliedschaftstyp:', 'membership2' ); ?></h3>
						<?php MS_Helper_Html::html_element( $fields['type'] ); ?>
					</div>
					<div class="ms-settings-row cf">
						<?php MS_Helper_Html::html_element( $fields['name'] ); ?>
					</div>
					<div class="ms-settings-row cf">
						<div class="ms-options-wrapper">
							<?php
							foreach ( $fields['config_fields'] as $field ) {
								echo '<span class="opt">';
								MS_Helper_Html::html_element( $field );
								echo '</span>';
							}
							?>
						</div>
					</div>
					<div class="ms-control-fields-wrapper">
						<?php
						foreach ( $fields['control_fields'] as $field ) {
							MS_Helper_Html::html_element( $field );
						}
						?>
					</div>
				</form>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	/**
	 * Prepare the fields displayed in the form.
	 *
	 * @since  1.0.0
	 * @return array
	 */
	public function prepare_fields() {
		$membership = $this->data['membership'];

		$fields = array(
			'type' => array(
				'id' => 'type',
				'type' => MS_Helper_Html::INPUT_TYPE_RADIO,
				'value' => ( $membership->type ) ? $membership->type : MS_Model_Membership::TYPE_STANDARD,
				'class' => 'ms-choose-type',
				'field_options' => array(
					MS_Model_Membership::TYPE_STANDARD => array(
						'text' => __( 'Standardmitgliedschaft', 'membership2' ),
						'desc' => __( 'Stelle Deine Inhalte Mitgliedern zur Verfügung und verstecke sie vor Gästen (abgemeldeten Benutzern).', 'membership2' ),
					),
					MS_Model_Membership::TYPE_DRIPPED => array(
						'text' => __( 'Interval Inhalt-Mitgliedschaft.', 'membership2' ),
						'desc' => __( 'Richte den Inhalt der Mitgliedschaft ein, der in Intervallen veröffentlicht/verfügbar gemacht werden soll.', 'membership2' ),
					),
					MS_Model_Membership::TYPE_GUEST => array(
						'text' => __( 'Gastmitgliedschaft', 'membership2' ),
						'desc' => __( 'Stelle Deine Inhalte nur Gästen (abgemeldeten Benutzern) zur Verfügung.', 'membership2' ),
					),
					MS_Model_Membership::TYPE_USER => array(
						'text' => __( 'Standardmitgliedschaft', 'membership2' ),
						'desc' => __( 'Der Inhalt steht allen angemeldeten Benutzern zur Verfügung, die noch keiner anderen Mitgliedschaft beigetreten sind.', 'membership2' ),
					),
				),
			),

			'name' => array(
				'id' => 'name',
				'type' => MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' => __( 'Benenne Deine Mitgliedschaft:', 'membership2' ),
				'value' => $membership->name,
				'class' => 'ms-text-large',
				'placeholder' => __( 'Wähle einen Namen, der diese Mitgliedschaft identifiziert...', 'membership2' ),
				'label_type' => 'h3',
				'wrapper_class' => 'opt',
				'after' => sprintf(
					'<span class="locked-info">%1$s</span>',
					__( 'Für diese Mitgliedschaft nicht verfügbar', 'membership2' )
				),
			),

			'config_fields' => array(
				'public' => array(
					'id' 	=> 'public',
					'type' 	=> MS_Helper_Html::INPUT_TYPE_CHECKBOX,
					'title' => __( 'Benutzern erlauben, sich für diese Mitgliedschaft zu registrieren.', 'membership2' ),
					'desc' 	=> __( 'Wenn diese Option ausgewählt ist, wird die Registrierungserfahrung zu Deiner Webseite hinzugefügt. Kreuze nicht an, wenn Du dies zu einer privaten Mitgliedschaft machen möchtest.', 'membership2' ),
					'after' => sprintf(
						'<span class="locked-info">%1$s</span>',
						__( 'Für diese Mitgliedschaft nicht verfügbar', 'membership2' )
					),
					'value' => ! $membership->private,
				),
				'public_flag' => array(
					// See MS_Controller_Membership->process_admin_page()
					'id' => 'set_public_flag',
					'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
					'value' => 1,
				),
				'paid' => array(
					'id' => 'paid',
					'type' => MS_Helper_Html::INPUT_TYPE_CHECKBOX,
					'title' => __( 'Dies ist eine bezahlte Mitgliedschaft.', 'membership2' ),
					'desc' => __( 'Wähle diese Option, wenn Du Zahlungen von Mitgliedern über Zahlungsgateways erhalten möchtest.', 'membership2' ),
					'after' => sprintf(
						'<span class="locked-info">%1$s</span>',
						__( 'Für diese Mitgliedschaft nicht verfügbar', 'membership2' )
					),
					'value' => ! $membership->is_free(),
				),
				'paid_flag' => array(
					// See MS_Controller_Membership->process_admin_page()
					'id' => 'set_paid_flag',
					'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
					'value' => 1,
				),
			),

			'control_fields' => array(
					'membership_id' => array(
						'id' => 'membership_id',
						'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
						'value' => $membership->id,
					),
					'step' => array(
						'id' => 'step',
						'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
						'value' => $this->data['step'],
					),
					'action' => array(
						'id' => 'action',
						'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
						'value' => $this->data['action'],
					),
					'_wpnonce' => array(
						'id' => '_wpnonce',
						'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
						'value' => wp_create_nonce( $this->data['action'] ),
					),
					'cancel' => array(
						'id' => 'cancel',
						'type' => MS_Helper_Html::INPUT_TYPE_BUTTON,
						'value' => __( 'Abbrechen', 'membership2' ),
						'data_ms' => array(
							'action' => MS_Controller_Settings::AJAX_ACTION_UPDATE_SETTING,
							'field' => 'initial_setup',
							'value' => '0',
						)
					),
					'save' => array(
						'id' => 'save',
						'type' => MS_Helper_Html::INPUT_TYPE_SUBMIT,
						'value' => __( 'Speichern und fortfahren', 'membership2' ) . ' &raquo;',
					),
			),
		);

		// Only one Guest Membership can be added
		if ( MS_Model_Membership::get_guest()->is_valid() ) {
			unset( $fields['type']['field_options'][MS_Model_Membership::TYPE_GUEST] );
		}

		// Only one User Membership can be added
		if ( MS_Model_Membership::get_user()->is_valid() ) {
			unset( $fields['type']['field_options'][MS_Model_Membership::TYPE_USER] );
		}

		// Wizard can only be cancelled when at least one membership exists in DB.
		$count = MS_Model_Membership::get_membership_count();
		if ( ! $count ) {
			unset( $fields['control_fields']['cancel'] );
		}

		return $fields;
	}
}
