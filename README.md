## This plugin has been created to the add AI ChatGPT based chatbot to your wordpress site

1.  Clone or download the my-chatbot folder and add that to your Wordpress plugin folder/directory
2.  Go to Wordpress Dashboard --> Plugins --> ACTIVATE the plugin my-chatbot
3.  Add the OPEN API KEY and OPEN API URL of your OPEN API account to the wp-config.php file

/** Define API token constant. */
define('API_TOKEN', '');

/** Define API URL constant. */
define('API_URL', 'https://api.openai.com/v1/chat');


4.  use SHORTCODE `[chatbot]` to add the chatbot to your page

![image](https://github.com/akilasay/AIChatbotPlugin/assets/145580412/ec224f3a-0bee-4207-9508-8688af494636)

