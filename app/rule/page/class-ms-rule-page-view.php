<?php

class MS_Rule_Page_View extends MS_View {

	public function to_html() {
		$membership = MS_Model_Membership::get_base();
		$rule = $membership->get_rule( MS_Rule_Page::RULE_ID );
		$rule_listtable = new MS_Rule_Page_ListTable( $rule );
		$rule_listtable->prepare_items();

		$header_data = apply_filters(
			'ms_view_membership_protectedcontent_header',
			array(
				'title' => __( 'Wende Schutz auf Seiten an und gewähre Mitgliedern Zugriff', 'membership2' ),
				'desc' 	=> __( 'Alle Seiten, auf die kein Inhaltsschutz angewendet wurde, sind für alle sichtbar', 'membership2' ),
			),
			MS_Rule_Page::RULE_ID,
			$this
		);

		ob_start();
		?>
		<div class="ms-settings">
			<?php
			MS_Helper_Html::settings_tab_header( $header_data );

			$rule_listtable->views();
			$rule_listtable->search_box( __( 'Seiten', 'membership2' ) );
			?>
			<form action="" method="post">
				<?php
				$rule_listtable->display();

				do_action(
					'ms_view_membership_protectedcontent_footer',
					MS_Rule_Page::RULE_ID,
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