<?php
class DCFM_Shortcodes {
    /**
     * Initializes the DevNetix Contact Form plugin.
     */
    public function __construct() {
        // Registers shortcodes for form display and data access.
        add_shortcode('dcfm_display_form', array($this, 'dcfm_display_form'));
        add_shortcode('dcfm_display_data', array($this, 'dcfm_display_data'));

        // Leverages `wp_enqueue_scripts` for efficient form submission handling.
        add_action('wp_enqueue_scripts', array($this, 'trigger_form_submission'));
        add_action('dcfm_form_submitted', array($this, 'handle_form_submission'));

    }
    /**
     * Generates the HTML structure for the contact form.
     *
     * @return string The contact form HTML.
     */
    public function dcfm_display_form() {
        $content = '';
        $content .= '<form action="' . get_the_permalink() . '" method="post">';
        $content .=  wp_nonce_field( 'dcfm_form_submission', 'dcfm_nonce' );
        $content .= '<input type="text" name="dcfm_name" placeholder="Your Name" required/><br/>';
        $content .= '<input type="email" name="dcfm_email" placeholder="Your Email" required/><br/>';
        $content .= '<input type="text" name="dcfm_phone" placeholder="Your Phone"/><br/>';
        $content .= '<input type="text" name="dcfm_company" placeholder="Company Name"/><br/>';
        $content .= '<textarea name="dcfm_message" placeholder="Your Message" required></textarea><br/>';
        // Build the form with necessary fields and submit button
        $content .= '<input type="submit" value="Submit"/>';
        $content .= '</form>';
    
        return $content;
    }
    /**
     * Retrieves and displays submitted form data from the database.
     *
     * @return string The formatted list of submitted entries.
     */
    public function dcfm_display_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'devnetix_contact_form';
        $entries = $wpdb->get_results("SELECT * FROM {$table_name}");
    
        $content = '<ul>';
        foreach ($entries as $entry) {
            $content .= "<li>Name: {$entry->name}, Email: {$entry->email}, Phone: {$entry->phone}, Company: {$entry->company_name}, Message: {$entry->message}</li>";
        }
        $content .= '</ul>';
    
        return $content;
    }
    /**
     * Detects form submission via POST requests and triggers the dedicated action.
     *
     */
    public function trigger_form_submission() {
        global $wpdb;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            do_action('dcfm_form_submitted', $wpdb); // Notifies other plugin components about the submission.
        }
      }
    /**
     * Processes submitted form data and inserts it into the database.
     *
     * @param object $wpdb The WordPress database instance.
     */
    public function handle_form_submission($wpdb) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          // Check nonce validity before processing data
          if (!wp_verify_nonce($_POST['dcfm_nonce'], 'dcfm_form_submission')) {
            error_log('Invalid form submission detected!');
            return; // Stop processing if nonce is invalid
          }
      
          $table_name = $wpdb->prefix . 'devnetix_contact_form';
      
          $name = isset($_POST['dcfm_name']) ? sanitize_text_field($_POST['dcfm_name']) : '';
          $email = isset($_POST['dcfm_email']) ? sanitize_email($_POST['dcfm_email']) : '';
          $phone = isset($_POST['dcfm_phone']) ? sanitize_text_field($_POST['dcfm_phone']) : '';
          $company = isset($_POST['dcfm_company']) ? sanitize_text_field($_POST['dcfm_company']) : '';
          $message = isset($_POST['dcfm_message']) ? sanitize_textarea_field($_POST['dcfm_message']) : '';
      
          $result = $wpdb->insert(
            $table_name,
            array(
              'name' => $name,
              'email' => $email,
              'phone' => $phone,
              'company_name' => $company,
              'message' => $message
            ),
            array('%s', '%s', '%s', '%s', '%s')
          );
      
          if (false === $result) {
            error_log('Database Insertion Error: ' . $wpdb->last_error);
          } else {
            error_log('Data inserted successfully');
          }
        }
      }      
      
}
