<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Exception;

class LastfmPlugin extends Plugin
{

    const DEFAULT_PATH = '/_lastfm';

    protected $lastfm_cache_id;

    public static function getSubscribedEvents() {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            return;
        }

        $this->initSetup();

        $this->enable([
            'onPagesInitialized' => ['pluginVueAppEndpoint', 0],
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
            'onTwigInitialized'   => ['onTwigInitialized', 0],
        ]);
    }

    private function initSetup()
    {
        $this->lastfm_cache_id = 'lastfm-scrobbler-'.$this->grav['cache']->getKey();
    }


    public function pluginVueAppEndpoint()
    {
        if (strpos($this->grav['uri']->path(), $this->config->get('plugins.lastfm.path', self::DEFAULT_PATH)) === false) {
            return;
        }

        $output = [
            'width' => $this->config->get('plugins.lastfm.width'),
            'height' => $this->config->get('plugins.lastfm.height'),
            'background_color' => $this->config->get('plugins.lastfm.background_color'),
            'display' => $this->config->get('plugins.lastfm.display'),
        ];

        $data = null;

        $cache_enabled = $this->config->get('plugins.lastfm.cache_enabled');
        
        if($cache_enabled) {
            $cache = $this->grav['cache'];

            if($data = $cache->fetch($this->lastfm_cache_id)) {

                $this->grav['debugger']->addMessage('lastfm cache hit.');

            } else {
                $this->grav['debugger']->addMessage('lastfm plugin cache miss.');
            }
        }

        if(!$data) {
            $username = $this->config->get('plugins.lastfm.lastfm_username');
            $api_key = $this->config->get('plugins.lastfm.api_key');
            
            if (!$username || !$api_key) {
                $output['error'] = 'Your Last.fm username or API Key is not configured. Please review your plugin settings.';
            } else {
                try {
                    $data = $this->lastfmApiRequest(
                        $api_key,
                        $username,
                        $this->config->get('plugins.lastfm.limit')
                    );
    
                } catch(Exception $e) {
                    $this->grav['log']->error('plugin.lastfm: '. $e->getMessage());
                }

                if ($cache_enabled)
                    $cache->save($this->lastfm_cache_id, $data, $this->config->get('plugins.lastfm.cache_lifetime'));
            }
        }

        $output['slides'] = $data;

        header('Content-Type: application/json');
        echo json_encode($output);
        exit();
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onAssetsInitialized()
    {
        $this->grav['assets']->addCss('plugin://lastfm/dist/style.css');
        $this->grav['assets']->addInlineJs(sprintf("window.lastfm_path = '%s';", $this->config->get('plugins.lastfm.path')));
        $this->grav['assets']->addJs('plugin://lastfm/dist/main.js', [
            'loading' => 'defer'
        ]);
    }

    /**
     * Add simple `lastfm()` Twig function
     */
    public function onTwigInitialized()
    {
        $this->grav['twig']->twig()->addFunction(
            new \Twig_SimpleFunction('lastfm', [$this, 'renderTemplate'])
        );
    }

    public function renderTemplate() {
        return $this->grav['twig']->processTemplate('partials/lastfm.html.twig');
    }

    private function lastfmApiRequest($apiKey, $username, $limit = 10) {

        $url = 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user='.$username.'&api_key='.$apiKey.'&limit='.$limit.'&format=json';

        try {

            $json = file_get_contents($url);

        } catch(Exception $e) {
            $this->grav['log']->error('plugin.lastfm: '. $e->getMessage());
        }

        $content = json_decode($json, true);
        
        return $this->parseScrobblingData($content);
    }

    private function parseScrobblingData($content) {

        $items = [];
        foreach($content['recenttracks']['track'] as $track) {
            // Track data
            $item = [
                'url' => $track['url'],
                'name' => $track['name'],
                'album' => $track['album']['#text'],
                'artist' => $track['artist']['#text']
            ];
            // Album image
            if($track['image']) {
                foreach($track['image'] as $image) {
                    if($image['size'] == $this->config->get('plugins.lastfm.image_size')) {
                        $item['image'] = $image['#text'];
                    }
                }
            }
            // Is nowplaying
            if(isset($track['@attr']) && $track['@attr']['nowplaying'] == true)
                $item['nowplaying'] = true;
            
            $items[] = $item;
        }

        return $items;
    }
}