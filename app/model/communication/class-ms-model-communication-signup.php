<?php
/**
 * Communication model - user sign up.
 * Triggered when a new user creates an WordPress account.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Signup extends MS_Model_Communication {

	/**
	 * Communication type.
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_SIGNUP;

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __(
			'Willkommens-E-Mail, die gesendet wird, nachdem ein neues Benutzerkonto erstellt wurde.', 'membership2'
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
			__( 'Willkommen bei %s!', 'membership2' ),
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
	 * @since  1.0.0
	 * @return string The email message.
	 */
	public static function get_default_message() {
		$subject = sprintf(
			__( 'Hallo %1$s,', 'membership2' ),
			self::COMM_VAR_USERNAME
		);
		$body_notice = sprintf(
			__( 'Willkommen bei %s! Wir haben ein brandneues Konto für Dich erstellt und Du kannst direkt zu %s gehen und sich mit Deinem Benutzernamen und Passwort anmelden.', 'membership2' ),
			self::COMM_VAR_BLOG_NAME,
			self::COMM_VAR_BLOG_URL
		);
		$body_account = sprintf(
			__( 'Benutzername: %s<br>Passwort: %s', 'membership2' ),
			self::COMM_VAR_USERNAME,
			self::COMM_VAR_PASSWORD
		);

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s',
			$subject,
			$body_notice,
			$body_account
		);

		return apply_filters(
			'ms_model_communication_signup_get_default_message',
			$html
		);
	}

	/**
	 * Process communication registration.
	 *
	 * @since  1.0.0
	 */
	public function process_communication( $event, $subscription ) {
		do_action(
			'ms_model_communication_signup_process_before',
			$subscription,
			$event,
			$this
		);

		$this->send_message( $subscription );

		do_action(
			'ms_model_communication_signup_process_after',
			$subscription,
			$event,
			$this
		);
	}
}