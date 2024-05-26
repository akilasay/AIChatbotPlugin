<?php
/*
Plugin Name: My Chatbot
Description: A simple chatbot plugin for WordPress.
Version: 1.0
Author: Akila
License: GPL v2 or later
*/

// Create database table on plugin activation
function create_chat_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'chat_messages';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        message text NOT NULL,
        date datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'create_chat_table');


// Enqueue scripts and styles
function chatbot_enqueue_scripts() {
    wp_enqueue_style('chatbot-style', plugin_dir_url(__FILE__) . 'css/chatbot-style.css');
    wp_enqueue_script('chatbot-script', plugin_dir_url(__FILE__) . 'js/chatbot-script.js', array('jquery'), null, true);
    wp_localize_script('chatbot-script', 'chatbot_vars', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'chatbot_enqueue_scripts');


// Handle user input and save chat message
function save_chat_message($message_content, $user_type) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'chat_messages';
    
    $wpdb->insert(
        $table_name,
        array(
            'message_content' => $message_content,
            'user_type' => $user_type,
        )
    );
}


add_action('wp_ajax_save_chat_message', 'save_chat_message');
add_action('wp_ajax_nopriv_save_chat_message', 'save_chat_message');


// Send to chatGPT API
function get_chatgpt_response($input) {
    $api_key = API_TOKEN;
    $endpoint = API_URL ;

    // request data with model configuration
    $data = array(
        'model' => 'gpt-3.5-turbo',
        'messages' => array(
            array(
                'role' => 'system',
                'content' => ''
            ),
            array(
                'role' => 'user',
                'content' => $input
            )
        )
    );

    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    );

    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => implode("\r\n", $headers),
            'content' => json_encode($data)
        )
    );

    $context = stream_context_create($options);
    $response = file_get_contents($endpoint, false, $context);
    
    if ($response === false) {
        return 'Error fetching response from ChatGPT API';
    } else {
        $decoded_response = json_decode($response, true);
        $assistant_response = $decoded_response['choices'][0]['message']['content']; // Get the assistant's response
        return $assistant_response;
    }
}


add_action('wp_ajax_get_chatgpt_response', 'get_chatgpt_response_ajax');
add_action('wp_ajax_nopriv_get_chatgpt_response', 'get_chatgpt_response_ajax');


function get_chatgpt_response_ajax() {
    $input = sanitize_text_field($_POST['message']);
    $response = get_chatgpt_response($input);
    echo $response;
    wp_die();
}



// Display chatbot interface
function chatbot_interface() {
    ?>
    <div id="chatbot-container">
        <div id="chatbot-messages"></div>
        <div id="user-input-container">
            <textarea id="user-input" placeholder="Type your message here..."></textarea>
            <button id="send-btn">Send</button>
        </div>
    </div>
    <?php
}
add_shortcode('chatbot', 'chatbot_interface');

add_action('wp_ajax_save_chat_message', 'save_chat_message_ajax');
add_action('wp_ajax_nopriv_save_chat_message', 'save_chat_message_ajax');

function save_chat_message_ajax() {
    $message_content = sanitize_text_field($_POST['message_content']);
    $user_type = intval($_POST['user_type']);
    save_chat_message($message_content, $user_type);
    wp_die();
}

