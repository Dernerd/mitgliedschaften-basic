<?php

class MS_Rule_MemberCaps_View extends MS_View {

	public function to_html() {
		$membership = MS_Model_Membership::get_base();
		$rule = $membership->get_rule( MS_Rule_MemberCaps::RULE_ID );

		$rule_listtable = new MS_Rule_MemberCaps_ListTable( $rule );
		$rule_listtable->prepare_items();

		$header_data = array();
		$header_data['title'] = __( 'Weise Deinen Mitgliedern Funktionen zu', 'membership2' );
		$header_data['desc'] = array(
			__( 'Optimiere die Berechtigungen für Mitglieder, indem Du jeder Mitgliedschaft bestimmte Funktionen zuweist. Allen Mitgliedern dieser Mitgliedschaft werden die angegebenen Fähigkeiten gewährt.', 'membership2' ),
			__( 'Wichtig: Allen Benutzern, die sich nicht in diesen Mitgliedschaften befinden, wird jede geschützte Funktion entzogen!', 'membership2' ),
			__( 'Du solltest diese Regeln nur anwenden, wenn Du weist, was Du tust! Wenn Du die falschen Funktionen gewährst, ist Deine Website anfällig für Missbrauch. Aus Sicherheitsgründen haben wir bereits die wichtigsten Funktionen aus dieser Liste entfernt.', 'membership2' ),
		);

		$header_data = apply_filters(
			'ms_view_membership_protectedcontent_header',
			$header_data,
			MS_Rule_MemberCaps::RULE_ID,
			$this
		);

		ob_start();
		?>
		<div class="ms-settings">
			<?php
			MS_Helper_Html::settings_tab_header( $header_data );

			$rule_listtable->views();
			$rule_listtable->search_box( __( 'Fähigkeit', 'membership2' ) );
			?>
			<form action="" method="post">
				<?php
				$rule_listtable->display();

				do_action(
					'ms_view_membership_protectedcontent_footer',
					MS_Rule_MemberCaps::RULE_ID,
					$this
				);
				?>
			</form>
		</div>
		<?php

		MS_Helper_Html::settings_footer();
		return ob_get_clean();
	}

}