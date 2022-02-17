/**
 * @file
 * Custom date-picker plugin for the module.
 */

(function ($, Drupal) {
  Drupal.behaviors.quizAction = {
    attach: function (context, settings) {
      $(".practice-test-form .q-number-test").each(function(){
         $(this).click(function(){
           var qid = $(this).attr("data-quiz");
           $(".NumberListQuestion .q-number-"+qid).addClass('active');
         })
      });

        var timer2 = $(".minutes-time").val()+":00";
        var interval = setInterval(function() {


            var timer = timer2.split(':');
            //by parsing integer, I avoid all extra string processing
            var minutes = parseInt(timer[0], 10);
            var seconds = parseInt(timer[1], 10);
            --seconds;
            minutes = (seconds < 0) ? --minutes : minutes;
            if (minutes < 0) clearInterval(interval);
            seconds = (seconds < 0) ? 59 : seconds;
            seconds = (seconds < 10) ? '0' + seconds : seconds;
            //minutes = (minutes < 10) ?  minutes : minutes;
            jQuery('.countdown').html(minutes + ':' + seconds);
            timer2 = minutes + ':' + seconds;
        }, 1000);


        $(window).scroll(function() {
            if($(window).scrollTop() > 50){
                $(".NumberListQuestion").addClass("pos");
            }else if($(window).scrollTop() < 50){
                $(".NumberListQuestion").removeClass("pos");
            }
        });
    }
  };
})(jQuery, Drupal);
