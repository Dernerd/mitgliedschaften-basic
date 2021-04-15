<?php
/**
 * Communication model - membership cancelled.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Cancelled extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_CANCELLED;

	/**
	 * Populates the field title/description of the Period before/after field
	 * in the admin settings.
	 *
	 * @since  1.0.0
	 * @param array $field A HTML definition, passed to mslib3()->html->element()
	 */
	public function set_period_name( $field ) {
		$field['title'] = __( 'Nachrichtenverzögerung', 'membership2' );
		$field['desc'] = __( 'Verwende "0", um sofort zu senden, oder einen anderen Wert, um die Nachricht zu verzögern.', 'membership2' );

		return $field;
	}

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __( 'Wird gesendet, wenn die Mitgliedschaft gekündigt wird.', 'membership2' );
	}

	/**
	 * Communication default communication.
	 *
	 * @since  1.0.0
	 */
	public function reset_to_default() {
		parent::reset_to_default();

		$this->subject = sprintf(
			__( 'Deine %s-Mitgliedschaft wurde gekündigt', 'membership2' ),
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
			__( 'Deine %1$s-Mitgliedschaft bei %2$s wurde erfolgreich gekündigt.', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME
		);
		$body_register = sprintf(
			__( 'Sollte sich Deine Meinung ändern, kannst Du Deinee Mitgliedschaft hier erneuern: %1$s', 'membership2' ),
			self::COMM_VAR_MS_ACCOUNT_PAGE_URL
		);
		$body_payments = __( 'Hier sind Deine neuesten Zahlungsdetails:', 'membership2' );

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s<br /><br />%4$s<br /><br />%5$s',
			$subject,
			$body_notice,
			$body_register,
			$body_payments,
			self::COMM_VAR_MS_INVOICE
		);

		return apply_filters(
			'ms_model_communication_cancelled_get_default_message',
			$html
		);
	}
}