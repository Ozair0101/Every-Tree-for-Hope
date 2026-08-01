<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging
    |--------------------------------------------------------------------------
    |
    | FCM's HTTP v1 API. The legacy `key=AAAA...` server key was switched off by
    | Google in 2024, so authentication is OAuth2 against a service account:
    | download the JSON from Firebase Console → Project settings → Service
    | accounts, store it OUTSIDE the web root, and point FCM_CREDENTIALS at it.
    |
    |   FCM_PROJECT_ID=every-tree-for-hope
    |   FCM_CREDENTIALS=/var/secrets/firebase-service-account.json
    |
    | Leave FCM_PROJECT_ID unset and the transport reports itself unconfigured:
    | pushes are logged and marked skipped rather than throwing. That is what
    | keeps local development and the test suite working without credentials.
    |
    | `expo_first` reflects how this app is actually built today — the React
    | Native client registers ExponentPushToken values, which FCM cannot accept.
    | The dispatcher routes each token by its own format, so both work side by
    | side and a migration from Expo to bare FCM needs no code change.
    |
    */
    /*
    |--------------------------------------------------------------------------
    | Google Maps
    |--------------------------------------------------------------------------
    |
    | Used by the admin review screen to show where a submission was recorded.
    | Optional: without a key the panel falls back to plain Google Maps links,
    | which need no authentication. An unauthenticated embed renders a grey
    | "for development purposes only" tile, so it is not attempted.
    |
    | The key needs only the Maps Embed API, and should be restricted to your
    | admin domain in the Google Cloud console — an embed key travels to the
    | browser and is readable by anyone who opens the page.
    |
    */
    'google_maps' => [
        'key' => env('GOOGLE_MAPS_KEY'),
    ],

    'fcm' => [
        'project_id' => env('FCM_PROJECT_ID'),
        'credentials' => env('FCM_CREDENTIALS'),
        // Seconds. FCM access tokens live an hour; refreshing a minute early
        // avoids a race where a token expires mid-flight.
        'token_ttl' => 3540,
        'timeout' => env('FCM_TIMEOUT', 10),
        // Android notification channel id the client app registers.
        'android_channel' => env('FCM_ANDROID_CHANNEL', 'default'),
    ],

];
