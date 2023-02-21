<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.shierd.com
 * @since      1.0.0
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ai_Companion
 * @subpackage Ai_Companion/public
 * @author     Your Name <email@example.com>
 */
class Ai_Companion_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/ai-companion-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/ai-companion-public.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * 添加AI聊天页面body标签的样式类
	 */
	public function addChatPageBodyClass($classes) {
		$page_tpl = get_page_template_slug();
		if (!empty($page_tpl) && $page_tpl == 'ai-companion-chat-page.php') {
			$classes[] = 'ai-companion-chat-page';
		}
		return $classes;
	}

	/**
	 * 页面应用插件的模板时，返回插件模板的正确路径
	 */
	public function includeTemplate($template) {
		$page_tpl = get_page_template_slug();
		if (empty($page_tpl) || $page_tpl != 'ai-companion-chat-page.php') {
			return $template;
		}

		$tmp_file = plugin_dir_path(  __FILE__  ) . 'partials/' . $page_tpl;
		if (file_exists($tmp_file)) {
			$template = $tmp_file;
		}
		
		return $template;
	}

}
