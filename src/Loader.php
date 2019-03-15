<?php

namespace MPorter\VueTemplateLoader;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

/**
 * Class Loader
 * @package MPorter\VueTemplateLoader
 */
class Loader
{
    /**
     * @param string $name
     * @return string
     */
    public static function getTemplate(string $name): string
    {
        if (config('vue-loader.enabled') && config('app.env') === 'local') {
            $dir = resource_path('views/vue');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $filepath = resource_path("views/vue/{$name}.blade.php");
            self::saveTemplate($name, $filepath);
        }

        return "vue.{$name}";
    }

    /**
     * @param string $name
     * @param string $filepath
     * @return void
     */
    public static function saveTemplate(string $name, string $filepath)
    {
        $tmpResource = fopen('php://tmp', 'w');

        $client = new Client([
            'base_uri' => config('vue-loader.webpack_endpoint'),
        ]);

        try {
            $response = $client->request('get', "/${name}.html", [
                'sink' => $tmpResource,
                'verify' => false,
            ]);
            if ($response->getStatusCode() === 200) {
                file_put_contents($filepath, $tmpResource);
                fclose($tmpResource);
            }
        } catch (GuzzleException $e) {
            Log::notice('[VueLoader] Failed saving stream.', [
                'base_uri' => config('vue-loader.webpack_endpoint'),
                'template' => $name,
                'filepath' => $filepath,
            ]);
        }
    }
}
