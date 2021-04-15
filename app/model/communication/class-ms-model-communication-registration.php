<?php
/**
 * Communication model - registration.
 *
 * Persisted by parent class MS_Model_CustomPostType.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Communication_Registration extends MS_Model_Communication {

	/**
	 * Add action to credit card expire event.
	 *
	 * Related Action Hooks:
	 * - ms_model_event_paid
	 *
	 * @since  1.0.0
	 * @var string The communication type.
	 */
	protected $type = self::COMM_TYPE_REGISTRATION;

	/**
	 * Get communication description.
	 *
	 * @since  1.0.0
	 * @return string The description.
	 */
	public function get_description() {
		return __(
			'Wird gesendet, wenn ein Mitglied die Anmeldung für eine bezahlte Mitgliedschaft abgeschlossen hat.', 'membership2'
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
			__( 'Bestätigung Deiner Mitgliedschaft bei %s', 'membership2' ),
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
			__( 'Danke! Deine <strong>%1$s</strong> Mitgliedschaft bei %2$s ist jetzt aktiviert!', 'membership2' ),
			self::COMM_VAR_MS_NAME,
			self::COMM_VAR_BLOG_NAME
		);
		$body_account = sprintf(
			__( 'Du kannst Deine Mitgliedschaftsdaten hier überprüfen und bearbeiten: %1$s', 'membership2' ),
			self::COMM_VAR_MS_ACCOUNT_PAGE_URL
		);
		$body_invoice = __( 'Hier sind die neuesten Zahlungsinformationen für Dein Abonnement:', 'membership2' );

		$html = sprintf(
			'<h2>%1$s</h2><br /><br />%2$s<br /><br />%3$s<br /><br />%4$s<br /><br />%5$s',
			$subject,
			$body_notice,
			$body_account,
			$body_invoice,
			self::COMM_VAR_MS_INVOICE
		);

		return apply_filters(
			'ms_model_communication_registration_get_default_message',
			$html
		);
	}

	/**
	 * Process communication registration.
	 *
	 * @since  1.0.0
	 */
	public function process_communication( $event, $subscription ) {
		$membership = $subscription->get_membership();

		// Only process Paid memberships here!
		// Email for free memberships is in MS_Model_Communiction_Registration_Free
		if ( $membership->is_free() ) { return; }

		do_action(
			'ms_model_communication_registration_process_before',
			$subscription,
			$event,
			$this
		);

		$this->send_message( $subscription );

		do_action(
			'ms_model_communication_registration_process_after',
			$subscription,
			$event,
			$this
		);
	}
}
