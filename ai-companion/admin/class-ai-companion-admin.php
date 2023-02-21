<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.shierd.com
 * @since      1.0.0
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/admin
 * @author     Your Name <email@example.com>
 */
class Ai_Companion_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ai-companion-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ai-companion-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * 添加页面模板
	 */
	public function addPageTemplate($templates) {
		$templates['ai-companion-chat-page.php'] = 'AI Companion Chat Page';
		return $templates;
	}

	/**
	 * 后台添加插件设置页面
	 */
	public function addSettingPage() {
		add_options_page(
			'AI Companion Setting',
			'AI Companion',
			'manage_options',
			'ai_companion',
			array($this, 'renderSettingPage')
		);
	}

	/**
	 * 渲染插件设置页面
	 */
	public function renderSettingPage() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		// // check if the user have submitted the settings
		// // WordPress will add the "settings-updated" $_GET parameter to the url
		// if ( isset( $_GET['settings-updated'] ) ) {
		// 	// add settings saved message with the class of "updated"
		// 	add_settings_error( 'wporg_messages', 'wporg_message', __( 'Settings Saved', 'wporg' ), 'updated' );
		// }
		// // show error/update messages
		// settings_errors( 'wporg_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "wporg"
				settings_fields( 'ai_companion' );
				// output setting sections and their fields
				// (sections are registered for "wporg", each field is registered to a specific section)
				do_settings_sections( 'ai_companion' );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * 注册设置选项
	 */
	public function registerSetting() {
		register_setting('ai_companion', Ai_Companion_OPTION_KEY);
		add_settings_section('basic', 'Basic Setting', array($this, 'renderSection'), 'ai_companion');
		add_settings_field('openai_api_key', 'Openai Api Key', array($this, 'renderField'), 'ai_companion', 'basic', ['label_for' => 'openai_api_key', 'class' => []]);
	}

	public function renderSection() {

	}

	public function renderField($args) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Ai_Companion_OPTION_KEY );
		$input_name = Ai_Companion_OPTION_KEY . "[" . esc_attr($args['label_for']) . "]"
		?>
		<input type="text" name="<?php echo $input_name; ?>" value="<?php echo isset( $options[$args['label_for']] ) ? esc_attr( $options[$args['label_for']] ) : ''; ?>">
		<?php
	}

}
