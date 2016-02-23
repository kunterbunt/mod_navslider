<?php

class modNavSliderHelper {
    
    /**
    * Convenience function to query the Joomla database.
    */
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
    
    /**
     * Fetches all active categories from the database and sorts and orders them so that the 
     * category hierarchy is represented.
     */
    public static function getCategories() {
        jimport('joomla.application.categories');
        // Fetch categories from database.
        $categoriesFromDb = modNavSliderHelper::queryDatabase('#__categories', 'title, id, level', 'level = 1', 0, 'created_time ASC');
        // Remove unwanted categories. 
        $categories[] = array('title' => 'All', 'id' => -1);
        for ($i = 0; $i < count($categoriesFromDb); $i++) {
            if ($categoriesFromDb[$i]['title'] !== "Uncategorised" && $categoriesFromDb[$i]['title'] !== "ROOT") {        
                $categories[] = array('title' => $categoriesFromDb[$i]['title'], 'id' => $categoriesFromDb[$i]['id']);
                // Recursively add children categories.
                $categories = modNavSliderHelper::getChildrenCategoryStrings($categoriesFromDb[$i]['id'], $categories, 1);
            }  
        }
        return $categories;
    }
    
    /**
     * Fetches all tags from the database.
     */
    public static function getTags() {        
        $tags = modNavSliderHelper::queryDatabase('#__tags', 'title, id', NULL, 0, 'title ASC');
        for ($i = 0; $i < count($tags); $i++) {                        
            if ($tags[$i]['title'] == "ROOT") {
                unset($tags[$i]);                
            }                
        }
        $tags = array_values($tags);
        return $tags;
    }
    
    /**
    * Loads article information for the category (and sub-categories)
    * that the user selected in the slider and returns that to the calling
    * AJAX function.
    */
    public static function updateSliderAjax() {
        $input = JFactory::getApplication()->input;
		$categoryId  = $input->get('data');                               
        
        // Prepare answer array.
        $result = array();        
        $result['url'] = JURI::root();    
                
         // -1 represents all categories - fetch all articles in that case.
        if ($categoryId == -1) {
            // Get article info from database.
            $categoryData = modNavSliderHelper::queryDatabase('#__content', 'title, images, alias, publish_up, id, introtext', 'state = 1', 0, 'publish_up DESC');
            // Also parse the images String.
            for ($j = 0; $j < count($categoryData); $j++) {
                $categoryData[$j] += array('image_fulltext' => modNavSliderHelper::parseImageString('image_fulltext', $categoryData[$j]['images']));
                $categoryData[$j] += array('image_intro' => modNavSliderHelper::parseImageString('image_intro', $categoryData[$j]['images']));
            } 
            
        // Otherwise we have a category id.
        } else {
            // Get children categories.        
            $ids = array();
            $ids[0] = $categoryId;
            $ids = modNavSliderHelper::getChildrenCategoryIds($ids[0], $ids);            
            // Go through all IDs.
            // Concatenate IDs into select string.
            $select_ids = "catid = " . $ids[0];
            for ($i = 1; $i < count($ids); $i++)
                $select_ids .= " OR catid = " . $ids[$i];            
            // Get article info from database.
            $categoryData = modNavSliderHelper::queryDatabase('#__content', 'title, images, alias, publish_up, id, introtext', 'state = 1 AND ' . $select_ids, 0, 'publish_up DESC');
            // Also parse the images String.
            for ($j = 0; $j < count($categoryData); $j++) {
                $categoryData[$j] += array('image_fulltext' => modNavSliderHelper::parseImageString('image_fulltext', $categoryData[$j]['images']));
                $categoryData[$j] += array('image_intro' => modNavSliderHelper::parseImageString('image_intro', $categoryData[$j]['images']));                
            }                                                                                   
        }
        
        // Retrieve tags for articles.            
        for ($i = 0; $i < count($categoryData); $i++) {
            $tags = new JHelperTags;
            $tags->getItemTags('com_content.article', $categoryData[$i]['id']);           
            $itemTags = $tags->itemTags;                
            // itemTags contains all info on the tags - filter out what we need.
            $strippedTags = array();                
            for ($j = 0; $j < count($itemTags); $j++) {
                $strippedTags[] = array('title' => $itemTags[$j]->title,
                                        'id' => $itemTags[$j]->id);
            }                    
            $categoryData[$i] += array('tags' => $strippedTags);
        }   
        $result['articles'] = $categoryData;  
                
        return json_encode($result);
    }
    
    /**
    * Looks up all child categories for category with $id, 
    * appends them to $ids array and recursively looks up
    * their children, too.
    */
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
    
    /**
    * Looks up all child categories for category with $id
    * and their children recursively, returning an array containing
    * strings that represent the category hierarchy.
    */
    public static function getChildrenCategoryStrings($id, $strings, $level) {
        jimport('joomla.application.categories');        
        $children = JCategories::getInstance('Content')->get($id)->getChildren();              
        for ($i = 0; $i < count($children); $i++) {
            // Append to array.
            $strings[] = array('title' => str_repeat('&nbsp;', $level) . $children[$i]->get('title'), 'id' => $children[$i]->get('id'));        
            // Recursively add their children, too.
            $strings = modNavSliderHelper::getChildrenCategoryStrings($children[$i]->get('id'), $strings, $level + 1);
        }
        return $strings;
    }
        
    /**
    * Image attributes of articles are saved in one long String in the 
    * Joomla database - this function parses needed parameters out of it.
    */
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