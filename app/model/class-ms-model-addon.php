<?php
/**
 * Manage Add-ons.
 *
 * Add-ons are stored in the directory /app/addon/<addon_name>/
 * Each Add-on must provide a file called `addon-<addon_name>.php`
 * This file must define class MS_Addon_<addon_name>.
 * This object is reponsible to initialize the the add-on logic.
 *
 * @since  1.0.0
 *
 * @package Membership2
 * @subpackage Model
 */
class MS_Model_Addon extends MS_Model_Option {

	/**
	 * Add-on name constants.
	 *
	 * @deprecated Since 1.1.0 the Add-On constants are deprecated.
	 *             Use the appropriate hooks to register new addons!
	 *             Example: See the "Taxamo" addon
	 *
	 * @since  1.0.0
	 *
	 * @var string
	 */
	const ADDON_MULTI_MEMBERSHIPS 		= 'multi_memberships';
	const ADDON_POST_BY_POST 			= 'post_by_post';
	const ADDON_HIDE_PAGES_FROM_SEARCH 	= 'hide_pages_from_search';
	const ADDON_URL_GROUPS 				= 'url_groups';
	const ADDON_CPT_POST_BY_POST 		= 'cpt_post_by_post';
	const ADDON_TRIAL 					= 'trial';
	const ADDON_MEDIA 					= 'media';
	const ADDON_SHORTCODE 				= 'shortcode';
	const ADDON_AUTO_MSGS_PLUS 			= 'auto_msgs_plus';
	const ADDON_SPECIAL_PAGES 			= 'special_pages';
	const ADDON_ADV_MENUS 				= 'adv_menus';
	const ADDON_ADMINSIDE 				= 'adminside';
	const ADDON_MEMBERCAPS 				= 'membercaps';
	const ADDON_MEMBERCAPS_ADV 			= 'membercaps_advanced';

	/**
	 * List of all registered Add-ons
	 *
	 * Related hook: ms_model_addon_register
	 *
	 * @var array {
	 *     @key <string> The add-on ID.
	 *     @value object {
	 *         The add-on data.
	 *
	 *         $name  <string>  Display name
	 *         $parent  <string>  Empty/The Add-on ID of the parent
	 *         $description  <string>  Description
	 *         $footer  <string>  For the Add-ons list
	 *         $icon  <string>  For the Add-ons list
	 *         $class  <string>  For the Add-ons list
	 *         $details  <array of HTML elements>  For the Add-ons list
	 *     }
	 * }
	 */
	static private $_registered = array();

	/**
	 * Used by function `flush_list`
	 *
	 * @since  1.0.0
	 *
	 * @var bool
	 */
	static private $_reload_files = false;

	/**
	 * List of add-on files to load when plugin is initialized.
	 *
	 * @since  1.0.0
	 *
	 * @var array of file-paths
	 */
	protected $addon_files = array();

	/**
	 * Add-ons array.
	 *
	 * @since  1.0.0
	 *
	 * @var array {
	 *     @key <string> The add-on ID.
	 *     @value <boolean> The add-on enbled status (always true).
	 * }
	 */
	protected $active = array();

	/**
	 * Initalize Object Hooks
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		parent::__construct();

		$this->add_action( 'ms_model_addon_flush', 'flush_list' );
	}

	/**
	 * Returns a list of all registered Add-Ons
	 *
	 * @since  1.0.0
	 * @return array Add-on lisl
	 */
	static public function get_addons() {
		static $Done = false;
		$res = null;

		if ( ! $Done || self::$_reload_files ) {
			self::$_registered = array();
			$addons = array();
			$Done 	= true;
			self::load_core_addons();

			// Register core add-ons
			$addons = self::get_core_list();

			/**
			 * Register new addons.
			 *
			 * @since  1.0.0
			 */
			$addons = apply_filters(
				'ms_model_addon_register',
				$addons
			);

			// Sanitation and populate default fields.
			foreach ( $addons as $key => $data ) {
				self::$_registered[ $key ] 	= $data->name;

				$addons[ $key ]->id 		= $key;
				$addons[ $key ]->active 	= self::is_enabled( $key );
				$addons[ $key ]->title 		= $data->name;

				if ( isset( $addons[ $key ]->icon ) ) {
					$addons[ $key ]->icon = '<i class="' . $addons[ $key ]->icon . '"></i>';
				} else {
					$addons[ $key ]->icon = '<i class="wpmui-fa wpmui-fa-puzzle-piece"></i>';
				}

				if ( empty( $addons[ $key ]->action ) ) {
					$addons[ $key ]->action = array();
					$addons[ $key ]->action[] = array(
						'id' 		=> 'ms-toggle-' . $key,
						'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
						'value' 	=> self::is_enabled( $key ),
						'class' 	=> 'toggle-plugin',
						'ajax_data' => array(
							'action' 	=> MS_Controller_Addon::AJAX_ACTION_TOGGLE_ADDON,
							'field' 	=> 'active',
							'addon'		 => $key,
						),
					);
					$addons[ $key ]->action[] = MS_Helper_Html::save_text( null, false, true );
				}

				/**
				 * Add custom Actions or remove default actions
				 *
				 * @since  1.0.0
				 */
				$addons[ $key ]->action = apply_filters(
					'ms_model_addon_action-' . $key,
					$addons[ $key ]->action,
					$addons[ $key ]
				);
			}

			natcasesort( self::$_registered );
			foreach ( self::$_registered as $key => $dummy ) {
				self::$_registered[ $key ] = $addons[ $key ];
			}

			/**
			 * The Add-on list is prepared. Initialize the addons now.
			 *
			 * @since  1.0.0
			 */
			do_action( 'ms_model_addon_initialize' );
		}

		return self::$_registered;
	}

