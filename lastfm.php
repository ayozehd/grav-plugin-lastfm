<?php
namespace Grav\Plugin;
use Grav\Common\Plugin;
class LastfmPlugin extends Plugin
{
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
            'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
            'onTwigSiteVariables' => ['onTwigSiteVariables', 0],
            'onTwigInitialized'   => ['onTwigInitialized', 0]
        ]);
    }

    private function initSetup()
    {
        $this->lastfm_cache_id = md5('lastfm-scrobbler'.$this->grav['cache']->getKey());
    }

    /**
     * Add current directory to twig lookup paths.
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }
    /**
     * Add CSS and JS to page header
     */
    public function onTwigSiteVariables()
    {
        $config = $this->config->get('plugins.lastfm');
        if ($config['built_in_css']) {
            $this->grav['assets']->addCss('plugin://lastfm/assets/css-compiled/lastfm.css', 999);
        }

        $cache = $this->grav['cache'];

        $data = $cache->fetch($this->lastfm_cache_id);

        if($data === false) {
            $data = $this->scrobblingRequest($config['api_key'], $config['lastfm_user'], $config['limit']);
            $cache->save($this->lastfm_cache_id, $data, $config['cache_lifetime']);
        }

        $this->grav['twig']->twig_vars['lastfm_scrobbling'] = $data;
    }

    /**
     * Add simple `sharer()` Twig function
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

    public function scrobblingRequest($apiKey, $username, $limit = 10) {
        $url = 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user='.$username.'&api_key='.$apiKey.'&limit='.$limit.'&format=json';
        if(!$json = @file_get_contents($url))
            return 'Error on Last.fm request';
        $content = json_decode($json, true);
        
        $items = [];
        foreach($content['recenttracks']['track'] as $track) {
            //var_dump($track);
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