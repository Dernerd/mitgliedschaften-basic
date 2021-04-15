<?php
/**
 * View.
 *
 * @package Membership2
 */

/**
 * Displays the Import preview.
 *
 * @since  1.0.0
 */
class MS_View_Settings_Import_Settings extends MS_View {

	/**
	 * Displays the import preview form.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function to_html() {
		$data = apply_filters(
			'ms_import_preview_data_before',
			$this->data['model']->source
		);
		$compact = ! empty( $this->data['compact'] );
		if ( ! is_object( $data ) ) {
			$data = (object) array(
				'memberships' 	=> array(),
				'members' 		=> array(),
				'notes' 		=> array(),
				'settings' 		=> array(),
				'source' 		=> '',
				'export_time' 	=> '',
			);
		}

		// Converts object to array.
		if ( is_a( $data, 'SimpleXMLElement' ) ) {
			$data 	= MS_Helper_Utility::xml2array( $data );
			$data 	= ( object ) $data;
		}
		$data->memberships 	= isset( $data->memberships ) ? (array) $data->memberships : array();
		$data->members 		= isset( $data->members ) ? (array) $data->members : array();

		$fields = $this->prepare_fields( $data );

		if ( $compact ) {
			$overview_box = array(
				$fields['batchsize'],
				$fields['sep'],
				$fields['clear_all'],
				$fields['skip'],
				$fields['import'],
			);
		} else {
			$overview_box = array(
				$fields['details'],
				$fields['sep'],
				$fields['batchsize'],
				$fields['sep'],
				$fields['clear_all'],
				$fields['back'],
				$fields['import'],
				$fields['download'],
			);
		}

		ob_start();
		MS_Helper_Html::settings_box(
			$overview_box,
			__( 'Importübersicht', 'membership2' )
		);

		if ( ! $compact ) {
			if ( !empty( $data->memberships ) ) {
				MS_Helper_Html::settings_box(
					array( $fields['memberships'] ),
					__( 'Liste aller Mitgliedschaften', 'membership2' ),
					'',
					'open'
				);
			}
			
			if ( !empty( $data->members ) ) {
				MS_Helper_Html::settings_box(
					array( $fields['members'] ),
					__( 'Liste aller Mitglieder', 'membership2' ),
					'',
					'open'
				);
			}

			if ( isset( $data->settings ) ) {
				MS_Helper_Html::settings_box(
					array( $fields['settings'] ),
					__( 'Importierte Einstellungen', 'membership2' ),
					'',
					'open'
				);
			}
		}

		echo '<script>window._ms_import_obj = ' . json_encode( $data ) . '</script>';

		$html = ob_get_clean();

		return apply_filters(
			'ms_import_preview_object',
			$html,
			$data
		);
	}

	/**
	 * Prepare the HTML fields that can be displayed
	 *
	 * @since  1.0.0
	 *
	 * @param  object $data The import data object.
	 * @return array
	 */
	protected function prepare_fields( $data ) {
		// List of known Membership types; used to display the nice-name.
		$ms_types 		= MS_Model_Membership::get_types();
		$ms_paytypes 	= MS_Model_Membership::get_payment_types();

		$total_memberships = 0;
		$total_members = 0;

		// Prepare the "Memberships" table.
		$memberships = array();
		if ( isset ( $data->memberships ) && !empty( $data->memberships ) ) {
			$memberships = array(
				array(
					__( 'Name der Mitgliedschaft', 'membership2' ),
					__( 'Art der Mitgliedschaft', 'membership2' ),
					__( 'Zahlungsart', 'membership2' ),
					__( 'Beschreibung', 'membership2' ),
				),
			);

			foreach ( $data->memberships as $item ) {
				if ( is_array( $item ) && isset( $item['membership'] ) && is_array( $item['membership'] ) ) {
					foreach ( $item['membership'] as $membership ) {
						$membership 	= ( object ) $membership;
						$output 		= MS_Helper_Import::membership_to_view( $membership, $ms_types, $ms_paytypes );
						$memberships[] 	= $output;
						$total_memberships++;
					}
				} else {
					$output 		= MS_Helper_Import::membership_to_view( $item, $ms_types, $ms_paytypes );
					$memberships[] 	= $output;
					$total_memberships++;
				}
				
			}
		}

		$members = array();
		if ( isset ( $data->members ) && !empty( $data->members )  ) {
			// Prepare the "Members" table.
			$members = array(
				array(
					__( 'Benutzername', 'membership2' ),
					__( 'Email', 'membership2' ),
					__( 'Abonnements', 'membership2' ),
					__( 'Rechnungen', 'membership2' ),
				),
			);

			foreach ( $data->members as $item ) {
				if ( is_array( $item ) && isset( $item['member'] ) && is_array( $item['member'] ) ) {
					foreach ( $item['member'] as $member ) {
						$member 	= ( object ) $member;
						$output 	= MS_Helper_Import::member_to_view( $member );
						$members[] 	= $output;
						$total_members++;
					}
				} else {
					$output = MS_Helper_Import::member_to_view( $item );
					$members[] = $output;
					$total_members++;
				}
			}
		}

		$settings = array();
		if ( isset ( $data->settings ) ) {
			foreach ( $data->settings as $setting => $value ) {
				switch ( $setting ) {
					case 'addons':
						$model = MS_Factory::load( 'MS_Model_Addon' );
						$list = $model->get_addon_list();
						$code = '';
						foreach ( $value as $addon => $state ) {
							if ( $state ) {
								$code .= __( 'Aktivieren: ', 'membership2' );
							} else {
								$code .= __( 'Deaktivieren: ', 'membership2' );
							}
							$code .= $list[ $addon ]->name . '<br/>';
						}
						$settings[] = array(
							__( 'Add-Ons', 'membership2' ),
							$code,
						);
						break;
				}
			}
		}

		// Prepare the return value.
		$fields = array();

		// Export-Notes.
		$notes = '';
		if ( isset( $data->notes ) ) {
			if ( is_scalar( $data->notes ) ) {
				$notes = array( $data->notes );
			}

			$in_sub = false;
			$notes = '<ul class="ms-import-notes">';
			foreach ( $data->notes as $line => $text ) {
				if ( is_array( $text ) && isset( $text['note'] ) ) {
					$text = $text['note'];
				}
				$is_sub = ( strpos( $text, '- ' ) === 0 );
				if ( $in_sub != $is_sub ) {
					$in_sub = $is_sub;
					if ( $is_sub ) {
						$notes .= '<ul>';
					} else {
						$notes .= '</ul>';
					}
				}
				if ( $in_sub ) {
					$text = substr( $text, 2 );
				}
				$notes .= '<li>' . $text;
			}
			$notes .= '</ul>';
		}

		if ( ( isset( $data->memberships ) && !empty( $data->memberships ) ) && ( isset( $data->members ) && !empty( $data->members ) ) ) {
			
			$fields['details'] = array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TABLE,
				'class' => 'ms-import-preview',
				'value' => array(
					array(
						__( 'Datenquelle', 'membership2' ),
						$data->source .
						' &emsp; <small>' .
						sprintf(
							__( 'exportiert auf %1$s', 'membership2' ),
							$data->export_time
						) .
						'</small>',
					),
					array(
						__( 'Inhalt', 'membership2' ),
						sprintf(
							_n(
								'%1$s Mitgliedschaft',
								'%1$s Mitgliedschaften',
								$total_memberships,
								'membership2'
							),
							'<b>' . $total_memberships . '</b>'
						) . ' / ' . sprintf(
							_n(
								'%1$s Mitglied',
								'%1$s Mitglieder',
								$total_members,
								'membership2'
							),
							'<b>' . $total_members . '</b>'
						),
					),
				),
				'field_options' => array(
					'head_col' 		=> true,
					'head_row' 		=> false,
					'col_class' 	=> array( 'preview-label', 'preview-data' ),
				),
			);
		} else if ( isset( $data->memberships ) && !empty( $data->memberships ) ) {
			$fields['details'] = array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TABLE,
				'class' => 'ms-import-preview',
				'value' => array(
					array(
						__( 'Datenquelle', 'membership2' ),
						$data->source .
						' &emsp; <small>' .
						sprintf(
							__( 'exportiert am %1$s', 'membership2' ),
							$data->export_time
						) .
						'</small>',
					),
					array(
						__( 'Inhalt', 'membership2' ),
						sprintf(
							_n(
								'%1$s Mitgliedschaft',
								'%1$s Mitgliedschaften',
								$total_memberships,
								'membership2'
							),
							'<b>' . $total_memberships . '</b>'
						)
					),
				),
				'field_options' => array(
					'head_col' 		=> true,
					'head_row' 		=> false,
					'col_class' 	=> array( 'preview-label', 'preview-data' ),
				),
			);
		} else if ( isset( $data->members ) && !empty( $data->members ) ) {
			$fields['details'] = array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TABLE,
				'class' => 'ms-import-preview',
				'value' => array(
					array(
						__( 'Datenquelle', 'membership2' ),
						$data->source .
						' &emsp; <small>' .
						sprintf(
							__( 'exportiert am %1$s', 'membership2' ),
							$data->export_time
						) .
						'</small>',
					),
					array(
						__( 'Inhalt', 'membership2' ),
						sprintf(
							_n(
								'%1$s Mitglied',
								'%1$s Mitglieder',
								$total_members,
								'membership2'
							),
							'<b>' . $total_members . '</b>'
						),
					),
				),
				'field_options' => array(
					'head_col' 	=> true,
					'head_row' 	=> false,
					'col_class' => array( 'preview-label', 'preview-data' ),
				),
			);
		}

		if ( ! empty( $notes ) ) {
			$fields['details']['value'][] = array(
				__( 'Bitte beachte', 'membership2' ),
				$notes,
			);
		}

		$batchsizes = array(
			1 	=> __( 'Jedes Element für sich', 'membership2' ),
			10 	=> __( 'Klein (10 Elemente)', 'membership2' ),
			30 	=> __( 'Normal (30 Elemente)', 'membership2' ),
			100 => __( 'Groß (100 Elemente)', 'membership2' ),
		);

		$fields['batchsize'] = array(
			'id' 			=> 'batchsize',
			'type' 			=> MS_Helper_Html::INPUT_TYPE_SELECT,
			'title' 		=> __( 'Stapelgröße für den Import', 'membership2' ),
			'desc' 			=> __( 'Große Stapel werden schneller verarbeitet, können jedoch zu PHP-Speicherfehlern führen.', 'membership2' ),
			'value' 		=> 10,
			'field_options' => $batchsizes,
			'class' 		=> 'sel-batchsize',
		);

		$fields['clear_all'] = array(
			'id' 	=> 'clear_all',
			'type' 	=> MS_Helper_Html::INPUT_TYPE_CHECKBOX,
			'title' => __( 'Ersetze aktuellen Inhalt durch Importdaten (entfernt vorhandene Mitgliedschaften/Mitglieder vor dem Importieren von Daten)', 'membership2' ),
			'class' => 'widefat',
		);

		if ( !empty ( $memberships ) ) {
			$fields['memberships'] = array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TABLE,
				'class' => 'ms-import-preview',
				'value' => $memberships,
				'field_options' => array(
					'head_col' 		=> false,
					'head_row' 		=> true,
					'col_class' 	=> array( 'preview-name', 'preview-type', 'preview-pay-type', 'preview-desc' ),
				),
			);
		}

		if ( !empty ( $members ) ) {
			$fields['members'] = array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TABLE,
				'class' => 'ms-import-preview',
				'value' => $members,
				'field_options' => array(
					'head_col' 		=> false,
					'head_row' 		=> true,
					'col_class' 	=> array( 'preview-name', 'preview-email', 'preview-count', 'preview-count' ),
				),
			);
		}
		if ( !empty( $settings ) ) {
			$fields['settings'] = array(
				'type' 	=> MS_Helper_Html::TYPE_HTML_TABLE,
				'class' => 'ms-import-preview',
				'value' => $settings,
				'field_options' => array(
					'head_col' 	=> true,
					'head_row' 	=> false,
				),
			);
		}

		$fields['sep'] = array(
			'type' => MS_Helper_Html::TYPE_HTML_SEPARATOR,
		);

		$fields['back'] = array(
			'type' 	=> MS_Helper_Html::TYPE_HTML_LINK,
			'class' => 'wpmui-field-button button',
			'value' => __( 'Abbrechen', 'membership2' ),
			'url' 	=> $_SERVER['REQUEST_URI'],
		);

		$fields['skip'] = array(
			'type' 	=> MS_Helper_Html::TYPE_HTML_LINK,
			'class' => 'wpmui-field-button button',
			'value' => __( 'Überspringen', 'membership2' ),
			'url' 	=> MS_Controller_Plugin::get_admin_url(
				false,
				array( 'skip_import' => 1 )
			),
		);

		$fields['import'] = array(
			'id' 			=> 'btn-import',
			'type' 			=> MS_Helper_Html::INPUT_TYPE_BUTTON,
			'value' 		=> __( 'Importieren', 'membership2' ),
			'button_value' 	=> MS_Controller_Import::AJAX_ACTION_IMPORT,
			'button_type' 	=> 'submit',
		);

		$fields['download'] = array(
			'id' 	=> 'btn-download',
			'type' 	=> MS_Helper_Html::INPUT_TYPE_BUTTON,
			'value' => __( 'Als Exportdatei herunterladen', 'membership2' ),
			'class' => 'button-link',
		);

		return $fields;
	}
}
