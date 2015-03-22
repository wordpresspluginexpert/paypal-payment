<?php

/**
 * @class       MBJ_PayPal_Payment_General_Setting
 * @version	1.0.0
 * @package	paypal-payment
 * @category	Class
 * @author      johnny manziel <phpwebcreators@gmail.com>
 */
class MBJ_PayPal_Payment_General_Setting {

    /**
     * Hook in methods
     * @since    1.0.0
     * @access   static
     */
    public static function init() {

        add_action('paypal_payment_general_setting', array(__CLASS__, 'paypal_payment_general_setting_function'));
        add_action('paypal_payment_email_setting', array(__CLASS__, 'paypal_payment_email_setting_function'));
        add_action('paypal_payment_help_setting', array(__CLASS__, 'paypal_payment_help_setting'));
        add_action('paypal_payment_mailchimp_setting_save_field', array(__CLASS__, 'paypal_payment_mailchimp_setting_save_field'));
        add_action('paypal_payment_mailchimp_setting', array(__CLASS__, 'paypal_payment_mailchimp_setting'));
        add_action('paypal_payment_general_setting_save_field', array(__CLASS__, 'paypal_payment_general_setting_save_field'));
        add_action('paypal_payment_email_setting_save_field', array(__CLASS__, 'paypal_payment_email_setting_save_field'));
    }

    public static function paypal_payment_email_setting_field() {
        $email_body = "Hello %first_name% %last_name%,

Thank you for your payment!

Your PayPal transaction ID is: %txn_id%
PayPal payment receiver email address: %receiver_email%
PayPal payment date: %payment_date%
PayPal payer first: %first_name%
PayPal payer last name: %last_name%
PayPal payment currency: %mc_currency%
PayPal payment amount: %mc_gross%

Thanks you very much,
Store Admin";


        update_option('paypal_payments_email_body_text_pre', $email_body);
        $settings = apply_filters('paypal_payments_email_settings', array(
            array('type' => 'sectionend', 'id' => 'email_recipient_options'),
            array('title' => __('Email settings', 'paypal-payment'), 'type' => 'title', 'desc' => __('Set your own sender name and email address. Default WordPress values will be used if empty.', 'paypal-payment'), 'id' => 'email_options'),
            array(
                'title' => __('Enable/Disable', 'paypal-payment'),
                'type' => 'checkbox',
                'desc' => __('Enable this email notification for payer', 'paypal-payment'),
                'default' => 'yes',
                'id' => 'paypal_payments_payer_notification'
            ),
            array(
                'title' => __('Enable/Disable', 'paypal-payment'),
                'type' => 'checkbox',
                'desc' => __('Enable this email notification for website admin', 'paypal-payment'),
                'default' => 'yes',
                'id' => 'paypal_payments_admin_notification'
            ),
            array(
                'title' => __('"From" Name', 'paypal-payment'),
                'desc' => '',
                'id' => 'paypal_payments_email_from_name',
                'type' => 'text',
                'css' => 'min-width:300px;',
                'default' => esc_attr(get_bloginfo('title')),
                'autoload' => false
            ),
            array(
                'title' => __('"From" Email Address', 'paypal-payment'),
                'desc' => '',
                'id' => 'paypal_payments_email_from_address',
                'type' => 'email',
                'custom_attributes' => array(
                    'multiple' => 'multiple'
                ),
                'css' => 'min-width:300px;',
                'default' => get_option('admin_email'),
                'autoload' => false
            ),
            array(
                'title' => __('Email subject', 'paypal-payment'),
                'desc' => '',
                'id' => 'paypal_payments_email_subject',
                'type' => 'text',
                'css' => 'min-width:300px;',
                'default' => 'Thank you for your payment',
                'autoload' => false
            ),
            array('type' => 'sectionend', 'id' => 'email_options'),
            array(
                'title' => __('Email body', 'paypal-payment'),
                'desc' => __('The text to appear in the Payment Email. Please read more Help section(tab) for more dynamic tag', 'paypal-payment'),
                'id' => 'paypal_payments_email_body_text',
                'css' => 'width:100%; height: 500px;',
                'type' => 'textarea',
                'editor' => 'false',
                'default' => $email_body,
                'autoload' => false
            ),
            array('type' => 'sectionend', 'id' => 'email_template_options'),
        ));

        return $settings;
    }

