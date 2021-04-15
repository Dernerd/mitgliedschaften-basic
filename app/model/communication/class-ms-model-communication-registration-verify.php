<?php
/**
 * Communication model - email verification
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.1.3
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Registration_Verify extends MS_Model_Communication {

	/**
	 * Add action to credit card expire event.
	 *
	 * Related Action Hooks:
	 * - ms_model_event_paid
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_REGISTRATION_VERIFY;


	/**
	 * Defines if it should be shown to admin
	 *
	 * Only relevant for user specific mails
	 *
	 * @since 1.1.3
	 * @var   bool
	 */
	protected $show_admin_cc = false;

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __(
			'Wird gesendet, wenn ein Benutzer die Anmeldung abgeschlossen hat, um seine E-Mail-Adresse zu bestätigen', 'membership2'
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
			__( 'Bestätigung Deiner E-Mail bei %s', 'membership2' ),
			self::COMM_VAR_BLOG_NAME
		);
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
	 * @since  1.1.3
	 * 
	 * @return string The email message.
	 */
	public static function get_default_message() {
		$subject = sprintf(
			__( 'Hallo %1$s,', 'membership2' ),
			self::COMM_VAR_USERNAME
		);
		$body_notice = sprintf(
			__( 'Vielen Dank, dass Du Dich für ein Konto bei %1$s registriert hast!', 'membership2' ),
			self::COMM_VAR_BLOG_NAME
		);
		$body_account = sprintf(
			__( 'Bitte klicke auf den Link %1$s, um Dein Konto zu bestätigen', 'membership2' ),
			self::COMM_VAR_VERIFICATION_URL
		);

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s',
			$subject,
			$body_notice,
			$body_account
		);

		return apply_filters(
			'ms_model_communication_registration_verify_get_default_message',
			$html
		);
	}

	/**
	 * Process communication verification code.
	 *
	 * @since  1.1.3
	 */
	public function process_communication( $event, $subscription ) {

		do_action(
			'ms_model_communication_registration_verify_process_before',
			$subscription,
			$event,
			$this
		);

		$this->send_message( $subscription );

		do_action(
			'ms_model_communication_registration_verify_process_after',
			$subscription,
			$event,
			$this
		);
	}
}