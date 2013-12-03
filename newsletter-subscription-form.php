<?php
/*
Plugin Name: Newsletter Subscription Widget
Description: Collect new subscribers to your newsletter through your WordPress website
Version: 1.0
*/

error_reporting( E_ALL );

require_once ( dirname(__FILE__) . '/inc/request.php' );
require_once ( dirname(__FILE__) . '/inc/api.php' );

/**
 * Add newsletter widget.
 */
class NewsletterSubscriptionForm extends WP_Widget {

    public function __construct($number = null) {
        load_plugin_textdomain( 'newsletter-subscription-widget', false, 'newsletter-subscription-widget/locale' );

        include dirname(__FILE__) . '/newsletter-subscription-form-overrides.php';
        $baseId = 'newslettersubscriptionform';

        parent::__construct(
            $baseId,
            $name,
            array( 'description' => $description )
        );

        if($number){
            $this->id = $baseId.'-'.$number;
            $this->number = $number;
        }

        // Register style content
        wp_register_style( 'newsletter-subscription-backend', plugins_url('newsletter-subscription-form/css/newsletter_subscription_backend.css') );
        wp_register_style( 'newsletter-subscription-backend-overrides', plugins_url('newsletter-subscription-form/css/newsletter_subscription_backend_overrides.css') );

        wp_register_style( 'newsletter-subscription-frontend', plugins_url('newsletter-subscription-form/css/newsletter_subscription_frontend.css') );

        // Register script content
        wp_register_script( 'newsletter-base', plugins_url('newsletter-subscription-form/js/newsletter_base.js') );
        wp_register_script( 'newsletter-subscription-backend', plugins_url('newsletter-subscription-form/js/newsletter_subscription_backend.js') );
        wp_register_script( 'newsletter-subscription-frontend', plugins_url('newsletter-subscription-form/js/newsletter_subscription_frontend.js') );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
        if( !$instance['registered'] || !$instance['selected_list'] )
            return false;

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('newsletter-base');
        wp_enqueue_script('newsletter-subscription-frontend');

        wp_enqueue_style( 'newsletter-subscription-frontend' );

        include dirname(__FILE__) . '/views/newsletter_subscription_frontend.php';
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance )
    {
        include dirname(__FILE__) . '/newsletter-subscription-form-overrides.php';

        $instance['username']            = isset( $instance[ 'username' ] ) ? $instance[ 'username' ] : '';
        $instance['title']               = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : $widgetTitle;
        $instance['description']         = isset( $instance[ 'description' ] ) ? $instance[ 'description' ] : $widgetDescription;
        $instance['confirmationmessage'] = isset( $instance[ 'confirmationmessage' ] ) ? $instance[ 'confirmationmessage' ] : $widgetConfirmationMessage;
        $instance['submit_txt']          = isset( $instance[ 'submit_txt' ] ) ? $instance[ 'submit_txt' ] : $widgetSubmitButton;

        $instance['registered']          = isset( $instance[ 'registered' ]) ? ($instance[ 'registered' ] !== true ? false : true) : false;
        $instance['is_lists_open']       = isset( $instance[ 'is_lists_open' ] ) ? $instance[ 'is_lists_open' ] : 0;
        $instance['is_settings_open']    = isset( $instance[ 'is_settings_open' ] ) && $instance[ 'is_settings_open' ] != '' ? $instance[ 'is_settings_open' ] : 1;

        $instance['registered'] = $instance['registered'] && !isset($instance['user']->id) ? false : $instance['registered'];

        $instance['widget_id'] = $this->id;

        if( isset($instance['registered']) && $instance['registered'] !== false ) {
            $this->getLists($instance);
        }

        wp_enqueue_script('newsletter-base');
        wp_enqueue_script('newsletter-subscription-backend');
        wp_enqueue_style( 'newsletter-subscription-backend' );
        wp_enqueue_style( 'newsletter-subscription-backend-overrides' );
 
        include dirname(__FILE__) . '/views/newsletter_subscription_backend.php';

        return $instance;
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance )
    {
        $instance = $new_instance;
        $instance['error_code'] = 0;

        if( (isset($instance['password']) && $instance['password'] != '') ) {
            $this->loginUser($instance);
        }
        else {
            $instance['user'] = isset($old_instance['user']) ? $old_instance['user'] : null; 
            $instance['registered'] = isset($old_instance['registered']) ? $old_instance['registered'] : false;
        }

        if( $instance['registered'] ) {
            $this->getUser($instance);
            $this->getClient($instance);
            $this->getLists($instance);
        }

        $instance['widget_id'] = $this->id;

        unset( $instance['password'] );

        return $instance;
    }

    /**
     * 
     */
    public function getHTMLFields( $instance )
    {
        ob_start();
        include dirname(__FILE__) . "/views/newsletter_subscription_backend_fields.php";
        return ob_get_clean();
    }

