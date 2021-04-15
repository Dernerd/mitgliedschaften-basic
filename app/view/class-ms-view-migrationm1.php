<?php
/**
 * Special View that is displayed to complete the migration from M1.
 *
 * @since  1.0.0
 */
class MS_View_MigrationM1 extends MS_View {

	/**
	 * Returns the HTML code of the view.
	 *
	 * @since  1.0.0
	 * @api
	 *
	 * @return string
	 */
	public function to_html() {
		$model = MS_Factory::create( 'MS_Model_Import_Membership' );

		if ( MS_Plugin::is_network_wide() && defined( 'BLOG_ID_CURRENT_SITE' ) ) {
			switch_to_blog( BLOG_ID_CURRENT_SITE );
			$model->prepare();
			restore_current_blog();
		} else {
			$model->prepare();
		}

		$view = MS_Factory::create( 'MS_View_Settings_Import_Settings' );
		$view->data = array( 'model' => $model, 'compact' => true );
		$msg = __(
			'Tipp: Du kannst Daten auch später importieren, indem Du die Admin-Seite besuchst <b>PS-Mitgliedschaften > Einstellungen > Import Werkzeug</b>.',
			'membership2'
		);

		ob_start();
		// Render tabbed interface.
		?>
		<div class="ms-wrap wrap">
			<h2>
				<?php _e( 'Importiere Deine Mitgliedschaftsdaten in Mitgliedschaften', 'membership2' ); ?>
			</h2>
			<?php
			if ( MS_Plugin::is_network_wide() ) {
				$msg .= '<br><br>' . __(
					'Du hast den netzwerkweiten Schutz aktiviert. Wir importieren Mitgliedschaftsdaten aus Deinem Hauptblog.',
					'membership2'
				);
			}

			mslib3()->ui->admin_message( $msg, 'info' );
			?>
			<div class="ms-settings-import">
				<?php echo $view->to_html(); ?>
			</div>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Enquque scripts and styles used by this special view.
	 *
	 * @since  1.0.0
	 */
	public function enqueue_scripts() {
		$data = array(
			'ms_init' 		=> array( 'view_settings_import' ),
			'close_link' 	=> MS_Controller_Plugin::get_admin_url(),
			'lang' 			=> array(
				'progress_title' 			=> __( 'Importiere Daten...', 'membership2' ),
				'close_progress' 			=> __( 'Okay', 'membership2' ),
				'import_done' 				=> __( 'Alles erledigt!', 'membership2' ),
				'task_start' 				=> __( 'Vorbereiten...', 'membership2' ),
				'task_done' 				=> __( 'Aufräumen...', 'membership2' ),
				'task_import_member' 		=> __( 'Mitglied importieren', 'membership2' ),
				'task_import_membership' 	=> __( 'Importiere Mitgliedschaften', 'membership2' ),
				'task_import_settings' 		=> __( 'Importiere Einstellungen', 'membership2' ),
			),
		);

		mslib3()->ui->data( 'ms_data', $data );
		wp_enqueue_script( 'ms-admin' );
	}

}