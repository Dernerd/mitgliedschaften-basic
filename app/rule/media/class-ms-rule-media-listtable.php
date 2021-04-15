<?php
/**
 * Membership List Table
 *
 * @since  1.0.0
 */
class MS_Rule_Media_ListTable extends MS_Helper_ListTable_Rule {

	protected $id = MS_Rule_Media::RULE_ID;

	public function __construct( $model ) {
		parent::__construct( $model );
		$this->name['singular'] = __( 'Mediendatei', 'membership2' );
		$this->name['plural'] = __( 'Medien-Dateien', 'membership2' );
	}

	public function get_columns() {
		$columns = array(
			'cb' 		=> true,
			'name' 		=> __( 'Titel', 'membership2' ),
			'access' 	=> true,
			'file_type' => __( 'Typ', 'membership2' ),
			'post_date' => __( 'Hinzugefügt', 'membership2' ),
			'dripped' 	=> false,
		);

		return apply_filters(
			'ms_helper_listtable_' . $this->id . '_columns',
			$columns
		);
	}

	public function get_sortable_columns() {
		return apply_filters(
			'membership_helper_listtable_' . $this->id . '_sortable_columns',
			array(
				'name' => array( 'name', false ),
				'dripped' => array( 'dripped', false ),
			)
		);
	}

	public function column_name( $item ) {
		$actions = array(
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				get_edit_post_link( $item->id, true ),
				__( 'Bearbeiten', 'membership2' )
			),
			sprintf(
				'<a href="%s" target="_blank">%s</a>',
				get_permalink( $item->id ),
				__( 'Ansehen', 'membership2' )
			),
		);
		$actions = apply_filters(
			'ms_rule_' . $this->id . '_column_actions',
			$actions,
			$item
		);

		return sprintf(
			'%1$s %2$s',
			$item->post_title,
			$this->row_actions( $actions )
		);
	}

	public function column_post_date( $item, $column_name ) {
		return MS_Helper_Period::format_date(
			$item->post_date,
			__( 'Y/m/d', 'membership2' )
		);
	}

	public function column_file_type( $item, $column_name ) {
		$meta = wp_get_attachment_metadata( $item->id );

		if ( isset( $meta['file'] ) ) {
			$type 		= wp_check_filetype( $meta['file'] );
		} else {
			$the_file 	= get_attached_file( $item->id );
			$ext 		= pathinfo( $the_file, PATHINFO_EXTENSION );

			if ( $ext ) {
				$type = array(
					'ext' 	=> $ext,
					'type' 	=> filetype( $the_file ),
				);
			} else {
				// Fallback to 'jpg' if filetype cannot be determined.
				$type = array(
					'ext' 	=> 'jpg',
					'type' 	=> 'image/jpeg',
				);
			}
		}

		return $type['ext'];
	}

}
