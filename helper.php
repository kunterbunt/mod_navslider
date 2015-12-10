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
        
        // Get children categories.
        $id = $categoryId;
        $ids = array();
        $ids[0] = $categoryId;
        $ids = modNavSliderHelper::getChildrenCategoryIds($ids[0], $ids);
        
        // Prepare answer array.
        $result = array();        
        $result['url'] = JURI::root();
        $result['numberOfArticles'] = count($ids);
        
        // Go through all IDs.
        for ($i = 0; $i < count($ids); $i++) {
            // Get article info from database.
            $categoryData = $categoryData = modNavSliderHelper::queryDatabase('#__content', 'title, images, alias', 'state = 1 AND catid = ' . $ids[$i], 0, NULL);
            // Also parse the images String.
            for ($j = 0; $j < count($categoryData); $j++) {
                $categoryData[$j] += array('image_fulltext' => modNavSliderHelper::parseImageString('image_fulltext', $categoryData[$j]['images']));
                $categoryData[$j] += array('image_intro' => modNavSliderHelper::parseImageString('image_intro', $categoryData[$j]['images']));
            }
            // Put that into resulting array.
            $result['articles' . $i] = $categoryData;            
        }
        return json_encode($result);
    }
    
    public static function getChildrenCategoryIds($id, $ids) {
        jimport('joomla.application.categories');        
        $children = JCategories::getInstance('Content')->get($id)->getChildren();        
        for ($i = 0; $i < count($children); $i++) {
            // Append to array.
            $ids[count($ids)] = $children[$i]->get('id');
            // Recursively add their children, too.
            $ids = modNavSliderHelper::getChildrenCategoryIds($ids[count($ids) - 1], $ids);
        }
        return $ids;
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