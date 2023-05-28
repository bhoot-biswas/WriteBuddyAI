<?php
namespace BengalStudio;

class Settings {
	private $tabs;

	private $writebuddy_options;

	public function __construct() {
		$this->tabs = get_setting_tabs();

		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	public function add_plugin_page() {
		add_menu_page(
			__( 'WriteBuddy AI', 'writebuddyai' ), // page_title
			__( 'WriteBuddy AI', 'writebuddyai' ), // menu_title
			'manage_options', // capability
			'writebuddyai', // menu_slug
			array( $this, 'create_admin_page' ), // function
			'data:image/svg+xml;base64,' . base64_encode( get_writebuddy_icon() ), // icon_url
		);
	}

	public function create_admin_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<h2 class="nav-tab-wrapper">
				<?php
				$current_tab              = isset( $_GET['tab'] ) ? $_GET['tab'] : 'openai';
				$this->writebuddy_options = get_option( 'writebuddy_option_' . $current_tab );

				foreach ( $this->tabs as $tab_key => $tab ) {
					$active = $current_tab === $tab_key ? 'nav-tab-active' : '';
					printf(
						'<a href="?page=writebuddyai&tab=%s" class="nav-tab %s">%s</a>',
						$tab_key,
						$active,
						$tab['title']
					);
				}
				?>
			</h2>

			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'writebuddy_option_group_' . $current_tab );
					do_settings_sections( 'writebuddy-admin-' . $current_tab );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function page_init() {
		foreach ( $this->tabs as $tab_key => $tab ) {
			register_setting(
				'writebuddy_option_group_' . $tab_key, // option_group
				'writebuddy_option_' . $tab_key, // option_name
				array( $this, 'sanitize_settings' ) // sanitize_callback
			);

			add_settings_section(
				'writebuddy_setting_section_' . $tab_key, // id
				'', // title
				function() use ( $tab ) {
					echo $tab['description'];
				},
				'writebuddy-admin-' . $tab_key // page
			);

			foreach ( $tab['fields'] as $field_key => $field ) {
				add_settings_field(
					$field_key,
					$field['title'],
					array( $this, 'field_callback' ),
					'writebuddy-admin-' . $tab_key,
					'writebuddy_setting_section_' . $tab_key,
					array(
						'id'       => $field_key,
						'type'     => $field['type'],
						'name'     => 'writebuddy_option_' . $tab_key . '[' . $field_key . ']',
						'desc'     => $field['description'],
						'options'  => isset( $field['options'] ) ? $field['options'] : '',
						'default'  => $field['default'],
						'required' => $field['required'],
					)
				);
			}
		}
	}

	public function sanitize_settings( $settings ) {
		$sanitary_values = array();

		foreach ( $this->tabs as $tab_key => $tab ) {
			foreach ( $tab['fields'] as $field_key => $field ) {
				$value                         = isset( $settings[ $field_key ] ) ? $settings[ $field_key ] : ( 'enable_logging' === $field_key ? 'off' : $field['default'] );
				$sanitary_values[ $field_key ] = call_user_func( $field['sanitize_callback'], $value );
			}
		}

		return $sanitary_values;
	}

	public function field_callback( $args ) {
		$field_value = isset( $this->writebuddy_options[ $args['id'] ] ) ? esc_attr( $this->writebuddy_options[ $args['id'] ] ) : $args['default'];
		switch ( $args['type'] ) {
			case 'text':
			case 'number':
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="%4$s" class="regular-text">',
					esc_attr( $args['type'] ),
					esc_attr( $args['id'] ),
					esc_attr( $args['name'] ),
					esc_attr( $field_value )
				);
				break;
			case 'select':
				printf(
					'<select id="%1$s" name="%2$s">',
					esc_attr( $args['id'] ),
					esc_attr( $args['name'] )
				);

				foreach ( $args['options'] as $value => $label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $value ),
						selected( $value, $field_value, false ),
						esc_html( $label )
					);
				}

				echo '</select>';
				break;
			case 'checkbox':
				printf(
					'<input type="%1$s" id="%2$s" name="%3$s" value="on" %4$s>',
					esc_attr( $args['type'] ),
					esc_attr( $args['id'] ),
					esc_attr( $args['name'] ),
					checked( $field_value, 'on', false )
				);
				break;
		}
		if ( ! empty( $args['desc'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['desc'] ) );
		}
		if ( ! empty( $args['required'] ) ) {
			printf( '<span class="required">*</span>' );
		}
	}
}
