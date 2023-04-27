jQuery(document).ready(function($) {

	var mediaUploader = null;

	fpdUpdateTooltip();

	/*----- MODAL ----------*/

	$('body').on('click', '.fpd-close-admin-modal', function(evt) {

		closeModal($(this).parents('.fpd-admin-modal-wrapper'));
		evt.preventDefault();

		if($().select2) {
			$('.radykal-select2').select2('close');
		}

	});

	//Tabs in Modal
	var $modalWrapper = $('.fpd-admin-modal-wrapper');
	$modalWrapper.find(".fpd-tabs-content").find("[id^='tab']").hide(); // Hide all content
    $modalWrapper.find(".fpd-tabs li:first").attr("id","current"); // Activate the first tab
    $modalWrapper.find(".fpd-tabs-content #tab1").fadeIn(); // Show first tab's content

    $modalWrapper.find('.fpd-tabs a').click(function(evt) {

        evt.preventDefault();

		if(jQuery().select2) {
			$modalWrapper.find('.fpd-select2').each(function() {
				jQuery(this).select2("close");
			});
		}

        if ($(this).closest("li").attr("id") == "current"){ //detection for current tab
	        return;
        }
        else{
			$modalWrapper.find(".fpd-tabs-content").find("[id^='tab']").hide(); // Hide all content
			$modalWrapper.find(".fpd-tabs li").attr("id",""); //Reset id's
			$(this).parent().attr("id","current"); // Activate this
			$('#' + $(this).attr('name')).fadeIn(); // Show content for the current tab
        }

    });


	/*----- SETTINGS ----------*/

	//hide labels tab when "use labels settings" is disabled
	$('#fpd_use_label_settings').change(function() {

		$('#radykal-nav-tab--labels').toggle($(this).is(':checked'));

	}).change();

	//toggle image options form
	$('#fpd-modal-edit-options [name="enabled"]').change(function() {

		var $this = $(this);

		if($this.is(':checked')) {
			$this.parents('.fpd-admin-modal-content:first').find('form table').removeClass('radykal-disabled');
		}
		else {
			$this.parents('.fpd-admin-modal-content:first').find('form table').addClass('radykal-disabled');
		}

	}).change();

	//only allow numeric values for text inputs with .fpd-only-numbers
    $('input.fpd-only-numbers').on('keypress', function(evt) {

		var charCode = (evt.which) ? evt.which : evt.keyCode;
		if($(this).hasClass('fpd-allow-dots')) {

			if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46)) {
			    return false;
		    }
		    else {
			    return true;
		    }
		}
		else {
			if (charCode > 31 && (charCode < 48 || charCode > 57)) {
			    return false;
		    }
		    else {
			    return true;
			}
		}

    });

	$('.fpd-allow-dots').keyup(function(){

        if($(this).val().indexOf('.')!=-1){
            if($(this).val().split(".")[1].length > 2){
                if( isNaN( parseFloat( this.value ) ) ) return;
                this.value = parseFloat(this.value).toFixed(2);
            }
         }
         return this;

    });

	//check that number inputs has a leading 0 if dots are allowed and first char is a dot
    $('.fpd-allow-dots').change(function(){

        if(this.value.charAt(0) == '.') {
	        this.value = '0'+this.value;
        }

    });

});

var openModal = function( $modalWrapper ) {

	jQuery('body').addClass('fpd-admin-modal-open');
	$modalWrapper.addClass('fpd-admin-modal-visible');

};

var closeModal = function( $modalWrapper ) {

	$modalWrapper.removeClass('fpd-admin-modal-visible');
	jQuery('body').removeClass('fpd-admin-modal-open');
	if(jQuery().select2) {
		$modalWrapper.find('.fpd-select2').each(function() {
			jQuery(this).select2("close");
		});
	}
	fpdResetForm($modalWrapper);

};

var toggleModalLoader = function($modalWrapper, toggle) {

	toggle = toggle === undefined ? false : toggle;
	$modalWrapper.find('.fpd-ui-blocker').toggle(toggle)

};

var fpdMessage = function(text, type) {

	jQuery('.fpd-message-box').remove();

	var $messageBox = jQuery('body').append('<div class="fpd-message-box fpd-'+type+'"><p>'+text+'</p></div>').children('.fpd-message-box').hide();
	$messageBox.css('margin-left', -$messageBox.width() * 0.5).fadeIn(300);

	$messageBox.delay(6000).fadeOut(200, function() {
		jQuery(this).remove();
	});

};

var fpdUpdateTooltip = function() {

	jQuery('.fpd-admin-tooltip').each(function(i, tooltip) {

		var $tooltip = jQuery(tooltip);
		if($tooltip.hasClass('tooltipstered')) {
			$tooltip.tooltipster('reposition');
		}
		else {
			$tooltip.tooltipster({
				offsetY: 0,
				theme: '.fpd-admin-tooltip-theme'
			});
		}

	});

};

var fpdParseJson = function(file) {

	try {
	  json = JSON.parse(file);
	} catch (exception) {
	  json = null;
	}

	if(json == null) {
		fpdMessage(fpd_fancy_products_opts.noJSON, 'error');
		return false;
	}
	else {
		return json;
	}

};

