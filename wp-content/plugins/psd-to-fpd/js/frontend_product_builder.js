jQuery(function($) {
	if( $('body').hasClass('custom') && $('body').hasClass('menus') ) {
		$li = $('<button class="upload_your_design">Upload your design</button>');
		$li.insertBefore('ul.modules');
		$li.on('click', function(event) {
			event.preventDefault();
			var view_canvas_width = fancyProductDesigner.currentViewInstance.options.stageWidth,
				view_canvas_height = fancyProductDesigner.currentViewInstance.options.stageHeight;

			/*FPDUtil.showModal('<div class="container"><div class="col-lg-12"><h3>Upload specifications</h3><p>Please read carefully before upload</p><ul><li>Upload only photoshop (.psd) file.</li><li>Design dimension must not be greater than '+view_canvas_width+' x '+view_canvas_height+' canvas.</li><li>Please make sure you upload the correct size, if size bigger than canvas it will not upload.</li><li>Fonts will be used only if available in our library else it will be default to Arial.</li><li>On uploading psd your current layers will be removed.</li></ul><p><label><strong>Select file:</strong> <input type="file" id="fpd-upload-psd" class="upload" accept=".psd" /></label></p></div></div>');*/

			FPDUtil.showModal('<div class="container"><div class="col-lg-12"><label><input type="file" id="fpd-upload-psd" class="upload" accept=".psd" /><label for="fpd-upload-psd"><i class="fa fa-angle-up"></i><b>Upload your .psd</b><span>Choose File</span></label></label><small style="text-align:center;display:block;"><b>Note:</b> fonts not in our library will default to Arial.</small></div></div>');
		});
		$('body').on('change', '#fpd-upload-psd', function(event) {

			event.preventDefault();

			var ext = $(this).val().split('.').pop().toLowerCase();
			if( ext != 'psd' ) {
				$(this).val('');
				alert('Please select only .psd file');
				return;
			}

			var view_canvas_width = fancyProductDesigner.currentViewInstance.options.stageWidth,
				view_canvas_height = fancyProductDesigner.currentViewInstance.options.stageHeight,
				cancelProcess = false;

			/*if( confirm( 'Are you sure you want to continue ? All current existing layers will be removed from the canvas ?' ) === false ) {
				$(this).val('');
				return;
			}*/

			$('.fpd-modal-close').trigger('click');

			var $dialog_element = $('<div id="dialog_psd" title="Reading PSD"><div class="progress-label">Reading PSD...</div><div id="progressbar"><div class="current-progress-label"></div></div></div>'),
				$process_label = $( ".progress-label", $dialog_element ),
				$current_progress_label = $( ".current-progress-label", $dialog_element ),
				$dialog_psd = $dialog_element.dialog({
					autoOpen: false,
					closeOnEscape: false,
					resizable: false,
					width: 450,
					modal: true,
					buttons: [{
							text: "Cancel",
							click: cancelDesignProcess
						}],
					close: function( event, ui ) {
						cancelDesignProcess();
					}
				}),
				$progressbar = $( "#progressbar", $dialog_element ).progressbar({
						value: false,
						change: function() {
							var current_value = $( "#progressbar", $dialog_element ).progressbar( "value" ),
								current_max = $( "#progressbar", $dialog_element ).progressbar( "option", 'max' ),
								current_percentage = Math.floor( ( current_value * 100 ) / current_max );
							$current_progress_label.text( current_percentage + "%" );
						}
					});

			function cancelDesignProcess() {
				cancelProcess = true;
				$selector.off('elementAddCustom');
				$progressbar.progressbar('destroy').remove();
				$dialog_psd.dialog('destroy').remove();
			}

			$dialog_psd.dialog('open');

			var input = event.target;
			/*var reader = new FileReader();

			reader.onload = function() {
				var dataURL = reader.result;*/

				var PSD = require('psd');
				var selectedNames = "";

				//PSD.fromURL(dataURL).then(function(psd) {
				event.dataTransfer = {};
				event.dataTransfer.files = [];
				event.dataTransfer.files[0] = $(input)[0].files[0];
				PSD.fromEvent(event).then(function(psd) {

					if( cancelProcess === true ) {
						return;
					}

					var psd_width = psd.tree().get("width"),
						psd_height = psd.tree().get("height");

					if( psd_width > view_canvas_width || psd_height > view_canvas_height ) {
						alert('Design dimension must not be greater than '+view_canvas_width+' x '+view_canvas_height+' canvas.');
						$dialog_psd.dialog( "close" );
						return false;
					}

					var existingElements = fancyProductDesigner.getCustomElements(fancyProductDesigner.currentViewIndex),
						removedExistingElements = false;

					if( existingElements.length > 0 && cancelProcess === false ) {


						$dialog_psd.dialog( "option", "title", "Removing Existing Layers" );
						$process_label.text('Removing Existing Layers...');
						$progressbar.progressbar( "option", "max", existingElements.length );
						$progressbar.progressbar( "option", "value", 0 );

						var removeExistingLayers = function() {

							existingElements = fancyProductDesigner.getCustomElements(fancyProductDesigner.currentViewIndex);

							$.each(existingElements, function(index,value) {
									fancyProductDesigner.currentViewInstance.removeElement(value.element);
									existingElements.shift();
									return false;
								});

							var progressbar_value = $progressbar.progressbar( "option", "value" ) + 1;
							$progressbar.progressbar( "option", "value", progressbar_value );

							if( existingElements.length > 0 && cancelProcess === false ) {
								setTimeout(function() {
									if( cancelProcess === false ) {
										removeExistingLayers();
									}
								}, 1000);
							} else {
								if( cancelProcess === false ) {
									$progressbar.progressbar( "option", "value", false );
									$current_progress_label.text('Please wait...');
								}
								removedExistingElements = true;
							}
						};

						removeExistingLayers();
					} else {
						removedExistingElements = true;
					}

					selectedNames = psd.tree().descendants().reverse();
					var totalLayers = selectedNames.length;

					function addLayer() {

						var node = selectedNames.shift(),
							allow_node = true,
							np = CheckParent(node.parent),
							remainingLayers = totalLayers - selectedNames.length;

						if( np.visible() === false )
							allow_node = false;

						if( !node.isGroup() && !node.isEmpty() && node.visible() && allow_node && cancelProcess === false ) {

							nodeTypeTool = node.get('typeTool');

							if( typeof nodeTypeTool !== 'undefined' ) {

								nodeExport = node.export();
								nodeStyles = nodeTypeTool.styles();

								var params = {autoCenter: false, custom_left: node.left , custom_top: node.top, top:node.top, left:node.left, originX:'left', originY:'top', editable: true, removable: true, draggable: true, rotatable: true, resizable: true, lockUniScaling:false, isCustom: true};

								if( psd_width > view_canvas_width ) {
									var new_node_left = ( view_canvas_width * node.left ) / psd_width;
									params.custom_left = new_node_left;
									params.left = new_node_left;
									console.log(node.name+' Orignal Left:'+node.left);
									console.log(node.name+' New Left:'+new_node_left);
								}

								if( psd_height > view_canvas_height ) {
									var new_node_top = ( view_canvas_height * node.top ) / psd_height;
									params.custom_top = new_node_top;
									params.top = new_node_top;
									console.log(node.name+' Orignal Top:'+node.top);
									console.log(node.name+' New Top:'+new_node_top);
								}

								params.textBox = true;
								params.width = nodeExport.width + 21;
								params.height = nodeExport.height;

								var fontNameStyle = nodeExport.text.font.name.split("-");

								if( typeof fontNameStyle[1] === 'undefined' )
									fontNameStyle.push('NULL');

								var fontFamilyName = fontNameStyle[0];
								var fontStyle = fontNameStyle[1];

								if( fontNameStyle.length > 2 ) {
									fontStyle = fontNameStyle.pop();
									fontFamilyName = fontNameStyle.join("");
								}

								if(  fontFamilyName.substr(fontFamilyName.length -2, 2) == 'PS' )
									fontFamilyName = fontFamilyName.substr(0, fontFamilyName.length - 2);
								fontFamilyName = fontFamilyName.replace(/([A-Z])/g, ' $1').trim();

								params.fontFamily = fontFamilyName;
								params.fontSize = Math.round((nodeExport.text.font.sizes[0] * nodeExport.text.transform.yy) * 100) * 0.01;
								if( params.fontSize <= 0 ) {
									try {
										//putting in try catch as some times nodeExport.text.transform.yy returns exponenatial numbers.
										params.fontSize = Math.round((nodeExport.text.font.sizes[0] * nodeExport.text.transform.yy.toFixed(55)) * 100) * 0.01;
									} catch( e ) {
										console.log(e.message);
									}
								}

								if( params.fontSize <= 0 )
									params.fontSize = nodeExport.text.font.sizes[0];

								params.fill = rgb2hex("rgb(" + nodeExport.text.font.colors[0] + "," + nodeExport.text.font.colors[1] + "," + nodeExport.text.font.colors[2] + ")");

								params.textAlign = nodeExport.text.font.alignment[0];

								if( typeof nodeStyles.Leading !== 'undefined' ) {
									params.lineHeight = (nodeStyles.Leading[0] / nodeExport.text.font.sizes[0]).toFixed(2);
								}

								if( typeof nodeStyles.Tracking !== 'undefined' ) {
									params.letterSpacing = ( ( nodeStyles.Tracking[0] * nodeExport.text.transform.yy ) / 1000 ).toFixed(2);
								}

								if( typeof nodeStyles.FauxBold !== 'undefined' ) {
									if( nodeStyles.FauxBold[0] === true )
										params.fontWeight = 'bold';
								}
								if( params.fontWeight != 'bold' ) {
									if(  fontStyle.substr(0,4) == 'Bold' )
										params.fontWeight = 'bold';
									if(  nodeExport.text.font.name.toLowerCase().indexOf('semibold') > -1)
										params.fontWeight = 'bold';
								}

								if( typeof nodeStyles.FauxItalic !== 'undefined' ) {
									if( nodeStyles.FauxItalic[0] === true )
										params.fontStyle = 'italic';
								}
								if( params.fontStyle != 'italic' ) {
									if( fontStyle.substr(0,6) == 'Italic' || fontStyle.substr(4,6) == 'Italic' )
										params.fontStyle = 'italic';
								}
								if( typeof nodeStyles.Underline !== 'undefined' ) {
									if( nodeStyles.Underline[0] === true )
										params.textDecoration = 'underline';
								}
								var NodeTextValue = nodeExport.text.value;
								if( typeof nodeStyles.FontCaps !== 'undefined' ) {
									if( nodeStyles.FontCaps[0] > 0 )
										NodeTextValue = NodeTextValue.toUpperCase();
								}
								
								NodeTextValue = NodeTextValue.replace(//g, '\n');
								nodeExport = null;

								params.colors = custom_options.customTextParameters.colors;

								setTimeout(function() {
									if( cancelProcess === false ) {
										fancyProductDesigner.currentViewInstance.addElementCustom(
											'text',
											NodeTextValue,
											node.name,
											params
										);
									}
								}, 500);

							} else {
								var image = node.toPng();

								image.onload = function() {

									var imageParams = {autoCenter: false, custom_left: node.left , custom_top: node.top, top:node.top, left:node.left, originX:'left', originY:'top', resizable: true, removable: true,isCustom: true, rotatable: true};

									imageParams.colors = custom_options.customImageParameters.colors;

									var imageH = this.height,
										imageW = this.width,
										scaleX = 1,
										scaleY = 1,
										image_src = this.src;

									if( psd_width > view_canvas_width ) {
										var new_node_left = ( view_canvas_width * node.left ) / psd_width;
										imageParams.custom_left = new_node_left;
										imageParams.left = new_node_left;
										/*console.log(node.name+' Orignal Left:'+node.left);
										console.log(node.name+' New Left:'+new_node_left);*/
									}

									if( psd_height > view_canvas_height ) {
										var new_node_top = ( view_canvas_height * node.top ) / psd_height;
										imageParams.custom_top = new_node_top;
										imageParams.top = new_node_top;
										/*console.log(node.name+' Orignal Top:'+node.top);
										console.log(node.name+' New Top:'+new_node_top);*/
									}

									/*if( ( psd_width > view_canvas_width || psd_height > view_canvas_height ) && ( imageW > view_canvas_width || imageH > view_canvas_height ) ) {
									//if( psd_width != view_canvas_width || psd_height != view_canvas_height  ) {

										scaleX = scaleY = FPDUtil.getScalingByDimesions(
															imageW,
															imageH,
															view_canvas_width,
															view_canvas_height
														);

										imageParams.scaleX = scaleX;
										imageParams.scaleY = scaleY;
										console.log('Scale:'+scaleX);
									}*/

									setTimeout(function() {

										if( cancelProcess === false ) {

											fancyProductDesigner.currentViewInstance.addElementCustom(
												'image',
												image_src,
												node.name,
												imageParams
											);

										}
									}, 500);
								};
							}
							$progressbar.progressbar( "option", "value", remainingLayers );
						} else {
							if( cancelProcess === false ) {
								$progressbar.progressbar( "option", "value", remainingLayers );
								addLayer();
							}
						}
					}

					var checkedExistingLayersRemoved = setInterval(function() {
						if( removedExistingElements === true && cancelProcess === false ) {
							$dialog_psd.dialog( "option", "title", "Processing" );
							$process_label.text('Processing Layers...');
							$progressbar.progressbar( "option", "max", totalLayers );
							$progressbar.progressbar( "option", "value", 0 );
							addLayer();
							clearInterval(checkedExistingLayersRemoved);
						}
					}, 500);

					$selector.on('elementAddCustom', function(event, fabric_object) {
						if( typeof fabric_object.custom_top !== 'undefined' ) {

							fancyProductDesigner.currentViewInstance.setElementParameters({top:fabric_object.custom_top, left:fabric_object.custom_left, autoCenter: false, originX:'left', originY:'top', autoSelect: false, fontFamily: fabric_object.fontFamily, editable: true, removable: true, draggable: true, rotatable: true, resizable: true, boundingBox: fabric_object.boundingBox}, fabric_object, false);
							if ( selectedNames.length > 0 && cancelProcess === false ) {
								addLayer();
							} else {
								if( cancelProcess === false ) {
									$current_progress_label.text('Process completed!');
									$process_label.text('');
									$process_label.append('<span>Click Cancel button or (x) to close this window.</span>');
									$dialog_psd.dialog( "option", 'title', 'Process Completed' );
									$dialog_psd.dialog('close');
								}
								$selector.off('elementAddCustom');
							}
						}
					});

				}).then(function () {
					//console.log("Finished in " + ((new Date()) - start) + "ms");
				}).catch(function (err) {
					//console.log(err.stack);
				});
			/*};
			reader.readAsDataURL(input.files[0]);*/
		});
	}
});