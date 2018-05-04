function check() {
  var word = $('#alx_answer').html();
  if ( checkletters(word) ) {
    //window.alert("Μπράβο! Το βρήκες!");
    $('#alx_happyface').html('<img src="images/you_did_it.jpg"></img>');
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
 setTimeout(function(){
  el.parentNode.removeChild(el);
 },duration);
 document.body.appendChild(el);
}

//# sourceURL=alx_word_checkers.js
