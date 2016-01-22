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

<div id='navslider-container'>   
<!--    Control Bar. -->
    <div id='navslider-control-bar' class='navslider-shadow--bottom'>     
<!--        Categories. -->
      <div id='navslider-control-bar-categories'>
        <p class="navslider-text_category navslider-noselect">Category</p>
        <select id='navslider-control-bar-select' class="navslider-noselect">
          <?php            
          for ($i = 0; $i < count($categories); $i++) {  
            // Set this option as selected if it's the current category.
            $selection = ($catid != -1 && $catid == $categories[$i]['id']) ? 'selected="selected "' : '';                    
            echo "<option " . $selection . "value=" . $categories[$i]['id'] . ">" . $categories[$i]['title'] . "</option>";
          }
              ?>
        </select> 
<!--        Arrow indicating compact or enlarged view. -->
        <img alt='Enlarge Button' src="<?php echo JURI::root() . 'modules/' . $module->module?>/imgs/right_arrow.png" class="navslider-enlarge_button navslider-noselect"/>
        <p class="navslider-text_tags navslider-noselect">Tags</p>
      </div>        
<!--        Tags. -->
      <div id='navslider-control-bar-tags'>            
          <?php
              for ($i = 0; $i < count($tags); $i++)
                  echo "<p onclick='onTagClicked(this)' data-id='" . $tags[$i]['id'] . "' class='navslider-noselect'>" . $tags[$i]['title'] . "</p>";
          ?>
      </div>
<!--         Search. -->      
      <form class="navslider-control-bar-search" action="#">
        <div class="mdl-textfield mdl-js-textfield mdl-textfield--expandable">
          <label class="navslider-control-bar-search-icon mdl-button mdl-js-button mdl-button--icon" for="navslider-search-textfield">
            <i class="material-icons">search</i>
          </label>
          <div class="mdl-textfield__expandable-holder">
            <input class="mdl-textfield__input" type="text" id="navslider-search-textfield">
            <label class="mdl-textfield__label" for="sample-expandable">Expandable Input</label>
          </div>
        </div>
      </form>
    </div>

    <div id='navslider-outer' class="navslider-compact">
        <div id='navslider'>
            <div class="navslider-showbox navslider-hide">
              <div class="navslider-loader">
                <svg class="navslider-circular" viewBox="25 25 50 50">
                  <circle class="navslider-path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
              </div>
            </div>
            <div id='navslider-articles'>                
               
            </div>
        </div>
    </div>
</div>
