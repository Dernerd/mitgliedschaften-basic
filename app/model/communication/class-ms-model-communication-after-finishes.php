<?php
/**
 * Communication model - after membership finishes.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_After_Finishes extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_AFTER_FINISHES;

	/**
	 * Populates the field title/description of the Period before/after field
	 * in the admin settings.
	 *
	 * @since  1.0.0
	 * @param array $field A HTML definition, passed to mslib3()->html->element()
	 */
	public function set_period_name( $field ) {
		$field['title'] = __( 'Nachrichtenverzögerung', 'membership2' );
		$field['desc'] 	= __( 'Verwende "0", um sofort zu senden, oder einen anderen Wert, um die Nachricht zu verzögern.', 'membership2' );

		return $field;
	}

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __(
			'Eine vordefinierte Anzahl von Tagen nach Beendigung der Mitgliedschaft gesendet. Du musst entscheiden, wie viele Tage nach dem Senden einer Nachricht.', 'membership2'
		);
	}

	/**
	 * Communication default communication.
	 *
	 * @since  1.0.0
	 */
	public function reset_to_default() {
		parent::reset_to_default();

		$this->subject 			= sprintf(
			__( 'Erinnerung: Deine %s-Mitgliedschaft ist beendet', 'membership2' ),
			self::COMM_VAR_MS_NAME
		);
		$this->message 			= self::get_default_message();
		$this->enabled 			= false;
		$this->period_enabled 	= true;

		do_action(
			'ms_model_communication_reset_to_default_after',
			$this->type,
			$this
		);
	}

	/**
	 * Get default email message.
	 *
	 * @since  1.0.0
	 * @return string The email message.
	 */
	public static function get_default_message() {
		$subject 		= sprintf(
			__( 'Hallo %1$s,', 'membership2' ),
			self::COMM_VAR_USERNAME
		);
		$body_notice 	= sprintf(
			__( 'Dies ist eine Erinnerung daran, dass Deine %1$s-Mitgliedschaft bei %2$s mit %3$s beendet wurde.', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME,
			self::COMM_VAR_MS_EXPIRY_DATE
		);
		$body_renew 	= sprintf(
			__( 'Hier kannst Du Deine Mitgliedschaft verlängern: %1$s', 'membership2' ),
			self::COMM_VAR_MS_ACCOUNT_PAGE_URL
		);

		$html 			= sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s',
			$subject,
			$body_notice,
			$body_renew
		);

		return apply_filters(
			'ms_model_communication_after_finished_get_default_message',
			$html
		);
	}
}