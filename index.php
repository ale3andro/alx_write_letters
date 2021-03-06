<?php
  $base_url = "index.php";
  $available_activities = json_decode(file_get_contents("activities.json"));
  $activity_set = FALSE;

  $helperUrl['kefalaia']['127.0.0.1'] = 'http://sxoleio.pw/6.keyboard/01_Κεφαλαία_γράμματα.html';
  $helperUrl['kefalaia']['104.223.64.131'] = 'http://www.sxoleio.pw/yliko_ana_taksi/gnorizo-to-pliktrologio/kefalaia-grammata';
  $helperUrl['allagh_glwssas']['127.0.0.1'] = 'http://sxoleio.pw/6.keyboard/02_Αλλαγή_γλώσσας_Ελληνικά-Αγγλικά.html';
  $helperUrl['allagh_glwssas']['104.223.64.131'] = 'http://www.sxoleio.pw/yliko_ana_taksi/gnorizo-to-pliktrologio/allagi-glwssas';
  $helperUrl['anw_katw_teleia']['127.0.0.1'] = 'http://sxoleio.pw/6.keyboard/05_Άνω_κάτω_τελεία.html';
  $helperUrl['anw_katw_teleia']['104.223.64.131'] = 'http://www.sxoleio.pw/yliko_ana_taksi/gnorizo-to-pliktrologio/anw-katw-teleia';
  
  $helperUrl['diagrafh_xarakthra']['127.0.0.1'] = 'http://sxoleio.pw/6.keyboard/08_Διαγραφή_χαρακτήρα.html';
  $helperUrl['diagrafh_xarakthra']['104.223.64.131'] = 'http://www.sxoleio.pw/yliko_ana_taksi/gnorizo-to-pliktrologio/diagrafh-xarakthra';
  $helperUrl['ellhniko_erwthmatiko']['127.0.0.1'] = 'http://sxoleio.pw/6.keyboard/04_Ερωτηματικό.html';
  $helperUrl['ellhniko_erwthmatiko']['104.223.64.131'] = 'http://www.sxoleio.pw/yliko_ana_taksi/gnorizo-to-pliktrologio/elliniko-etwthmatiko';
  $helperUrl['tonoi']['127.0.0.1'] = 'http://sxoleio.pw/6.keyboard/03_Τόνοι.html';
  $helperUrl['tonoi']['104.223.64.131'] = 'http://www.sxoleio.pw/yliko_ana_taksi/gnorizo-to-pliktrologio/tonoi';

  function getActivity($available_activities, $activity)
  {
    foreach ($available_activities as $single_activity) {
      if ($single_activity->id==$activity)
        return $single_activity;
    }
    return FALSE;
  }

  function getQuestionData($data_file_contents, $num)
  {
    if ($num<0 or $num>getNumberOfQuestions($data_file_contents))
      return -1;
    $jsonQuestions = json_decode($data_file_contents);
    $counter=0;
    foreach ($jsonQuestions as $item){
      $counter++;
      if ($counter==$num)
        return $item;
    }
    return -1;
  }

  function getNumberOfQuestions($data_file_contents)
  {
    $jsonQuestions = json_decode($data_file_contents);
    $counter=0;
    foreach ($jsonQuestions as $key => $value)
      $counter++;
    return $counter;
  }

  if (!isset($_GET["q"]))
    $q_num=1;
  else if (!intval($_GET["q"]))
    die("Ο αριθμός της ερώτησης πρέπει να είναι ακέραιος μη μηδενικός αριθμός");

  if (!isset($q_num))
    $q_num=(intval($_GET["q"]));

  if (isset($_GET["activity"]))
    $activity_set=TRUE;

  if ($activity_set) {
    $selected_activity = getActivity($available_activities, $_GET["activity"]);
    if (!$selected_activity)
      die("Δεν βρέθηκε δραστηριότητα με αυτό το id");

    $str = file_get_contents($selected_activity->filename);
    if ($q_num<0 or $q_num>getNumberOfQuestions($str))
      die("Δεν υπάρχει ερώτηση με αυτό τον αριθμό");

    $questionData = getQuestionData($str, $q_num);
  }
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Ασκήσεις Πληκτρολόγησης - ale3andro.gr</title>

    <!-- Bootstrap core CSS -->
    <link href="node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link href="node_modules/jquery-ui-dist/jquery-ui.css" rel="stylesheet"> -->
    <link rel="stylesheet" href = "node_modules/jquery-ui-themes/themes/pepper-grinder/jquery-ui.css">

    <!-- Custom styles for this template -->
    <link href="css/simple-sidebar.css" rel="stylesheet">
