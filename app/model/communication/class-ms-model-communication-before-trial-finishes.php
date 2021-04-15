<?php
/**
 * Communication model - before trial finishes.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Before_Trial_Finishes extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_BEFORE_TRIAL_FINISHES;

	/**
	 * Populates the field title/description of the Period before/after field
	 * in the admin settings.
	 *
	 * @since  1.0.0
	 * @param array $field A HTML definition, passed to mslib3()->html->element()
	 */
	public function set_period_name( $field ) {
		$field['title'] = __( 'Kündigungsfrist', 'membership2' );
		$field['desc'] = __( 'Lege fest, wie viele Tage im Voraus der Benutzer benachrichtigt werden soll.', 'membership2' );

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
			'Eine vordefinierte Anzahl von Tagen vor Ablauf der Testphase gesendet. Du musst entscheiden, wie viele Tage im Voraus eine Nachricht gesendet werden soll.', 'membership2'
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
			__( 'Die Testversion Deiner %s-Mitgliedschaft endet bald', 'membership2' ),
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
			__( 'Dies ist eine Erinnerung daran, dass Deine %1$s Testmitgliedschaft bei %2$s mit %3$s endet.', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME,
			self::COMM_VAR_MS_REMAINING_TRIAL_DAYS
		);
		$body_renew = sprintf(
			'Du kannst Deine Mitgliedschaftsdaten hier erneuern und bearbeiten: %1$s',
			self::COMM_VAR_MS_ACCOUNT_PAGE_URL
		);

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s',
			$subject,
			$body_notice,
			$body_renew
		);

		return apply_filters(
			'ms_model_communication_before_trial_finishes_get_default_message',
			$html
		);
	}
}