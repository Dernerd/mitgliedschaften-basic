<?php
/**
 * Add-on: Allow Search-Engines to index protected content.
 *
 * @since  1.0.1.0
 */
class MS_Addon_Searchindex extends MS_Addon {

	/**
	 * The Add-on ID
	 *
	 * @since  1.0.1.0
	 */
	const ID = 'addon_searchindex';


	/**
	 * Checks if the current Add-on is enabled.
	 *
	 * @since  1.0.1.0
	 * @return bool
	 */
	static public function is_active() {
		return false;
	}

	/**
	 * Returns the Add-on ID (self::ID).
	 *
	 * @since  1.0.1.0
	 * @return string
	 */
	public function get_id() {
		return self::ID;
	}

	/**
	 * Initializes the Add-on. Always executed.
	 *
	 * @since  1.0.1.0
	 */
	public function init() {
		MS_Model_Addon::disable( self::ID );
	}

	/**
	 * Registers the Add-On.
	 *
	 * @since  1.0.1.0
	 * @param  array $list The Add-Ons list.
	 * @return array The updated Add-Ons list.
	 */
	public function register( $list ) {
		$list[ self::ID ] = (object) array(
			'name' 			=> __( 'Suchindex', 'membership2' ),
			'description' 	=> __( 'Suchmaschinen erlauben, geschützten Inhalt zu indizieren.', 'membership2' ),
			'icon' 			=> 'wpmui-fa wpmui-fa-search',
			'details' 		=> array(
				array(
					'type' 	=> MS_Helper_Html::TYPE_HTML_TEXT,
					'value' => sprintf(
						'%s<br><br>%s',
						__( 'Die spezielle Mitgliedschaft "<b>Suchindex</b>" ist auf Deiner Seite "Schutzregeln" verfügbar.<br>Alle Inhalte, die für diese Mitgliedschaft verfügbar gemacht werden, sind für Suchmaschinen-Crawler immer sichtbar.', 'membership2' ),
						__( 'Unterstützte Suchmaschinen: Google, Yahoo, Bing', 'membership2' )
					),
				),
				array(
					'id' 	=> 'first_click_free',
					'type' 	=> MS_Helper_Html::INPUT_TYPE_RADIO_SLIDER,
					'title' => __( 'Erster Klick Frei', 'membership2' ),
					'desc' 	=> sprintf(
						__( 'Alle Inhalte, die für Suchmaschinen verfügbar sind, sind auch für alle Besucher verfügbar, die <b>direkt von einer Suchmaschine kommen</b> (Richtlinie "%sErster Klick Frei%s"). <br> Durch Deaktivieren dieser Funktion werden möglicherweise Strafen für Deine Webseite verhängt von Google', 'membership2' ),
						'<a href="http://googlewebmastercentral.blogspot.com/2008/10/first-click-free-for-web-search.html" target="_blank">',
						'</a>'
					),
					'class' 		=> 'has-labels',
					'before' 		=> __( 'Deaktiviere "Erster Klick Frei"', 'membership2' ),
					'after' 		=> __( 'Aktiviere "Erster Klick Frei"', 'membership2' ),
					'value' 		=> true,
					'wrapper_class' => 'disabled',
				),
			),
			'action' => array( __( 'Pro Version', 'membership2' ) ),
		);

		return $list;
	}

}