</head>

<body>

    <div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="#">
                        Διαθέσιμες ασκήσεις
                    </a>
                </li>
                <?php
                    foreach ($available_activities as $single_activity)
                      print "<li><a href=\"" . $base_url . "?activity=" . $single_activity->id . "\">" . $single_activity->description . "</a></li>";
                ?>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <?php
                  if ($activity_set) {
                    echo "<h1>" . $selected_activity->full_description . "</h1>";
                    echo "<h2>Ερώτηση <span id=\"alx_questionnumber\">1</span></h2>";
                    echo "<div class=\"row\"><div class=\"col-sm-4\">";
                    echo "<p><div id=\"alx_text\">Κείμενο ερώτησης</div></p>";
                    echo "<div id=\"alx_answer\" style=\"display: none;\"></div>";
                    echo "<p><div id=\"alx_image\"><img class=\"img-responsive\" src=\"preloader.gif\" /></div></p>";
                    echo "</div>"; # Closes the col-sm-6 column
                    echo "<div class=\"col-sm-8\">";
                    echo "<p><div id=\"alx_letters\"></div><div id=\"alx_happyface\"><img src=\"images/garfield.gif\" /></div></p>";
                    echo "<p><div id=\"alx_capslock\"></div></p>";
                    echo "<p>";
                    echo "<button id=\"alx_check_button\" type=\"button\" class=\"btn btn-primary\" onclick=\"check();\"><img src='icons/task-8x.png' /><br/>Έλεγχος</button>    ";
                    if ($questionData->allow_reload=='yes') {
                      echo "<button id=\"alx_check_button\" type=\"button\" class=\"btn btn-danger\" onclick=\"location.reload();\"><img src='icons/reload-8x.png' /><br />Ξανά</button>    ";
                    }
                    echo "<button id=\"next_question_button\" type=\"button\" class=\"btn btn-success\" disabled onclick=\"window.location='" . $base_url . "?activity=" . $_GET["activity"] ."&q=" . ($q_num+1) . "'\"><img src='icons/chevron-right-8x.png' /><br />Επόμενη</button>";
                    echo "</p>";

                    echo "<button id=\"alx_hint\" type=\"button\" class=\"btn btn-warning\" onclick=\"tempAlert('" . $questionData->answer . "', 2000);\"><img src='icons/eye-8x.png' /><br />Βοήθεια</button>    ";
                    echo "<div class=\"d-inline dropdown\">
                      <button class=\"btn btn-warning dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                        <img src='icons/question-mark-8x.png' /><br />Πώς...
                      </button>
                      <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
                        <a class=\"dropdown-item\" id=\"open_capslock\">βάζω κεφαλαία ή μικρά γράμματα</a>
                        <a class=\"dropdown-item\" id=\"open_tonoi\">βάζω τόνο σε φωνήεν</a>
                        <a class=\"dropdown-item\" id=\"open_questionmark\">βάζω Ελληνικό ερωτηματικό</a>
                        <a class=\"dropdown-item\" id=\"open_semicolon\">βάζω άνω και κάτω τελεια</a>
                        <a class=\"dropdown-item\" id=\"open_delete\">σβήνω ένα γράμμα</a>
                        <a class=\"dropdown-item\" id=\"open_language\">αλλάζω γλώσσα πληκτρολογίου</a>
                      </div>
                    </div>";

                    echo "<a href=\"#menu-toggle\" class=\"btn btn-secondary\" id=\"menu-toggle\"><img src='icons/menu-8x.png' /><br />Μενού</a>";
                    echo "</div>"; # Closes the col-sm-6 column
                    echo "</div>"; # Closes the div class=row
                 
                    echo "
                          <div id=\"dialog\" title=\"Πληκτρολόγιο\">
                          ...
                        </div>";
                }
                else {
                  echo "Πάτησε το παρακάτω κουμπί για να δεις τις διαθέσιμες ασκήσεις<br />";
                  echo "<a href=\"#menu-toggle\" class=\"btn btn-secondary\" id=\"menu-toggle\">Μενού επιλογών</a>";
                }
              ?>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    
    <script src="node_modules/jquery/dist/jquery.min.js"></script>
    
    <script src="node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="node_modules/jquery-ui-dist/jquery-ui.min.js"></script>
    
    <script>
  $( function() {
    $( "#dialog" ).dialog({
      autoOpen: false,
      width: 840,
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
  </script>

    <!-- Menu Toggle Script -->
    <script type="text/javascript">
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    </script>
    <script type="text/javascript">
    function check() {
      var word = $('#alx_answer').html();
      var the_result = checkletters(word);

      if ( is_result_all_correct(the_result) ) {
        <?php
          if ( $q_num==getNumberOfQuestions($str) ) {
            echo "$('#alx_happyface').html('<img src=\"images/emojis-high-five.gif\"></img>');";
            echo "$('#alx_check_button').hide();";
          }
          else {
            echo "$('#alx_happyface').html('<img src=\"images/you_did_it.jpg\"></img>');";
            echo "$('#alx_check_button').show();";
          }
        ?>
        for (var i=0; i<the_result.length; i++)
          $("#letter" + i).css({'border' : '4px solid #008000'});
        $('#alx_happyface').effect( "shake" );
        $('#next_question_button').prop("disabled", false);
      }
      else {
        $("#alx_letters" ).effect( "shake" );
        for (var i=0; i<the_result.length; i++) {
          if (the_result[i]==0)
            $("#letter" + i).css({'border' : '4px solid #ff0000'});
          else 
            $("#letter" + i).css({'border' : '4px solid #008000'});
        }
        
      }
    }

    function is_result_all_correct(result_set) {
      for (i=0; i<result_set.length; i++) {
        if (result_set[i]==0)
          return false;
      }
      return true;
    }

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

    function tempAlert(msg,duration)
    {
      var el = document.createElement("div");
      el.setAttribute("style","position:absolute;top:30%;left:15%;background-color:grey;font-size: 50px;");
      el.innerHTML = msg;
      setTimeout(function(){ el.parentNode.removeChild(el); },duration);
      document.body.appendChild(el);
    }
    $( document ).ready(function() {
          $('#alx_questionnumber').html("<?php echo $q_num . " από " . getNumberOfQuestions($str); ?>");
          $('#alx_image').html("<img class='rounded mx-auto d-block img_fluid' src='" + "<?php echo $questionData->image; ?>" +"' />");
          $('#alx_text').html("<?php echo $questionData->text; ?>");
          $('#alx_answer').html("<?php echo $questionData->answer; ?>");
          <?php
            if ( $q_num==getNumberOfQuestions($str) )
              echo "$('#next_question_button').hide();";
            else {
              echo "$('#next_question_button').show();";
            }
            $hintLetters = explode(",", $questionData->hints);
            $htmlCode ="";
            for ($i=0; $i<mb_strlen($questionData->answer); $i++)
            {
              $givehint=0;
              $allhints=FALSE;
              for ($j=0; $j<sizeof($hintLetters); $j++)
              {
                if ($hintLetters[$j]=="all") {
                  $givehint=1;
                  $allhints=TRUE;
                }
                else if ($i==$hintLetters[$j]) {
                  $givehint=1;
                  break;
                }
              }
              $htmlCode .= "<input id='letter" . $i . "' type='text' maxlength='1' size='1' class='onecharonly' ";
              if ($givehint==1) {
                if ($allhints)
                  $htmlCode .= "value='" . mb_substr($questionData->answer_to_correct, $i, 1);
                else
                  $htmlCode .= "value='" . mb_substr($questionData->answer, $i, 1);
                if ($questionData->disabled_hints=="yes")
                  $htmlCode .= "' disabled";
                else
                  $htmlCode .= "'";
              }
              $htmlCode .= "/>";

            }
          ?>
          $('#alx_letters').html("<?php echo $htmlCode; ?>");
    });
    </script>
</body>

</html>