	/**
	 * Force to reload the add-on list
	 *
	 * Related action hooks:
	 * - ms_model_addon_flush
	 *
	 * @since  1.0.0
	 */
	public function flush_list() {
		self::$_reload_files = true;
		self::get_addons();
	}

	/**
	 * Checks the /app/addon directory for a list of all addons and loads these
	 * files.
	 *
	 * @since  1.0.0
	 */
	static protected function load_core_addons() {
		$model 			= MS_Factory::load( 'MS_Model_Addon' );
		$content_dir 	= trailingslashit( dirname( dirname( MS_Plugin::instance()->dir ) ) );
		$plugin_dir 	= substr( MS_Plugin::instance()->dir, strlen( $content_dir ) );

		$addon_dirs 	= array();
		$paths 			= MS_Loader::load_paths();
				
		foreach( $paths as $path ) {
			$addon_dirs[] = $plugin_dir . $path . '/addon/';
		}

		if ( empty( $model->addon_files ) || self::$_reload_files ) {
			// In Admin dashboard we always refresh the addon-list...
			self::$_reload_files = false;
			$model->addon_files = array();

			foreach ( $addon_dirs as $addon_dir ) {
				$mask = $content_dir . $addon_dir . '*/class-ms-addon-*.php';
				$addons = glob( $mask );

				foreach ( $addons as $file ) {
					$addon = basename( $file );
					if ( empty( $model->addon_files[ $addon ] ) ) {
						$addon_path = substr( $file, strlen( $content_dir ) );
						$model->addon_files[ $addon ] = $addon_path;
					}
				}
			}

			/**
			 * Allow other plugins/themes to register custom addons
			 *
			 * @since  1.0.0
			 *
			 * @var array
			 */
			$model->addon_files = apply_filters(
				'ms_model_addon_files',
				$model->addon_files
			);

			$model->save();
		}

		// Loop all recignized Add-ons and initialize them.
		foreach ( $model->addon_files as $file ) {
			$addon = $content_dir . $file;

			// Get class-name from file-name
			$class = basename( $file );
			$class = str_replace( '.php', '', $class );
			$class = implode( '_', array_map( 'ucfirst', explode( '-', $class ) ) );
			$class = substr( $class, 6 ); // remove 'Class_' prefix

			if ( file_exists( $addon ) ) {
				if ( ! class_exists( $class ) ) {
					try {
						include_once $addon;
					} catch ( Exception $ex ) {
					}
				}

				if ( class_exists( $class ) ) {
					MS_Factory::load( $class );
				}
			}
		}

		/**
		 * Allow custom addon-initialization code to run
		 *
		 * @since  1.0.0
		 */
		do_action( 'ms_model_addon_load' );
	}

	/**
	 * Verify if an add-on is enabled
	 *
	 * @since  1.0.0
	 *
	 * @var string $addon The add-on type.
	 * @return boolean True if enabled.
	 */
	static public function is_enabled( $addon ) {
		$model 		= MS_Factory::load( 'MS_Model_Addon' );
		$enabled 	= ! empty( $model->active[ $addon ] );

		if ( $enabled ) {
			// Sub-addons are considered enabled only when the parent add-on is enabled also.
			switch ( $addon ) {
				case self::ADDON_MEMBERCAPS_ADV:
					$enabled = self::is_enabled( self::ADDON_MEMBERCAPS );
					break;
			}
		}

		return apply_filters(
			'ms_model_addon_is_enabled_' . $addon,
			$enabled
		);
	}

