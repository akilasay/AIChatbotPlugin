// jQuery(document).ready(function($) {
//     $('#send-btn').click(function() {
//         var message = $('#user-input').val().trim();
//         if (message !== '') {
//             $.ajax({
//                 url: chatbot_vars.ajaxurl,
//                 type: 'POST',
//                 data: { action: 'get_chatgpt_response', message: message },
//                 success: function(response) {
//                     $('#chatbot-messages').append('<div class="user-message">' + message + '</div>');
//                     $('#user-input').val('');
//                     setTimeout(function() {
//                         $('#chatbot-messages').append('<div class="bot-message">' + response + '</div>');
//                     }, 500); // Delayed response for a more realistic effect
//                 }
//             });
//         }
//     });
// });


jQuery(document).ready(function($) {
    $('#send-btn').click(function() {
        var message = $('#user-input').val().trim();
        if (message !== '') {
            $.ajax({
                url: chatbot_vars.ajaxurl,
                type: 'POST',
                data: { action: 'get_chatgpt_response', message: message },
                success: function(response) {
                    $('#chatbot-messages').append('<div class="user-message">' + message + '</div>');
                    $('#user-input').val('');
                    
                    // Save user message to database
                    $.post(chatbot_vars.ajaxurl, {
                        action: 'save_chat_message',
                        message_content: message,
                        user_type: 1 // User type 1 for user message
                    });

                    setTimeout(function() {
                        $('#chatbot-messages').append('<div class="bot-message">' + response + '</div>');
                        
                        // Save bot message to database
                        $.post(chatbot_vars.ajaxurl, {
                            action: 'save_chat_message',
                            message_content: response,
                            user_type: 0 // User type 0 for bot message
                        });
                    }, 500); // Delayed response for a more realistic effect
                }
            });
        }
    });
});
