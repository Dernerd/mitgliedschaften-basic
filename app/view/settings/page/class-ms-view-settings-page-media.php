<?php

/**
 * Advanced Media Settings
 *
 * @since 1.0.4
 */
class MS_View_Settings_Page_Media extends MS_View_Settings_Edit {

	/**
	 * Overrides parent's to_html() method.
	 *
	 * Creates an output buffer, outputs the HTML and grabs the buffer content before releasing it.
	 * HTML contains the list of advanced media settings
	 *
	 * @since  1.0.4
	 *
	 * @return string
	 */
	public function to_html() {
		$settings = MS_Factory::load( 'MS_Model_Settings' );

		$direct_access = array( 'jpg', 'jpeg', 'png', 'gif', 'mp3', 'ogg' );
		if ( isset( $settings->downloads['direct_access'] ) ) {
			$direct_access = $settings->downloads['direct_access'];
		}

		$server = MS_Helper_Media::get_server();
		if ( isset( $settings->downloads['application_server'] ) && !empty( $settings->downloads['application_server'] ) ) {
			$server = $settings->downloads['application_server'];
		}
		$upload_dir     = wp_upload_dir();
        $uploads_dir    = $upload_dir['basedir'];
		//Wp-content dir used for nginx
		if ( DIRECTORY_SEPARATOR == '\\' ) {
			//Windows
			$wp_content     = str_replace( ABSPATH, '', $uploads_dir );
		} else {
			$wp_content     = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $uploads_dir );
		}
		

		$fields = array(
			'wp_content_dir' => array(
				'id' 	=> 'wp_content_dir',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_HIDDEN,
				'value' => $wp_content,
			),

			'select_server' => array(
				'id' 			=> 'application_server',
				'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
				'title' 		=> __( 'Anwendungsserver', 'membership2' ),
				'value' 		=> $server,
				'field_options' => MS_Helper_Media::server_types(),
				'class' 		=> 'ms-select',
				'data_ms' 		=> array(
					'field' 	=> 'application_server',
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_PROTECTION_FILE,
					'_wpnonce' 	=> true, // Nonce will be generated from 'action'
				),
			),

			'direct_access' => array(
				'id' 	=> 'direct_access',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_TEXT,
				'desc' 	=> __( 'Erlaube nur den direkten Zugriff auf die folgenden Dateierweiterungen.', 'membership2' ),
				'value' => implode( ",", $direct_access ),
				'class' => 'ms-text-large',
				'data_ms' => array(
					'field' 	=> 'direct_access',
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_UPDATE_SETTING,
					'_wpnonce' 	=> true, // Nonce will be generated from 'action'
				),
			)
		);

		$apache_settings = array(
			'regenerate_htaccess' => array(
				'id' 	=> 'regenerate_htaccess',
				'type' 	=> MS_Helper_Html::INPUT_TYPE_BUTTON,
				'value' => __( 'Aktualisiere htaccess', 'membership2' ),
				'data_ms' => array(
					'field' 	=> 'regenerate_htaccess',
					'action' 	=> MS_Controller_Settings::AJAX_ACTION_TOGGLE_PROTECTION_FILE,
					'_wpnonce' 	=> true, // Nonce will be generated from 'action'
				),
			),
			'instructions'	=> array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TEXT,
				'value' => __( "Wir platzieren eine <strong>.htaccess</strong> Datei in die /wp-content/uploads/ Ordner, um den direkten Zugriff auf andere als die definierten Dateien zu verhindern. Bei jeder Änderung der Dateien muss die htaccess-Datei aktualisiert werden", "membership2" ),
			)
		);
		

		ob_start();
		?>
		<div class="cf">
			<?php
			MS_Helper_Html::settings_tab_header(
				array(
					'title' => __( 'Hochgeladene Dateien schützen', 'membership2' ),
					'desc' => __( 'Verhindere den direkten Zugriff auf Deine hochgeladenen Mediendateien', 'membership2' ),
				)
			);
			foreach ( $fields as $field ) {
				MS_Helper_Html::html_element( $field );
			}
			?>
			<div style="display:<?php echo ( $server != 'apache' ) ? 'none' : 'block'; ?>"  class="application-servers application-server-apache">
				<?php 
				MS_Helper_Html::html_element( $apache_settings['instructions'] );
				MS_Helper_Html::html_separator();
				MS_Helper_Html::html_element( $apache_settings['regenerate_htaccess'] ); 
				?>
			</div>
			<div style="display:<?php echo ( $server != 'litespeed' ) ? 'none' : 'block'; ?>" class="application-servers application-server-litespeed">
				<?php 
				MS_Helper_Html::html_element( $apache_settings['instructions'] );
				MS_Helper_Html::html_separator();
				MS_Helper_Html::html_element( $apache_settings['regenerate_htaccess'] ); 
				?>
			</div>
			<div style="display:<?php echo ( $server != 'nginx' ) ? 'none' : 'block'; ?>" class="application-servers application-server-nginx">
				<p><?php esc_html_e( "Für NGINX Server:", "membership2" ); ?></p>
                    <ol>
                        <li>
                            <?php esc_html_e( "Kopiere den generierten Code in Deine Webseitenspezifische .conf-Datei, die sich normalerweise in einem Unterverzeichnis befindet unter /etc/nginx/... or /usr/local/nginx/conf/...", "membership2" ); ?>
                        </li>
                        <li>
                            <?php _e( "Füge den obigen Code im Abschnitt <strong>Server</strong> in der Datei direkt vor dem PHP-Speicherortblock hinzu. Sieht ungefähr so aus:", "membership2" ); ?>
                            <pre>location ~ \.php$ {</pre>
                        </li>
                        <li>
                            <?php esc_html_e( "NGINX neustarten.", "membership2" ); ?>
                        </li>
                    </ol>
                    <p><?php echo sprintf( __( "Du hast immer noch Probleme? <a target='_blank' href=\"%s\">Offne ein Supportticket</a>.", "membership2" ), 'https://n3rds.work/n3rdswork-support-team/supportanfrage-stellen/' ); ?></p>
				<?php
				$rules = "
				# Verweigert den direkten Zugriff auf Mediendateien im Verzeichnis /wp-content/uploads/ (einschließlich Unterordnern).
				location ^~ ^/$wp_content {
				  deny all;
				}
				";
				?>
				<pre style="white-space: pre-line;">
				## Mitgliedschaften - Medienschutz ##
				<?php echo esc_html( $rules ); ?>
				<span class="application-servers-nginx-extra-instructions">
				<?php
				$extensions = implode( "|", $direct_access );
				$rules = "location ~* ^$wp_content/.*\.($extensions)$ {
					allow all;
				}";
				echo esc_html( $rules );
				?>
				</span>
				## Mitgliedschaften - Ende ##
				</pre>
			</div>
			<div style="display:<?php echo ( $server != 'iis' ) ? 'none' : 'block'; ?>" class="application-servers application-server-iis">
				<p><?php printf( __( 'Bitte <a href="%s">besuche Microsoft Docs</a> zum Konfigurieren des IIS', "membership2" ), 'https://docs.microsoft.com/en-us/iis/configuration/system.webserver/security/requestfiltering/fileextensions/' ); ?></p>
			</div>
		</div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	

}