<?php
/**
 * Add-on: Enable the Pro-Rating function.
 *
 * @since  1.0.1.0
 */
class MS_Addon_Prorate extends MS_Addon {

	/**
	 * The Add-on ID
	 *
	 * @since  1.0.1.0
	 */
	const ID = 'addon_prorate';

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
			'name' 			=> __( 'Pro-Rating', 'membership2' ),
			'description' 	=> __( 'Pro-Rate frühere Zahlungen beim Wechsel der Mitgliedschaft.', 'membership2' ),
			'icon' 			=> 'wpmui-fa wpmui-fa-money',
			'details' 		=> array(
				array(
					'type' 	=> MS_Helper_Html::TYPE_HTML_TEXT,
					'value' => __( 'Pro-Rating wird angewendet, wenn ein Benutzer eine Mitgliedschaft aktualisiert/herabstuft. Nicht, wenn er in zwei Schritten kündigt und abonniert.<br><br>Grund:<br>Wenn ein Benutzer eine Mitgliedschaft kündigt, behält er den Zugriff auf die Mitgliedschaft bis zum Ablauf des aktuellen Zeitraums (Ausnahme: Der permanente Zugriff läuft sofort ab).', 'membership2' ),
				),
				array(
					'type' 	=> MS_Helper_Html::TYPE_HTML_TEXT,
					'title' => '<b>' . __( 'Wenn das Add-on für mehrere Mitgliedschaften deaktiviert ist', 'membership2' ) . '</b>',
					'value' => __( 'Durch das Ändern einer Mitgliedschaft laufen die alten Mitgliedschaften immer ab und es wird in einem Schritt <em>ein Abonnement für die neue Mitgliedschaft</em> hinzugefügt. Pro Rating wird hier immer angewendet.', 'membership2' ),
				),
				array(
					'type' 	=> MS_Helper_Html::TYPE_HTML_TEXT,
					'title' => '<b>' . __( 'Wenn das Add-on für mehrere Mitgliedschaften aktiviert ist', 'membership2' ) . '</b>',
					'value' => __( 'Nur wenn Du die Einstellung "Abbrechen und Pro-Rate" in den Einstellungen für Upgrade-Pfade der Mitgliedschaft manuell festlegst, wird die Änderung als Upgrade/Downgrade erkannt. In diesem Fall wird die alte Mitgliedschaft deaktiviert, wenn das neue Abonnement erstellt wird.<br>Wenn Du diese Option nicht festlegst, gilt die Standardlogik: Der Benutzer kann für die von ihm bezahlte Dauer auf die alte Mitgliedschaft zugreifen, auch wenn er früher kündigt. Also kein Pro-Rating dann.', 'membership2' ),
				),
			),
			'action' 		=> array( __( 'Pro Version', 'membership2' ) ),
		);
		return $list;
	}

}