    public static function help() {


        echo '<p>' . __('Some dynamic tags can be included in your email template :', 'wp-better-emails') . '</p>
					<ul>
						<li>' . __('<strong>%blog_url%</strong> : will be replaced with your blog URL.', 'wp-better-emails') . '</li>
						<li>' . __('<strong>%home_url%</strong> : will be replaced with your home URL.', 'wp-better-emails') . '</li>
						<li>' . __('<strong>%blog_name%</strong> : will be replaced with your blog name.', 'wp-better-emails') . '</li>
						<li>' . __('<strong>%blog_description%</strong> : will be replaced with your blog description.', 'wp-better-emails') . '</li>
						<li>' . __('<strong>%admin_email%</strong> : will be replaced with admin email.', 'wp-better-emails') . '</li>
						<li>' . __('<strong>%date%</strong> : will be replaced with current date, as formatted in <a href="options-general.php">general options</a>.', 'wp-better-emails') . '</li>
						<li>' . __('<strong>%time%</strong> : will be replaced with current time, as formatted in <a href="options-general.php">general options</a>.', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%txn_id%</strong> : will be replaced with PayPal payment transaction ID.', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%receiver_email%</strong> : will be replaced with PayPal payment receiver email address%.', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%payment_date%</strong> : will be replaced with PayPal payment date%.', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%first_name%</strong> : will be replaced with PayPal payment first name%.', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%last_name%</strong> : will be replaced with PayPal payment last name%.', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%mc_currency%</strong> : will be replaced with PayPal payment currency like USD', 'wp-better-emails') . '</li>
                                                <li>' . __('<strong>%mc_gross%</strong> : will be replaced with PayPal payment amount', 'wp-better-emails') . '</li>
                                          </ul>';
    }

