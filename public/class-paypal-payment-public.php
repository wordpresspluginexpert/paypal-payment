<?php

/**
 * @class       MBJ_PayPal_Payment_Public
 * @version	1.0.0
 * @package	paypal-payment
 * @category	Class
 * @author      johnny manziel <phpwebcreators@gmail.com>
 */
class MBJ_PayPal_Payment_Public {

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
    public function __construct($plugin_name, $version) {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->paypal_payment_add_shortcode();
        add_filter('widget_text', 'do_shortcode');
    }

    public function paypal_payment_add_shortcode() {
        add_shortcode('paypal_payment', array($this, 'paypal_payment_button_generator'));
    }

    public function paypal_payment_button_generator() {

        $paypal_payment_custom_button = get_option('paypal_payment_custom_button');
        $paypal_payment_button_image = get_option('paypal_payment_button_image');
        $paypal_payment_item_name = get_option('paypal_payment_item_name');
        $paypal_payment_amount = get_option('paypal_payment_amount');
        $paypal_payment_notify_url = site_url('?MBJ_PayPal_Payment&action=ipn_handler');
        $paypal_payment_return_page = get_option('paypal_payment_return_page');
        $paypal_payment_currency = get_option('paypal_payment_currency');
        $paypal_payment_bussiness_email = get_option('paypal_payment_bussiness_email');
        $paypal_payment_PayPal_sandbox = get_option('paypal_payment_PayPal_sandbox');
        $paypal_payment_button_label = get_option('paypal_payment_button_label');


        if (isset($paypal_payment_button_image) && !empty($paypal_payment_button_image)) {
            switch ($paypal_payment_button_image) {
                case 'button1':
                    $button_url = 'https://www.paypalobjects.com/en_AU/i/btn/btn_buynow_LG.gif';
                    break;
                case 'button2':
                    $button_url = 'https://www.paypalobjects.com/en_AU/i/btn/btn_paynow_LG.gif';
                    break;
                case 'button3':
                    $button_url = !empty($paypal_payment_custom_button) ? $paypal_payment_custom_button : 'https://www.paypalobjects.com/en_AU/i/btn/btn_buynow_LG.gif';
                    break;
               
            }
        } elseif (isset($paypal_payment_custom_button) && !empty($paypal_payment_custom_button)) {
            $button_url = $paypal_payment_custom_button;
        } else {
            $button_url = 'https://www.paypalobjects.com/en_AU/i/btn/btn_buynow_LG.gif';
        }

        if (isset($paypal_payment_PayPal_sandbox) && $paypal_payment_PayPal_sandbox == 'yes') {
            $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
        }

        ob_start();

		$output = '';
        $output = '<div class="page-sidebar widget">';

        $output .= '<form action="' . esc_url($paypal_url) . '" method="post" target="_blank">';

        if (isset($paypal_payment_button_label) && !empty($paypal_payment_button_label)) {
            $output .= '<p><label for=' . esc_attr($paypal_payment_button_label) . '>' . esc_attr($paypal_payment_button_label) . '</label></p>';
        }

        $output .= '<input type="hidden" name="business" value="' . esc_attr($paypal_payment_bussiness_email) . '">';

        $output .= '<input type="hidden" name="bn" value="mbjtechnolabs_SP">';

        $output .= '<input type="hidden" name="cmd" value="_xclick">';

        if (isset($paypal_payment_item_name) && !empty($paypal_payment_item_name)) {
            $output .= '<input type="hidden" name="item_name" value="' . esc_attr($paypal_payment_item_name) . '">';
        } else {
            $output .= '<input type="hidden" name="item_name" value="cup of coffee">';
        }

       if (isset($paypal_payment_amount) && !empty($paypal_payment_amount)) {
            $output .= '<input type="hidden" name="amount" value="' . esc_attr($paypal_payment_amount) . '">';
        }

        if (isset($paypal_payment_currency) && !empty($paypal_payment_currency)) {
            $output .= '<input type="hidden" name="currency_code" value="' . esc_attr($paypal_payment_currency) . '">';
        }

        if (isset($paypal_payment_notify_url) && !empty($paypal_payment_notify_url)) {
            $output .= '<input type="hidden" name="notify_url" value="' . esc_url($paypal_payment_notify_url) . '">';
        }

        if (isset($paypal_payment_return_page) && !empty($paypal_payment_return_page)) {
            $paypal_payment_return_page = get_permalink($paypal_payment_return_page);
            $output .= '<input type="hidden" name="return" value="' . esc_url($paypal_payment_return_page) . '">';
        }

        $output .= '<input type="image" name="submit" border="0" src="' . esc_url($button_url) . '" alt="PayPal - The safer, easier way to pay online">';
        $output .= '</form></div>';

        return $output;
        return ob_get_clean();
    }

}

