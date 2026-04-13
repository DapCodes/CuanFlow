<?php

require __DIR__ . '/vendor/autoload.php';

// Load Laravel to use environment variables
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Google\Client;
use Google\Service\Drive;

/**
 * Utility script to generate a Google Drive Refresh Token for CuanFlow Backup.
 */
function generateRefreshToken() {
    // Try to get from config first, then env, then direct getenv
    $clientId = config('cuanflow-backup.google_drive.client_id') ?: env('GOOGLE_DRIVE_CLIENT_ID') ?: getenv('GOOGLE_DRIVE_CLIENT_ID');
    $clientSecret = config('cuanflow-backup.google_drive.client_secret') ?: env('GOOGLE_DRIVE_CLIENT_SECRET') ?: getenv('GOOGLE_DRIVE_CLIENT_SECRET');
    $redirectUri = 'https://developers.google.com/oauthplayground';

    if (!$clientId || !$clientSecret) {
        // Fallback: manually parse .env if everything else fails
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile);
            foreach ($lines as $line) {
                if (str_contains($line, 'GOOGLE_DRIVE_CLIENT_ID=')) {
                    $parts = explode('=', $line);
                    $clientId = trim($parts[1] ?? '');
                }
                if (str_contains($line, 'GOOGLE_DRIVE_CLIENT_SECRET=')) {
                    $parts = explode('=', $line);
                    $clientSecret = trim($parts[1] ?? '');
                }
            }
        }
    }

    if (!$clientId || !$clientSecret) {
        die("\n❌ ERROR: GOOGLE_DRIVE_CLIENT_ID or GOOGLE_DRIVE_CLIENT_SECRET not found in .env\n");
    }

    $client = new Client();
    $client->setClientId($clientId);
    $client->setClientSecret($clientSecret);
    $client->setRedirectUri($redirectUri);
    $client->addScope(Drive::DRIVE);
    $client->setAccessType('offline');
    $client->setPrompt('select_account consent');

    // Step 1: Generate Auth URL
    $authUrl = $client->createAuthUrl();

    echo "\n--- GOOGLE DRIVE REFRESH TOKEN GENERATOR ---\n";
    echo "1. Visit this URL in your browser:\n\n";
    echo "$authUrl\n\n";
    echo "2. Log in with your Google account and authorize the application.\n";
    echo "3. You will be redirected to OAuth Playground.\n";
    echo "4. Copy the 'code' parameter from the URL in your browser's address bar.\n";
    echo "   (Example: ?code=4/0AfgeX...)\n\n";
    
    echo "Enter the Authorization Code: ";
    $handle = fopen("php://stdin", "r");
    $authCode = trim(fgets($handle));

    if (empty($authCode)) {
        die("❌ ERROR: Auth code cannot be empty.\n");
    }

    try {
        // Step 2: Exchange Auth Code for Tokens
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        if (array_key_exists('error', $accessToken)) {
            throw new \Exception(isset($accessToken['error_description']) ? $accessToken['error_description'] : json_encode($accessToken['error']));
        }

        if (!isset($accessToken['refresh_token'])) {
            throw new \Exception("Refresh token not returned. Make sure to revoke access to the app in your Google Account settings and try again.");
        }

        echo "\n✅ SUCCESS!\n";
        echo "--------------------------------------------------\n";
        echo "Your Refresh Token is:\n\n";
        echo $accessToken['refresh_token'] . "\n\n";
        echo "--------------------------------------------------\n";
        echo "Copy this token and paste it into your .env file as:\n";
        echo "GOOGLE_DRIVE_REFRESH_TOKEN=your_token_here\n";
        echo "--------------------------------------------------\n";

    } catch (\Exception $e) {
        echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    }
}

generateRefreshToken();
