<?php
/**
 * Model
 *
 * @package Membership2
 */

/**
 * Base class for all export handlers.
 *
 * @since  1.1.3
 */
class MS_Model_Export extends MS_Model {

	/**
	 * Export Settings
	 */
	const PLUGIN_SETTINGS 		= 'plugin';
	const FULL_MEMBERSHIP_DATA 	= 'full';
	const MEMBERSHIP_ONLY 		= 'membership';
	const MEMBERS_ONLY 			= 'members';
 
	/**
	 * Export formats
	 */
	const JSON_EXPORT 			= 'json';
	const XML_EXPORT 			= 'xml';
 
 
	 /**
	  * Main entry point: Handles the export action.
	  *
	  * This task will exit the current request as the result will be a download
	  * and no HTML page that is displayed.
	  *
	  * @since  1.1.3
	  */
	public function process() {
		 $type 				= $_POST['type'];
		 $format 			= $_POST['format'];
		 $supported_types 	= self::export_types();
		 $supported_formats = self::export_formats();
		 $supported_types 	= array_keys( $supported_types );
		 $supported_formats = array_keys( $supported_formats );
		if ( in_array( $type, $supported_types ) && in_array( $format, $supported_formats ) ) {
			switch ( $type ) {
				case self::PLUGIN_SETTINGS :
					 $handler = MS_Factory::create( 'MS_Model_Export_Settings' );
					 $handler->process();
					 break;

				case self::FULL_MEMBERSHIP_DATA :
					 $handler = MS_Factory::create( 'MS_Model_Export_Full' );
					 $handler->process( $format );
					 break;

				case self::MEMBERSHIP_ONLY :
					 $handler = MS_Factory::create( 'MS_Model_Export_Membership' );
					 $handler->process( $format );
					 break;

				case self::MEMBERS_ONLY :
					 $handler = MS_Factory::create( 'MS_Model_Export_Members' );
					 $handler->process( $format );
					 break;
 
				default :
					 mslib3()->net->file_download( __( 'Exporttyp wird noch nicht unterstützt', 'membership2' ), 'error.json' );
					 break;
			}
		} else {
			 mslib3()->net->file_download( __( 'Ungültiger Exporttyp oder -format', 'membership2' ), 'error.json' );
		}
		 
	 }
 
	 /**
	  * Export types
	  *
	  * @since 1.1.3
	  *
	  * @return Array
	  */
	public static function export_types() {
		return array(
			 self::PLUGIN_SETTINGS 			=> __( 'Plugin-Einstellungen (Beachte, dass dies keine vollständige Sicherung der Plugin-Einstellungen ist.)', 'membership2' ),
			 self::FULL_MEMBERSHIP_DATA 	=> __( 'Vollständige Mitgliedschaftsdaten (Mitglieder und Mitgliedschaften)', 'membership2' ),
			 self::MEMBERSHIP_ONLY 			=> __( 'Nur Mitgliedschaften', 'membership2' ),
			 self::MEMBERS_ONLY 			=> __( 'Nur für Mitglieder', 'membership2' )
		);
	}
 
	 /**
	  * Supported Export types
	  *
	  * @since 1.1.3
	  *
	  * @return Array
	  */
	public static function export_formats() {
		return array(
			 self::JSON_EXPORT 	=> __( 'JSON', 'membership2' ),
			 self::XML_EXPORT 	=> __( 'XML', 'membership2' )
		);
	}

	/**
	 * Get Membership list
	 *
	 * @return Array
	 */
	public static function get_memberships() {
		$membership_select = array();
		$memberships = MS_Model_Membership::get_public_memberships();
		$membership_select[] = __( 'Keiner', 'membership2' );
		foreach ( $memberships as $key => $item ) {
			$membership_select[ $item->id ] = $item->name;
		}
		return $membership_select;
	}
	
}
