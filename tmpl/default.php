<?php 
// No direct access
defined('_JEXEC') or die; 
?>

<div id='navslider-container'>
    <div id='navslider-control'>
        <p>Category</p>
        <select id='navslider-category-selector' onchange="navsliderOnCategorySelected(this)">
            <option value='-1'>All</option>
            <?php
            for ($i = 0; $i < count($categories); $i++) {                  
                echo "<option value=" . $categories[$i]['id'] . ">" . $categories[$i]['title'] . "</option>";
            }
            ?>
        </select>        
        <img alt='right-arrow' src="<?php echo JURI::root() . 'modules/' . $module->module?>/imgs/right_arrow.png" />
        <p>Tags</p>
    </div>
    <div id='navslider-outer'>
        <div id='navslider'>
            <div class="navslider-showbox hide">
              <div class="navslider-loader">
                <svg class="navslider-circular" viewBox="25 25 50 50">
                  <circle class="navslider-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
              </div>
            </div>
            <div id='navslider-articles'>
                <?php 
                // Create slide for every article.
                for ($i = 0; $i < count($articles); $i++) {
                    $image = parseImageString("image_intro", $articles[$i]['images']);
                    $title = $articles[$i]['title'];      
                    $alias = $articles[$i]['alias'];                        
                    // Default picture when there is none set.
                    if (strcmp($image, "") == 0)
                        $image = JURI::root().'modules/'.$module->module . "/imgs/no_image.png";  
                    else
                        $image = JURI::base() . '/' . $image;
                    // HTML generation.
                    echo "<a href='$alias' class='slide'>
                            <figure>
                                <img alt='$title intro image' src=\"$image\"/>
                            </figure>
                            <span class='title'>$title</span>
                        </a>";            
                }
                ?>
            </div>
        </div>
    </div>
</div>
