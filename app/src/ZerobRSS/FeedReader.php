<?php
declare(strict_types=1);

namespace ZerobRSS;

use SimplePie;

class FeedReader
{
    /** @var SimplePie */
    private $simplePie;

    public function __construct(Config $config, SimplePie $simplePie)
    {
        $this->simplePie = $simplePie;

        $this->simplePie->set_cache_location($config->projectRoot.'/cache');
        $this->simplePie->enable_cache(true);
    }



    /**
     * Open feed URI for reading
     */
    public function open($feedUri)
    {
        $this->simplePie->set_feed_url($feedUri);
        $this->simplePie->init();
    }



    /**
     * Get feed description
     */
    public function getFeedTitle()
    {
        return $this->simplePie->get_title();
    }



    /**
     * Get feed description
     */
    public function getFeedDescription()
    {
        return $this->simplePie->get_description();
    }



    /**
     * Get first link from feed, most of the time a link to the website,
     * otherwise you might get the link to the feed itself.
     */
    public function getFeedLink()
    {
        return $this->simplePie->get_link();
    }



    /**
     * Get all posts from feed
     */
    public function getPosts()
    {
        $spItems = $this->simplePie->get_items();
        $items = [];

        foreach ($spItems as $spItem) {
            /**
             * $item values to read:
             * - get_id()
             * - get_title()
             * - get_content() <-> get_description()
             * - get_categories()
             * - get_authors()
             * - get_date('Y-m-d H:i:s') <-> get_gmdate('Y-m-d H:i:s')
             * - get_link() <-> get_permalink()
             * - get_enclosures() - Podcast files etc
             */

            /**
             * Build object to return
             */
            $item = (object) [
                'id'         => $spItem->get_id(true),
                'title'      => $spItem->get_title(),
                'link'       => $spItem->get_permalink(),
                'date'       => $spItem->get_gmdate('Y-m-d H:i:s'),
                'content'    => $spItem->get_content(),
                'categories' => [],
                'authors'    => []
            ];

            /**
             * Get categories, loop trough them and add their label to the item
             */
            $spCategories = $spItem->get_categories();
            if (null !== $spCategories) {
                foreach ($spCategories as $spCategory) {
                    $item->categories[] = $spCategory->get_label();
                }
            }

            /**
             * Get authors, loop trough them and add them to the item
             */
            $spAuthors = $spItem->get_authors();
            if (null !== $spAuthors) {
                foreach ($spAuthors as $spAuthor) {
                    $item->authors[] = $spAuthor->get_name();
                }
            }

            /**
             * Ignoring enclosures for now, might do something with it later
             */
            #var_dump($spItem->get_enclosures());

            $items[] = $item;
        }

        return $items;
    }
}
