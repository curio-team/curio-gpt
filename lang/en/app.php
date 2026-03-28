<?php

return [
    'nav' => [
        'manage_agents' => 'Manage Assistants',
        'monitoring' => 'Monitoring',
        'log_in' => 'Log in',
    ],

    'home' => [
        'title' => 'Welcome',
        'choose_agent' => 'Choose an assistant',
        'select_agent_to_start' => 'Select an assistant to start chatting.',
        'no_agents_available' => 'No assistants are available to you yet.',
    ],

    'common' => [
        'copy' => 'Copy',
        'copied' => 'Copied',
        'copy_code' => 'Copy code',
        'model' => 'Model',
        'message' => 'Message',
        'history' => 'History',
        'new' => 'New',
        'send' => 'Send',
        'type_a_message' => 'Type a message…',
        'cancel_edit' => 'Cancel edit',
        'enter_to_send' => 'Enter to send',
        'shift_enter_new_line' => 'Shift+Enter for new line',
        'enabled' => 'Enabled',
        'disabled' => 'Disabled',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'download' => 'Download',
        'upload' => 'Upload',
        'unknown' => 'Unknown',
        'unknown_student' => 'Unknown student',
        'no_agent' => 'No assistant',
        'tokens' => 'tokens',
        'date' => 'Date',
        'price_missing' => 'Price missing',
    ],

    'chat' => [
        'start_new_chat' => 'Start a new chat',
    ],

    'teacher' => [
        'agents' => [
            'manage_title' => 'Manage Assistants',
            'agents' => 'Assistants',
            'new_agent' => 'New Assistant',
            'no_agents_yet' => 'No assistants yet. Create one to get started.',
            'always_available' => 'Always available',
            'delete_confirm' => 'Delete this assistant?',
            'view_all_student_chats' => '→ View all student chats',
            'observations' => 'Assistant Observations',
            'revoke_history' => 'Revoke history',
            'revoke_history_confirm' => 'Are you sure you want to revoke conversation history for students for this assistant? This will immediately hide all existing conversations for students, but teachers can still view them.',

            'flash' => [
                'created' => 'Assistant created successfully.',
                'updated' => 'Assistant updated successfully.',
                'deleted' => 'Assistant deleted.',
                'attachment_uploaded' => 'Attachment uploaded.',
                'upload_failed' => 'Upload failed: :msg',
                'attachment_not_found' => 'Attachment not found.',
                'attachment_deleted' => 'Attachment deleted.',
                'history_revoked' => 'Conversation history has been revoked for students for this assistant.',
            ],

            'create' => [
                'back' => 'Back to Assistants',
                'title' => 'New Assistant',
                'submit' => 'Create Assistant',
            ],

            'edit' => [
                'back' => 'Back to Assistants',
                'title' => 'Edit Assistant',
                'submit' => 'Save Changes',
            ],

            'form' => [
                'tabs' => [
                    'general' => 'General',
                    'access' => 'Access',
                    'advanced' => 'Advanced',
                    'monitoring' => 'Monitoring',
                    'attachments' => 'Attachments',
                ],
                'name' => 'Name',
                'name_placeholder' => 'e.g. History Tutor',
                'short_description' => 'Short Description',
                'short_description_placeholder' => 'e.g. Helps you study history topics',
                'image' => 'Image',
                'upload_new_image' => 'Upload a new image to replace the current one.',
                'instructions' => 'Instructions',
                'instructions_placeholder' => 'Describe how the assistant should behave…',

                'availability' => 'Availability',
                'enabled' => 'Enabled',
                'disabled' => 'Disabled',
                'restricted_time_window' => 'Restricted to a time window',
                'not_restricted_time_window' => 'Not restricted to a time window',
                'from' => 'From',
                'until' => 'Until',
                'current_server_time' => 'Current server time:',

                'conversation_history' => 'Conversation history',
                'conversation_history_help' => 'Disable to prevent students from seeing or returning to previous chats for this assistant. Existing history remains until you revoke it.',
                'history_enabled' => 'History enabled',
                'history_disabled' => 'History disabled',

                'groups_heading' => 'Accessible to Groups',
                'groups_help' => 'Select which groups can access this assistant. Teachers always have access.',
                'search_groups' => 'Search groups…',
                'select_all' => 'Select all',
                'select_none' => 'Select none',
                'no_groups_match' => 'No groups match your search.',
                'no_groups_available' => 'No groups available.',

                'student_selectable_models' => 'Student-Selectable Models',
                'student_selectable_models_help' => 'Choose which OpenAI models students can pick for this assistant. Sorted by overall estimated price per 1M tokens (cheapest first). Leave empty to always use the system default.',
                'search_models' => 'Search models…',
                'no_models_match' => 'No models match your search.',

                'monitoring_enabled' => 'Monitoring enabled',
                'monitoring_disabled' => 'Monitoring disabled',
                'monitoring_instructions' => 'Monitoring Instructions',
                'monitoring_instructions_placeholder' => 'Describe how the system should observe chats and report notable events',
                'monitoring_help' => 'The monitoring function allows you to have the system observes the chat messages the student sends to this assistant. This way you can identify notable events, such as smart questions, or moments where the student gets stuck.',
                'monitoring_model' => 'Model for Monitoring',
                'use_system_default' => 'Use system default',

                'teacher_attachments' => 'Teacher Attachments',
                'attachments_help' => 'Upload documents that the AI can reference. Files are stored privately and also uploaded to the AI provider. Students’ chats with this assistant will include these as attachments.',
                'delete_attachment_confirm' => 'Delete this attachment?',
                'no_attachments_yet' => 'No attachments uploaded yet.',

                'per_million_cost' => '(~$:price per 1M)',
                'pricing_unknown' => 'pricing unknown',
            ],
        ],

        'chats' => [
            'title' => 'Monitoring Student Chats',
            'view_usage' => 'View Usage',
            'no_chats_yet' => 'No chats yet.',
            'show' => [
                'page_title' => 'Chat - :student',
                'back' => 'Back to Chats',
                'no_messages' => 'No messages in this conversation.',
            ],
        ],

        'observations' => [
            'title' => 'Agent Observations',
            'view_chats' => 'View Chats',
            'no_observations_yet' => 'No observations yet.',
            'back' => 'Back to Observations',
            'view_related_conversation' => 'View related conversation',
            'metadata' => 'Metadata',
            'observation' => 'Observation',
        ],

        'usage' => [
            'title' => 'Token Usage',
            'estimated_cost_overall' => 'Estimated Cost by Model (Overall)',
            'based_on_configured_pricing' => 'Based on configured per-model token pricing',
            'no_usage_yet' => 'No usage yet.',
            'estimated_cost_today' => 'Estimated Cost by Model (Today)',
            'todays_usage_only' => "Today's usage only",
            'no_usage_today' => 'No usage today.',
            'overall_leaderboard' => 'Overall Leaderboard',
            'top_users_total' => 'Top users by total tokens used',
            'today' => 'Today',
            'top_users_today' => 'Top users by tokens used today',
            'last_14_days_total' => 'Last 14 Days (Total)',
            'total_tokens_per_day' => 'Total tokens used per day by all users',
            'total_tokens' => 'Total tokens',
            'input_tokens' => 'Input tokens',
            'output_tokens' => 'Output tokens',
            'est_cost_usd' => 'Est. cost (USD)',
            'no_recent_usage' => 'No recent usage.',
        ],
    ],

    'errors' => [
        'error' => 'Error',
        'something_wrong' => 'Something went wrong',
        'unexpected_error' => 'An unexpected error occurred. Please try again later.',
        'go_back_home' => 'Go back home',
        'page_not_found' => 'Page not found',
        'not_found_message' => "The page you're looking for has moved or doesn't exist.",
    ],

];
