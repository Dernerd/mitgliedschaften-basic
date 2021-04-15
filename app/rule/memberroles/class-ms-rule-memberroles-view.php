<?php

class MS_Rule_MemberRoles_View extends MS_View {

	public function to_html() {
		$membership = MS_Model_Membership::get_base();
		$rule = $membership->get_rule( MS_Rule_MemberRoles::RULE_ID );

		$rule_listtable = new MS_Rule_MemberRoles_ListTable( $rule );
		$rule_listtable->prepare_items();

		$header_data['title'] 	= __( 'Weise Deinen Mitgliedern Benutzerrollen zu', 'membership2' );
		$header_data['desc'] 	= array(
			__( 'Wenn Du einer Rolle eine Mitgliedschaft zuweist, wird diese Rolle allen Mitgliedern dieser Mitgliedschaft hinzugefügt. Du kannst einer einzelnen Mitgliedschaft sogar mehrere Rollen zuweisen.', 'membership2' ),
			__( 'Aus Sicherheitsgründen kann die Administratorrolle keiner Mitgliedschaft zugewiesen werden.', 'membership2' ),
		);

		$header_data = apply_filters(
			'ms_view_membership_protectedcontent_header',
			$header_data,
			MS_Rule_MemberRoles::RULE_ID,
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
					MS_Rule_MemberRoles::RULE_ID,
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