var fpdFillFormWithObject = function(values, $form) {

	try {

		var settingsObject = typeof values === 'object' ? values : JSON.parse(values);
		for(var prop in settingsObject) {

			if(settingsObject.hasOwnProperty(prop)) {

				var value = settingsObject[prop],
					$formElement = $form.find('[name="'+prop+'"]');

				if($formElement.is('input[type="radio"]') || $formElement.is('input[type="checkbox"]')) {
					$formElement.filter('[value="'+value+'"]').prop('checked', true);
				}
				else {
					$formElement.val(value);
				}

			}

		}
	}
	catch(e) {
	  // nothing
	  console.log(e);
	}

};

var fpdResetForm = function($form) {

	$form.find('[type="text"], [type="number"], textarea, select').val('');
	$form.find('[type="checkbox"], option').removeAttr('checked').removeAttr('selected');

};

var fpdSerializeObject = function(fields) {
    var o = {};
    var a = fields.serializeArray();
    jQuery.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
			if(this.value) {
				o[this.name].push(this.value || '');
			}

        } else {
        	if(this.value) {
	        	o[this.name] = this.value || '';
        	}
        }
    });
    return o;
};

//add new product via ajax
var fpdAddProduct = function(callback, options, thumbnail) {

	options = options === undefined ? '' : options;
	thumbnail = thumbnail === undefined ? '' : thumbnail;

	radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

		if(title === false) {
			callback(false);
			fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
		}
		else if(title !== null) {

			jQuery.ajax({
				url: fpd_admin_opts.adminAjaxUrl,
				data: {
					action: 'fpd_newproduct',
					_ajax_nonce: fpd_admin_opts.ajaxNonce,
					title: title,
					options: typeof options === 'object' ? JSON.stringify(options) : options,
					thumbnail: thumbnail
				},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data !== undefined || data.id !== undefined) {

						fpdMessage(data.message, data.id ? 'success' : 'error');

						if(callback !== undefined) {
							callback(data);
						}

						fpdUpdateTooltip();

					}

				}
			});

		}
		else {
			callback(false);
		}

	});

};

//add views to a product via ajax
var fpdAddViews = function(productId, views, addToLibrary, viewAdded, complete) {

	var keys = Object.keys(views),
		viewCount = 0;

	function _addView(view) {

		jQuery.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: {
				action: 'fpd_newview',
				_ajax_nonce: fpd_admin_opts.ajaxNonce,
				title: view.title,
				elements: JSON.stringify(view.elements),
				thumbnail: view.thumbnail,
				thumbnail_name: view.thumbnail_name ? view.thumbnail_name : view.title,
				add_images_to_library: addToLibrary ? 1 : 0,
				product_id: productId
			},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				viewCount++;

				if(data !== 0) {
					if(viewAdded !== undefined) {
						viewAdded(data);
					}
				}

				if(viewCount < keys.length) {
					_addView(views[keys[viewCount]]);
				}
				else {

					if(complete !== undefined) {
						complete();
					}

					fpdUpdateTooltip();

				}

			},
			error: function() {
				complete(false);
				fpdAjaxError();
			}
		});

	}

	if(keys.length > 0) {
		_addView(views[keys[0]]);
	}
	else {
		if(complete !== undefined) {
			complete();
		}
	}

};

var fpdAjaxError = function() {

	fpdMessage(fpd_admin_opts.tryAgain, 'error');

};

var fpdBlockPanel = function($panel) {

	$panel.find('.fpd-ui-blocker').show();

};

var fpdUnblockPanel = function($panel) {

	$panel.find('.fpd-ui-blocker').hide();

};


//update the form fields
var fpdSetDesignFormFields = function(paramsInput, thumbnailInput) {

	var $designThumbnail = jQuery('#fpd-set-design-thumbnail'), //thumbnail img-element
		$modalWrapper = $designThumbnail.parents('.fpd-admin-modal-wrapper:first');

	if(thumbnailInput) {
		$designThumbnail.css('background-image', 'url('+thumbnailInput.val()+')')
		.data('thumbnail', thumbnailInput.val());
	}

	jQuery('.fpd-hidden-design-category').toggle(Boolean(thumbnailInput)).each(function(i, item) {

		var $item = jQuery(item);
		if($item.is('input')) {
			$item.parents('tr:first').toggle(Boolean(thumbnailInput));
		}


	});


	var parameter_str = paramsInput.val().length > 0 ? paramsInput.val() : 'enabled=0&x=0&y=0&z=-1&scale=1&price=0&replace=&bounding_box_control=0&boundingBoxClipping=0';

	jQuery.each(parameter_str.split('&'), function (index, elem) {
		var vals = elem.split('='),
			$targetElement = $modalWrapper.find("form [name='" + vals[0] + "']");

		if($targetElement.is(':checkbox')) {
			$targetElement.prop('checked', vals[1] == 1 || vals[1] == 'yes');
		}
		else if($targetElement.is(':radio')) {
			$targetElement.filter('[value="'+vals[1]+'"]').prop('checked', true);
		}
		else {
			$targetElement.val(unescape(vals[1]));
		}

	});

	$modalWrapper.find('input[name="enabled"],[name="bounding_box_control"],[name="svgColors"]').change();

	openModal($modalWrapper);

};