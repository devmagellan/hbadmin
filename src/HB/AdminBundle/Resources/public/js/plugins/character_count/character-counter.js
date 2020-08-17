(function($) {

	$.fn.characterCounter = function(options) {

		var settings = $.extend({
		    max     : 100,
		    opacity : ".8",
		    color : "#1bcccc",
		    textArea: false
		}, options);

		return this.each( function() {

			$(this).wrap('<div class="character-wrap"></div>');
			$(this).after('<span class="remaining"></span>');

			// This will write the input's value on database
			var value = $(this).val().length;
            $(this).next('.remaining').text(value);

            // This is counter
			if (settings.textArea === false) {
                $(this).keyup(function() {
                    var value = $(this).val().length;
                    $(this).next('.remaining').text(value);
                });
			} else {
                $(this).change(function() {
                    var value = $(this).val().length;
                    $(this).next('.remaining').text(value);
                    $(this).siblings('.remaining').text(value);
                });
			}


            // Css
            $(this).css("padding-right", "35px");
            $(this).parent('.character-wrap').css("position", "relative");
            $(this).next('.remaining').css({
				'position' : 'absolute',
				'opacity' : settings.opacity,
				'color' : settings.color,
				'right' : '10px'
			});

            // textArea
			if (settings.textArea === false) {
				$(this).next('.remaining').css({
					'top' : '50%',
					'transform' : 'translateY(-50%)'
				});
			} else {
				$(this).next('.remaining').css({
					'bottom' : '10px',
				});
			}

        });
	}

}(jQuery));