<?php
/**
 * Helper functions and data used by the Member class.
 */
class MS_Helper_Member extends MS_Helper {

	const MSG_MEMBER_ADDED 					= 1;
	const MSG_MEMBER_DELETED 				= 2;
	const MSG_MEMBER_UPDATED 				= 3;
	const MSG_MEMBER_ACTIVATION_TOGGLED 	= 4;
	const MSG_MEMBER_BULK_UPDATED 			= 5;
	const MSG_MEMBER_USER_ADDED 			= 6;
	const MSG_MEMBER_NOT_ADDED 				= -1;
	const MSG_MEMBER_NOT_DELETED 			= -2;
	const MSG_MEMBER_NOT_UPDATED 			= -3;
	const MSG_MEMBER_ACTIVATION_NOT_TOGGLED = -4;
	const MSG_MEMBER_BULK_NOT_UPDATED 		= -5;

	public static function get_admin_message( $msg = 0 ) {
		$messages = apply_filters(
			'ms_helper_member_get_admin_messages',
			array(
				self::MSG_MEMBER_ADDED 					=> __( 'Mitgliedschaft hinzugefügt.', 'membership2' ),
				self::MSG_MEMBER_DELETED 				=> __( 'Mitgliedschaft gelöscht.', 'membership2' ),
				self::MSG_MEMBER_UPDATED 				=> __( 'Mitglied aktualisiert.', 'membership2' ),
				self::MSG_MEMBER_ACTIVATION_TOGGLED 	=> __( 'Die Aktivierung des Mitglieds wurde umgeschaltet.', 'membership2' ),
				self::MSG_MEMBER_BULK_UPDATED 			=> __( 'Mitglieder-Bulk aktualisiert.', 'membership2' ),
				self::MSG_MEMBER_USER_ADDED 			=> __( 'Benutzer zur Mitgliedschaften-Mitgliederliste hinzugefügt.', 'membership2' ),
				self::MSG_MEMBER_NOT_ADDED 				=> __( 'Mitgliedschaft nicht hinzugefügt.', 'membership2' ),
				self::MSG_MEMBER_NOT_DELETED 			=> __( 'Mitgliedschaft nicht gelöscht.', 'membership2' ),
				self::MSG_MEMBER_NOT_UPDATED 			=> __( 'Mitglied nicht aktualisiert.', 'membership2' ),
				self::MSG_MEMBER_ACTIVATION_NOT_TOGGLED => __( 'Die Aktivierung des Mitglieds wurde nicht umgeschaltet.', 'membership2' ),
				self::MSG_MEMBER_BULK_NOT_UPDATED 		=> __( 'Mitglieder-Bulk nicht aktualisiert.', 'membership2' ),
			)
		);

		if ( array_key_exists( $msg, $messages ) ) {
			return $messages[ $msg ];
		}

		return null;
	}

	public static function print_admin_message() {
		$msg = ! empty( $_GET['msg'] ) ? (int) $_GET['msg'] : 0;

		$class = ( $msg > 0 ) ? 'updated' : 'error';

		if ( $msg = self::get_admin_message( $msg ) ) {
			mslib3()->ui->admin_message( $msg, $class );
		}
	}

}