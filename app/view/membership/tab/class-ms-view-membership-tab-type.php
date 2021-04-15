<?php

class MS_View_Membership_Tab_Type extends MS_View {

	/**
	 * Create view output.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function to_html() {
		$fields = $this->prepare_fields();

		ob_start();
		?>
		<div class="ms-membership-add ms-cols-2">
			<form method="post" id="ms-choose-type-form">
				<div class="ms-settings-row cf">
					<h3><?php _e( 'Lege den Mitgliedschaftstyp fest:', 'membership2' ); ?></h3>
					<?php MS_Helper_Html::html_element( $fields['type'] ); ?>
				</div>
				<div>
					<?php
					foreach ( $fields['control_fields'] as $field ) {
						MS_Helper_Html::html_element( $field );
					}
					?>
				</div>
			</form>
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
		$action = 'save';

		$fields = array(
			'type' => array(
				'id' => 'type',
				'type' => MS_Helper_Html::INPUT_TYPE_RADIO,
				'value' => $membership->type,
				'class' => 'ms-choose-type',
				'after' => '<div class="ms-italic ms-block">' . __( 'Das Ändern dieser Option kann zum Verlust oder zu Änderungen Deiner Schutzregeln für diese Mitgliedschaft führen.<br>Bitte überprüfe Deine Regeln, nachdem Du die Änderungen hier gespeichert hast.', 'membership2' ) . '</div>',
				'field_options' => array(
					MS_Model_Membership::TYPE_STANDARD => array(
						'text' => __( 'Standardmitgliedschaft', 'membership2' ),
						'desc' => __( 'Stelle Deine Inhalte Mitgliedern zur Verfügung und verstecke sie vor Gästen (abgemeldeten Benutzern).', 'membership2' ),
					),
					MS_Model_Membership::TYPE_DRIPPED => array(
						'text' => __( 'Versetzter Inhalt-Mitgliedschaft.', 'membership2' ),
						'desc' => __( 'Richte den Inhalt der Mitgliedschaft ein, der in Intervallen veröffentlicht/verfügbar gemacht werden soll.', 'membership2' ),
					),
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
					'value' => MS_Controller_Membership::STEP_EDIT,
				),
				'tab' => array(
					'id' => 'tab',
					'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
					'value' => MS_Controller_Membership::TAB_TYPE,
				),
				'action' => array(
					'id' => 'action',
					'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
					'value' => $action,
				),
				'_wpnonce' => array(
					'id' => '_wpnonce',
					'type' => MS_Helper_Html::INPUT_TYPE_HIDDEN,
					'value' => wp_create_nonce( $action ),
				),
				'save' => array(
					'id' => 'save',
					'type' => MS_Helper_Html::INPUT_TYPE_SUBMIT,
					'value' => __( 'Speichern', 'membership2' ),
				),
			),
		);

		return $fields;
	}
}
