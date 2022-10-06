/** FROM https://www.kevinleary.net/javascript-get-url-parameters/
 * JavaScript Get URL Parameter
 * 
 * @param String prop The specific URL parameter you want to retreive the value for
 * @return String|Object If prop is provided a string value is returned, otherwise an object of all properties is returned
 */
 function getUrlParams( prop ) {
    var params = {};
    var search = decodeURIComponent( window.location.href.slice( window.location.href.indexOf( '?' ) + 1 ) );
    var definitions = search.split( '&' );

    definitions.forEach( function( val, key ) {
        var parts = val.split( '=', 2 );
        params[ parts[ 0 ] ] = parts[ 1 ];
    } );

    return ( prop && prop in params ) ? params[ prop ] : params;
}

// The duration of showing the hint popup
var hint_duration = 2000;

$( function() {
    $( "#dialog" ).dialog({
        autoOpen: false,
        width: 840,
        open: function() {
            $(this).closest(".ui-dialog")
            .find(".ui-dialog-titlebar-close")
            .removeClass("ui-dialog-titlebar-close")
            .html("<span class='ui-button-icon-primary ui-icon ui-icon-closethick'></span>");
        },
        show: {
          effect: "blind",
          duration: 1000
        },
        hide: {
          effect: "explode",
          duration: 1000
        }
      });
 
    $( "#open_capslock" ).on( "click", function() {
      $("#dialog").html("<p><video controls width=\"800\"><source src=\"videos/Caps_Lock.webm\" type=\"video/webm\"></video></p>");
      $( "#dialog" ).dialog( "open" );
    });

    $( "#open_tonoi" ).on( "click", function() {
      $("#dialog").html("<p><video controls width=\"800\"><source src=\"videos/tonoi.webm\" type=\"video/webm\"></video></p>");
      $( "#dialog" ).dialog( "open" );
    });

    $( "#open_questionmark" ).on( "click", function() {
      $("#dialog").html("<p><video controls width=\"800\"><source src=\"videos/questionmark.webm\" type=\"video/webm\"></video></p>");
      $( "#dialog" ).dialog( "open" );
    });

    $( "#open_semicolon" ).on( "click", function() {
      $("#dialog").html("<p><video controls width=\"800\"><source src=\"videos/semicolon.webm\" type=\"video/webm\"></video></p>");
      $( "#dialog" ).dialog( "open" );
    });

    $( "#open_delete" ).on( "click", function() {
      $("#dialog").html("<p><video controls width=\"800\"><source src=\"videos/delete.webm\" type=\"video/webm\"></video></p>");
      $( "#dialog" ).dialog( "open" );
    });

    $( "#open_language" ).on( "click", function() {
      $("#dialog").html("<p><video controls width=\"800\"><source src=\"videos/language.webm\" type=\"video/webm\"></video></p>");
      $( "#dialog" ).dialog( "open" );
    });
  

  } );

