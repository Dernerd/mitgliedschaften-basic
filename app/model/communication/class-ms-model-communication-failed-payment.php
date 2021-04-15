<?php
/**
 * Communication model -  failed payment.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Failed_Payment extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_FAILED_PAYMENT;

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __(
			'Wird gesendet, wenn die automatische wiederkehrende Zahlung eines Mitglieds fehlschlägt.', 'membership2'
		);
	}

	/**
	 * Communication default communication.
	 *
	 * @since  1.0.0
	 */
	public function reset_to_default() {
		parent::reset_to_default();

		$this->subject = __( 'Ihre Mitgliedszahlung ist fehlgeschlagen', 'membership2' );
		$this->message = self::get_default_message();
		$this->enabled = false;

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
			__( 'Leider ist Deine wiederkehrende Zahlung für Deine %1$s-Mitgliedschaft bei %2$s fehlgeschlagen.', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME
		);
		$body_continue = sprintf(
			__( 'Um als Mitglied fortzufahren, überprüfe und bearbeite bitte Deine Zahlungsinformationen in Deinem Konto nach Bedarf hier: %1$s', 'membership2' ),
			self::COMM_VAR_MS_ACCOUNT_PAGE_URL
		);
		$body_invoice = __( 'Hier ist Deine letzte Rechnung, die jetzt fällig ist:', 'membership2' );

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s<br /><br />%4$s<br /><br />%5$s',
			$subject,
			$body_notice,
			$body_continue,
			$body_invoice,
			self::COMM_VAR_MS_INVOICE
		);

		return apply_filters(
			'ms_model_communication_failed_payment_get_default_message',
			$html
		);
	}
}