	/**
	 * Enable an add-on type in the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var string $addon The add-on type.
	 */
	static public function enable( $addon ) {
		$model = MS_Factory::load( 'MS_Model_Addon' );
		$model->refresh();
		$model->active[ $addon ] = true;
		$model->save();

		do_action( 'ms_model_addon_enable', $addon, $model );
	}

	/**
	 * Disable an add-on type in the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var string $addon The add-on type.
	 */
	static public function disable( $addon ) {
		$model = MS_Factory::load( 'MS_Model_Addon' );
		$model->refresh();
		unset( $model->active[ $addon ] );
		$model->save();

		do_action( 'ms_model_addon_disable', $addon, $model );
	}

	/**
	 * Toggle add-on type status in the plugin.
	 *
	 * @since  1.0.0
	 *
	 * @var string $addon The add-on type.
	 */
	static public function toggle_activation( $addon, $value = null ) {
		$model = MS_Factory::load( 'MS_Model_Addon' );
		if ( null === $value ) {
			$value = self::is_enabled( $addon );
		}

		if ( $value ) {
			$model->disable( $addon );
		} else {
			$model->enable( $addon );
		}

		do_action( 'ms_model_addon_toggle_activation', $addon, $model );
	}

	/**
	 * Enable add-on necessary to membership.
	 *
	 * @since  1.0.0
	 *
	 * @var string $addon The add-on type.
	 */
	public function auto_config( $membership ) {
		if ( $membership->trial_period_enabled ) {
			$this->enable( self::ADDON_TRIAL );
		}

		do_action( 'ms_model_addon_auto_config', $membership, $this );
	}

	/**
	 * Returns a list of all registered Add-Ons.
	 * Alias for the `get_addons()` function.
	 *
	 * @since  1.0.0
	 * @return array List of all registered Add-ons.
	 */
	public function get_addon_list() {
		return self::get_addons();
	}

