<?php
class DCFM_REST_API {
   /**
   * Initializes the DCFM Contact Form REST API functionality.
   *
   * This function is automatically called when WordPress initializes its REST API.
   * It sets up the API routes for handling form data submissions and retrievals.
   */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_routes'));
    }
   /**
   * Registers REST API routes for the DevNetix Contact Form.
   *
   * This function defines two routes:
   * - `/insert`: Accepts POST requests with form data and triggers insertion.
   * - `/select`: Handles GET requests and retrieves submitted entries.
   *
   * Each route is associated with a specific callback function:
   * - `/insert`: Calls `insert_data` to process and store form data.
   * - `/select`: Calls `select_data` to fetch and return existing entries.
   */
    public function register_routes() {
        register_rest_route('dcfm/v1', '/insert', array(
            'methods' => 'POST',
            'callback' => array($this, 'insert_data'),
        ));

        register_rest_route('dcfm/v1', '/select', array(
            'methods' => 'GET',
            'callback' => array($this, 'select_data'),
        ));
    }
   /**
   * Processes a POST request containing contact form data.
   *
   * This function performs the following actions:
   * 1. Sanitizes user input to prevent security vulnerabilities.
   * 2. Extracts data from the request body.
   * 3. Inserts the data into the `devnetix_contact_form` database table.
   * 4. Checks for successful insertion and returns appropriate responses:
   *    - 200 OK with a success message on successful insertion.
   *    - 500 Internal Server Error with an error message on failure.
   *
   * @param WP_REST_Request $request The request object containing form data.
   * @return WP_REST_Response The response object with success or error message.
   */
    public function insert_data($request) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'devnetix_contact_form';
    
        $name = sanitize_text_field($request['name']);
        $email = sanitize_email($request['email']);
        $phone = sanitize_text_field($request['phone']);
        $company = sanitize_text_field($request['company']);
        $message = sanitize_textarea_field($request['message']);
    
        $result = $wpdb->insert(
            $table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'company_name' => $company,
                'message' => $message
            )
        );
    
        if ($result) {
            return new WP_REST_Response('Data inserted successfully', 200);
        } else {
            return new WP_REST_Response('Failed to insert data', 500);
        }
    }    
   /**
   * Retrieves all submitted form entries from the database.
   *
   * This function handles GET requests sent to the `/select` route.
   * It performs the following actions:
   * 1. Connects to the WordPress database using the global $wpdb object.
   * 2. Constructs the table name by combining the WordPress prefix and "devnetix_contact_form".
   * 3. Executes a SQL query to fetch all entries from the specified table.
   * 4. Checks if any entries were retrieved:
   *    - If yes, returns a 200 OK response with an array containing all entries.
   *    - If no, returns a 404 Not Found response with a "No data found" message.
   *
   * @return WP_REST_Response An object containing retrieved entries or a no-data message.
   */
    public function select_data() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'devnetix_contact_form';
    
        $entries = $wpdb->get_results("SELECT * FROM {$table_name}");
        if (!empty($entries)) {
            return new WP_REST_Response($entries, 200);
        } else {
            return new WP_REST_Response('No data found', 404);
        }
    }    
}
