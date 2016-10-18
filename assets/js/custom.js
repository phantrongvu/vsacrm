var waitForFinalEvent = (function () {
  var timers = {};
  return function (callback, ms, uniqueId) {
    if (!uniqueId) {
      uniqueId = "Don't call this twice without a uniqueId";
    }
    if (timers[uniqueId]) {
      clearTimeout (timers[uniqueId]);
    }
    timers[uniqueId] = setTimeout(callback, ms);
  };
})();

jQuery( document ).ready( function() {
	jQuery('#cssmenu ul > li.has-sub > a').each( function() {
		jQuery(this).attr('href', '#');
	});
		
	$('.event-action-modal').on('shown.bs.modal', function() {
		if ( $(window).height() <= 640 )
		{
			resizePopup();
		}
	});
});

$(window).resize(function () {
	if ( $(window).height() <= 640 )
	{
		waitForFinalEvent(function(){
		  resizePopup();
		  //...
		}, 500, "some unique string");
	}
});

function resizePopup()
{
	var cn = jQuery('.event-action-modal').outerHeight();
	var ch = jQuery('.event-action-modal .modal-header').outerHeight();
	var cf = jQuery('.event-action-modal .modal-footer').outerHeight();
	 
	jQuery('.event-action-modal .modal-body').css('height', cn - ch - cf - 30 );	
}

$(function() {
        var scntDiv = $('#student_search');
        var i = $('#student_search p').size() + 1;
        
        $('#addScnt').live('click', function() {
                $('<p><label for="student_search"><input type="text" id="student_search_' + i +'" class="student-autocomplete input-xxlarge" size="20" name="student_search_' + i +'" value="" placeholder="Enter first name or last name or email to search" /></label> <a href="#" id="remScnt">Remove Student</a></p>').appendTo(scntDiv);
                i++;
                return false;
        });
        
        $('#remScnt').live('click', function() { 
                if( i > 2 ) {
                        $(this).parents('p').remove();
                        i--;
                }
                return false;
        });
});