	/**
	 * Returns Add-On details for the core add-ons in legacy format.
	 * New Add-ons are stored in the /app/addon folder and use the
	 * ms_model_addon_register hook to provide these informations.
	 *
	 *    **       This function should not be extended       **
	 *    **  Create new Add-ons in the app/addon/ directory  **
	 *
	 * @since  1.0.0
	 * @return array List of Add-ons
	 */
	static private function get_core_list() {
		$settings = MS_Factory::load( 'MS_Model_Settings' );

		$options_text = sprintf(
			'<i class="dashicons dashicons dashicons-admin-settings"></i> %s',
			__( 'Optionen verfügbar', 'membership2' )
		);

		$list[ self::ADDON_MULTI_MEMBERSHIPS ] = (object) array(
			'name' 			=> __( 'Mehrfachmitgliedschaften', 'membership2' ),
			'description' 	=> __( 'Deine Mitglieder können gleichzeitig mehreren Mitgliedschaften beitreten.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-forms',
		);

		$list[ self::ADDON_TRIAL ] = (object) array(
			'name' 			=> __( 'Test Zeitraum', 'membership2' ),
			'description'	=> __( 'Ermögliche Deinen Mitgliedern, sich für eine kostenlose Testversion der Mitgliedschaft anzumelden. Testdetails können für jede Mitgliedschaft separat konfiguriert werden.', 'membership2' ),
		);

		$list[ self::ADDON_POST_BY_POST ] = (object) array(
			'name'			=> __( 'Einzelne Beiträge', 'membership2' ),
			'description' 	=> __( 'Schütze einzelne Beiträge anstelle von Kategorien.', 'membership2' ),
		);

		$list[ self::ADDON_HIDE_PAGES_FROM_SEARCH ] = (object) array(
			'name' 			=> __( 'Seiten vor der Suche ausblenden', 'membership2' ),
			'description' 	=> __( 'Schließe geschützte Seiten von der Webseiten-Suche aus.', 'membership2' ),
		);

		$list[ self::ADDON_CPT_POST_BY_POST ] = (object) array(
			'name' 			=> __( 'Einzelne benutzerdefinierte Beiträge', 'membership2' ),
			'description' 	=> __( 'Schütze einzelne Beiträge eines benutzerdefinierten Beitrags-Typs.', 'membership2' ),
		);

		$list[ self::ADDON_MEDIA ] = (object) array(
			'name' 			=> __( 'Medienschutz', 'membership2' ),
			'description' 	=> __( 'Schütze Bilder und andere Inhalte der Medienbibliothek.', 'membership2' ),
			'footer' 		=> $options_text,
			'icon' 			=> 'dashicons dashicons-admin-media',
			'class' 		=> 'ms-options',
			'details' 		=> array(
				array(
					'id' 		=> 'masked_url',
					'before' 	=> esc_html( trailingslashit( get_option( 'home' ) ) ),
					'type' 		=> MS_Helper_Html::INPUT_TYPE_TEXT,
					'title' 	=> __( 'Maskierte Download URL', 'membership2' ),
					'value' 	=> $settings->downloads['masked_url'],
					'data_ms' 	=> array(
						'field' 	=> 'masked_url',
						'action' 	=> MS_Controller_Settings::AJAX_ACTION_UPDATE_SETTING,
						'_wpnonce' 	=> true, // Nonce will be generated from 'action'
					),
				),
				array(
					'id' 		=> 'protection_type',
					'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO,
					'title' 	=> __( 'Schutzmethode', 'membership2' ),
					'desc' 		=> __( 'Du kannst die Art und Weise ändern, in der Mitgliedschaften die Standard-URL zu Deinen WordPress-Medienbibliotheksdateien ändert.<br>Dies geschieht zum besseren Schutz, indem Du den tatsächlichen Dateinamen und den Pfad ausblendest.', 'membership2' ),
					'value' 	=> $settings->downloads['protection_type'],
					'field_options' => MS_Rule_Media_Model::get_protection_types(),
					'data_ms' 	=> array(
						'field' 	=> 'protection_type',
						'action' 	=> MS_Controller_Settings::AJAX_ACTION_UPDATE_SETTING,
						'_wpnonce' 	=> true, // Nonce will be generated from 'action'
					),
				),
				array(
					'id' 		=> 'advanced_protection',
					'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
					'title' 	=> __( 'Erweiterter Medienschutz', 'membership2' ),
					'desc' 		=> __( 'Aktiviere diese Option, um auf der Einstellungsseite "Mitgliedschaften" eine neue Registerkarte hinzuzufügen, auf der Du den erweiterten Medienschutz für alle hochgeladenen Dateien manuell festlegen kannst', 'membership2' ),
					'value' 	=> $settings->is_advanced_media_protection,
					'data_ms' 	=> array(
						'field' 	=> 'advanced_media_protection',
						'action' 	=> MS_Controller_Settings::AJAX_ACTION_UPDATE_SETTING,
						'_wpnonce' 	=> true, // Nonce will be generated from 'action'
					),
				),
				array(
					'id' 		=> 'advanced',
					'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
					'title' 	=> __( 'Einzelne Mediendateien schützen', 'membership2' ),
					'desc' 		=> __( 'Aktiviere diese Option, um eine neue Registerkarte in "Mitgliedschaften" anzuzeigen, auf der Du den Zugriff auf jedes Medienbibliothekselement manuell ändern kannst.<br>Standard: Wenn diese Option deaktiviert ist, steuert der übergeordnete Beitrag den Zugriff auf die Mediendatei.', 'membership2' ),
					'value' 	=> self::is_enabled( MS_Addon_Mediafiles::ID ),
					'data_ms' 	=> array(
						'action' 	=> MS_Controller_Addon::AJAX_ACTION_TOGGLE_ADDON,
						'field' 	=> 'active',
						'addon' 	=> MS_Addon_Mediafiles::ID,
						'_wpnonce' => true, // Nonce will be generated from 'action'
					),
				),
			),
		);

		$list[ self::ADDON_SHORTCODE ] = (object) array(
			'name' 			=> __( 'Shortcode-Schutz', 'membership2' ),
			'description' 	=> __( 'Schütze Shortcode-Ausgabe über Mitgliedschaften.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-editor-code',
		);

		$list[ self::ADDON_URL_GROUPS ] = (object) array(
			'name' 			=> __( 'URL-Schutz', 'membership2' ),
			'description' 	=> __( 'Der URL-Schutz schützt Seiten durch die URL. Diese Regel überschreibt alle anderen Regeln. Verwende sie daher sorgfältig.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-admin-links',
		);

		$list[ self::ADDON_AUTO_MSGS_PLUS ] = (object) array(
			'name' 			=> __( 'Zusätzliche automatisierte Nachrichten', 'membership2' ),
			'description' 	=> __( 'Sende Deinen Mitgliedern automatisierte E-Mail-Antworten für verschiedene zusätzliche Ereignisse.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-email',
		);

		$list[ self::ADDON_SPECIAL_PAGES ] = (object) array(
			'name' 			=> __( 'Spezielle Seiten schützen', 'membership2' ),
			'description' 	=> __( 'Ändere den Schutz spezieller Seiten wie der Suchergebnisse.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-admin-home',
		);

		$list[ self::ADDON_ADV_MENUS ] = (object) array(
			'name' 			=> __( 'Erweiterter Menüschutz', 'membership2' ),
			'description' 	=> __( 'Fügt den allgemeinen Einstellungen eine neue Option hinzu, die steuert, wie WordPress-Menüs geschützt werden.<br/>Schütze einzelne Menüelemente, ersetze den Inhalt von WordPress-Menüpositionen oder ersetze jedes Menü einzeln.', 'membership2' ),
			'footer' 		=> $options_text,
			'class' 		=> 'ms-options',
			'details' 		=> array(
				array(
					'id' 			=> 'menu_protection',
					'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
					'title' 		=> __( 'Wähle aus, wie Du Deine WordPress-Menüs schützen möchtest.', 'membership2' ),
					'value' 		=> $settings->menu_protection,
					'field_options' => array(
						'item' 			=> __( 'Einzelne Menüelemente schützen (Standard)', 'membership2' ),
						'menu' 			=> __( 'Einzelne Menüs ersetzen', 'membership2' ),
						'location' 		=> __( 'Überschreibe den Inhalt von Menüpositionen', 'membership2' ),
					),
					'data_ms'		 => array(
						'action'		=> MS_Controller_Settings::AJAX_ACTION_UPDATE_SETTING,
						'field' 		=> 'menu_protection',
					),
				),
			),
		);

		
		$list[ self::ADDON_ADMINSIDE ] = (object) array(
			'name' 			=> __( 'Admin-Seitenschutz', 'membership2' ),
			'description' 	=> __( 'Steuere die Seiten und sogar Meta-Boxen, auf die Mitglieder auf der Administratorseite zugreifen können.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-admin-network',
		);
		

		$list[ self::ADDON_MEMBERCAPS ] = (object) array(
			'name' 			=> __( 'Mitgliederfähigkeiten', 'membership2' ),
			'description' 	=> __( 'Verwalte Benutzerfunktionen auf Mitgliedschaftsebene.', 'membership2' ),
			'footer' 		=> $options_text,
			'class' 		=> 'ms-options',
			'icon' 			=> 'dashicons dashicons-admin-users',
			'details' 		=> array(
				array(
					'id' 		=> 'ms-toggle-' . self::ADDON_MEMBERCAPS_ADV,
					'title' 	=> __( 'Erweiterter Funktionsschutz', 'membership2' ),
					'desc' 		=> __( 'Ermöglicht den Schutz einzelner WordPress-Funktionen. Bei Aktivierung wird die Registerkarte "Benutzerrollen" durch eine Registerkarte "Mitgliederfunktionen" ersetzt, auf der Du einzelne WordPress-Funktionen anstelle von Rollen schützen und zuweisen kannst.', 'membership2' ),
					'type' 		=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
					'value'	 	=> self::is_enabled( self::ADDON_MEMBERCAPS_ADV ),
					'class' 	=> 'toggle-plugin',
					'ajax_data' => array(
						'action' 	=> MS_Controller_Addon::AJAX_ACTION_TOGGLE_ADDON,
						'field' 	=> 'active',
						'addon' 	=> self::ADDON_MEMBERCAPS_ADV,
					),
				),
			),
		);
		

		return $list;
	}


	/**
	 * Toggle Media htaccess creation
	 *
	 * @since 1.0.4
	 */
	public static function toggle_media_htaccess( $settings = false ) {
		if ( MS_Helper_Media::get_server() === 'apache' ) {
			if ( MS_Model_Addon::is_enabled( MS_Model_Addon::ADDON_MEDIA ) ) {
				if ( !$settings ) {
					$settings 	= MS_Factory::load( 'MS_Model_Settings' );
				}
				if ( $settings->is_advanced_media_protection ) {
					$direct_access 	= array( 'jpg', 'jpeg', 'png', 'gif', 'mp3', 'ogg' );
					if ( isset( $settings->downloads['direct_access'] ) ) {
						$direct_access = $settings->downloads['direct_access'];
					}
					MS_Helper_Media::write_htaccess_rule( $direct_access );
				} else {
					MS_Helper_Media::clear_htaccess();
				}
			} else {
				MS_Helper_Media::clear_htaccess();
			}
		}
		
	}
}
