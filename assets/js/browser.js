

/* ==========================================================================
 BROWSER
 ========================================================================== */
var browser = {
	
    init: function () {
        if ($.browser.msie) {			
  modal_header.text('Old browser detected');
        $('#modal').modal({
            backdrop: 'static',
            keyboard: false
        })
		modal_body.append("<p>IE version (" + $.browser.versionNumber + ") does not support many of the features within this system.</p><p>The browsers below are guaranteed to work with this system, we <b>strongly</b> recommend you switch.</p><p><a href='https://www.google.com/chrome/index.html' target='blank'><img src='"+helper.baseUrl+"assets/img/chrome-logo.jpg'/></a> <a href='https://www.mozilla.org/en-GB/firefox/new/' target='blank'><img src='"+helper.baseUrl+"assets/img/firefox-logo.jpg' /></a></p>");
        $(".confirm-modal").off('click').text('I don\'t care').show();
		$('.confirm-modal').on('click', function (e) {
            $('#modal').modal('toggle');
        });
        }
    }
}
