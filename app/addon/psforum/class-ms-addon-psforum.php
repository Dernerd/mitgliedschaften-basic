<?php
class MS_Addon_Psforum extends MS_Addon {

	/**
	 * The Add-on ID
	 *
	 * @since  1.0.0
	 */
	const ID = 'psforum';

	/**
	 * Checks if the current Add-on is enabled
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	static public function is_active() {
		if ( ! self::psforum_active()
			&& MS_Model_Addon::is_enabled( self::ID )
		) {
			$model = MS_Factory::load( 'MS_Model_Addon' );
			$model->disable( self::ID );
		}

		return MS_Model_Addon::is_enabled( self::ID );
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
	 * @since  1.0.0
	 */
	public function init() {

		if ( self::is_active() ) {
			// Always remove psforum from MS_Rule_CptGroup_Model.
			$this->add_filter(
				'ms_rule_cptgroup_model_get_excluded_content',
				'exclude_psforum_cpts'
			);

			$this->add_filter(
				'ms_controller_protection_tabs',
				'rule_tabs'
			);

			//Dripped content register rule
			MS_Model_Rule::register_rule(
				self::ID,
				__CLASS__,
				__( 'PSForum', 'membership2' ),
				50,
				true // can be dripped
			);

			MS_Factory::load( 'MS_Addon_Psforum_Rule' );
		}
	}

	/**
	 * Registers the Add-On
	 *
	 * @since  1.0.0
	 * @param  array $list The Add-Ons list.
	 * @return array The updated Add-Ons list.
	 */
	public function register( $list ) {
		$list[ self::ID ] = (object) array(
			'name' 			=> __( 'PSForum Integration', 'membership2' ),
			'description' 	=> __( 'Aktiviere die Integration von PSForum-Regeln.', 'membership2' ),
			'icon' 			=> 'dashicons dashicons-format-chat',
		);

		if ( ! self::psforum_active() ) {
			$list[ self::ID ]->description .= sprintf(
				'<br /><b>%s</b>',
				__( 'Aktiviere PSForum, um dieses Add-On zu verwenden', 'membership2' )
			);
			$list[ self::ID ]->action = '-';
		}

		return $list;
	}

	/**
	 * Returns true, when the BuddyPress plugin is activated.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	static public function psforum_active() {
		return class_exists( 'PSForum' );
	}

	/**
	 * Add psforum rule tabs in membership level edit.
	 *
	 * @since  1.0.0
	 *
	 * @filter ms_controller_membership_get_tabs
	 *
	 * @param array $tabs The current tabs.
	 * @param int $membership_id The membership id to edit
	 * @return array The filtered tabs.
	 */
	public function rule_tabs( $tabs ) {
		$rule 			= MS_Addon_Psforum_Rule::RULE_ID;
		$tabs[ $rule  ] = true;

		return $tabs;
	}

	/**
	 * Exclude PS Forum custom post type from MS_Rule_CptGroup_Model.
	 *
	 * @since  1.0.0
	 *
	 * @filter ms_rule_cptgroup_model_get_excluded_content
	 *
	 * @param array $excluded The current excluded ctps.
	 * @return array The filtered excluded ctps.
	 */
	public function exclude_psforum_cpts( $excluded ) {
		$excluded = array_merge(
			$excluded,
			MS_Addon_Psforum_Rule_Model::get_bb_cpt()
		);

		return $excluded;
	}
}