   /**
     * Login the user
     *
     * @param array &$instance Values just sent to be saved.
     *
     */
    private function loginUser( &$instance )
    {
        $instance['registered'] = false;
        $instance['user'] = NewsletterAPI::getLogin($instance['username'], $instance['password']);

        if(!$instance['user']) {
            $instance['error_code'] = 1;
            return false;
        }

        $instance['registered'] = true;

        return $instance['user'];
    }

    /**
     * Retrieve user information.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getUser( &$instance )
    {
        $instance['user'] = NewsletterAPI::getUser($instance['user']->user_key, $instance['user']->user_id);

        $instance['user']->user_id = $instance['user']->id;
        $instance['user']->gravatar = 'https://www.gravatar.com/avatar/' . md5(strtolower(trim($instance['user']->email)));

        return $instance['user'];
    }

    /**
     * Retrieve client information.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getClient( &$instance )
    {
        $instance['client'] = NewsletterAPI::getClient($instance['user']->user_key);
        return $instance['client'];
    }

    /**
     * Retrieve user's lists.
     *
     * @param array &$instance Values just sent to be saved.
     *
     * @return bool Is the process completed successfully.
     */
    private function getLists( &$instance )
    {
        $this->refreshLists($instance);
        $this->getSelectedList($instance);

        update_option('newsletter_subscription_widget_'.$this->id.'_lists', $instance['lists']);

        return $instance['lists'];
    }

    /**
     * Refresh list with saved data subscribers lists.
     *
     * @param array &$instance Values just sent to be saved.
     *
     */
    private function refreshLists( &$instance )
    {
        $lists = NewsletterAPI::getLists($instance['user']->user_key);

        // If the user has no list, just create one
        if( count($lists->lists) == 0 ){
            $this->createList($instance);
            $lists = NewsletterAPI::getLists($instance['user']->user_key);
        }

        $instance['lists'] = array();

        foreach ($lists->lists as $id => $list) {
            $this->refreshFields($instance, $list);
        }

        return $instance['lists'];
    }

    /**
     * Creates subscribers lists.
     *
     * @param array &$instance Values just sent to be saved.
     *
     */
    private function createList( $instance )
    {
        $listCreateParams = array(
            'user_key'     => $instance['user']->user_key,
            'name'         => 'Wordpress Default List',
            'sender_name'  => $instance['user']->first_name . " " . $instance['user']->last_name,
            'sender_email' => $instance['user']->email,
            'list_policy'  => 'accepted',
            'list_setup'   => 'true'
        );

        return NewsletterAPI::createList($listCreateParams);
    }

    /**
     *
     */
    private function refreshFields( &$instance, $list )
    {
        $list->fields = array();
        $instance['lists'][$list->id] = $list;

        foreach ((array)NewsletterAPI::getFields($instance['user']->user_key, $list->id) as $name => $values) {
            $list->fields[$name] = array( 'type' =>  $values ); 
        }
    }

    public function getSelectedList( &$instance )
    {
        $instance['selected_list'] = isset($instance['opt-lists']) && $instance['opt-lists'] !== '' && isset($instance['lists'][$instance['opt-lists']]) 
            ? $instance['lists'][$instance['opt-lists']] 
            : reset($instance['lists']);

        foreach ($instance['selected_list']->fields as $name => $values) {
            $show = isset($instance['field-chk-'.$name]) && $instance['field-chk-'.$name] == 'on' ? true : $name == 'email' ? true : false;
            $label = isset($instance['field-'.$name]) ? $instance['field-'.$name] : "";
            $index = isset($instance['field-chk-index-'.$name]) && $instance['field-chk-index-'.$name] != "" ? $instance['field-chk-index-'.$name] : 999 - $show;

            $instance['selected_list']->fields[$name]['show'] = $show;
            $instance['selected_list']->fields[$name]['label'] = $label;
            $instance['selected_list']->fields[$name]['index'] = $index;
        }

        uasort($instance['selected_list']->fields, "self::sortByFieldOrder");
        $instance['lists'][$instance['selected_list']->id] = $instance['selected_list'];

        return $instance['selected_list'];
    }

    /**
     *
     */
    public static function sortByFieldOrder( $el1, $el2 )
    {
        return $el1['index'] - $el2['index'];
    }

}

add_action('widgets_init',
     create_function('', 'return register_widget("NewsletterSubscriptionForm");')
);

add_action( 'wp_ajax_get_fields', function(){
    $widget = new NewsletterSubscriptionForm($_POST['widget_index']);

    $instance = $widget->get_settings();
    $instance = $instance[$_POST['widget_index']];

    $instance['opt-lists'] = $_POST['list_id'];

    $widget->getSelectedList($instance);

    die($widget->getHTMLFields($instance));
});

?>