$( document ).ready(function() {
    document.body.classList.toggle('sb-sidenav-toggled');
    var all_activities = [];
    $.ajax({
        url: 'activities.json',
        contentType: "application/json",
        dataType: "json",
        success: function(result){ 
            for (i=0; i<result.length; i++) {
                all_activities[result[i].id] = result[i];
                // Remove the loading.gif
                if (i==0) 
                    $('#alx_activites_list').html('');
                // Create the menu
                $('#alx_activites_list').html($('#alx_activites_list').html() + 
                    '<a class="list-group-item list-group-item-action list-group-item-light p-3" href="?id=' + result[i].id + '">' + result[i].description + '</a>');
            }

            // Read the specific activity - if any
            if ( (!('id' in getUrlParams())) || (getUrlParams('id')==undefined) || (getUrlParams('id')=='') ) {
                $('#alx_full_description').html("Δεν έχει οριστεί το id της δραστηριότητας");
                var ids_to_hide = ['alx_question_text', 'alx_text', 'alx_happyface', 'alx_check_button', 'alx_reload_button', 'next_question_button', 'alx_hint', 'dropdownMenuButton', 'dialog'];
                for (var i=0; i<ids_to_hide.length; i++) 
                    $('#' + ids_to_hide[i]).hide();
                $('#alx_image').html('<img class="img-responsive" src="images/404.png" />');
                return;
            } 
            
            $.ajax({
                url: 'activities/' + getUrlParams('id') + '.json',
                contentType: "application/json",
                dataType: "json",
                success: function(result){ 
                    $('#alx_full_description').html(all_activities[getUrlParams('id')].full_description);
                    $('#alx_numquestions').html(result.length);
                    setup_question(result, 0);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    $('#alx_full_description').html("Δεν βρέθηκε δραστηριότητα με αυτό το id");
                    var ids_to_hide = ['alx_question_text', 'alx_text', 'alx_happyface', 'alx_check_button', 'alx_reload_button', 'next_question_button', 'alx_hint', 'dropdownMenuButton', 'dialog'];
                    for (var i=0; i<ids_to_hide.length; i++) 
                        $('#' + ids_to_hide[i]).hide();
                    $('#alx_image').html('<img class="img-responsive" src="images/404.png" />');
                    return;
                }
            });
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.log('Error 101 - Unable to load the activities list');
            return;
        }
    });

    

    function build_alx_letters(answer, hints, answer_to_correct='') {
        var return_value = '';
        var all_hints = hints.split(',');
       
        for (var i=0; i<answer.length; i++) {
            return_value = return_value + '<input id="letter' + i + '" type="text" maxlength="1" size="1" class="onecharonly" ';
            if (answer_to_correct!='')
                return_value = return_value + 'value="' + answer_to_correct.charAt(i) + '" enabled';
            else if (all_hints.indexOf(i.toString())>=0)
                return_value = return_value + 'value="' + answer.charAt(i) + '" disabled';
            return_value = return_value + '/>';
        }
        return return_value;
    }


    function setup_question(questions, question_num) {
        $('#alx_questionnumber').html(question_num+1);
        $('#alx_text').html(questions[question_num].text);
        $('#alx_image').html('<img class="img-responsive" src="' + questions[question_num].image +'" />');
        if (questions[question_num].hasOwnProperty("answer_to_correct"))
            $('#alx_letters').html(build_alx_letters(questions[question_num].answer, questions[question_num].hints, questions[question_num].answer_to_correct));    
        else
            $('#alx_letters').html(build_alx_letters(questions[question_num].answer, questions[question_num].hints, ''));
        $('#next_question_button').hide();
        $('#alx_happyface').html('<img src="images/garfield.gif"></img>');
        $('#alx_answer').html(questions[question_num].answer);
        if (questions[question_num].allow_reload=="yes")
            $('#alx_reload_button').prop("disabled", false);
        else
            $('#alx_reload_button').prop("disabled", true);
        
        
        $('#alx_reload_button').unbind();
        $('#alx_reload_button').click(function() {
            setup_question(questions, question_num);
        });
        
        $('#alx_hint').unbind();
        $('#alx_hint').click(function () {
            var el = document.createElement("div");
            el.setAttribute("style","position:absolute;top:30%;left:15%;background-color:grey;font-size: 50px;");
            el.innerHTML = questions[question_num].answer;
            setTimeout(function(){ el.parentNode.removeChild(el); }, hint_duration);
            document.body.appendChild(el);
        });

        $('#next_question_button').unbind();
        $('#next_question_button').click(function () {
            setup_question(questions, question_num+1);
        });

        $('#alx_check_button').unbind();
        $('#alx_check_button').click(function () {
            var the_result = checkletters($('#alx_answer').html());
            if (is_result_all_correct(the_result)) {
                if (question_num!=(questions.length-1)) {
                    // Activity not completed yet
                    $('#alx_happyface').html('<img src="images/you_did_it.jpg"></img>');
                    $('#alx_happyface').effect( "shake" );
                    for (var i=0; i<the_result.length; i++)
                        $("#letter" + i).css({'border' : '4px solid #008000'});
                    $('#next_question_button').prop("disabled", false);
                    $('#next_question_button').show();
                } else {
                    // Activity completed
                    $('#alx_happyface').html('<img src="images/emojis-high-five.gif"></img>');
                    $('#alx_happyface').effect( "shake" );
                    $('#alx_check_button').hide();
                    $('#alx_reload_button').hide();
                    if (sessionStorage.getItem("quest_return")!=null) {
                        sessionStorage.setItem("quest" + sessionStorage.getItem("quest_ordinal") + "_is_complete", 'true');
                        $("#alx_mapquestreturn").html('<a href="' + sessionStorage.getItem("quest_return") + '"><img src="images/back_66percent.png"></a>');    
                    }
                }
            } else {
                $("#alx_letters" ).effect( "shake" );
                for (var i=0; i<the_result.length; i++) {
                    if (the_result[i]==0)
                        $("#letter" + i).css({'border' : '4px solid #ff0000'});
                    else 
                        $("#letter" + i).css({'border' : '4px solid #008000'});
                }
            }
        });
    }
});

// This function is a keeper
function checkletters(word) {
    var check_result = [];
    for (i=0;i<word.length;i++) {
      idname = '#letter' + i;
      check_result[i]=1; // If the value is 1 then the letter was typped correctly by the student
      if ($(idname).val()!=word[i])
        check_result[i]=0; // If the value is 0 then the student made a mistake
    }
    return check_result;
}

// This function is a keeper
function is_result_all_correct(result_set) {
    for (i=0; i<result_set.length; i++) {
      if (result_set[i]==0)
        return false;
    }
    return true;
}  

