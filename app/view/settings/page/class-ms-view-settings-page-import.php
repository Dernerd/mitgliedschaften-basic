<?php
/**
 * View.
 *
 * @package Membership2
 */

/**
 * Render the Settings > Import Tool pages.
 */
class MS_View_Settings_Page_Import extends MS_View_Settings_Edit {

	/**
	 * Return the page contents.
	 * Note: Return, not output!
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function to_html() {
		$export_action 		= MS_Controller_Import::ACTION_EXPORT;
		$import_action 		= MS_Controller_Import::ACTION_PREVIEW;
		$import_user_action = MS_Controller_Import::ACTION_IMPORT_USER;
		$messages 			= $this->data['message'];

		$preview = false;
		if ( isset( $messages['preview'] ) ) {
			$preview = $messages['preview'];
		}

		$export_fields = array(
			'type' => array(
				'id' 			=> 'type',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'title' 		=> __( 'Exporttyp auswählen', 'membership2' ),
				'field_options' => $this->data['types'],
				'class' 		=> 'ms-select ms-select-type'
			),
			'format' => array(
				'id' 			=> 'format',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'title' 		=> __( 'Exportformat auswählen', 'membership2' ),
				'field_options' => $this->data['formats'],
				'class' 		=> 'ms-select ms-select-format'
			),
			'export' 	=> array(
				'id' 	=> 'btn_export',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_SUBMIT,
				'value' => __( 'Export generieren', 'membership2' ),
				'desc' 	=> __( 'Generiere eine Exportdatei mit einer der oben genannten Optionen', 'membership2' ),
			),
			'action' 	=> array(
				'id' 		=> 'action',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' 	=> $export_action,
			),
			'nonce' 	=> array(
				'id' 		=> '_wpnonce',
				'type' 		=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' 	=> wp_create_nonce( $export_action ),
			),
		);

		$file_field = array(
			'id' 		=> 'upload',
			'type'		=> MS_Helper_Html::INPUT_TYPE_FILE,
			'title' 	=> __( 'Aus der Exportdatei', 'membership2' ),
		);
		$import_options = array(
			'file' 		=> array(
				'text' 		=> MS_Helper_Html::html_element( $file_field, true ),
				'disabled' 	=> ! MS_Model_Import_File::present(),
			),
			'membership' => array(
				'text' 		=> __( 'PS-Mitgliedschaften (WMS N@W)', 'membership2' ),
				'disabled' 	=> ! MS_Model_Import_Membership::present(),
			),
		);

		$sel_source = 'file';
		if ( isset( $_REQUEST['import_source'] )
			&& isset( $import_options[ $_REQUEST['import_source'] ] )
		) {
			$sel_source = $_REQUEST['import_source'];
		}

		$import_fields = array(
			'source' => array(
				'id' 			=> 'import_source',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_RADIO,
				'title' 		=> __( 'Wähle eine Importquelle', 'membership2' ),
				'field_options' => $import_options,
				'value' 		=> $sel_source,
			),
			'import' => array(
				'id' 	=> 'btn_import',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_SUBMIT,
				'value' => __( 'Vorschau Import', 'membership2' ),
				'desc' 	=> __(
					'Importiere Daten in diese Installation.',
					'membership2'
				),
			),
			'action' => array(
				'id' 	=> 'action',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $import_action,
			),
			'nonce' => array(
				'id' 	=> '_wpnonce',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => wp_create_nonce( $import_action ),
			),
		);


		$status_options = array(
			MS_Model_Relationship::STATUS_PENDING 		=> __( 'Ausstehend (bei nächster Zahlung aktivieren)', 'membership2' ),
			MS_Model_Relationship::STATUS_WAITING 		=> __( 'Warten (am Startdatum aktivieren)', 'membership2', 'membership2' ),
			MS_Model_Relationship::STATUS_ACTIVE 		=> __( 'Aktiv', 'membership2' ),
			MS_Model_Relationship::STATUS_CANCELED 		=> __( 'Abgebrochen (am Ablaufdatum deaktivieren)', 'membership2' ),
			MS_Model_Relationship::STATUS_EXPIRED 		=> __( 'Abgelaufen (kein Zugriff)', 'membership2' ),
			MS_Model_Relationship::STATUS_DEACTIVATED 	=> __( 'Deaktiviert (kein Zugriff)', 'membership2' ),
		);

		$import_users_fields = array(
			'file' 		=> array(
				'id' 		=> 'upload',
				'type'		=> MS_Helper_Html::INPUT_TYPE_FILE,
				'title' 	=> sprintf( __( 'Benutzerliste CSV-Datei %sBeispieldateien herunterladen%s', 'membership2' ), '<a href="'.$this->data['sample'].'">', '</a>' ),
			),
			'membership' => array(
				'id' 			=> 'users-membership',
				'type'			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'field_options' => MS_Model_Export::get_memberships(),
				'class' 		=> 'ms-select',
				'title' 		=> __( 'Optional kannst Du ausgewählten Mitgliedern Mitgliedschaften zuweisen', 'membership2' ),
			),
			'status' => array(
				'id' 			=> 'users-status',
				'type'			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'field_options' => $status_options,
				'class' 		=> 'ms-select',
				'title' 		=> __( 'Optional kannst Du ausgewählten Mitgliedern Mitgliedschaften zuweisen', 'membership2' ),
			),
			'start' => array(
				'name' 			=> 'users-start',
				'type'			=> MS_Helper_Html::INPUT_TYPE_DATEPICKER,
				'desc' 			=> __( 'Anfangsdatum', 'membership2' ) . ' <sup>*)</sup>'
			),
			'expire' => array(
				'name' 			=> 'users-expire',
				'type'			=> MS_Helper_Html::INPUT_TYPE_DATEPICKER,
				'desc' 			=> __( 'Ablaufdatum', 'membership2' ) . ' <sup>*)</sup>'
			),
			'import' => array(
				'id' 	=> 'btn_user_import',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_SUBMIT,
				'value' => __( 'Benutzer hochladen', 'membership2' ),
				'desc' 	=> __(
					'Lade Benutzer als Mitglieder hoch und erstelle sie. Alle hochgeladenen Mitglieder haben aktive Abonnements',
					'membership2'
				),
			),
			'action' => array(
				'id' 	=> 'action',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $import_user_action,
			),
			'nonce' => array(
				'id' 	=> '_wpnonce',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => wp_create_nonce( $import_user_action ),
			),
		);

		ob_start();

		MS_Helper_Html::settings_tab_header(
			array( 'title' => __( 'Import Werkzeug', 'membership2' ) )
		);
		?>

		<div>
			<?php if ( $preview ) : ?>
				<form action="" method="post">
					<?php echo $preview; ?>
				</form>
			<?php else : ?>
				<form action="" method="post" enctype="multipart/form-data">
					<?php MS_Helper_Html::settings_box(
						$import_fields,
						__( 'Importiere Daten', 'membership2' )
					); ?>
				</form>
				<form action="" method="post">
					<?php MS_Helper_Html::settings_box(
						$export_fields,
						__( 'Exportiere Daten', 'membership2' )
					); ?>
				</form>
				<form action="" method="post" enctype="multipart/form-data">
					<?php MS_Helper_Html::settings_box(
						$import_users_fields,
						__( 'Massenimport Benutzer', 'membership2' )
					); ?>
				</form>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
