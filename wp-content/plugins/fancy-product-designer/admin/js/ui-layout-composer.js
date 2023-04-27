var fancyProductDesigner;

jQuery(document).ready(function($) {

	if(!initial_opts) {
		return;
	}

	var $select = $('[name="fpd_ui_layouts"]'),
		$preview = $('#fpd-product-designer-preview'),
		isReady = false;


	/********************************
	******** SETUP ******************
	*********************************/

	var pluginOpts = $.extend({}, fpd_ui_layout_composer_opts.general_plugin_opts, initial_opts.plugin_options);
	pluginOpts.templatesType = 'php';
	//console.log(pluginOpts);

	$preview.on('ready', function() {

		fpdUpdateTooltip();
		isReady = true;

	});

	fancyProductDesigner = new FancyProductDesigner($preview, pluginOpts);

	/********************************
	******** TOP INTERACTIONS *******
	*********************************/

	$select.change(function() {

		$('[name="fpd_selected_layout"]').val($select.val())
		.parent('form').submit();

	});

	$('#fpd-save-as-new-layout, #fpd-save-layout').click(function(evt) {

		evt.preventDefault();

		if(!isReady) {return;}

		if(this.id == 'fpd-save-layout') { //overwrite

			radykalConfirm({ msg: fpd_ui_layout_composer_opts.overwrite_info}, function(c) {

				if(c) {

					$('[name="fpd_method"]').val('save');
					$('[name="fpd_selected_layout"]').val($select.val());
					$('[name="fpd_ui_layout"]').val(getFormJSON($select.children(':selected').text()))
					.parent('form').submit();

				}

			});

		}
		else {// save as new

			radykalPrompt({placeholder: fpd_admin_opts.enterTitlePrompt}, function(title) {

				if(title === false) {
					fpdMessage(fpd_admin_opts.enterTitlePrompt, 'error');
				}
				else if(title !== null) {

					$('[name="fpd_method"]').val('save-new');
					$('[name="fpd_selected_layout"]').val(title);
					$('[name="fpd_ui_layout"]').val(getFormJSON(title))
					.parent('form').submit();

				}

			});

		}

	});

	$('#fpd-delete-layout').click(function(evt) {

		evt.preventDefault();

		if(!isReady) {return;}

		var title = $select.val();

		if(title === 'default') {
			radykalAlert({msg: fpd_ui_layout_composer_opts.fpd_ui_layout_composer_opts});
			return;
		}

		radykalConfirm({ msg: fpd_ui_layout_composer_opts.delete_info}, function(c) {

			if(c) {

				$('[name="fpd_method"]').val('delete');
				$('[name="fpd_selected_layout"]').val($select.val())
				.parent('form').submit();

			}

		});

	});

	$('#fpd-reset-to-default-layout').click(function(evt) {

		evt.preventDefault();

		radykalConfirm({ msg: fpd_ui_layout_composer_opts.reset_default_info}, function(c) {

			if(c) {

				$('[name="fpd_method"]').val('reset');
				$('[name="fpd_selected_layout"]').val($select.val())
				.parent('form').submit();

			}

		});

	});



	/********************************
	******** COMMON *****************
	*********************************/

	function getFormJSON(title) {

		var layout_json = {};
		layout_json.name = title;
		layout_json.plugin_options = {
			stageWidth: isNaN($('#stageWidth').val()) ? 1000 : Number($('#stageWidth').val()),
			stageHeight: isNaN($('#stageHeight').val()) ? 600 : Number($('#stageHeight').val()),
			gridColumns: $('[name="grid_columns"]').val(),
			initialActiveModule: $('[name="initial_active_module"]').val(),
			selectedColor: $('[name="element_boundary_color"]').val(),
			boundingBoxColor: $('[name="bounding_box_color"]').val(),
			outOfBoundaryColor: $('[name="out_of_bounding_box_color"]').val(),
			cornerIconColor: $('[name="corner_control_icons_color"]').val(),
			mainBarModules: fancyProductDesigner.mainBar.currentModules,
			actions: fancyProductDesigner.actions.currentActions,
			toolbarPlacement: $('[name="toolbar_placement"]').val()
		};
		layout_json.container_classes = $preview.attr('class');
		layout_json.css_colors = {
			primary_color: $('[name="primary_color"]').val(),
			secondary_color: $('[name="secondary_color"]').val()
		};
		layout_json.toolbar_exclude_tools = $('[name="toolbar_exclude_tools[]"]').val();

		var editor = ace.edit(document.getElementById('fpd-custom-css'));
		layout_json.custom_css = editor.getValue();

		return JSON.stringify(layout_json);

	};

});