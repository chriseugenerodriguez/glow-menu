jQuery(document).ready(function($) {

	var mediaUploader = null,
		$currentOptionsInput = null,
		$modalWrapper = $('#fpd-modal-edit-options'),
		$modalManageCategories = $('#fpd-modal-manage-categories'),
		$designCategoriesSelect = $('[name="design_category"]');

	//modal: manage categories
	var $modalManageCategories = $('#fpd-modal-manage-categories');
	$('#fpd-manage-categories').click(function(evt) {

		evt.preventDefault();
		openModal($modalManageCategories);

	});

	//update categories select2
	var _updateSelect = function() {

		$designCategoriesSelect.empty();

		$rootCategoriesList.find('li').each(function(i, item) {

			var $item = $(item),
				subDashes = Array($item.parents('ul').length).join('- ');

			$designCategoriesSelect.append('<option value="'+item.id+'">'+subDashes+$(item).find('.fpd-category-title:first').text()+'</option>');

		});

		$designCategoriesSelect.trigger('change.select2');

	};

	//make sortable list
	var $rootCategoriesList = $('#fpd-design-categories-list').nestedSortable({
		handle: 'div',
	    items: 'li',
	    toleranceElement: '> div',
	    listType: 'ul',
	    helper:	'clone',
	    opacity: .8,
	    forcePlaceholderSize: true,
	    containment: 'document',
	    placeholder: "ui-sortable-placeholder",
	    relocate: function(evt, ui) {

		    var categoryID = ui.item.attr('id'),
		    	parentID = ui.item.parents('li:first').attr('id');

			parentID = parentID === undefined ? 0 : parentID;

		    if(categoryID === undefined) {
			    return;
		    }

		    fpdBlockPanel($modalManageCategories);

		    $.ajax({
				url: fpd_admin_opts.adminAjaxUrl,
				data: {
					action: 'fpd_editdesigncategory',
					_ajax_nonce: fpd_admin_opts.ajaxNonce,
					category_id: categoryID,
					parent_id: parentID
				},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data !== undefined && data.error === undefined) {

						_updateSelect();
						fpdMessage(data.message, 'success');

					}
					else if(data.error) {
						fpdMessage(data.error, 'error');
					}

					fpdUnblockPanel($modalManageCategories);

				}
			});

	    }
	});

	//add new design category
	$modalManageCategories.on('click', '#fpd-add-new-design-category', function(evt) {

		evt.preventDefault();

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				fpdBlockPanel($modalManageCategories);

			    $.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_newdesigncategory',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						title: title
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined && data.error === undefined) {

							$rootCategoriesList.append(data.html);
							_updateSelect();
							fpdMessage(data.message, 'success');

						}
						else if(data.error) {
							fpdMessage(data.error, 'error');
						}

						fpdUnblockPanel($modalManageCategories);

					}
				});

			}

		});

	});

	//add category thumbnail
	$modalManageCategories.on('click', '.fpd-item-thumbnail', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			categoryID = $listItem.attr('id');

		mediaUploader = null;
        mediaUploader = wp.media({
	         title: fpd_admin_opts.chooseThumbnail,
            multiple: false
        });

        mediaUploader.listItem = $listItem;
        mediaUploader.categoryID = categoryID;
        mediaUploader.on('select', function() {

	        var thumbnail = mediaUploader.state().get('selection').toJSON()[0].url;

			fpdBlockPanel($modalManageCategories);

	        $.ajax({
				url: fpd_admin_opts.adminAjaxUrl,
				data: {
					action: 'fpd_editdesigncategory',
					_ajax_nonce: fpd_admin_opts.ajaxNonce,
					thumbnail: thumbnail,
					category_id: mediaUploader.categoryID
				},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data !== undefined && data.error === undefined) {

						mediaUploader.listItem.find('.fpd-item-thumbnail:first img').remove();
						mediaUploader.listItem.find('.fpd-item-thumbnail:first').append('<img src="'+thumbnail+'" />');

						fpdMessage(data.message, 'success');

					}
					else if(data.error) {

						fpdMessage(data.error, 'error');

					}

					fpdUnblockPanel($modalManageCategories);

				}
			});

	    });

		mediaUploader.open();

	});

	//remove category thumbnail
	$modalManageCategories.on('click', '.fpd-remove-item-thumbnail', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			categoryID = $listItem.attr('id');

		if($listItem.find('.fpd-item-thumbnail:first img').length === 0) {return;}

		fpdBlockPanel($modalManageCategories);

        $.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: {
				action: 'fpd_editdesigncategory',
				_ajax_nonce: fpd_admin_opts.ajaxNonce,
				thumbnail: '',
				category_id: categoryID
			},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				if(data !== undefined && data.error === undefined) {

					$listItem.find('.fpd-item-thumbnail:first img').remove();
					fpdMessage(data.message, 'success');

				}
				else if(data.error) {

					fpdMessage(data.error, 'error');

				}

				fpdUnblockPanel($modalManageCategories);

			}
		});

	});

	//edit category title
	$modalManageCategories.on('click', '.fpd-edit-category-title', function(evt) {

		evt.preventDefault();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			categoryID = $listItem.attr('id');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt, value: $listItem.find('.fpd-category-title:first').text()}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				fpdBlockPanel($modalManageCategories);

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_editdesigncategory',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						category_id: categoryID,
						title: title
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined && data.error === undefined) {

							$listItem.find('.fpd-category-title:first').text(title);
							_updateSelect();
							fpdMessage(data.message, 'success');

						}
						else if(data.error) {

							fpdMessage(data.error, 'error');

						}

						fpdUnblockPanel($modalManageCategories);

					}
				});

			}

		});

	});

	//edit category title
	$modalManageCategories.on('click', '.fpd-delete-category', function(evt) {

		evt.preventDefault();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			categoryID = $listItem.attr('id');

		radykalConfirm({ msg: fpd_admin_opts.remove}, function(c) {

			if(!c) {
				return false;
			}

			fpdBlockPanel($modalManageCategories);

			$.ajax({
				url: fpd_admin_opts.adminAjaxUrl,
				data: {
					action: 'fpd_deletedesigncategory',
					_ajax_nonce: fpd_admin_opts.ajaxNonce,
					category_id: categoryID
				},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data !== undefined && data.error === undefined) {

						if($listItem.children('ul').length > 0) {
							$listItem.children('ul').children('li').prependTo($listItem.parent('ul:first'));
						}

						$listItem.remove();
						_updateSelect();
						fpdMessage(data.message, 'success');

					}
					else if(data.error) {

						fpdMessage(data.error, 'error');

					}

					fpdUnblockPanel($modalManageCategories);

				}
			});

		});

	});

	//toggle collapse
	$modalManageCategories.on('click', '.fpd-collapse-list', function(evt) {

		evt.preventDefault();
		var $this = $(this).parents('li:first').toggleClass('fpd-collapsed');

	});


	//select2 box
	$designCategoriesSelect.change(function() {

		$('#fpd-designs-form').attr('action', fpd_fancy_designs_opts.adminUrl+"admin.php?page=fpd_manage_designs&category_id="+this.value+"").submit();

	});

	//set images from media library
	$('#fpd-manage-designs').on('click', '.fpd-add-designs', function(evt) {
		evt.preventDefault();

		if (mediaUploader) {
	        mediaUploader.open();
	        return;
	    }

	    mediaUploader = wp.media({
	        title: fpd_fancy_designs_opts.chooseDesign,
	        multiple: true
	    });

		mediaUploader.on('select', function() {

			mediaUploader.state().get('selection').each(function(item) {

				var attachment = item.toJSON();
				$('#fpd-designs-list').append('<li><img src="'+attachment.url+'" /><a href="#" class="fpd-edit-parameters"><i class="fpd-admin-icon-settings"></i></a><a href="#" class="fpd-remove-design"><i class="fpd-admin-icon-close"></i></a><input type="hidden" value="'+attachment.id+'" name="image_ids[]" /><input type="hidden" value="" name="parameters[]" /><input type="hidden" value="" name="thumbnail[]" /></li>');

			});

	    });

	    mediaUploader.open();
	});

	//change parameters of design
	$('#fpd-designs-form').on('click', '#fpd-edit-category-options, .fpd-edit-parameters', function(evt) {

		evt.preventDefault();

		var $this = $(this),
			$thumbnailInput = false;

		if($this.attr('id') == 'fpd-edit-category-options' ) {
			$currentOptionsInput = $('[name="fpd_category_options"]');
		}
		else {
			$currentOptionsInput = $this.parent().children('input[name="parameters[]"]');
			$thumbnailInput = $this.parent().children('input[name="thumbnail[]"]');
		}

		fpdSetDesignFormFields($currentOptionsInput, $thumbnailInput);

	});

	//switch background color of list items
	$('#fpd-black-white-switcher > a').click(function(evt) {

		evt.preventDefault();
		$('#fpd-designs-list li').css('backgroundColor', this.id === 'fpd-white' ? '#ffffff' : '#000000');

	});

	//make image list draggable
	$( "#fpd-designs-list" ).sortable({
		placeholder: 'fpd-sortable-placeholder'
	}).disableSelection();


	//save and close modal
	$('#fpd-modal-edit-options').on('click', '.fpd-save-admin-modal', function(evt) {

		evt.preventDefault();

		$currentOptionsInput
		.parent().children('[name="thumbnail[]"]').val($('#fpd-set-design-thumbnail').data('thumbnail'));

		$currentOptionsInput.val($modalWrapper.find('form').serialize());
		$currentOptionsInput = null;

		closeModal($modalWrapper);

	})
	.on('click', '.fpd-close-admin-modal', function(evt) {

		evt.preventDefault();
		$currentOptionsInput = null;

	});

	//remove design
	$('#fpd-manage-designs').on('click', '.fpd-remove-design', function(evt) {

		evt.preventDefault();
		$(this).parent('li:first').remove();

	});

});