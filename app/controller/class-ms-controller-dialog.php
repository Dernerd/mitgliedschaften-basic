<?php
/**
 * Controller to manage Membership popup dialogs.
 *
 * @since  1.0.0
 *
 * @package Membership2
 * @subpackage Controller
 *
 * @return object
 */
class MS_Controller_Dialog extends MS_Controller {

	/**
	 * Prepare the Dialog manager.
	 *
	 * @since  1.0.0
	 * @internal
	 */
	public function __construct() {
		parent::__construct();

		// Listen to Ajax requests that want to display a popup.
		$this->add_ajax_action( 'ms_dialog', 'ajax_dialog' );

		// Listen to Ajax requests that submit form data.
		$this->add_ajax_action( 'ms_submit', 'ajax_submit' );

		//Password reset
		$this->add_ajax_action( 'ms_lostpass', 'ajax_lostpass', true, true );
	}

	/**
	 * Ajax handler. Returns the HTML code of an popup dialog.
	 * The process is terminated after this handler.
	 *
	 * @since  1.0.0
	 * @internal
	 */
	public function ajax_dialog() {
		$data = '';

		if ( isset( $_REQUEST['dialog'] ) ) {
			$dialog = $_REQUEST['dialog'];
			$dlg = MS_Factory::create( 'MS_' . $dialog );

			$dlg->prepare();

			$data = array(
				'id' => $dialog,
				'title' => $dlg->title,
				'content' => $dlg->content,
				'height' => $dlg->height,
				'width' => $dlg->width,
				'modal' => $dlg->modal,
			);
		}

		$this->respond( $data );
	}

	/**
	 * Ajax handler. Handles incoming form data that was submitted via ajax.
	 * Typically this form is displayed inside a popup.
	 *
	 * @since  1.0.0
	 * @internal
	 */
	public function ajax_submit() {
		$data = '';

		if ( isset( $_REQUEST['dialog'] ) ) {
			$dialog = $_REQUEST['dialog'];
			$dlg = MS_Factory::create( 'MS_' . $dialog );
			$data = $dlg->submit();
		}

		$this->respond( $data );
	}

	/**
	 * Ajax handler. Used by shortcode `ms-membership-login` to recover password
	 *
	 * @since  1.0.0
	 * @internal
	 */
	public function ajax_lostpass() {
		$resp = array();

		// First check the nonce, if it fails the function will break
		check_ajax_referer( 'ms-ajax-lostpass', '_membership_auth_lostpass_nonce' );

		// Nonce is checked, get the POST data and sign user on
		$errors = new WP_Error();

		if ( empty( $_POST['user_login'] ) ) {
			$resp['error'] = __( 'Gib einen Benutzernamen oder eine E-Mail-Adresse ein.', 'membership2' );
		} else if ( strpos( $_POST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', trim( $_POST['user_login'] ) );
			if ( empty( $user_data ) ) {
				$resp['error'] = __( 'Es ist kein Benutzer mit dieser E-Mail-Adresse registriert.', 'membership2' );
			}
		} else {
			$login = trim( $_POST['user_login'] );
			$user_data = get_user_by( 'login', $login );
		}

		do_action( 'lostpassword_post' );

		if ( ! empty( $resp['error'] ) ) {
			$this->respond( $resp );
		}

		if ( ! $user_data ) {
			$resp['error'] = __( 'Ungültiger Benutzername oder ungültige E-Mail.', 'membership2' );
			$this->respond( $resp );
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;

		do_action( 'retreive_password', $user_login ); // Legacy (misspelled)
		do_action( 'retrieve_password', $user_login );

		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if ( ! $allow ) {
			$resp['error'] = __( 'Das Zurücksetzen des Passworts ist für diesen Benutzer nicht zulässig', 'membership2' );
			$this->respond( $resp );
		} elseif ( is_wp_error( $allow ) ) {
			return $allow;
		}

		// Save an event about the password reset; also send the email template.
		$member = MS_Factory::load( 'MS_Model_Member', $user_data->ID );
		MS_Model_Event::save_event( MS_Model_Event::TYPE_MS_RESETPASSWORD, $member );

		// Send our default email if the user does not have a custom email template in place.
		if ( ! apply_filters( 'ms_sent_reset_password_email', false ) ) {
			// Get a new reset-key.
			$reset 	= $member->new_password_reset_key();

			$schema = is_ssl() ? 'https' : 'http';

			$message = sprintf(
				__( 'Jemand hat ein Zurücksetzen des Passworts für das folgende Konto angefordert: %sWenn dies ein Fehler war, ignoriere einfach diese E-Mail und es wird nichts passieren.%s %s', 'membership2' ),
				"\r\n\r\n" . network_home_url( '/', $schema ) . "\r\n\r\n" .
				sprintf( __( 'Benutzername: %s', 'membership2' ), $user_login ) . "\r\n\r\n",
                                "\r\n\r\n" . __( 'Um Dein Passwort zurückzusetzen, besuche die folgende Adresse:', 'membership2' ) . "\r\n",
				"\r\n<" . $reset->url . ">\r\n"
			);

			if ( is_multisite() ) {
				$blogname = $GLOBALS['current_site']->site_name;
			} else {
				$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			}

			$title 		= sprintf( __( '[%s] Passwort zurücksetzen', 'membership2' ), $blogname );
			$title 		= apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );
			$message 	= apply_filters( 'retrieve_password_message', $message, $user_login, $reset->url, $user_data );

			if ( $message && ! wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
				$resp['error'] = __( 'Die E-Mail konnte nicht gesendet werden.', 'membership2' ) . '<br />' .
					__( 'Möglicher Grund: Dein Host hat möglicherweise die Funktion mail() deaktiviert.', 'membership2' );
			} else {
				$resp['success'] = __( 'Schaue in Deinen E-Mails nach dem Bestätigungslink.', 'membership2' );
			}
		} else {
			$resp['success'] = __( 'Schaue in Deinen E-Mails nach dem Bestätigungslink.', 'membership2' );
		}

		$this->respond( $resp );
	}

	/**
	 * Output Ajax response (in JSON format) and terminate the process.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $resp The data to output.
	 */
	private function respond( $resp ) {
		echo json_encode( $resp );
		exit();
	}

};