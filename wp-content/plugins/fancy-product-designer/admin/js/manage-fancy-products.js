jQuery(document).ready(function($) {

	var mediaUploader = null,
		$productsList = $('#fpd-products-list'),
		$categoriesList = $('#fpd-categories-list');

	_updateSortable();

	//add new product
	$('#fpd-add-product').click(function(evt) {

		evt.preventDefault();

		blockProducts();
		fpdAddProduct(function(data) {

			if(data) {
				$productsList.siblings('.fpd-error-message').remove();
				$productsList.prepend('<ul class="fpd-views-list"></ul>').prepend(data.html);

				$('.fpd-error-message').remove();
			}

			unblockProducts();

		});

	});

	//modal: shortcodes
	var $modalShortcodes = $('#fpd-modal-shortcodes');
	$('#fpd-shortcode-builder').click(function(evt) {

		evt.preventDefault();
		openModal($modalShortcodes);

	});

	$modalShortcodes.find('textarea').focus(function() {
		$(this).select();
	});

	$modalShortcodes.find('#fpd-sc-pd-price').keyup(function() {
		$modalShortcodes.find('#fpd-sc-pd').val('[fpd] [fpd_form price_format="'+this.value+'"]');
	});

	function toTitleCase(str) {
	    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
	}

	var $scActionType = $modalShortcodes.find('#fpd-sc-action-type'),
		scActionTypeVal = scActionLayoutVal = '';

	FPDActions.availableActions.forEach(function(type) {

		if(type !== 'zoom') {
			$scActionType.append('<option value="'+type+'">'+toTitleCase(type.replace('-', ' '))+'</option>');
		}

	});
	$modalShortcodes.find('#fpd-action-attr select').change(function() {

		if(this.id === 'fpd-sc-action-type') {
			scActionTypeVal = this.value;
		}
		else {
			scActionLayoutVal = this.value;
		}

		setScAction();

	});

	function setScAction() {

		var scVal = '[fpd_action';
		if(scActionTypeVal !== '') {
			scVal += ' type="'+scActionTypeVal+'"';
		}
		if(scActionLayoutVal !== '') {
			scVal += ' layout="'+scActionLayoutVal+'"';
		}
		scVal += ']';

		$modalShortcodes.find('#fpd-sc-action').val(scVal);;

	}

	//modal: load demo
	var $modalLoadDemo = $('#fpd-modal-load-demo');
	$('#fpd-load-demo').click(function(evt) {

		evt.preventDefault();
		openModal($modalLoadDemo);

	});

	//modal: load template
	var $modalLoadTemplate = $('#fpd-modal-load-template');
	$('#fpd-load-template').click(function(evt) {

		evt.preventDefault();
		openModal($modalLoadTemplate);

	});

	//load template by id
	$modalLoadTemplate.on('click', 'li a:not(.fpd-remove-template)', function(evt) {

		evt.preventDefault();

		var templateID = this.id;

		blockProducts();

		fpdAddProduct(function(data) {

			if(data) {

				$productsList.append(data.html)
				.append('<ul class="fpd-views-list"></ul>');

				closeModal($modalLoadTemplate);

				//add views to product
				var productId = $productsList.children('.fpd-product-item:last').attr('id');

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_loadtemplate',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						id: templateID,
						product_id: productId
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data === 0 || data.error !== undefined) {
							fpdMessage(data.message, 'error');
						}
						else {
							$productsList.children('.fpd-views-list:last').html(data.html);
						}

						_updateSortable();
						unblockProducts();
						fpdUpdateTooltip();

					}
				});

			}
			else {
				unblockProducts();
			}

		});

	});

	//remove template
	$modalLoadTemplate.on('click', '.fpd-remove-template', function(evt) {

		evt.preventDefault();

		var $this = $(this);

		radykalConfirm({ msg: fpd_admin_opts.remove}, function(c) {

			if(c) {

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_removetemplate',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						id: $this.prev('a').attr('id')
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data == 0) {
							fpdMessage(fpd_admin_opts.tryAgain, 'error');
						}
						else {
							$this.parents('li').remove();
						}

					}
				});

			}

		});

	});

	//export product
	var $fileImport = $('[name="fpd_import_file"]');
	$('#fpd-import-product').click(function(evt) {

		evt.preventDefault();
		$fileImport.click();

	});

	$fileImport.change(function(evt) {

		radykalConfirm({ msg: fpd_admin_opts.addToLibrary+'<p>'+fpd_fancy_products_opts.importedFileStored+'</p>'}, function(addToLibrary) {

			$('[name="fpd_import_to_library"]').prop('checked', addToLibrary)
			.parent('form:first').submit();

			$fileImport.val('');

		});

	});


	//filter by
	$('[name="fpd_filter_by"],[name="fpd_order_by"]').change(function() {

		$(this).parent('form').submit();
	});

	//add new category
	$('#fpd-add-category').click(function(evt) {

		evt.preventDefault();

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				blockCategories();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_newcategory',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						title: title
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined || data.id !== undefined) {
							if(data.id) {
								$categoriesList.append(data.html);
							}
							fpdMessage(data.message, data.id ? 'success' : 'error');
							fpdUpdateTooltip();
							unBlockCategories();
						}

					}
				});

			}

		});

	});

	//select product
	$productsList.on('click', '>li', function() {

		if($(this).hasClass('fpd-active')) {
			return false;
		}

		$productsList.children('li').removeClass('fpd-active')
		$productsList.children('ul').stop().slideUp(200);

		$(this).addClass('fpd-active')
		.nextAll('.fpd-views-list:first').stop().slideDown(300);

		selectCategoriesByProduct();

	});

	//add product thumbnail
	$productsList.on('click', '.fpd-single-image-upload', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			productID = $listItem.attr('id');

		mediaUploader = null;
        mediaUploader = wp.media({
            multiple: false,
            title: fpd_fancy_products_opts.chooseThumbnail
        });

        mediaUploader.listItem = $listItem;
        mediaUploader.productID = productID;
        mediaUploader.on('select', function() {

	        var thumbnail = mediaUploader.state().get('selection').toJSON()[0].url;

			blockProducts();

	        $.ajax({
				url: fpd_admin_opts.adminAjaxUrl,
				data: {
					action: 'fpd_editproduct',
					_ajax_nonce: fpd_admin_opts.ajaxNonce,
					thumbnail: thumbnail,
					id: mediaUploader.productID
				},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data !== undefined || data.columns !== undefined) {
						if(data.columns.thumbnail !== undefined) {
							mediaUploader.listItem.find('.fpd-single-image-upload img').remove();
							mediaUploader.listItem.find('.fpd-single-image-upload').append('<img src="'+data.columns.thumbnail+'" />');
						}
					}
					else {
						fpdMessage(fpd_admin_opts.tryAgain, 'error');
					}

					unblockProducts();

				}
			});

	    });

		mediaUploader.open();

	});

	//remove product thumbnail
	$productsList.on('click', '.fpd-remove', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			productID = $listItem.attr('id');

		if($listItem.find('.fpd-single-image-upload img').length === 0) {return;}

		blockProducts();

        $.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: {
				action: 'fpd_editproduct',
				_ajax_nonce: fpd_admin_opts.ajaxNonce,
				thumbnail: '',
				id: productID
			},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				if(data !== undefined || data.columns !== undefined) {
					if(data.columns.thumbnail !== undefined) {
						$listItem.find('.fpd-single-image-upload img').remove();
					}
				}
				else {
					fpdMessage(fpd_admin_opts.tryAgain, 'error');
				}

				unblockProducts();

			}
		});

	});


	//edit product title
	$productsList.on('click', '.fpd-edit-product-title', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $productItem = $(this).parents('li');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt, value: $productItem.find('.fpd-product-title').text()}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_editproduct',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						id: $productItem.attr('id'),
						title: title
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined || data.columns !== undefined) {
							if(data.columns.title !== undefined) {
								$productsList.children('li').filter('[id="'+data.id+'"]').find('.fpd-product-title').text(title);
								fpdMessage(data.message, 'success');
							}
							else {
								fpdMessage(fpd_admin_opts.tryAgain, 'error');
							}

						}

						unblockProducts();

					}
				});

			}

		});

	});

	//edit product options
	var $modalEditProductOptions = $('#fpd-modal-edit-product-options'),
		$relatedProductOption = null;
	$productsList.on('click', '.fpd-edit-product-options', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var productID = $(this).parents('li').attr('id');

		$modalEditProductOptions.data('product_id', productID);

		openModal($modalEditProductOptions);
		toggleModalLoader($modalEditProductOptions, true);

		$.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: {
				action: 'fpd_loadproductoptions',
				_ajax_nonce: fpd_admin_opts.ajaxNonce,
				product_id: productID
			},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				toggleModalLoader($modalEditProductOptions, false);
				if(data && data.options) {
					fpdFillFormWithObject(data.options, $modalEditProductOptions);
				}

			}
		});

	});

	$modalEditProductOptions.find('.fpd-save-admin-modal').click(function() {

		var $formFields = $modalEditProductOptions.find('input'),
			newValues = fpdSerializeObject($formFields);

		toggleModalLoader($modalEditProductOptions, true);
		newValues = _.isEmpty(newValues) ? '' : newValues;

		$.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: {
				action: 'fpd_editproduct',
				_ajax_nonce: fpd_admin_opts.ajaxNonce,
				id: $modalEditProductOptions.data('product_id'),
				options: newValues
			},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				toggleModalLoader($modalEditProductOptions, false);
				closeModal($modalEditProductOptions);

				if(!_.isUndefined(data) && !_.isUndefined(data.columns)) {

					if(!_.isUndefined(data.columns.options)) {
						fpdMessage(data.message, 'success');
					}
					else {
						fpdMessage(fpd_admin_opts.tryAgain, 'error');
					}

				}

			}
		});

	});


	//export product
	$productsList.on('click', '.fpd-export-product',function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $listItem = $(this).parents('li:first');

		if($listItem.next('ul:first').children('li').length == 0) {

			fpdMessage(fpd_fancy_products_opts.nothingToExport, 'info');
			return;
		}

		var urlAjaxExport = fpd_admin_opts.adminAjaxUrl+'?action=fpd_export&_ajax_nonce='+fpd_admin_opts.ajaxNonce+'&id='+$listItem.attr('id')+'';
		location.href = urlAjaxExport;


	});

	//remove a fancy product
	$productsList.on('click', '.fpd-remove-product', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $listItem = $(this).parents('li');

		radykalConfirm({ msg: fpd_admin_opts.remove}, function(c) {

			if(c) {

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: { action: 'fpd_removeproduct', _ajax_nonce: fpd_admin_opts.ajaxNonce, id: $listItem.attr('id')},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined) {
							if(data == 1) {
								$listItem.next('.fpd-views-list:first').remove();
								$listItem.remove();
							}
							else {
								fpdMessage(fpd_admin_opts.tryAgain, 'error');
							}
						}

						selectCategoriesByProduct();
						unblockProducts();

					}
				});

			}

		});

	});

	//save as template
	$productsList.on('click', '.fpd-save-as-template',function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $listItem = $(this).parents('li');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_saveastemplate',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						title: title,
						product_id: $listItem.attr('id')
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined) {
							if(data.html) {
								$modalLoadTemplate.find('.fpd-admin-modal-content p').remove();
								$modalLoadTemplate.find('ul').append(data.html);
								fpdMessage(data.message, 'success');
							}
							else {
								fpdMessage(fpd_admin_opts.tryAgain, 'error');
							}
						}

						selectCategoriesByProduct();
						unblockProducts();

					}
				});

			}

		});

	});

	//duplicate product
	$productsList.on('click', '.fpd-duplicate-product',function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $listItem = $(this).parents('li:first'),
			options = $listItem.find('.fpd-product-options').val();

		blockProducts();

		fpdAddProduct(function(data) {

			if(data) {

				$productsList.append(data.html)
				.append('<ul class="fpd-views-list"></ul>');

				//add views to new product
				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_duplicateproduct',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						new_id: data.id,
						source_id: $listItem.attr('id')
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data === 0 || data.error !== undefined) {
							fpdMessage(data.message, 'error');
						}
						else {
							$productsList.children('.fpd-views-list:last').html(data.html);
						}

						_updateSortable();
						unblockProducts();
						fpdUpdateTooltip();

					}
				});

			}
			else {
				unblockProducts();
			}

		}, options, $listItem.find('.fpd-single-image-upload img').attr('src'));

	});

	//assign category
	$categoriesList.on('click', 'input', function() {

		if($productsList.children('li.fpd-active').length == 0) {
			radykalAlert({msg: fpd_fancy_products_opts.selectProduct});
			return false;
		}

		blockCategories();
		showAllProducts();

		var $this = $(this),
			productID = $productsList.children('li.fpd-active').attr('id'),
			categoryID = $this.parents('li').attr('id');

		$.ajax({
			url: fpd_admin_opts.adminAjaxUrl,
			data: { action: 'fpd_assigncategory', _ajax_nonce: fpd_admin_opts.ajaxNonce, productID: productID, categoryID: categoryID, checked: $this.is(':checked') ? 1 : 0},
			type: 'post',
			dataType: 'json',
			success: function(data) {

				var selecteCats = [];
				$categoriesList.find('input:checked').parents('li').each(function(i, item) {
					selecteCats.push(item.id);
				});

				$productsList.children('li.fpd-active').data('categories', selecteCats.toString());
				unBlockCategories();

			}
		});


	});

	//edit product title
	$categoriesList.on('click', '.fpd-edit-category-title', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $categoryItem = $(this).parents('li');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt, value: $categoryItem.find('.fpd-category-title').text()}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				blockCategories();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_editcategory',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						id: $categoryItem.attr('id'),
						title: title
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined || data.columns !== undefined) {
							if(data.columns.title !== undefined) {
								$categoryItem.find('.fpd-category-title').text(title);
								fpdMessage(data.message, 'success');
							}
							else {
								fpdMessage(fpd_admin_opts.tryAgain, 'error');
							}

						}

						unBlockCategories();

					}
				});

			}

		});

	});

	//remove a fancy product category
	$categoriesList.on('click', '.fpd-remove-category', function(evt) {

		evt.preventDefault();

		var $listItem = $(this).parents('li');

		radykalConfirm({ msg: fpd_admin_opts.remove}, function(c) {

			if(c) {

				blockCategories();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: { action: 'fpd_removecategory', _ajax_nonce: fpd_admin_opts.ajaxNonce, id: $listItem.attr('id')},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined) {
							if(data == 1) {
								$listItem.remove();
							}
							else {
								fpdMessage(fpd_admin_opts.tryAgain, 'error');
							}

						}

						unBlockCategories();

					}
				});

			}


		});

	});

	//add new view
	$productsList.on('click', '.fpd-add-view', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			productID = $listItem.attr('id');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				mediaUploader = null;
		        mediaUploader = wp.media({
		            multiple: false,
		            title: fpd_fancy_products_opts.chooseThumbnail
		        });

		        mediaUploader.viewTitle = title;
		        mediaUploader.listItem = $listItem;
		        mediaUploader.productID = productID;
		        mediaUploader.on('select', function() {

		        	blockProducts();

					var viewThumbnail = mediaUploader.state().get('selection').toJSON()[0].url;

		        	if(viewThumbnail.length > 4) {

		        		//add new view
		        		$.ajax({
							url: fpd_admin_opts.adminAjaxUrl,
							data: {
								action: 'fpd_newview',
								_ajax_nonce: fpd_admin_opts.ajaxNonce,
								title: mediaUploader.viewTitle,
								thumbnail: viewThumbnail,
								product_id: mediaUploader.productID
							},
							type: 'post',
							dataType: 'json',
							success: function(data) {

								if(data == 0) {
									fpdMessage(fpd_admin_opts.tryAgain, 'error');
								}
								else {
									mediaUploader.listItem.next('ul:first').append(data.html);
								}

								fpdUpdateTooltip();
								unblockProducts();

							}
						});

		        	}
		        });

				mediaUploader.open();

			}

		});

	});

	//edit view thumbnail
	$productsList.on('click', '.fpd-view-item img', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			viewId = $listItem.attr('id');


        mediaUploader = null;
        mediaUploader = wp.media({
            multiple: false,
            title: fpd_fancy_products_opts.chooseThumbnail
        });

        mediaUploader.listItem = $listItem;
        mediaUploader.viewID = viewId;
        mediaUploader.on('select', function() {

	        var viewThumbnail = mediaUploader.state().get('selection').toJSON()[0].url;

			blockProducts();

	        $.ajax({
				url: fpd_admin_opts.adminAjaxUrl,
				data: {
					action: 'fpd_editview',
					_ajax_nonce: fpd_admin_opts.ajaxNonce,
					thumbnail: viewThumbnail,
					id: mediaUploader.viewID
				},
				type: 'post',
				dataType: 'json',
				success: function(data) {

					if(data !== undefined || data.columns !== undefined) {
						if(data.columns.thumbnail !== undefined) {
							mediaUploader.listItem.find('span:first img').attr('src', data.columns.thumbnail);
						}
					}
					else {
						fpdMessage(fpd_admin_opts.tryAgain, 'error');
					}

					unblockProducts();

				}
			});

	    });

		mediaUploader.open();

	});

	//edit view title
	$productsList.on('click', '.fpd-edit-view-title', function(evt) {

		evt.preventDefault();
		evt.stopPropagation();

		var $this = $(this),
			$listItem = $this.parents('li:first'),
			viewId = $listItem.attr('id');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt, value: $listItem.find('span:first label').text()}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_editview',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						title: title,
						id: viewId
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined || data.columns !== undefined) {
							if(data.columns.title !== undefined) {
								$listItem.find('span:first label').text(data.columns.title);
							}
						}
						else {
							fpdMessage(fpd_admin_opts.tryAgain, 'error');
						}

						unblockProducts();

					}
				});

			}

		});

	});

	$productsList.on('click', '.fpd-duplicate-view', function(evt) {

		evt.preventDefault();

		var $listItem = $(this).parents('li:first'),
			viewId = $listItem.attr('id');

		radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

			if(title === false) {
				fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
			}
			else if(title !== null) {

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_duplicateview',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						id: viewId,
						title: title
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data == 0) {
							fpdMessage(fpd_admin_opts.tryAgain, 'error');
						}
						else {
							$listItem.parent('ul').append(data.html);
						}

						unblockProducts();

					},
					error: ajaxErrorFunction
				});

			}

		});

	});

	$productsList.on('click', '.fpd-remove-view', function(evt) {

		evt.preventDefault();

		var $listItem = $(this).parents('li');

		radykalConfirm({ msg: fpd_admin_opts.remove}, function(c) {

			if(c) {

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_removeview',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						id: $listItem.attr('id')
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined) {
							if(data == 1) {
								$listItem.remove();
							}
							else {
								fpdMessage(fpd_admin_opts.tryAgain, 'error');
							}

						}

						unblockProducts();

					},
					error: ajaxErrorFunction
				});

			}

		});

	});

	function blockCategories() {
		$categoriesList.nextAll('.fpd-ui-blocker').show();
	};

	function unBlockCategories() {
		$categoriesList.nextAll('.fpd-ui-blocker').hide();
	};

	function blockProducts() {
		$productsList.nextAll('.fpd-ui-blocker').show();
	};

	function unblockProducts() {
		$productsList.nextAll('.fpd-ui-blocker').hide();
	};

	function showAllProducts() {
		$productsList.children('li').show();
		$categoriesList.find('.fpd-filter-category').removeClass('fpd-active');
	};

	//select categories by selected product
	function selectCategoriesByProduct() {

		$categoriesList.find('input').prop('checked', false);
		var catIDs = String($productsList.children('li.fpd-active').data('categories'));
		catIDs = catIDs.split(',');

		for(var i=0; i<catIDs.length; ++i) {
			$categoriesList.children('[id="'+catIDs[i]+'"]').find('input').prop('checked', true);
		}

	};

	//update sortable list
	function _updateSortable() {

		$productsList.children('ul:not(.ui-sortable)').sortable({
			cursor: 'move',
			axis: 'y',
			scrollSensitivity: 40,
			forcePlaceholderSize: true,
			helper: 'clone',
			opacity: 0.65,
			placeholder: 'fpd-sortable-placeholder',
			update: function( event, ui ) {

				//save views
				var ids = $(this).children('li').map(function(){
				  return $(this).attr('id');
				}).toArray();

				blockProducts();

				$.ajax({
					url: fpd_admin_opts.adminAjaxUrl,
					data: {
						action: 'fpd_saveviews',
						_ajax_nonce: fpd_admin_opts.ajaxNonce,
						ids: ids
					},
					type: 'post',
					dataType: 'json',
					success: function(data) {

						if(data !== undefined || data.message !== undefined) {
							fpdMessage(data.message, 'success');
						}
						else {
							fpdMessage(fpd_admin_opts.tryAgain, 'error');
						}

						unblockProducts();

					}
				});

			}
		}).disableSelection();

	};

	function ajaxErrorFunction(data) {

		fpdMessage(fpd_admin_opts.tryAgain, 'error');
		unblockProducts();
		unBlockCategories();

	};

});