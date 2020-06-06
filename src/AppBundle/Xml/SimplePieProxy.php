<?php

namespace AppBundle\Xml;

class SimplePieProxy
{
    protected $feed;

    /**
     * Create a new Proxy for SimplePie.
     *
     * @param string $cache       Path to cache folder
     * @param int    $itemLimit   The maximum number of items to return
     * @param bool   $enableCache Enable caching
     */
    public function __construct($cache, $itemLimit = 25, $enableCache = true, $proxyHost = '', $proxyPort = 0)
    {
        $this->feed = new \SimplePie();
        $this->feed->set_cache_location($cache);
        $this->feed->set_item_limit($itemLimit);

        // Force the given URL to be treated as a feed
        $this->feed->force_feed(true);
        $this->feed->enable_cache($enableCache);

        if (!empty($proxyHost) && $proxyPort != 0) {
            $this->feed->set_curl_options(array(
                CURLOPT_PROXYTYPE => CURLPROXY_HTTP,
                CURLOPT_PROXY => $proxyHost,
                CURLOPT_PROXYPORT => $proxyPort,
                CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
                CURLOPT_PROXYUSERPWD => ''
            ));
        }

        // be sure that the cache is writable by SimplePie
        if ($enableCache && !is_writable($cache)) {
            @mkdir($cache, 0777, true);
            chmod($cache, 0777);
        }
    }

    /**
     * Set the URL of the feed you want to parse.
     *
     * @param string $url
     *
     * @see  SimplePie->set_feed_url
     */
    public function setUrl($url)
    {
        $this->feed->set_feed_url($url);

        return $this;
    }

    /**
     * Initialize the feed object.
     *
     * @return \SimplePie
     *
     * @see  SimplePie->init
     */
    public function init()
    {
        $this->feed->init();

        return $this->feed;
    }
}