    public static function paypal_payment_email_setting_function() {


        $paypal_payment_setting_fields = self::paypal_payment_email_setting_field();
        $Html_output = new MBJ_PayPal_Payment_Html_output();
        ?>

        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($paypal_payment_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    public static function paypal_payment_setting_fields() {

        $currency_code_options = self::get_paypal_payment_currencies();

        foreach ($currency_code_options as $code => $name) {
            $currency_code_options[$code] = $name . ' (' . self::get_paypal_payment_symbol($code) . ')';
        }

        $fields[] = array('title' => __('PayPal Account Setup', 'paypal-payment'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array(
            'title' => __('Enable PayPal sandbox', 'paypal-payment'),
            'type' => 'checkbox',
            'id' => 'paypal_payment_PayPal_sandbox',
            'label' => __('Enable PayPal sandbox', 'paypal-payment'),
            'default' => 'no',
            'css' => 'min-width:300px;',
            'desc' => sprintf(__('PayPal sandbox can be used to test payments. Sign up for a developer account <a href="%s">here</a>.', 'paypal-payment'), 'https://developer.paypal.com/'),
        );



        $fields[] = array(
            'title' => __('PayPal Email address to receive payments', 'paypal-payment'),
            'type' => 'email',
            'id' => 'paypal_payment_bussiness_email',
            'desc' => __('This is the Paypal Email address where the payments will go.', 'paypal-payment'),
            'default' => '',
            'placeholder' => 'you@youremail.com',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );


        $fields[] = array(
            'title' => __('Currency', 'paypal-payment'),
            'desc' => __('This is the currency for your visitors to make Payments or Payments in.', 'paypal-payment'),
            'id' => 'paypal_payment_currency',
            'css' => 'min-width:250px;',
            'default' => 'GBP',
            'type' => 'select',
            'class' => 'chosen_select',
            'options' => $currency_code_options
        );

        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        $fields[] = array('title' => __('Optional Settings', 'paypal-payment'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array(
            'title' => __('Button Label', 'paypal-payment'),
            'type' => 'text',
            'id' => 'paypal_payment_button_label',
            'desc' => __('PayPal payment button label  (Optional).', 'paypal-payment'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );

        $fields[] = array(
            'title' => __('Return Page', 'paypal-payment'),
            'id' => 'paypal_payment_return_page',
            'desc' => __('URL to which the Payer comes to after completing the payment; for example, a URL on your site that displays a "Thank you for your payment".', 'paypal-payment'),
            'type' => 'single_select_page',
            'default' => '',
            'class' => 'chosen_select_nostd',
            'css' => 'min-width:300px;',
        );


        $fields[] = array(
            'title' => __('Amount', 'paypal-payment'),
            'type' => 'text',
            'id' => 'paypal_payment_amount',
            'desc' => __('The default amount for a payment (Optional).', 'paypal-payment'),
            'default' => ''
        );

        $fields[] = array(
            'title' => __('Item Name', 'paypal-payment'),
            'type' => 'text',
            'id' => 'paypal_payment_item_name',
            'desc' => __('The default purpose of a payment (Optional).', 'paypal-payment'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );

       
        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        $fields[] = array('title' => __('Payment Button', 'paypal-payment'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array(
            'title' => __('Select Payment Button', 'paypal-payment'),
            'id' => 'paypal_payment_button_image',
            'default' => 'no',
            'type' => 'radio',
            'desc' => __('Select Button.', 'paypal-payment'),
            'options' => array(
                'button1' => __('<img style="vertical-align: middle;" alt="large" src="https://www.paypalobjects.com/en_AU/i/btn/btn_buynow_LG.gif">', 'paypal-payment'),
                'button2' => __('<img style="vertical-align: middle;" alt="large" src="https://www.paypalobjects.com/en_AU/i/btn/btn_paynow_LG.gif">', 'paypal-payment'),
                'button3' => __('Custom Button ( If you select this option then pleae enter url in Custom Button textbox, Otherwise payment button will not display. )', 'paypal-payment')
            ),
        );

        $fields[] = array(
            'title' => __('Custom Button', 'paypal-payment'),
            'type' => 'text',
            'id' => 'paypal_payment_custom_button',
            'desc' => __('Enter a URL to a custom payment button.', 'paypal-payment'),
            'default' => '',
            'css' => 'min-width:300px;',
            'class' => 'input-text regular-input'
        );



        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');
        return $fields;
    }

    public static function paypal_payment_general_setting_save_field() {

        $paypal_payment_setting_fields = self::paypal_payment_setting_fields();
        $Html_output = new MBJ_PayPal_Payment_Html_output();
        $Html_output->save_fields($paypal_payment_setting_fields);
    }

    public static function paypal_payment_email_setting_save_field() {
        $paypal_payment_email_setting_field = self::paypal_payment_email_setting_field();
        $Html_output = new MBJ_PayPal_Payment_Html_output();
        $Html_output->save_fields($paypal_payment_email_setting_field);
    }

    public static function paypal_payment_help_setting() {
        ?>
        <div class="postbox">
            <h2><label for="title">&nbsp;&nbsp;Plugin Usage</label></h2>
            <div class="inside">      
                <p>There are a few ways you can use this plugin:</p>
                <ol>
                    <li>Configure the options below and then add the shortcode <strong>[paypal_payment]</strong> to a post or page (where you want the payment button)</li>
                    <li>Call the function from a template file: <strong>&lt;?php echo do_shortcode( '[paypal_payment]' ); ?&gt;</strong></li>
                    <li>Use the <strong>PayPal Payment</strong> Widget from the Widgets menu</li>
                </ol>
                <p><h3>Archive of PayPal Buttons and Images</h3><br>
                The following reference pages list the localized PayPal buttons and images and their URLs.
                </p>
                <p><h4>English</h4></p>
                <ul>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/AU/">Australia</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/US-UK/">United Kingdom</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/US-UK/">United States</a></li>
                </ul>
                <p><h4>Asia-Pacific</h4></p>
                <ul>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/JP/">Japan</a></li>
                </ul>
                <p><h4>EU Non-English</h4></p>
                <ul>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/DE/">Germany</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/ES/">Spain</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/FR/">France</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/IT/">Italy</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/NL/">Netherlands</a></li>
                    <li><a target="_blank" href="https://developer.paypal.com/docs/classic/archive/buttons/PL/">Poland</a></li>
                </ul>
                <br>
                <h2> <label>Email dynamic tag list</label></h2>
                <?php self::help(); ?>
            </div></div>
        <?php
    }

    public static function paypal_payment_general_setting_function() {
        $paypal_payment_setting_fields = self::paypal_payment_setting_fields();
        $Html_output = new MBJ_PayPal_Payment_Html_output();
        ?>

        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($paypal_payment_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    /**
     * Get full list of currency codes.
     * @return array
     */
    public static function get_paypal_payment_currencies() {
        return array_unique(
                apply_filters('paypal_payment_currencies', array(
            'AED' => __('United Arab Emirates Dirham', 'paypal-payment'),
            'AUD' => __('Australian Dollars', 'paypal-payment'),
            'BDT' => __('Bangladeshi Taka', 'paypal-payment'),
            'BRL' => __('Brazilian Real', 'paypal-payment'),
            'BGN' => __('Bulgarian Lev', 'paypal-payment'),
            'CAD' => __('Canadian Dollars', 'paypal-payment'),
            'CLP' => __('Chilean Peso', 'paypal-payment'),
            'CNY' => __('Chinese Yuan', 'paypal-payment'),
            'COP' => __('Colombian Peso', 'paypal-payment'),
            'CZK' => __('Czech Koruna', 'paypal-payment'),
            'DKK' => __('Danish Krone', 'paypal-payment'),
            'DOP' => __('Dominican Peso', 'paypal-payment'),
            'EUR' => __('Euros', 'paypal-payment'),
            'HKD' => __('Hong Kong Dollar', 'paypal-payment'),
            'HRK' => __('Croatia kuna', 'paypal-payment'),
            'HUF' => __('Hungarian Forint', 'paypal-payment'),
            'ISK' => __('Icelandic krona', 'paypal-payment'),
            'IDR' => __('Indonesia Rupiah', 'paypal-payment'),
            'INR' => __('Indian Rupee', 'paypal-payment'),
            'NPR' => __('Nepali Rupee', 'paypal-payment'),
            'ILS' => __('Israeli Shekel', 'paypal-payment'),
            'JPY' => __('Japanese Yen', 'paypal-payment'),
            'KIP' => __('Lao Kip', 'paypal-payment'),
            'KRW' => __('South Korean Won', 'paypal-payment'),
            'MYR' => __('Malaysian Ringgits', 'paypal-payment'),
            'MXN' => __('Mexican Peso', 'paypal-payment'),
            'NGN' => __('Nigerian Naira', 'paypal-payment'),
            'NOK' => __('Norwegian Krone', 'paypal-payment'),
            'NZD' => __('New Zealand Dollar', 'paypal-payment'),
            'PYG' => __('Paraguayan Guaraní', 'paypal-payment'),
            'PHP' => __('Philippine Pesos', 'paypal-payment'),
            'PLN' => __('Polish Zloty', 'paypal-payment'),
            'GBP' => __('Pounds Sterling', 'paypal-payment'),
            'RON' => __('Romanian Leu', 'paypal-payment'),
            'RUB' => __('Russian Ruble', 'paypal-payment'),
            'SGD' => __('Singapore Dollar', 'paypal-payment'),
            'ZAR' => __('South African rand', 'paypal-payment'),
            'SEK' => __('Swedish Krona', 'paypal-payment'),
            'CHF' => __('Swiss Franc', 'paypal-payment'),
            'TWD' => __('Taiwan New Dollars', 'paypal-payment'),
            'THB' => __('Thai Baht', 'paypal-payment'),
            'TRY' => __('Turkish Lira', 'paypal-payment'),
            'USD' => __('US Dollars', 'paypal-payment'),
            'VND' => __('Vietnamese Dong', 'paypal-payment'),
            'EGP' => __('Egyptian Pound', 'paypal-payment'),
                        )
                )
        );
    }

    /**
     * Get Currency symbol.
     * @param string $currency (default: '')
     * @return string
     */
    public static function get_paypal_payment_symbol($currency = '') {
        if (!$currency) {
            $currency = get_paypal_payment_currencies();
        }

        switch ($currency) {
            case 'AED' :
                $currency_symbol = 'د.إ';
                break;
            case 'BDT':
                $currency_symbol = '&#2547;&nbsp;';
                break;
            case 'BRL' :
                $currency_symbol = '&#82;&#36;';
                break;
            case 'BGN' :
                $currency_symbol = '&#1083;&#1074;.';
                break;
            case 'AUD' :
            case 'CAD' :
            case 'CLP' :
            case 'COP' :
            case 'MXN' :
            case 'NZD' :
            case 'HKD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '&#36;';
                break;
            case 'EUR' :
                $currency_symbol = '&euro;';
                break;
            case 'CNY' :
            case 'RMB' :
            case 'JPY' :
                $currency_symbol = '&yen;';
                break;
            case 'RUB' :
                $currency_symbol = '&#1088;&#1091;&#1073;.';
                break;
            case 'KRW' : $currency_symbol = '&#8361;';
                break;
            case 'PYG' : $currency_symbol = '&#8370;';
                break;
            case 'TRY' : $currency_symbol = '&#8378;';
                break;
            case 'NOK' : $currency_symbol = '&#107;&#114;';
                break;
            case 'ZAR' : $currency_symbol = '&#82;';
                break;
            case 'CZK' : $currency_symbol = '&#75;&#269;';
                break;
            case 'MYR' : $currency_symbol = '&#82;&#77;';
                break;
            case 'DKK' : $currency_symbol = 'kr.';
                break;
            case 'HUF' : $currency_symbol = '&#70;&#116;';
                break;
            case 'IDR' : $currency_symbol = 'Rp';
                break;
            case 'INR' : $currency_symbol = 'Rs.';
                break;
            case 'NPR' : $currency_symbol = 'Rs.';
                break;
            case 'ISK' : $currency_symbol = 'Kr.';
                break;
            case 'ILS' : $currency_symbol = '&#8362;';
                break;
            case 'PHP' : $currency_symbol = '&#8369;';
                break;
            case 'PLN' : $currency_symbol = '&#122;&#322;';
                break;
            case 'SEK' : $currency_symbol = '&#107;&#114;';
                break;
            case 'CHF' : $currency_symbol = '&#67;&#72;&#70;';
                break;
            case 'TWD' : $currency_symbol = '&#78;&#84;&#36;';
                break;
            case 'THB' : $currency_symbol = '&#3647;';
                break;
            case 'GBP' : $currency_symbol = '&pound;';
                break;
            case 'RON' : $currency_symbol = 'lei';
                break;
            case 'VND' : $currency_symbol = '&#8363;';
                break;
            case 'NGN' : $currency_symbol = '&#8358;';
                break;
            case 'HRK' : $currency_symbol = 'Kn';
                break;
            case 'EGP' : $currency_symbol = 'EGP';
                break;
            case 'DOP' : $currency_symbol = 'RD&#36;';
                break;
            case 'KIP' : $currency_symbol = '&#8365;';
                break;
            default : $currency_symbol = '';
                break;
        }

        return apply_filters('paypal_payment_currency_symbol', $currency_symbol, $currency);
    }
    
       
    public static function paypal_payment_mcapi_setting_fields() {

        $fields[] = array('title' => __('MailChimp Integration', 'paypal-payment'), 'type' => 'title', 'desc' => '', 'id' => 'general_options');

        $fields[] = array('title' => __('Enable MailChimp', 'paypal-payment'), 'type' => 'checkbox', 'desc' => '', 'id' => 'enable_mailchimp');

        $fields[] = array(
            'title' => __('MailChimp API Key', 'paypal-payment'),
            'desc' => __('Enter your API Key. <a target="_blank" href="http://admin.mailchimp.com/account/api-key-popup">Get your API key</a>', 'paypal-payment'),
            'id' => 'mailchimp_api_key',
            'type' => 'text',
            'css' => 'min-width:300px;',
        );

        $fields[] = array(
            'title' => __('MailChimp lists', 'paypal-payment'),
            'desc' => __('After you add your MailChimp API Key above and save it this list will be populated.', 'Option'),
            'id' => 'mailchimp_lists',
            'css' => 'min-width:300px;',
            'type' => 'select',
            'options' => self::paypal_payments_angelleye_get_mailchimp_lists(get_option('mailchimp_api_key'))
        );

        $fields[] = array(
            'title' => __('Force MailChimp lists refresh', 'paypal-payment'),
            'desc' => __("Check and 'Save changes' this if you've added a new MailChimp list and it's not showing in the list above.", 'paypal-payment'),
            'id' => 'paypal_payments_force_refresh',
            'type' => 'checkbox',
        );

       


        $fields[] = array('type' => 'sectionend', 'id' => 'general_options');

        return $fields;
    }

    public static function paypal_payment_mailchimp_setting() {
        $mcapi_setting_fields = self::paypal_payment_mcapi_setting_fields();
        $Html_output = new MBJ_PayPal_Payment_Html_output();
        ?>
        <form id="mailChimp_integration_form" enctype="multipart/form-data" action="" method="post">
            <?php $Html_output->init($mcapi_setting_fields); ?>
            <p class="submit">
                <input type="submit" name="mailChimp_integration" class="button-primary" value="<?php esc_attr_e('Save changes', 'Option'); ?>" />
            </p>
        </form>
        <?php
    }

    /**
     *  Get List from MailChimp
     */
    public static function paypal_payments_angelleye_get_mailchimp_lists($apikey) {

        $mailchimp_lists = unserialize(get_transient('mailchimp_mailinglist'));

        if (empty($mailchimp_lists) || get_option('paypal_payments_force_refresh') == 'yes') {

            include_once PPW_PLUGIN_DIR . '/includes/class-paypal-payment-mcapi.php';

            $mailchimp_api_key = get_option('mailchimp_api_key');
            $apikey = (isset($mailchimp_api_key)) ? $mailchimp_api_key : '';
            $api = new MBJ_PayPal_Payment_MailChimp_MCAPI($apikey);

            $retval = $api->lists();
            if ($api->errorCode) {
                $mailchimp_lists['false'] = __("Unable to load MailChimp lists, check your API Key.", 'eddms');
            } else {
                if ($retval['total'] == 0) {
                    $mailchimp_lists['false'] = __("You have not created any lists at MailChimp", 'eddms');
                    return $mailchimp_lists;
                }

                foreach ($retval['data'] as $list) {
                    $mailchimp_lists[$list['id']] = $list['name'];
                }
                set_transient('mailchimp_mailinglist', serialize($mailchimp_lists), 86400);
                update_option('paypal_payments_force_refresh', 'no');
            }
        }
        return $mailchimp_lists;
    }

    public static function paypal_payment_mailchimp_setting_save_field() {
        $mcapi_setting_fields = self::paypal_payment_mcapi_setting_fields();
        $Html_output = new MBJ_PayPal_Payment_Html_output();
        $Html_output->save_fields($mcapi_setting_fields);
        //self::paypal_payments_angelleye_get_mailchimp_lists(get_option('mailchimp_api_key'));
    }

    

}

MBJ_PayPal_Payment_General_Setting::init();
