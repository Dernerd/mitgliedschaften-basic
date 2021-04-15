<?php
/**
 * Communication model -  credit card expire.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Credit_Card_Expire extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_CREDIT_CARD_EXPIRE;

	/**
	 * Populates the field title/description of the Period before/after field
	 * in the admin settings.
	 *
	 * @since  1.0.0
	 * @param array $field A HTML definition, passed to mslib3()->html->element()
	 */
	public function set_period_name( $field ) {
		$field['title'] = __( 'Benachrichtigungszeitraum', 'membership2' );
		$field['desc'] = __( 'Wir möchten den Benutzer einige Tage im Voraus benachrichtigen, damit Zeit zum Reagieren bleibt.<br>Gib hier ein, wie viele Tage im Voraus diese Nachricht gesendet werden soll.', 'membership2' );

		return $field;
	}

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __( 'Ein Hinweis darauf, dass die Kreditkarte des Mitglieds bald abläuft.', 'membership2' );
	}

	/**
	 * Communication default communication.
	 *
	 * @since  1.0.0
	 */
	public function reset_to_default() {
		parent::reset_to_default();

		$this->subject = __( 'Deine Kreditkarte läuft bald ab', 'membership2' );
		$this->message = self::get_default_message();
		$this->enabled = false;
		$this->period_enabled = true;

		do_action( 'ms_model_communication_reset_to_default_after', $this->type, $this );
	}

	/**
	 * Get default email message.
	 *
	 * @since  1.0.0
	 * @return string The email message.
	 */
	public static function get_default_message() {
		$subject = sprintf(
			__( 'Hallo %1$s,', 'membership2' ),
			self::COMM_VAR_USERNAME
		);
		$body_notice = __( 'Dies ist eine Erinnerung daran, dass Deine Kreditkarte bald abläuft.', 'membership2' );
		$body_continue = sprintf(
			__( 'Um Deine Mitgliedschaft bei %1$s bei %2$s fortzusetzen, aktualisiere bitte Deine Kartendaten, bevor Deine nächste Zahlung hier fällig wird: %3$s', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME,
			self::COMM_VAR_MS_ACCOUNT_PAGE_URL
		);

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s',
			$subject,
			$body_notice,
			$body_continue
		);

		return apply_filters(
			'ms_model_communication_credit_card_expire_get_default_message',
			$html
		);
	}
}