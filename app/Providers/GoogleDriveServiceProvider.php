<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

/**
 * Registers the Google Drive filesystem driver
 * using the masbug/flysystem-google-drive-ext package.
 */
class GoogleDriveServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];

                if (! empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new Client;
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);

                $service = new Drive($client);

                // Add support for Shared Drives (optional for OAuth2 but good to have)
                $options['supportsAllDrives'] = true;
                $options['supportsTeamDrives'] = true;

                $adapter = new GoogleDriveAdapter(
                    $service,
                    $config['folder'] ?? null,
                    $options
                );

                return new FilesystemAdapter(
                    new Filesystem($adapter),
                    $adapter,
                    $config
                );
            });
        } catch (\Exception $e) {
            // Silently fail if Google Drive is not configured
            // This prevents errors when the package/credentials are not set up
        }
    }
}
