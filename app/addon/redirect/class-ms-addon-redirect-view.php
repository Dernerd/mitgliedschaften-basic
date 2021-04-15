<?php

/**
 * The Settings-Form
 */
class MS_Addon_Redirect_View extends MS_View {

	public function render_tab() {
		$fields = $this->prepare_fields();
		ob_start();
		?>
		<div class="ms-addon-wrap">
			<?php
			MS_Helper_Html::settings_tab_header(
				array(
					'title' => __( 'Umleitung Einstellungen', 'membership2' ),
					'desc' 	=> array(
						__( 'Gib hier Deine benutzerdefinierten URLs an. Du kannst entweder eine absolute URL (beginnend mit "http://") oder einen Webseiten-relativen Pfad (wie "/some-page/") verwenden.', 'membership2' ),
						sprintf(
							__( 'Die hier angegebenen URLs können jederzeit im %sLogin-Shortcode%s mit den Redirect-Attributen überschrieben werden. Beispiel: <code>[%s redirect_login="/welcome/" redirect_logout="/good-bye/"]</code>.', 'membership2' ),
							sprintf(
								'<a href="%s#ms-membership-login" target="_blank">',
								MS_Controller_Plugin::get_admin_url(
									'help',
									array( 'tab' => 'shortcodes' )
								)
							),
							'</a>',
							MS_Helper_Shortcode::SCODE_LOGIN
						),
					),
				)
			);

			foreach ( $fields as $field ) {
				MS_Helper_Html::html_element( $field );
			}
			?>
		</div>
		<?php
		$html = ob_get_clean();
		echo $html;
	}

	public function prepare_fields() {
		$model = MS_Addon_Redirect::model();

		$action = MS_Addon_Redirect::AJAX_SAVE_SETTING;

		$fields = array(
			'redirect_login' => array(
				'id' 			=> 'redirect_login',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' 		=> __( 'Nach dem Login', 'membership2' ),
				'desc' 			=> __( '<p>Diese Seite wird den Benutzern direkt nach der Anmeldung angezeigt.</p>
                                                                <p>Du kannst der URL [username] hinzufügen, die durch den Benutzernamen der Mitglieder ersetzt wird.<p>
                                                                <p>Nützlich für die Weiterleitung zu einer BuddyPress-Profilseite.</p>
                                                                <p>Beispiel: http://yourdomain.com/members/[username]/profile wird ersetzt durch http://yourdomain.com/members/myusername/profile</p>', 'membership2' ),
				'placeholder' 	=> MS_Model_Pages::get_url_after_login( false ),
				'value' 		=> $model->get( 'redirect_login' ),
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array(
					'field' 		=> 'redirect_login',
					'action' 		=> $action,
				),
			),

			'redirect_logout' => array(
				'id' 			=> 'redirect_logout',
				'type'			=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'title' 		=> __( 'Nach dem Abmelden', 'membership2' ),
				'desc' 			=> __( 'Diese Seite wird Benutzern direkt nach dem Abmelden angezeigt.', 'membership2' ),
				'placeholder' 	=> MS_Model_Pages::get_url_after_logout( false ),
				'value' 		=> $model->get( 'redirect_logout' ),
				'class' 		=> 'ms-text-large',
				'ajax_data' 	=> array(
					'field' 		=> 'redirect_logout',
					'action' 		=> $action,
				),
			),
		);

		return $fields;
	}
}