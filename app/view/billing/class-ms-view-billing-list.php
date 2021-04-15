<?php
/**
 * Renders Billing/Transaction History.
 *
 * Extends MS_View for rendering methods and magic methods.
 *
 * @since  1.0.0
 *
 * @package Membership2
 * @subpackage View
 */
class MS_View_Billing_List extends MS_View {

	/**
	 * Create view output.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function to_html() {
		$this->check_simulation();

		$buttons = array();

		$module = 'billing';
		if ( isset( $_GET['show'] ) ) {
			$module = $_GET['show'];
		}

		if ( ! $module ) {
			// Show a message if there are error-state transactions.
			$args = array( 'state' => 'err' );
			$error_count = MS_Model_Transactionlog::get_item_count( $args );

			if ( $error_count ) {
				if ( 1 == $error_count ) {
					$message = __( 'Eine Transaktion ist fehlgeschlagen. Bitte überprüfe %2$sdie Protokolle%3$s und entscheide, ob Du die Transaktion ignorieren oder manuell einer Rechnung zuweisen möchtest.', 'membership2' );
				} else {
					$message = __( '%1$s Transaktionen sind fehlgeschlagen. Bitte überprüfe %2$sdie Protokolle%3$s und entscheide, ob Du die Transaktion ignorieren oder manuell einer Rechnung zuweisen möchtest.', 'membership2' );
				}
				$review_url = MS_Controller_Plugin::get_admin_url(
					'billing',
					array(
						'show' => 'logs',
						'state' => 'err',
					)
				);

				mslib3()->ui->admin_message(
					sprintf(
						$message,
						$error_count,
						'<a href="' . $review_url . '">',
						'</a>'
					),
					'err'
				);
			}
		}

		// Decide which list to display in the Billings page.
		switch ( $module ) {
			// Transaction logs.
			case 'logs':
				$title = __( 'Transaktionsprotokolle', 'membership2' );

				$listview = MS_Factory::create( 'MS_Helper_ListTable_TransactionLog' );
				$listview->prepare_items();

				$buttons[] = array(
					'type' => MS_Helper_Html::TYPE_HTML_LINK,
					'url' => MS_Controller_Plugin::get_admin_url(
						'billing'
					),
					'value' => __( 'Rechnungen anzeigen', 'membership2' ),
					'class' => 'button',
				);
				break;

			// M1 Migration matching.
			case 'matching':
				$title = __( 'Automatischer Transaktionsabgleich', 'membership2' );

				$listview = MS_Factory::create( 'MS_Helper_ListTable_TransactionMatching' );
				$listview->prepare_items();

				$buttons[] = array(
					'type' => MS_Helper_Html::TYPE_HTML_LINK,
					'url' => MS_Controller_Plugin::get_admin_url(
						'billing'
					),
					'value' => __( 'Rechnungen anzeigen', 'membership2' ),
					'class' => 'button',
				);
				$buttons[] = array(
					'type' => MS_Helper_Html::TYPE_HTML_LINK,
					'url' => MS_Controller_Plugin::get_admin_url(
						'billing',
						array( 'show' => 'logs' )
					),
					'value' => __( 'Transaktionsprotokolle anzeigen', 'membership2' ),
					'class' => 'button',
				);

				break;

			// Default billings list.
			case 'billing':
			default:
				$title = __( 'Abrechnung', 'membership2' );

				$listview = MS_Factory::create( 'MS_Helper_ListTable_Billing' );
				$listview->prepare_items();

				$buttons[] = array(
					'id' => 'add_new',
					'type' => MS_Helper_Html::TYPE_HTML_LINK,
					'url' => MS_Controller_Plugin::get_admin_url(
						'billing',
						array(
							'action' => MS_Controller_Billing::ACTION_EDIT,
							'invoice_id' => 0,
						)
					),
					'value' => __( 'Neue Rechnung erstellen', 'membership2' ),
					'class' => 'button',
				);
				$buttons[] = array(
					'type' => MS_Helper_Html::TYPE_HTML_LINK,
					'url' => MS_Controller_Plugin::get_admin_url(
						'billing',
						array( 'show' => 'logs' )
					),
					'value' => __( 'Transaktionsprotokolle anzeigen', 'membership2' ),
					'class' => 'button',
				);

				if ( ! empty( $_GET['gateway_id'] ) ) {
					$gateway = MS_Model_Gateway::factory( $_GET['gateway_id'] );
					if ( $gateway->name ) {
						$title .= ' - ' . $gateway->name;
					}
				}
				break;
		}

		if ( 'matching' != $module ) {
			if ( MS_Model_Import::can_match() ) {
				$btn_label = __( 'Richte den automatischen Abgleich ein', 'membership2' );
				$btn_class = 'button';
			} else {
				$btn_label = '(' . __( 'Richte den automatischen Abgleich ein', 'membership2' ) . ')';
				$btn_class = 'button button-link';
			}

			$buttons[] = array(
				'type' => MS_Helper_Html::TYPE_HTML_LINK,
				'url' => MS_Controller_Plugin::get_admin_url(
					'billing',
					array( 'show' => 'matching' )
				),
				'value' => $btn_label,
				'class' => $btn_class,
			);
		}

		// Default list view part - dislay prepared values from above.
		ob_start();
		?>

		<div class="wrap ms-wrap ms-billing">
			<?php
			MS_Helper_Html::settings_header(
				array(
					'title' => $title,
					'title_icon_class' => 'wpmui-fa wpmui-fa-credit-card',
				)
			);
			?>
			<div>
				<?php
				$buttons = apply_filters( 'ms_view_billing_list_buttons', $buttons, $this );
				foreach ( $buttons as $button ) {
					MS_Helper_Html::html_element( $button );
				}
				?>
			</div>
			<?php
			$listview->views();
			do_action( 'ms_view_billing_list_before_search', $module, $this );
			$listview->search_box(
				__( 'Benutzer', 'membership2' ),
				'search'
			);
			do_action( 'ms_view_billing_list_after_search', $module, $this );
			?>
			<form action="" method="post">
				<?php $listview->display(); ?>
			</form>
		</div>

		<?php
		$html = ob_get_clean();

		return apply_filters(
			'ms_view_billing_list',
			$html,
			$this
		);
	}
}