<?php defined( 'ABSPATH' ) OR die( 'This script cannot be accessed directly.' );

/**
 * Lite version of site asset withdrawal
 */
final class USBuilder_Assets {

	/**
	 * The current name of the object
	 *
	 * @var string
	 */
	private $name = 'default';

	/**
	 * Assets for the admin part of the builder
	 *
	 * @var array
	 */
	private $_handles = array();

	/**
	 * @var array USBuilder_Assets
	 */
	protected static $instances = array();

	/**
	 * @access public
	 * @param string $name Current name for the object
	 * @return USBuilder_Assets
	 */
	public static function instance( $name ) {
		if ( ! isset( self::$instances[ $name ] ) ) {
			$instance = new self;
			$instance->name = ( string ) $name;
			self::$instances[ $name ] = $instance;
		}

		return self::$instances[ $name ];
	}

	/**
	 * Add handle to asset output
	 *
	 * @access public
	 * @param string|array $handles The handles
	 * @return self
	 *
	 * TODO: Add dependency support
	 */
	public function add( $handles ) {
		if ( is_string( $handles ) ) {
			$handles = array( $handles );
		}

		// Check the correctness of the handles
		if ( ! is_array( $handles ) OR empty( $handles ) ) {
			return;
		}

		// Note: The order of adding assets is important here.
		foreach ( array_map( 'strval', $handles ) as $handle ) {
			if ( ! in_array( $handle, $this->_handles ) ) {
				$this->_handles[] = $handle;
			}
		}

		return $this;
	}

	/**
	 * Get list of installed handles
	 *
	 * @access public
	 * @return array The handles.
	 */
	public function get_handles() {
		return $this->_handles;
	}

	/**
	 * Get styles that are in the $_handles queue.
	 *
	 * @access public
	 * @return string
	 */
	public function get_styles() {
		if (
			! is_array( $this->_handles )
			OR empty( $this->_handles )
		) {
			return;
		}

		global $wp_styles;
		$result = array();
		foreach ( $this->_handles as $handle ) {
			if ( ! $style = us_arr_path( $wp_styles->registered, $handle ) ) {
				continue;
			}
			if ( $src = $style->src ) {
				if ( $style->ver ) {
					$src .= '?ver=' . $style->ver;
				}
				$src = site_url( $src );
				$result[] = "<link rel='stylesheet' id='" . esc_attr( $handle ) . "-css' href='" . esc_url( $src ) . "' />";
			}
		}

		// Concatenating assets into a string
		return implode( "\n", $result );
	}

	/**
	 * Get scripts that are in the $_handles queue.
	 *
	 * @access public
	 * @return string
	 */
	public function get_scripts() {
		if (
			! is_array( $this->_handles )
			OR empty( $this->_handles )
		) {
			return;
		}

		global $wp_scripts;
		$result = array();
		foreach ( $this->_handles as $handle ) {
			if ( ! $script = $wp_scripts->registered[ $handle ] ) {
				continue;
			}
			if ( isset( $script->extra['data'] ) ) {
				$result[] = '<script type="text/javascript">' . $script->extra['data'] . '</script>';
			}
			if ( $src = $script->src ) {
				if ( $script->ver ) {
					$src .= '?ver=' . $script->ver;
				}
				$src = site_url( $src );
				$result[] = "<script id='" . esc_attr( $handle ) . "-js' src='" . esc_url( $src ) . "'></script>";
			}
		}

		// Concatenating assets into a string
		return implode( "\n", $result );
	}

	/**
	 * Get styles and scripts that are in the $_handles queue.
	 *
	 * @access public
	 * @return string
	 */
	public function get_assets() {
		return $this->get_styles() . "\n" . $this->get_scripts();
	}
}
