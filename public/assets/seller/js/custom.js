// On Scroll add class
$(window).scroll(function(){
  $('body').removeClass('scroll');
  if ($(this).scrollTop() > 200) {
      $('body').addClass('scroll');
  } else {
      $('body').removeClass('scroll');
  }
});
$(document).ready(function () {

});

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-36251023-1']);
_gaq.push(['_setDomainName', 'jqueryscript.net']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();


$('.user-button').hover(function(){
  console.log('Hello');
});

// $('.dropdown-toggle').hover(function(){
//   $(this).dropdown();
// });

//Bootstrap Steps
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  if(x[n] !== undefined){
      x[n].style.display = "block";
      //... and fix the Previous/Next buttons:
      if (n == 0) {
          document.getElementById("prevBtn").style.display = "none";
      } else {
          document.getElementById("prevBtn").style.display = "inline";
      }
      if (n == (x.length - 1)) {
          document.getElementById("nextBtn").innerHTML = "Submit";
      } else {
          document.getElementById("nextBtn").innerHTML = "Next";
      }
      //... and run a function that will display the correct step indicator:
      fixStepIndicator(n)
  }
}

function nextPrev(n) {
  var showNext=true;
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:

  // Increase or decrease the current tab by 1:

  if(currentTab===0){
      showNext=false;
      $('#basic_form').ajaxSubmit({
          beforeSubmit: function(){
              showOverlay();
          },
          success : function (response) {
              hideOverlay();
              var info=JSON.parse(response);
              if(info.status=='true'){
                  displayTab(n,x);
              }
          },
          error : function (response) {
              hideOverlay();
          }
      });
  }
  if(currentTab===1){
      showNext=false;
      $('#account_form').ajaxSubmit({
          beforeSubmit: function(){
              showOverlay();
          },
          success : function (response) {
              hideOverlay();
              var info=JSON.parse(response);
              if(info.status=='true'){
                  displayTab(n,x);
              }
          },
          error : function (response) {
              hideOverlay();
          }
      });
  }
  if(currentTab===2){
      showNext=false;
      $('#kyc_form').ajaxSubmit({
          beforeSubmit: function(){
              showOverlay();
          },
          success : function (response) {
              hideOverlay();
              var info=JSON.parse(response);
              if(info.status=='true'){
                  displayTab(n,x);
              }
          },
          error : function (response) {
              hideOverlay();
          }
      });
  }
  if(currentTab===3){
      showNext=false;
      $('#agreement_form').ajaxSubmit({
          beforeSubmit: function(){
              showOverlay();
          },
          success : function (response) {
              hideOverlay();
              var info=JSON.parse(response);
              if(info.status=='true'){
                  location.reload();
              }
          },
          error : function (response) {
              hideOverlay();
          }
      });
  }
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
      // ... the form gets submitted:
      document.getElementById("regForm").submit();
      return false;
  }
  // Otherwise, display the correct tab:
  if(showNext)
      displayTab(n,x);
}

function displayTab(n,x) {
  x[currentTab].style.display = "none";
  currentTab = currentTab + n;
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i,agree, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  agree = document.getElementById("terms1");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
      // If a field is empty...
      if(y[i].required===true){
          if(y[i].type === "checkbox" && y[i].checked === false){
              y[i].className += " invalid";
              agree.className += " myterm";
              //y[i].css = ('border-color','red')
              // and set the current valid status to false
              valid = false;
          }
          else if (y[i].value == "") {
              // add an "invalid" class to the field:
              y[i].className += " invalid";
              // and set the current valid status to false
              valid = false;
          }
          else{
              y[i].classList.remove('invalid');
              agree.classList.remove('myterm');
          }
          if(y[i].id === 'gst_number'){
              if(y[i].value.length != 15){
                  y[i].className += " invalid";
                  valid = false;
              }
          }
          if(y[i].id === 'pan_number'){
              if(y[i].value.length != 10){
                  y[i].className += " invalid";
                  valid = false;
              }
          }
      }else{
          y[i].classList.remove('invalid');
      }
  }

  y = x[currentTab].getElementsByTagName("select");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
      // If a field is empty...
      if(y[i].required===true){
          if (y[i].value == "") {
              // add an "invalid" class to the field:
              y[i].className += " invalid";
              // and set the current valid status to false
              valid = false;
          }
          else{
              y[i].classList.remove('invalid');
          }
      }else{
          y[i].classList.remove('invalid');
      }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
      document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
          x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class on the current step:

    x[n].className += " active";
}
