var isMobile = false;
var tiles = [];
var selectedTags = [];
var searchPhrase = "";
var baseUrl;
var tileSlider = null;
var fadeDuration = 400;

jQuery(document).ready(function() {  
  // Try to evaluate whether the user is on a mobile device.
  checkIfUserOnMobile();     

  // Set onchange function for selection box and call it now to load it for the first time.
  jQuery("#navslider__control--categories-select").bind("change", function() {
      navsliderOnCategorySelected();
  }).change();
  
  // Search button function.
  jQuery(".navslider__control--search-textfield").on("change keydown paste input", function() {
    searchPhrase = jQuery(this).val();
    updateSlider(false);
  });

  // Style select boxes.
  jQuery("#navslider__control--categories-select").select2({
    minimumResultsForSearch: 10
  });
  
//  // Add articles for test purposes.
//  jQuery(".navslider-text_tags").click(function() {
//    for (var i = 0, j = tiles.length; i < j; i++)
//      tiles.push(tiles[i]);    
//    updateSlider(true);
//  });
});

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
    updateSlider(true);
}

/**
 * When the user has selected a category this fires an AJAX call to the server.
 * Upon recieving the JSON-formatted result it parses all article data and updates the tiles array. Finally it calls updateSlider().
 * @param {integer} categorySelector 
 */
function navsliderOnCategorySelected() {      
    var selectedValue = jQuery('#navslider__control--categories-select').val(); 
    // Empty slider.
    jQuery("#navslider__articles").empty();
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
                var intro_text = result['articles'][i]['introtext'];                
                var alias = result['articles'][i]['alias'];   
                var date = result['articles'][i]['publish_up'];                 
                var image_intro = result['articles'][i]['image_intro'];
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
                    tags: tags,
                    intro_text: intro_text,
                    date: date
                };
                
                // Append to collection.
                tiles.push(tile);                                       
            }
            // Show tiles.
           updateSlider(true);
        },
        error  : function (response) {
            alert(response.responseText);
        }
    });
}

/**
 * Fills the slider with articles that match the current filter rules. 
 */
function updateSlider(animate) {      
    jQuery("#navslider__articles").empty();
    var numberOfTilesAdded = 0;
    var monthNames = ["January", "February", "March", "April", "May", "June",
    "July", "August", "September", "October", "November", "December"
  ];
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
        // Filter by search phrase.
        if (searchPhrase != "" && tiles[i]['title'].toUpperCase().indexOf(searchPhrase.trim().toUpperCase()) <= -1)
          filteredOut = true;
        if (!filteredOut) {     
          // Construct tile.          
          var tile = jQuery("<div class='navslider__article'></div>");
          // The link contains all visible elements.
          var tile_link = jQuery("<a href='" + baseUrl + tiles[i]['alias'] + "'></a>");
          tile_link.appendTo(tile);      
          // Date.     
          var date = new Date(tiles[i]['date']);          
          var tile_date = jQuery("<div class='navslider__article--date'>" + monthNames[date.getMonth()] + " " + date.getDay() + "</div>");
          tile_date.appendTo(tile_link);
          // Visible content except for date for nicer shadow effects.
          var tile_visible_content = jQuery("<div class='navslider__article--content'></div>");
          tile_visible_content.appendTo(tile_link);
          // Image.
          var tile_image = jQuery("<div class='navslider__article--image' style='background: url(" + tiles[i]['image_intro'] + ")'></div>");
          if (animate)
            tile_image.hide()
          tile_image.appendTo(tile_visible_content);          
          // Title.
          var tile_title = jQuery("<h3 class='navslider__article--title'>" + tiles[i]['title'] + "</h3>");
          tile_title.appendTo(tile_visible_content);
          
          var articles = jQuery("#navslider__articles");
          tile.appendTo(articles);
          // Grey line after an article.
          var line = jQuery("<div class='vertical-line vertical-line--light vertical-line--50px vertical-line--light'></div>");
          line.appendTo(articles);
          if (animate)
            tile_image.fadeIn(fadeDuration);
          numberOfTilesAdded++;
        }                
    }            
        
    if (numberOfTilesAdded == 0)
        jQuery("#navslider__articles").append("<p class='navslider__articles--no-content-message'>Nothing to show</p>");            
}

function checkIfUserOnMobile() {
    if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;   
}