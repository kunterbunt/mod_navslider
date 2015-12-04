<?php

class modNavSliderHelper {
    
    public static function queryDatabase($table, $select, $where, $limit, $order) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);    
        $query->select($select);
        $query->from($db->quoteName($table));    
        if (!($where === NULL))
            $query->where($where); 
        if (!($order === NULL))
            $query->order($order);        
        if (!($limit === NULL) && $limit > 0)
            $query->setLimit($limit);        
        $db->setQuery($query);
        return $db->loadAssocList();    
    }
    
    public static function updateSliderAjax() {
        $input = JFactory::getApplication()->input;
		$categoryId  = $input->get('data');
        if ($categoryId > -1)
            $categoryData = modNavSliderHelper::queryDatabase('#__content', 'title, images, alias', 'state = 1 AND catid = ' . $categoryId, 0, NULL);
        else
            $categoryData = modNavSliderHelper::queryDatabase('#__content', 'title, images, alias', 'state = 1', 0, NULL);
        for ($i = 0; $i < count($categoryData); $i++) {
            $categoryData[$i] += array('image_fulltext' => modNavSliderHelper::parseImageString('image_fulltext', $categoryData[$i]['images']));
            $categoryData[$i] += array('image_intro' => modNavSliderHelper::parseImageString('image_intro', $categoryData[$i]['images']));
        }
        $result = array();
        $result['articles'] = $categoryData;
        $result['url'] = JURI::root();
        return json_encode($result);
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