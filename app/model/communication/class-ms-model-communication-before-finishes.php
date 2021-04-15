<?php
/**
 * Communication model - before finishes.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Before_Finishes extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_BEFORE_FINISHES;

	/**
	 * Populates the field title/description of the Period before/after field
	 * in the admin settings.
	 *
	 * @since  1.0.0
	 * @param array $field A HTML definition, passed to mslib3()->html->element()
	 */
	public function set_period_name( $field ) {
		$field['title'] = __( 'Kündigungsfrist', 'membership2' );
		$field['desc'] 	= __( 'Lege fest, wie viele Tage im Voraus der Benutzer benachrichtigt werden soll.', 'membership2' );

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
			'Eine vordefinierte Anzahl von Tagen gesendet, bevor die Mitgliedschaft endet. Du musst entscheiden, wie viele Tage im Voraus eine Nachricht gesendet werden soll.', 'membership2'
		);
	}

	/**
	 * Communication default communication.
	 *
	 * @since  1.0.0
	 */
	public function reset_to_default() {
		parent::reset_to_default();

		$this->subject = sprintf(
			__( 'Deine %s-Mitgliedschaft endet bald', 'membership2' ),
			self::COMM_VAR_MS_NAME
		);
		$this->message = self::get_default_message();
		$this->enabled = false;
		$this->period_enabled = true;

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
		$subject = sprintf(
			__( 'Hallo %1$s,', 'membership2' ),
			self::COMM_VAR_USERNAME
		);
		$body_notice = sprintf(
			__( 'Dies ist nur eine Erinnerung daran, dass Deine %1$s-Mitgliedschaft bei %2$s mit %3$s endet.', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME,
			self::COMM_VAR_MS_REMAINING_DAYS
		);

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s',
			$subject,
			$body_notice
		);

		return apply_filters(
			'ms_model_communication_before_finishes_get_default_message',
			$html
		);
	}
}