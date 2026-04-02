<?php

return [
    'nav' => [
        'manage_agents' => 'Assistenten beheren',
        'monitoring' => 'Monitoring',
        'log_in' => 'Inloggen',
    ],

    'home' => [
        'title' => 'Welkom',
        'choose_agent' => 'Kies een assistent',
        'select_agent_to_start' => 'Selecteer een assistent om te beginnen met chatten.',
        'no_agents_available' => 'Er zijn nog geen assistenten voor je beschikbaar.',
    ],

    'common' => [
        'copy' => 'Kopiëren',
        'copied' => 'Gekopieerd',
        'copy_code' => 'Code kopiëren',
        'model' => 'Model',
        'message' => 'Bericht',
        'history' => 'Geschiedenis',
        'new' => 'Nieuw',
        'send' => 'Versturen',
        'type_a_message' => 'Typ een bericht...',
        'cancel_edit' => 'Bewerking annuleren',
        'enter_to_send' => 'Enter om te versturen',
        'shift_enter_new_line' => 'Shift+Enter voor nieuwe regel',
        'enabled' => 'Ingeschakeld',
        'disabled' => 'Uitgeschakeld',
        'edit' => 'Bewerken',
        'delete' => 'Verwijderen',
        'download' => 'Downloaden',
        'upload' => 'Uploaden',
        'unknown' => 'Onbekend',
        'unknown_student' => 'Onbekende student',
        'no_agent' => 'Geen agent',
        'tokens' => 'tokens',
        'date' => 'Datum',
        'price_missing' => 'Prijs ontbreekt',
    ],

    'chat' => [
        'start_new_chat' => 'Nieuwe chat starten',
        'ai_disclaimer' => 'AI maakt fouten. Deze AI kan niet op het internet zoeken en weet alleen wat het is getraind.',
    ],

    'teacher' => [
        'agents' => [
            'manage_title' => 'Assistenten beheren',
            'agents' => 'Assistenten',
            'new_agent' => 'Nieuwe assistent',
            'no_agents_yet' => 'Nog geen assistenten. Maak er een om te beginnen.',
            'always_available' => 'Altijd beschikbaar',
            'delete_confirm' => 'Deze assistent verwijderen?',
            'view_all_student_chats' => '→ Alle studentchats bekijken',
            'observations' => 'Assistentobservaties',
            'revoke_history' => 'Geschiedenis intrekken',
            'revoke_history_confirm' => 'Weet je zeker dat je de gesprekshistorie voor studenten voor deze assistent wilt intrekken? Dit zal direct alle bestaande gesprekken voor studenten verbergen, maar docenten kunnen ze nog steeds bekijken.',

            'flash' => [
                'created' => 'Assistent succesvol aangemaakt.',
                'updated' => 'Assistent succesvol bijgewerkt.',
                'deleted' => 'Assistent verwijderd.',
                'attachment_uploaded' => 'Bijlage geüpload.',
                'upload_failed' => 'Upload mislukt: :msg',
                'attachment_not_found' => 'Bijlage niet gevonden.',
                'attachment_deleted' => 'Bijlage verwijderd.',
                'history_revoked' => 'Gesprekshistorie is ingetrokken voor studenten van deze assistent.',
            ],

            'create' => [
                'back' => 'Terug naar assistenten',
                'title' => 'Nieuwe assistent',
                'submit' => 'Assistent aanmaken',
            ],

            'edit' => [
                'back' => 'Terug naar assistenten',
                'title' => 'Assistent bewerken',
                'submit' => 'Wijzigingen opslaan',
            ],

            'form' => [
                'tabs' => [
                    'general' => 'Algemeen',
                    'access' => 'Toegang',
                    'advanced' => 'Geavanceerd',
                    'monitoring' => 'Monitoring',
                    'attachments' => 'Bijlagen',
                ],
                'name' => 'Naam',
                'name_placeholder' => 'bijv. Geschiedenistutor',
                'short_description' => 'Korte beschrijving',
                'short_description_placeholder' => 'bijv. Helpt je met geschiedenisonderwerpen',
                'image' => 'Afbeelding',
                'upload_new_image' => 'Upload een nieuwe afbeelding om de huidige te vervangen.',
                'instructions' => 'Instructies',
                'instructions_placeholder' => 'Beschrijf hoe de assistent zich moet gedragen…',

                'availability' => 'Beschikbaarheid',
                'enabled' => 'Ingeschakeld',
                'disabled' => 'Uitgeschakeld',
                'restricted_time_window' => 'Beperkt tot een tijdvenster',
                'not_restricted_time_window' => 'Niet beperkt tot een tijdvenster',
                'from' => 'Van',
                'until' => 'Tot',
                'current_server_time' => 'Huidige servertijd:',

                'conversation_history' => 'Conversatielijst',
                'conversation_history_help' => 'Uitschakelen om te voorkomen dat studenten de vorige chats voor deze assistent kunnen zien of terugkeren. Bestaande geschiedenis blijft tot je het intrekt.',
                'history_enabled' => 'Geschiedenis ingeschakeld',
                'history_disabled' => 'Geschiedenis uitgeschakeld',

                'groups_heading' => 'Toegankelijk voor groepen',
                'groups_help' => 'Selecteer welke groepen toegang hebben tot deze assistent. Docenten hebben altijd toegang.',
                'search_groups' => 'Groepen zoeken…',
                'select_all' => 'Alles selecteren',
                'select_none' => 'Niets selecteren',
                'no_groups_match' => 'Geen groepen komen overeen met je zoekopdracht.',
                'no_groups_available' => 'Geen groepen beschikbaar.',

                'student_selectable_models' => 'Modellen die studenten kunnen kiezen',
                'student_selectable_models_help' => 'Kies welke OpenAI-modellen studenten voor deze assistent kunnen selecteren. Gesorteerd op geschatte totale prijs per 1M tokens (goedkoopst eerst). Laat leeg om altijd de systeemstandaard te gebruiken.',
                'search_models' => 'Modellen zoeken…',
                'no_models_match' => 'Geen modellen komen overeen met je zoekopdracht.',

                'monitoring_enabled' => 'Monitoring ingeschakeld',
                'monitoring_disabled' => 'Monitoring uitgeschakeld',
                'monitoring_instructions' => 'Monitoring-instructies',
                'monitoring_instructions_placeholder' => 'Beschrijf hoe het systeem chats moet observeren en notable events moet rapporteren',
                'monitoring_help' => 'Met de monitoringfunctie kan het systeem de chatberichten observeren die de student naar deze assistent stuurt. Zo kun je opmerkelijke gebeurtenissen identificeren, zoals slimme vragen of momenten waarop de student vastloopt.',
                'monitoring_model' => 'Model voor monitoring',
                'use_system_default' => 'Systeemstandaard gebruiken',

                'teacher_attachments' => 'Docentbijlagen',
                'attachments_help' => 'Upload documenten waar de AI naar kan verwijzen. Bestanden worden privé opgeslagen en ook geüpload naar de AI-provider. Chats van studenten met deze assistent bevatten deze bijlagen.',
                'delete_attachment_confirm' => 'Deze bijlage verwijderen?',
                'no_attachments_yet' => 'Nog geen bijlagen geüpload.',

                'per_million_cost' => '(~$:price per 1 mln)',
                'pricing_unknown' => 'prijs onbekend',
            ],
        ],

        'chats' => [
            'title' => 'Monitoring van studentchats',
            'view_usage' => 'Gebruik bekijken',
            'no_chats_yet' => 'Nog geen chats.',
            'show' => [
                'page_title' => 'Chat - :student',
                'back' => 'Terug naar chats',
                'no_messages' => 'Geen berichten in dit gesprek.',
            ],
        ],

        'observations' => [
            'title' => 'Assistentobservaties',
            'view_chats' => 'Chats bekijken',
            'no_observations_yet' => 'Nog geen observaties.',
            'back' => 'Terug naar observaties',
            'view_related_conversation' => 'Gerelateerd gesprek bekijken',
            'metadata' => 'Metadata',
            'observation' => 'Observatie',
        ],

        'usage' => [
            'title' => 'Tokengebruik',
            'estimated_cost_overall' => 'Geschatte kosten per model (totaal)',
            'based_on_configured_pricing' => 'Gebaseerd op geconfigureerde prijs per model',
            'no_usage_yet' => 'Nog geen gebruik.',
            'estimated_cost_today' => 'Geschatte kosten per model (vandaag)',
            'todays_usage_only' => 'Alleen het gebruik van vandaag',
            'no_usage_today' => 'Vandaag geen gebruik.',
            'overall_leaderboard' => 'Algemeen klassement',
            'top_users_total' => 'Topgebruikers op basis van totale tokens',
            'today' => 'Vandaag',
            'top_users_today' => 'Topgebruikers op basis van tokens vandaag',
            'last_14_days_total' => 'Afgelopen 14 dagen (totaal)',
            'total_tokens_per_day' => 'Totaal aantal tokens per dag voor alle gebruikers',
            'total_tokens' => 'Totaal aantal tokens',
            'input_tokens' => 'Invoertokens',
            'output_tokens' => 'Uitvoertokens',
            'est_cost_usd' => 'Geschatte kosten (USD)',
            'no_recent_usage' => 'Geen recent gebruik.',
        ],
    ],

    'errors' => [
        'error' => 'Fout',
        'something_wrong' => 'Er ging iets mis',
        'unexpected_error' => 'Er is een onverwachte fout opgetreden. Probeer het later opnieuw.',
        'go_back_home' => 'Terug naar de startpagina',
        'page_not_found' => 'Pagina niet gevonden',
        'not_found_message' => 'De pagina die je zoekt is verplaatst of bestaat niet.',
    ],

];
