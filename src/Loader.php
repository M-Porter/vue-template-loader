<?php

namespace MPorter\VueTemplateLoader;

use Illuminate\Support\Facades\App;
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
        if (App::isLocal()) {
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
    private static function saveTemplate(string $name, string $filepath)
    {
        $ch = curl_init(self::buildUrl($name));

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($result !== false && $statusCode === 200) {
            file_put_contents($filepath, $result);
        } else {
            Log::notice('[VueLoader] Failed saving stream.', [
                'uri' => config('vue_loader.webpack_endpoint'),
                'template' => $name,
                'filepath' => $filepath,
            ]);
        }
    }

    /**
     * @param string $name
     * @return string
     */
    private static function buildUrl(string $name): string
    {
        $base = rtrim(config('vue_loader.webpack_endpoint'), '/');
        return "{$base}/${name}.html";
    }
}
