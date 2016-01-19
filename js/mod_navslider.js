var isMobile = false;
var tiles = [];
var selectedTags = [];
var baseUrl;
var tileSlider;
var fadeDuration = 400;

jQuery(document).ready(function() {  
    // Try to evaluate whether the user is on a mobile device.
    checkIfUserOnMobile();     
    
    // Set onchange function for selection box and call it now to load it for the first time.
    jQuery("#navslider-control-bar-select").bind("change", function() {
        navsliderOnCategorySelected(document.getElementById('navslider-control-bar-select'));
    }).change();
  
  jQuery(".navslider-enlarge_button").click(function() {
    jQuery(this).toggleClass("down");   
    var navslider_outer = jQuery("#navslider-outer");
    navslider_outer.toggleClass("navslider-enlarged");
    navslider_outer.toggleClass("navslider-compact");
    if (navslider_outer.hasClass("navslider-compact"))
      assignIScroll();
    else
      tileSlider.destroy();
  });
  
  jQuery(".navslider-text_tags").click(function() {
    for (var i = 0, j = tiles.length; i < j; i++)
      tiles.push(tiles[i]);    
    updateSlider();
  });
});

function assignIScroll() {
    if (isMobile) { 
        tileSlider = new IScroll('#navslider-outer', { 
            scrollX: true, 
            scrollY: false, 
            mouseWheel: false,
            snap: 'a',        
            eventPassthrough: true
        });   
    // No eventPassthrough for desktop device as that forces you to click twice to be able to scroll.
    } else {
        tileSlider = new IScroll('#navslider-outer', { 
            scrollX: true, 
            scrollY: false, 
            snap: 'a',        
            mouseWheel: false,
            eventPassthrough: false
        }); 
    }        
}

/**
 * Keeps track of which tags have been selected by the user by adding or removing it in selectedTags array.
 * @param {integer} tag ID of selected tag
 */
function onTagClicked(tag) {    
    var id = tag.dataset.id;
    var index = selectedTags.indexOf(id);
    if (index == -1)
        selectedTags.push(id);
    else
        selectedTags.splice(index, 1);        
    tag = jQuery(tag);
    tag.toggleClass("selected");
    updateSlider();
}

/**
 * When the user has selected a category this fires an AJAX call to the server.
 * Upon recieving the JSON-formatted result it parses all article data and updates the tiles array. Finally it calls updateSlider().
 * @param {integer} categorySelector 
 */
function navsliderOnCategorySelected(categorySelector) {    
    var selectedValue = categorySelector.value;
    // Show loader.
    jQuery(".navslider-showbox").removeClass("hide");
    // Empty slider.
    jQuery("#navslider-articles").empty();
    var request = {
        'option' : 'com_ajax',
        'module' : 'navslider',
        'data'   : selectedValue,
        'format' : 'raw',
        'method' : 'updateSlider',
        'cmd'    : 'action'
    };
    jQuery.ajax({
        type   : 'POST',
        data   : request,
        success: function (response) {              
            var result = JSON.parse(response);                               
            baseUrl = result['url'];            
            var itemsAdded = 0;            
            tiles = [];       
            for (var i = 0; i < result['articles'].length; i++) {  
                itemsAdded++;          
                // Fetch primitive data.
                var title = result['articles'][i]['title'];
                
                var alias = result['articles'][i]['alias'];   
                
                var image_intro = result['articles'][i]['image_intro'];                   
                if (image_intro == "")
                    image_intro = "modules/mod_navslider/imgs/no_image.png"  
                    
                var tags = [];
                for (var j = 0; j < result['articles'][i]['tags'].length; j++) {
                    tags.push({title: result['articles'][i]['tags'][j]['title'], 
                              id: result['articles'][i]['tags'][j]['id']});
                }            
                
                // Combine into object.
                var tile = {
                    title: title,
                    alias: alias,
                    image_intro: image_intro,
                    tags: tags
                };
                
                // Append to collection.
                tiles.push(tile);                                       
            }
            // Show tiles.
           updateSlider();
        },
        error  : function (response) {
            alert(response.responseText);
        }
    });
}

/**
 * Fills the slider with articles that match the current filter rules. 
 */
function updateSlider() {      
    jQuery("#navslider-articles").empty();
    var numberOfTilesAdded = 0;
    for (var i = 0; i < tiles.length; i++) {
        // If tags are selected then filter out those articles that don't match.
        filteredOut = false;
        if (selectedTags.length > 0) {                     
            // If one selected tag matches the article tags then show the article.
            filteredOut = true;
            for (var j = 0; j < tiles[i]['tags'].length; j++) {
                var tag = tiles[i]['tags'][j]['id'];
                if (selectedTags.indexOf(tag) > -1) {
                    filteredOut = false;
                    break;
                }
            }
        }
        if (!filteredOut) {                    
          var tile = jQuery("<a href='" + baseUrl + tiles[i]['alias'] + "' class='navslider-slide'></a>");
          var tile_image = jQuery("<figure><img class='navslider-slide--img' alt='intro image' src='" + tiles[i]['image_intro'] + "'></figure><span class='navslider-slide--title'>" + tiles[i]['title'] + "</span>");
          tile_image.hide().appendTo(tile);
          tile.appendTo(jQuery("#navslider-articles"));
          tile_image.fadeIn(fadeDuration);
          numberOfTilesAdded++;
        }                
    }            
        
    if (numberOfTilesAdded == 0)
        jQuery("#navslider-articles").append("<p id='navslider-no-articles-msg' >Nothing to show.</p>");
    
    // Hide loader.
    jQuery(".navslider-showbox").addClass("hide");
    if (numberOfTilesAdded > 0)
      assignIScroll();
}

function checkIfUserOnMobile() {
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;   
}