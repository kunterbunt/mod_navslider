<?php 
// No direct access
defined('_JEXEC') or die; 
?>

<?php 
  // Get current category id for auto-selection.
  $app = Jfactory::getApplication();
  $input = $app->input;
  $catid = -1;
  if ($input->getCmd('option') == 'com_content' && $input->getCmd('view') == 'article') {
    $cmodel = JModelLegacy::getInstance('Article', 'ContentModel');
    $catid = $cmodel->getItem($app->input->get('id'))->catid;      
  }    
?>

<div id='navslider'>   
<!--    Control Bar. -->
    <div class='navslider__control'>     
      <!--         Search. --> 
      <div>
        <form class="navslider__control--search" action="#">
          <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
            <label class="mdl-button mdl-js-button mdl-button--icon" for="navslider__control--search-textfield">
              <i class="material-icons">&#xE8B6;</i>
            </label>
            <div class="mdl-textfield__expandable-holder">
              <input class="mdl-textfield__input" type="text" id="navslider__control--search-textfield">
              <label class="mdl-textfield__label"></label>
            </div>
          </div>
        </form>
      </div>
<!--        Categories. -->
      <div class='navslider__control--categories'>        
        <select id="navslider__control--categories-select">
          <?php            
          for ($i = 0; $i < count($categories); $i++) {  
            // Set this option as selected if it's the current category.
            $selection = ($catid != -1 && $catid == $categories[$i]['id']) ? 'selected="selected "' : '';                    
            echo "<option " . $selection . "value=" . $categories[$i]['id'] . ">" . $categories[$i]['title'] . "</option>";
          }
              ?>
        </select> 
      </div>              
<!--        Tags. -->
      <div class='navslider__control--tags'>            
          <?php
              for ($i = 0; $i < count($tags); $i++)
                  echo "<p onclick='onTagClicked(this)' data-id='" . $tags[$i]['id'] . "' class='navslider--noselect'>" . $tags[$i]['title'] . "</p>";
          ?>
      </div>
    </div>

<!--    Prefix arrow.-->
    <div class="mdl-grid mdl-grid--no-spacing navslider__articles-container--timeline-arrow">
      <div class="mdl-layout-spacer"></div> 
      <div class="mdl-cell--6-col">        
        <i class="material-icons">&#xE5C5;</i>
        <div class="vertical-line vertical-line--light vertical-line--50px vertical-line--light"></div>        
      </div>
      <div class="mdl-layout-spacer"></div> 
    </div>
<!--  Articles.-->
    <div class="mdl-grid navslider__articles-container">            
      <div class="mdl-layout-spacer"></div>  
      <div id='navslider__articles' class="mdl-cell--6-col">        
<!--        Articles go here.-->        
      </div>
      <div class="mdl-layout-spacer"></div>  
    </div>
<!--    Suffix arrow.-->
    <div class="mdl-grid mdl-grid--no-spacing navslider__articles-container--timeline-arrow">
      <div class="mdl-layout-spacer"></div> 
      <div class="mdl-cell--6-col">        
        <div class="vertical-line vertical-line--light vertical-line--50px vertical-line--light"></div>
        <i class="material-icons">&#xE5C7;</i>      
      </div>
      <div class="mdl-layout-spacer"></div> 
    </div>
</div>
