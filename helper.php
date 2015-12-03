<?php
// This file contains the helper class which is used to do the actual work in retrieving the information to be displayed in the module (usually from the database or some other source).

class modNavSlider {
    
    public static function getArticles() {
        return queryDatabase_Posts('title, images, alias', 0);        
    }
    
    // Query the Joomla database for posts.
    public static function queryDatabase_Posts($select, $limit) {
        // Database connection and query object.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);    
        $query->select($select);
        $query->from($db->quoteName('#__content'));
        // Published posts only.
        $query->where($db->quoteName('state') . ' = 1'); 
        // Order by the date they are published.
        $query->order('publish_up DESC');
        if ($limit > 0)
            $query->setLimit($limit);
        // Set and fetch.
        $db->setQuery($query);
        return $db->loadAssocList();     
    }
    
    // Image attributes of articles are saved in one long String in the 
    // Joomla database - this function parses needed parameters out of it.
    private static function parseImageString($parameter, $images_string) {
        switch($parameter) {
            // Full image.
            case "image_fulltext":
                $startsAt = strpos($images_string, "image_fulltext\":") + strlen("image_fulltext\":");
                $endsAt = strpos($images_string, ",\"float_fulltext", $startsAt);
                $fullimg = substr($images_string, $startsAt, $endsAt - $startsAt);  
                $fullimg = str_replace("\\", "", $fullimg);
                $fullimg = str_replace("\"", "", $fullimg);
                return $fullimg;    
            // Intro image.
            case "image_intro":
                $startsAt = strpos($images_string, "image_intro\":") + strlen("image_intro\":");
                $endsAt = strpos($images_string, ",\"float_intro", $startsAt);
                $fullimg = substr($images_string, $startsAt, $endsAt - $startsAt);  
                $fullimg = str_replace("\\", "", $fullimg);
                $fullimg = str_replace("\"", "", $fullimg);
                return $fullimg;       
            default:
                return "unsupported parameter";
        }
    }
}