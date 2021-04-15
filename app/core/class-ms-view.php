<?php
/**
 * Abstract class for all Views.
 *
 * All views will extend or inherit from the MS_View class.
 * Methods of this class will prepare and output views.
 *
 * @since  1.0.0
 * @package Membership2
 * @subpackage View
 */
class MS_View extends MS_Hooker {

	/**
	 * The storage of all data associated with this render.
	 *
	 * @since  1.0.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Flag is set to true while in Simulation mode.
	 *
	 * @since  1.0.0
	 *
	 * @var bool
	 */
	static protected $is_simulating = false;

	/**
	 * Constructor.
	 *
	 * @since  1.0.0
	 *
	 * @param array $data The data what has to be associated with this render.
	 */
	public function __construct( $data = array() ) {
		static $Simulate = null;

		$this->data = $data;

		/**
		 * Actions to execute when constructing the parent View.
		 *
		 * @since  1.0.0
		 * @param object $this The MS_View object.
		 */
		do_action( 'ms_view_construct', $this );

		if ( null === $Simulate && MS_Model_Simulate::can_simulate() ) {
			$Simulate = MS_Factory::load( 'MS_Model_Simulate' );
			self::$is_simulating = $Simulate->is_simulating();
		}

		$this->run_action( 'wp_enqueue_scripts', 'enqueue_scripts' );
	}

	/**
	 * Displays a note while simulation mode is enabled.
	 *
	 * @since  1.0.0
	 */
	protected function check_simulation() {
		if ( self::$is_simulating ) :
		?>
		<div class="error below-h2">
			<p>
				<strong><?php _e( 'Du befindest Dich im Simulationsmodus!', 'membership2' ); ?></strong>
			</p>
			<p>
				<?php _e( 'Der hier angezeigte Inhalt kann aufgrund simulierter Einschränkungen geändert werden.', 'membership2' ); ?><br />
				<?php
				printf(
					__( 'Wir empfehlen die %sSimulation zu verlassen%s, bevor Du Änderungen vornimmst!', 'membership2' ),
					'<a href="' . MS_Controller_Adminbar::get_simulation_exit_url() . '">',
					'</a>'
				);
				?>
			</p>
			<p>
				<em><?php _e( 'Diese Seite steht nur Administratoren zur Verfügung - Du kannst sie auch während der Simulation immer sehen.', 'membership2' ); ?></em>
			</p>
		</div>
		<?php
		endif;
	}

	/**
	 * Displays a warning if network-wide protection is enabled for a large
	 * network.
	 *
	 * @since  1.0.0
	 */
	protected function check_network() {
		if ( MS_Plugin::is_network_wide() && wp_is_large_network() ) :
		?>
			<div class="error below-h2">
			<p>
				<strong><?php _e( 'Warnung!', 'membership2' ); ?></strong>
			</p>
			<p>
				<?php _e( 'Dieses Netzwerk hat eine große Anzahl von Webseiten. Einige Funktionen des Netzwerkschutzes sind möglicherweise langsam oder nicht verfügbar.', 'membership2' ); ?>
			</p>
			</div>
		<?php
		endif;
	}

	/**
	 * Builds template and return it as string.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	protected function to_html() {
		// This function is implemented different in each child class.
		return apply_filters( 'ms_view_to_html', '' );
	}

	/**
	 * Output the rendered template to the browser.
	 *
	 * @since  1.0.0
	 */
	public function render() {
		$html = $this->to_html();

		echo apply_filters(
			'ms_view_render',
			$html,
			$this
		);
	}

	public function enqueue_scripts() {
		
	}
}