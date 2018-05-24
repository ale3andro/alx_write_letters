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
    <link href="../bower_components/bootstrap4/dist/css/bootstrap.min.css" rel="stylesheet">

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
                    echo "<p><div id=\"alx_text\">Ποιό μέρος του σώματος βλέπεις στην παρακάτω εικόνα;</div></p>";
                    echo "<div id=\"alx_answer\" style=\"display: none;\"></div>";
                    echo "<p><div id=\"alx_image\"><img class=\"img-responsive\" src=\"preloader.gif\" /></div></p>";
                    echo "</div>"; # Closes the col-sm-6 column
                    echo "<div class=\"col-sm-8\">";
                    echo "<p><div id=\"alx_letters\"></div><div id=\"alx_happyface\"><img src=\"images/garfield.gif\" /></div></p>";
                    echo "<p><div id=\"alx_capslock\"></div></p>";
                    echo "<p>";
                    echo "<button id=\"alx_check_button\" type=\"button\" class=\"btn btn-primary\" onclick=\"check();\">Έλεγχος</button>    ";
                    if ($questionData->allow_reload=='yes') {
                      echo "<button id=\"alx_check_button\" type=\"button\" class=\"btn btn-primary\" onclick=\"location.reload();\">Ξανά!</button>    ";
                    }
                    echo "<button id=\"next_question_button\" type=\"button\" class=\"btn btn-info\" disabled onclick=\"window.location='" . $base_url . "?activity=" . $_GET["activity"] ."&q=" . ($q_num+1) . "'\">Επόμενη ερώτηση</button>";
                    echo "</p>";

                    echo "<button id=\"alx_hint\" type=\"button\" class=\"btn btn-warning\" onclick=\"tempAlert('" . $questionData->answer . "', 2000);\">Βοήθεια</button>    ";
                    echo "<div class=\"d-inline dropdown\">
                      <button class=\"btn btn-warning dropdown-toggle\" type=\"button\" id=\"dropdownMenuButton\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
                        Πως...
                      </button>
                      <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenuButton\">
                        <a class=\"dropdown-item\" target=\"_blank\" href=\"http://sxoleio.pw/6.keyboard/01_Κεφαλαία_γράμματα.html\">βάζω κεφαλαία ή μικρά γράμματα</a>
                        <a class=\"dropdown-item\" target=\"_blank\" href=\"http://sxoleio.pw/6.keyboard/03_Τόνοι.html\">βάζω τόνο σε φωνήεν</a>
                        <a class=\"dropdown-item\" target=\"_blank\" href=\"http://sxoleio.pw/6.keyboard/04_Ερωτηματικό.html\">βάζω Ελληνικό ερωτηματικό</a>
                        <a class=\"dropdown-item\" target=\"_blank\" href=\"http://sxoleio.pw/6.keyboard/05_Άνω_κάτω_τελεία.html\">βάζω άνω και κάτω τελεία</a>
                        <a class=\"dropdown-item\" target=\"_blank\" href=\"http://sxoleio.pw/6.keyboard/08_Διαγραφή_χαρακτήρα.html\">διαγράφω ένα χαρακτήρα</a>
                        <a class=\"dropdown-item\" target=\"_blank\" href=\"http://sxoleio.pw/6.keyboard/02_Αλλαγή_γλώσσας_Ελληνικά-Αγγλικά.html\">αλλάζω γλώσσα πληκτρολογίου</a>
                      </div>
                    </div>";

                    echo "<a href=\"#menu-toggle\" class=\"btn btn-secondary\" id=\"menu-toggle\">Μενού επιλογών</a>";
                    echo "</div>"; # Closes the col-sm-6 column
                    echo "</div>"; # Closes the div class=row
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
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
    <script src="../bower_components/jquery-ui/jquery-ui.min.js"></script>
    <script src="popper/popper.min.js"></script>
    <script src="../bower_components/bootstrap4/dist/js/bootstrap.bundle.min.js"></script>



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
      if ( checkletters(word) ) {
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
        $('#alx_happyface').effect( "shake" );
        $('#next_question_button').prop("disabled", false);
      }
      else
        $("#alx_letters" ).effect( "shake" );
    }

    function checkletters(word) {
      for (i=0;i<word.length;i++) {
        idname = '#letter' + i;
        if ($(idname).val()!=word[i]) {
          return false;
        }
      }
      return true;
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
