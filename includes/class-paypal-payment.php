<?php

/**
 * @class       MBJ_PayPal_Payment
 * @version	1.0.0
 * @package	paypal-payment
 * @category	Class
 * @author      johnny manziel <phpwebcreators@gmail.com>
 */
class MBJ_PayPal_Payment {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      MBJ_PayPal_Payment_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the Dashboard and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct() {

        $this->plugin_name = 'paypal-payment';
        $this->version = '1.0.3';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();

        //add_filter('wp_mail_content_type', array($this, 'set_html_content_type'));
        add_action('init', array($this, 'add_endpoint'), 0);
        add_action('parse_request', array($this, 'handle_api_requests'), 0);
        add_action('paypal_payment_send_notification_mail', array($this, 'paypal_payment_send_notification_mail'), 10, 1);

        add_action('paypal_payment_api_ipn_handler', array($this, 'paypal_payment_api_ipn_handler'));
        $prefix = is_network_admin() ? 'network_admin_' : '';
        add_filter("{$prefix}plugin_action_links_" . PPW_PLUGIN_BASENAME, array($this, 'plugin_action_links'), 10, 4);

        add_filter('widget_text', 'do_shortcode');
    }

    public function plugin_action_links($actions, $plugin_file, $plugin_data, $context) {
        $custom_actions = array(
            'configure' => sprintf('<a href="%s">%s</a>', admin_url('options-general.php?page=paypal-payment'), __('Configure', 'paypal-payment')),
            'support' => sprintf('<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/plugin/paypal-payment/', __('Support', 'paypal-payment')),
            'review' => sprintf('<a href="%s" target="_blank">%s</a>', 'http://wordpress.org/support/view/plugin-reviews/paypal-payment', __('Write a Review', 'paypal-payment')),
        );

        return array_merge($custom_actions, $actions);
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - MBJ_PayPal_Payment_Loader. Orchestrates the hooks of the plugin.
     * - MBJ_PayPal_Payment_i18n. Defines internationalization functionality.
     * - MBJ_PayPal_Payment_Admin. Defines all hooks for the dashboard.
     * - MBJ_PayPal_Payment_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-payment-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-payment-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the Dashboard.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-paypal-payment-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-paypal-payment-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/class-paypal-payment-list.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-payment-mailchimp-helper.php';
        

        $this->loader = new MBJ_PayPal_Payment_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the MBJ_PayPal_Payment_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new MBJ_PayPal_Payment_i18n();
        $plugin_i18n->set_domain($this->get_plugin_name());

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    /**
     * Register all of the hooks related to the dashboard functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new MBJ_PayPal_Payment_Admin($this->get_plugin_name(), $this->get_version());
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new MBJ_PayPal_Payment_Public($this->get_plugin_name(), $this->get_version());

        //$this->loader->add_filter('widget_text', $plugin_public, 'do_shortcode');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    MBJ_PayPal_Payment_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    public function handle_api_requests() {
        global $wp;

        if (isset($_GET['action']) && $_GET['action'] == 'ipn_handler') {
            $wp->query_vars['MBJ_PayPal_Payment'] = $_GET['action'];
        }

        // paypal-payment endpoint requests
        if (!empty($wp->query_vars['MBJ_PayPal_Payment'])) {

            // Buffer, we won't want any output here
            ob_start();

            // Get API trigger
            $api = strtolower(esc_attr($wp->query_vars['MBJ_PayPal_Payment']));

            // Trigger actions
            do_action('paypal_payment_api_' . $api);

            // Done, clear buffer and exit
            ob_end_clean();
            die('1');
        }
    }

    /**
     * add_endpoint function.
     *
     * @access public
     * @since 1.0.0
     * @return void
     */
    public function add_endpoint() {

        // paypal-payment API for PayPal gateway IPNs, etc
        add_rewrite_endpoint('MBJ_PayPal_Payment', EP_ALL);
    }

    public function paypal_payment_api_ipn_handler() {

        /**
         * The class responsible for defining all actions related to paypal ipn listener 
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-paypal-payment-paypal-listner.php';
        $MBJ_PayPal_Payment_PayPal_listner = new MBJ_PayPal_Payment_PayPal_listner();

        /**
         * The check_ipn_request function check and validation for ipn response
         */
        if ($MBJ_PayPal_Payment_PayPal_listner->check_ipn_request()) {
            $MBJ_PayPal_Payment_PayPal_listner->successful_request($IPN_status = true);
        } else {
            $MBJ_PayPal_Payment_PayPal_listner->successful_request($IPN_status = false);
        }
    }

    public function set_html_content_type() {
        return 'text/html';
    }

    public function paypal_payment_send_notification_mail($posted) {

        $template = get_option('paypal_payments_email_body_text');

        $template_value = isset($template) ? $template : get_option('paypal_payments_email_body_text_pre');

        $parse_templated = $this->paypal_payment_template_vars_replacement($template_value, $posted);



        $from_name = get_option('paypal_payments_email_from_name');
        $from_name_value = isset($from_name) ? $from_name : 'From';

        $sender_address = get_option('paypal_payments_email_from_address');
        $sender_address_value = isset($sender_address) ? $sender_address : get_option('admin_email');


        if (isset($from_name_value) && !empty($from_name_value)) {
            $headers = "From: " . $from_name_value . " <" . $sender_address_value . ">";
        }


        if (isset($posted['payer_email']) && !empty($posted['payer_email'])) {
            $subject = get_option('paypal_payments_email_subject');
            $subject_value = isset($subject) ? $subject : 'Thank you for your payment';

            $enable_admin = get_option('paypal_payments_admin_notification');
            $admin_email = get_option('admin_email');
            if (isset($headers) && !empty($headers)) {
                wp_mail($posted['payer_email'], $subject_value, $parse_templated, $headers);
                if ($enable_admin) {
                    wp_mail($admin_email, $subject_value, $parse_templated, $headers);
                }
            } else {
                wp_mail($posted['payer_email'], $subject_value, $parse_templated);
                if ($enable_admin) {
                    wp_mail($admin_email, $subject_value, $parse_templated);
                }
            }
            //remove_filter('wp_mail_content_type', 'set_html_content_type');
        }
    }

    public function paypal_payment_template_vars_replacement($template, $posted) {



        $to_replace = array(
            'blog_url' => get_option('siteurl'),
            'home_url' => get_option('home'),
            'blog_name' => get_option('blogname'),
            'blog_description' => get_option('blogdescription'),
            'admin_email' => get_option('admin_email'),
            'date' => date_i18n(get_option('date_format')),
            'time' => date_i18n(get_option('time_format')),
            'txn_id' => $posted['txn_id'],
            'receiver_email' => $posted['receiver_email'],
            'payment_date' => $posted['payment_date'],
            'first_name' => $posted['first_name'],
            'last_name' => $posted['last_name'],
            'mc_currency' => $posted['mc_currency'],
            'mc_gross' => $posted['mc_gross']
        );

        foreach ($to_replace as $tag => $var) {

            $template = str_replace('%' . $tag . '%', $var, $template);
        }

        return $template;
    }

}
