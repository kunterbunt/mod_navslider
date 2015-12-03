<?php 
// This is the module template. This file will take the data collected by mod_helloworld.php and generate the HTML to be displayed on the page.
// No direct access
defined('_JEXEC') or die; ?>
<?php 
// Create a slide for every loaded post.
echo "<div id='navslider-outer'>";
echo "<div id='navslider'>";
for ($i = 0; $i < count($articles); $i++) {
    $image = parseImageString("image_intro", $articles[$i]['images']);
    $title = $articles[$i]['title'];      
    $alias = $articles[$i]['alias'];                        
    // Default picture when there is none set.
    if (strcmp($image, "") == 0)
        $image = JURI::base().'modules/'.$module->module . "/imgs/no_image.png";  
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
echo "</div>";
echo "</div>";
?>