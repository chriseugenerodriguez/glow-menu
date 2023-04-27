var  closeProcess = false;
jQuery(document).ready(function(){
	jQuery('#fpd-add-element').append('<span class="fileUpload"><span class="add-new-h2">Load From PSD</span><input type="file" id="fpd-add-psd-element" class="upload" accept=".psd" /></span><div id="dialogPSD" title="PSD Upload Process"><div class="pbar2"><h3>Deleting Layers:</h3><div id="progressbar2"></div><div class="span01"></div><div class="span02"></div><div class="span03"></div></div><div class="pbar1"><h3>Uploading Layers:</h3><div id="progressbar"></div><div class="span1"></div><div class="span2"></div><div class="span3"></div></div></div>');
	jQuery( "#dialogPSD" ).dialog({
	  width: 500,
	  autoOpen: false
	});
	jQuery( "#dialogPSD" ).on( "dialogopen", function( event, ui ) {
		closeProcess = false;
	});
	jQuery( '#dialogPSD' ).on('dialogclose', function( event, ui ) {
	    closeProcess = true;
	    setTimeout(function() {
			jQuery('#fpd-save-layers').click();
			throw new Error("Process stopped!!");
		}, 4000);
	});
})

jQuery(document).ready(function($) {
//add new element buttons handler
	
	var $fpd = $('#fpd-preview-wrapper'),
		fancyProductDesigner = $fpd.data('instance');

	/*$fpd.on('elementAdd', function(event, fabric_object) {
		event.pause();
		setTimeout(function() {
			event.resume();
		}, 50000000);
		console.log('sleep');
	});*/

		//fancyProductDesigner.setDimensions(600,400);
	$(document).on('change.fpd-add-psd-element','#fpd-add-psd-element', function(event) {
		event.preventDefault();
		//console.log('Change');
			//alert($(this).val());
			$( ".pbar1, .pbar2" ).hide();

			$( "#progressbar" ).progressbar({ value: 0 });
			$( "#progressbar2" ).progressbar({ value: 0 });
			$( "#dialogPSD .span1, #dialogPSD .span01" ).html("");
			$( "#dialogPSD .span2, #dialogPSD .span02" ).html("");
			$( "#dialogPSD .span3, #dialogPSD .span03" ).html("");

			var ConfMsg = 'Click YES to Continue';
			if($( "#fpd-elements-list .fpd-layer-item" ).length > 0) {
				ConfMsg = 'Remove all layers before loading layers from PSD?<br/>Click YES to remove all existing layers.<br/>Click NO to append to existing layers.';
			}
			viewInstance = fancyProductDesigner.currentViewInstance;
			radykalConfirm({ msg: ConfMsg}, function(c) {

				if(c) {

					if(ConfMsg != 'Click YES to Continue') {

					var layerids = [];
					$( "#fpd-elements-list .fpd-layer-item" ).each(function( index ) {
							layerids[index] = $(this).attr('id');
					});

					var totalRLayer = layerids.length;
					
					if(layerids.length == 0)
						totalRLayer = 0;
					var remover = 1;

					$( ".pbar2" ).show();$( ".pbar1" ).hide();
					$( "#dialogPSD" ).dialog( "open" );
					$( "#dialogPSD .span01" ).html("Deleted Layer : 0");
					$( "#dialogPSD .span02" ).html("Total Layers : " + totalRLayer);
					$( "#dialogPSD .span03" ).html("Process Completed 0%");

					function RemoveLayer() {
					    var ids = layerids.shift();
					    //remover++;

					    if( $("#" + ids).hasClass( "fpd-layer-item--image" ) )
						{

							jQuery.when( jQuery.post(
							    ajaxurl, 
							    {
							        'action': 'remove_images',
							        'unlink':  $("#" + ids).find('img').attr('src')
							    })).then(function ( response ) {

							/*jQuery.post(
							    ajaxurl, 
							    {
							        'action': 'remove_images',
							        'unlink':  $("#" + ids).find('img').attr('src')
							    }, 
							    function(response){
							    	//console.log(response);
							        
							    }).done(function () {*/

							    	viewInstance.removeElement(viewInstance.getElementByID(ids));
							    	//console.log("R1 - " + layerids.length);
						            if (layerids.length > 0 && closeProcess == false) {
						            	remover++;

						            	$( "#progressbar2" ).progressbar({
										  value: remover,
										  max: totalRLayer
										});
										$( "#dialogPSD .span01" ).html("Deleted Layers : " + remover);

										Rpercent = Math.floor( (remover * 100) / totalRLayer );
										$( "#dialogPSD .span03" ).html("Process Completed " + Rpercent + "%");
										//console.log('R1');
										setTimeout(function() {
											RemoveLayer();
										}, 500);
										return;
						            }
						        });
						        
						}
						else
						{
					  		viewInstance.removeElement(viewInstance.getElementByID(ids));
					  		//console.log("R2 - " + layerids.length);
					  		if (layerids.length > 0 && closeProcess == false) {
					  			remover++;
					  			$( "#progressbar2" ).progressbar({
								  value: remover,
								  max: totalRLayer
								});
								$( "#dialogPSD .span01" ).html("Deleted Layers : " + remover);

								Rpercent = Math.floor( (remover * 100) / totalRLayer );
								$( "#dialogPSD .span03" ).html("Process Completed " + Rpercent + "%");
								//console.log('R2');
								setTimeout(function() {
									RemoveLayer();
								}, 500);
								return;
				            }
					  	}
					  	//console.log(totalRLayer + " <= " + remover);
						if(totalRLayer <= remover && closeProcess == false)
						{
	    					$( "#dialogPSD .span03" ).html("Process Completed 100%");
	    					//console.log('U1');
	    					setTimeout(function() {
								UploadLayerImages();
							}, 500);
	    				}
					}
					//console.log('R3');
					RemoveLayer();
					
					}
					else
					{
						if( closeProcess == false)
						{
							setTimeout(function() {
								UploadLayerImages();
							}, 500);
						}
					}

				}else if(ConfMsg == 'Click YES to Continue') {
					//console.log('U2');
					return;
				}else{
					//console.log('U3');
					if( closeProcess == false )
					{
						UploadLayerImages();
					}
				}

function UploadLayerImages() {	
					//var openFile = function(event) {
					
						/*$fpd.on('elementAdd', function(event, fabric_object) {
		console.log(fabric_object.custom_top);
		//if( typeof fabric_object.custom_top !== 'udefinded' ) {
		//
			//fancyProductDesigner.currentViewInstance.setElementParameters({top:fabric_object.custom_top, left:fabric_object.custom_left}, fabric_object);
			fabric_object.setOptions({top:fabric_object.custom_top, left:fabric_object.custom_left});
		//}
	});*/

					    var input = event.target;

					    var reader = new FileReader();
					    reader.onload = function(){
					      var dataURL = reader.result;
					      
					    

						//var imageParams = {autoCenter: false};

						/**/

var PSD = require('psd');

var selectedNames = "";


// Load from URL
//Disabled fromURL as it is possible to load psd from change event using psd fromEvent. Also remove reader onload as it is not required and handled by psd fromEvent
PSD.fromURL(dataURL).then(function(psd) {
/*event.dataTransfer = {};
event.dataTransfer.files = [];
event.dataTransfer.files[0] = $(input)[0].files[0];
PSD.fromEvent(event).then(function(psd) {*/

	selectedNames = psd.tree().descendants().reverse();
	var totalLayer = selectedNames.length;
	if(selectedNames.length == 0)
		totalLayer = 0;
	var counter = 1;

	//Set Stage Height/Width
	if( totalLayer > 0 && closeProcess == false )
	{
		var psd_width = psd.tree().get("width");
		var psd_height = psd.tree().get("height");

		fancyProductDesigner.setDimensions( psd_width , psd_height );

		jQuery.post(
		    ajaxurl, 
		    {
		        'action': 'stage_size',
		        'view': $('#fpd-view-switcher').val(),
		        'width':  psd_width,
		        'height':  psd_height
		    }, 
		    function(response){
		    	$('.fpd-panel h3 span.description').html( response );
		    }).done(function () {

	        });
	}

	//PSD  = null;
	//console.log(PSD);return;
	//psdMode = psd.header.modeName();
	//console.log(fancyProductDesigner.mainOptions.fonts);return;
	//console.log(psd.tree().export());
	//console.log(psd.header.modeName());return;RGBColor
	//console.log(psd.tree().get("width"));
	//return;

	$( ".pbar1" ).show();$( ".pbar2" ).hide();
	$( "#dialogPSD" ).dialog( "open" );
	$( "#dialogPSD .span1" ).html("Processed Layer : 0");
	$( "#dialogPSD .span2" ).html("Total Layers : " + totalLayer);
	$( "#dialogPSD .span3" ).html("Process Completed 0%");

	var imageLayerCounter = 0;
	function SaveLayer() {
	    var node = selectedNames.shift();
	    //counter++;
	    //return;
	    //if(node.get("width")>0)
	    //console.log(node.name ++ node.isGroup());

	    var allow_node = true;
	    /*if(node.parent.isGroup() && !node.parent.visible())
	    	allow_node = false;*/

	    /*var np = node.parent;
	    console.log(node.name);
	    console.log(np);
	    console.log(np.isRoot());
	    console.log(np.visible());
	    console.log(np.visible()==false);
	    return;*/
	    /*while( typeof np !== 'undefined' && ( !np.isRoot() && ( typeof np.visible() !== 'undefined' && np.visible() != 'undefined' && np.visible()==true ) ) )
	    {
	    	np = np.parent;
	    }*/
	    var np = CheckParent(node.parent);
	    /*console.log(np);
	    console.log(np.visible());
	    console.log("---------");*/
	    if(np.visible() == false)
	    	allow_node = false;
	    if(!node.isGroup() && !node.isEmpty() && node.visible() && allow_node && closeProcess == false )
	    {
	    	/*if(node.parent.isGroup())
	    		console.log( node.parent.isGroup() + " - " + node.parent.visible());*/
	    	//console.log('Name: ' + node.name + ', Top: ' + node.top + ', Left: ' + node.left);
	    	
	    	//nodeExport = node.export();
	    	nodeTypeTool = node.get('typeTool');
	    	//console.log(node.get('typeTool'));
	    	//if(typeof nodeExport.text !== 'undefined')
	    	if(typeof nodeTypeTool !== 'undefined')
    		{
    			nodeExport = node.export();
    			/*nodeTypeTool = node.get('typeTool');
    			nodeStyles = nodeTypeTool.styles();*/
    			nodeStyles = nodeTypeTool.styles();

    			/*console.log(node.name);
    			console.log(nodeExport);
    			console.log(nodeStyles);
    			console.log(nodeTypeTool);
    			console.log("==============");*///Tracking
    			/*if(nodeExport.text.value == "LOREMIPSUM.COM"){
	    			console.log('=======================');
	    			console.log(node);
	    			console.log(nodeExport);
	    			console.log(nodeStyles);
    			}*/

    			var params = {autoCenter: false, custom_left: node.left , custom_top: node.top, top:node.top, left:node.left, originX:'left', originY:'top', editable: 1, removable: 1, draggable: 1, rotatable: 1};

				params.textBox = true;
				params.width = nodeExport.width + 21;
				//console.log(node.name + " : " + node.get('mask').width + " " + nodeExport.width);
				params.height = nodeExport.height;

				var fontNameStyle = nodeExport.text.font.name.split("-");

				if( typeof fontNameStyle[1] === 'undefined' )
					fontNameStyle.push('NULL');

				var fontFamilyName = fontNameStyle[0];
				var fontStyle = fontNameStyle[1];

				if(fontNameStyle.length > 2)
				{
					fontStyle = fontNameStyle.pop();
					fontFamilyName = fontNameStyle.join("");
				}

				/*console.log(node.name);
				console.log(nodeExport.text.font);
				console.log(nodeStyles);
				console.log("======================");*/
				/*console.log(fontNameStyle);
				console.log(nodeExport.text.font);
				console.log(fontStyle);
				console.log(fontFamilyName);*/

				if(  fontFamilyName.substr(fontFamilyName.length -2, 2) == 'PS' )
					fontFamilyName = fontFamilyName.substr(0, fontFamilyName.length - 2);
				fontFamilyName = fontFamilyName.replace(/([A-Z])/g, ' $1').trim();

				//if($.inArray(nodeExport.text.font.name, fancyProductDesigner.mainOptions.fonts))
				params.fontFamily = fontFamilyName;
				//params.fontSize = nodeExport.text.font.sizes[0];
				params.fontSize = Math.round((nodeExport.text.font.sizes[0] * nodeExport.text.transform.yy) * 100) * 0.01;
				//console.log(node.name + " - " + nodeExport.text.font.sizes[0] + " - " + nodeExport.text.transform.yy);
				if(params.fontSize <= 0) {
					try {
						//putting in try catch as some times nodeExport.text.transform.yy returns exponenatial numbers.
						params.fontSize = Math.round((nodeExport.text.font.sizes[0] * nodeExport.text.transform.yy.toFixed(55)) * 100) * 0.01;
					} catch( e ) {
						console.log(e.message);
					}
				}
				if(params.fontSize <= 0)
					params.fontSize = nodeExport.text.font.sizes[0];
				/*var data = node.export().text;
				var transY = data.transform.yy; // 2.000077137715913
				var fontSize = data.font.sizes[0]; // 15.99938 ✘
				var lineHeight = data.font.leadings[0];  // 60 ✘

				fontSize = Math.round((fontSize * transY) * 100) * 0.01; // 32 ✔
				lineHeight = Math.round((fontSize * transY) * 100) * 0.01; // 64 ✔*/
				params.fill = rgb2hex("rgb(" + nodeExport.text.font.colors[0] + "," + nodeExport.text.font.colors[1] + "," + nodeExport.text.font.colors[2] + ")");
				/*if(psdMode == 'RGBColor'){
					params.fill = rgb2hex("rgb(" + nodeExport.text.font.colors[0] + "," + nodeExport.text.font.colors[1] + "," + nodeExport.text.font.colors[2] + ")");
				}else{
					params.fill = cmykToRGB(nodeExport.text.font.colors[0] + "," + nodeExport.text.font.colors[1] + "," + nodeExport.text.font.colors[2] + "," + nodeExport.text.font.colors[3]);
				}*/
				params.textAlign = nodeExport.text.font.alignment[0];
				
				if( typeof nodeStyles.Leading !== 'undefined' ) {
					params.lineHeight = (nodeStyles.Leading[0] / nodeExport.text.font.sizes[0]).toFixed(2);
				}
				/*if(params.lineHeight < 1)
					params.lineHeight = 1;*/
				
				/*console.log("=============");
				console.log(node.name);
				console.log(nodeStyles);*/
				if( typeof nodeStyles.Tracking !== 'undefined' ) {
					params.letterSpacing = ( ( nodeStyles.Tracking[0] * nodeExport.text.transform.yy ) / 1000 ).toFixed(2);
				}

				if( typeof nodeStyles.FauxBold !== 'undefined' ) {
					if( nodeStyles.FauxBold[0] == true )
						params.fontWeight = 'bold';
				}
				if( params.fontWeight != 'bold' ) {
					if(  fontStyle.substr(0,4) == 'Bold' )
						params.fontWeight = 'bold';
					if(  nodeExport.text.font.name.toLowerCase().indexOf('semibold') > -1)
						params.fontWeight = 'bold';
				}

				if( typeof nodeStyles.FauxItalic !== 'undefined' ) {
					if( nodeStyles.FauxItalic[0] == true )
						params.fontStyle = 'italic';
				}
				if( params.fontStyle != 'italic' ) {
					if( fontStyle.substr(0,6) == 'Italic' || fontStyle.substr(4,6) == 'Italic' )
						params.fontStyle = 'italic';
				}
				if( typeof nodeStyles.Underline !== 'undefined' ) {
					if( nodeStyles.Underline[0] == true )
						params.textDecoration = 'underline';
				}
				var NodeTextValue = nodeExport.text.value;
				if( typeof nodeStyles.FontCaps !== 'undefined' ) {
					if( nodeStyles.FontCaps[0] > 0 )
						NodeTextValue = NodeTextValue.toUpperCase();
				}
				//NodeTextValue = NodeTextValue.replace(/[\uE000-\uF8FF]/g, "\n");
				NodeTextValue = NodeTextValue.replace(//g, '\n');
				nodeExport = null;

				params.colors = custom_options.customTextParameters.colors;

				setTimeout(function() {
					fancyProductDesigner.currentViewInstance.addElementCustom(
						'text',
						NodeTextValue,
						node.name,
						params
					);
				}, 500);
				

				/*if (selectedNames.length > 0) {
	            	counter++;
	            	$( "#progressbar" ).progressbar({
					  value: counter,
					  max: totalLayer
					});
					$( "#dialogPSD .span1" ).html("Proccessed Layers : " + counter);

					Upercent = Math.floor( (counter * 100) / totalLayer );
					$( "#dialogPSD .span3" ).html("Proccess Completed " + Upercent + "%");

	                SaveLayer();
	            }*/
			}
			else
			{
				//console.log('image : ' + counter);
				
				var imageSRC = node.toPng().src;
				var base64ImageContent = imageSRC.replace(/^data:image\/(png|jpg);base64,/, "");
				var blob = b64toBlob(base64ImageContent, 'image/png');
				
				var formData = new FormData();
			    formData.append('upload', blob);
			    //formData.append('upload', 'blob');
			    formData.append('action', 'load_images');
			    formData.append('name', node.name);

				jQuery.when( jQuery.ajax({
					type: "POST",
					processData: false,
    				contentType: false,
					url:ajaxurl, 
					data: formData})).then(function ( response ) {

						formData = null;
		    			//fancyProductDesigner.currentViewInstance.deselectElement();

		    			//pbOptionsV = {top:node.top, left:node.left, autoCenter: false, custom_left: node.left , custom_top: node.top, originX:'left', originY:'top'}
		    			var imageParams = {autoCenter: false, custom_left: node.left , custom_top: node.top, top:node.top, left:node.left, originX:'left', originY:'top', resizable: 1};
		    			//var imageParams = {autoCenter: false, left: node.left , top: node.top, originX:'left', originY:'top'};
		    			//var imageParams = {autoCenter: false, custom_left: node.left , custom_top: node.top, top:node.top, left:node.left, originX:'left', originY:'top', pbOptions:pbOptionsV};
				    	
				    	imageLayerCounter++;
						if(imageLayerCounter==1)
						{
							node.name = 'BG';
							boundingBox = {width: node.width, height: node.height, x: node.left, y: node.top};
							imageParams.boundingBox = boundingBox;
						}

						imageParams.colors = custom_options.customImageParameters.colors;

				    	fancyProductDesigner.currentViewInstance.addElementCustom(
							'image',
							response,
							node.name,
							imageParams
						);
				    	
			            /*if (selectedNames.length > 0) {
			            	counter++;
			            	$( "#progressbar" ).progressbar({
							  value: counter,
							  max: totalLayer
							});
							$( "#dialogPSD .span1" ).html("Proccessed Layers : " + counter);

							Upercent = Math.floor( (counter * 100) / totalLayer );
							$( "#dialogPSD .span3" ).html("Proccess Completed " + Upercent + "%");

			                SaveLayer();
			            }*/
			        });
			}

	    }else{
	    	//console.log(selectedNames.length);
	    	if (selectedNames.length > 0 && closeProcess == false ) {
	    		counter++;
	    		$( "#progressbar" ).progressbar({
				  	value: counter,
					max: totalLayer
				});
				$( "#dialogPSD .span1" ).html("Processed Layers : " + counter);

				Upercent = Math.floor( (counter * 100) / totalLayer );
				$( "#dialogPSD .span3" ).html("Process Completed " + Upercent + "%");

	    		SaveLayer();
	    	}
	    }
	    if(totalLayer <= counter)
	    {
	    	$( "#dialogPSD .span3" ).html("Process Completed 100%");
	    	$('#fpd-add-psd-element').val("");
	    	setTimeout(function() {
				$( "#dialogPSD" ).dialog( "close" );
			}, 15000);
	    }
	}

/*$( "#progressbar" ).on( "progressbarchange", function( event, ui ) {
	if (selectedNames.length > 0) {
        SaveLayer();
    }
} );*/


	SaveLayer();
	
$fpd.on('elementAddCustom', function(event, fabric_object) {
	if( typeof fabric_object.custom_top !== 'undefined' ) {

		if (selectedNames.length > 0 && closeProcess == false ) {
	            	counter++;
	            	$( "#progressbar" ).progressbar({
					  value: counter,
					  max: totalLayer
					});
					$( "#dialogPSD .span1" ).html("Processed Layers : " + counter);

					Upercent = Math.floor( (counter * 100) / totalLayer );
					$( "#dialogPSD .span3" ).html("Process Completed " + Upercent + "%");

	                SaveLayer();
	            }
	}
});


}).then(function () {
  //console.log("Finished in " + ((new Date()) - start) + "ms");
}).catch(function (err) {
  //console.log(err.stack);
});

						
			        	}; //reader onload end
			        	//console.log(input.files[0]);
					    reader.readAsDataURL(input.files[0]);
					//};

$fpd.on('elementAdd', function(event, fabric_object) {
	if( typeof fabric_object.custom_top !== 'undefined' ) {

		/*var nodeX = $('#'+ fabric_object.id +' input[name="element_parameters[]"]')[0];
		Object.defineProperty(nodeX, 'value', {
		    set: function() { throw new Error('button value modified'); }
		});*/

		/*arguments.callee.ccount = ++arguments.callee.ccount || 1
		console.log("Called " + arguments.callee.ccount + " times");*/
		/*fabric_object.setOptions({top:fabric_object.custom_top, left:fabric_object.custom_left, autoCenter: false, originX:'left', originY:'top'});

		//console.log(fabric_object);
		fabric_object.setOriginX('left');
		fabric_object.setOriginY('top');
		fabric_object.setTop(fabric_object.custom_top);
		fabric_object.setLeft(fabric_object.custom_left);*/
		/*fabric_object.center();
		fabric_object.setCoords();*/
		
		/*var parameters = fabric_object.originParams;
		$.extend(parameters, {
				top:fabric_object.custom_top,
				left:fabric_object.custom_left,
				autoCenter: false,
				originX:'left',
				originY:'top',
				autoSelect: false
			}
		);*/
		//fancyProductDesigner.currentViewInstance.setElementParameters(parameters, fabric_object, false);
		//console.log(fancyProductDesigner.currentViewInstance.getBoundingBoxCoords(fabric_object));
		fancyProductDesigner.currentViewInstance.setElementParameters({top:fabric_object.custom_top, left:fabric_object.custom_left, autoCenter: false, originX:'left', originY:'top', autoSelect: false, fontFamily: fabric_object.fontFamily, editable: true, removable: 1, draggable: 1, rotatable: 1, boundingBox: fabric_object.boundingBox}, fabric_object, false);
		
		/*console.log("=====");
		console.log(fabric_object.left);
		fabric_object.left = fabric_object.custom_left;
		fabric_object.top = fabric_object.custom_top;
		console.log(fabric_object.left);
		console.log("-----");*/
		//fabric_object.pbOptions = {top:fabric_object.custom_top, left:fabric_object.custom_left};
		//$fpd.trigger('elementModify', [fabric_object, {top:fabric_object.custom_top,left:fabric_object.custom_left,autoCenter: false,originX:'left',originY:'top',autoSelect: false,tushar: false}]);
		/*fabric_object.pbOptions.left = fabric_object.custom_left;
		fabric_object.pbOptions.top = fabric_object.custom_top;*/
		var json_val = $('#'+ fabric_object.id +' input[name="element_parameters[]"]').val();
		setTimeout(function() {
			var json_array = $.parseJSON(json_val);
			json_array.editable = 1;
			if(fabric_object.type == "image" && fabric_object.title == "BG"){
				/*json_array.bounding_box_x = fabric_object.boundingBox.x;
				json_array.bounding_box_y = fabric_object.boundingBox.y;
				json_array.bounding_box_width = fabric_object.boundingBox.width;
				json_array.bounding_box_height = fabric_object.boundingBox.height;*/
				json_array.bounding_box_control = true;
				json_array.bounding_box_by_other = fabric_object.title;
			}else{
				json_array.removable = 1;
				json_array.draggable = 1;
				json_array.rotatable = 1;
			}
			
			$('#'+ fabric_object.id +' input[name="element_parameters[]"]').val( JSON.stringify(json_array) );
		}, 100);
		/*var json_val = $('#'+ fabric_object.id +' input[name="element_parameters[]"]');
		$('#'+ fabric_object.id +' input[name="element_parameters[]"]').remove();
		setTimeout(function() {
			$('#'+ fabric_object.id +' input[name="element_types[]"]').after(json_val);
		}, 5000);*/
	}
});

/*$fpd.on('elementModify', function(event, fabric_object, parameters) {
	if( typeof fabric_object.custom_top !== 'undefined' ) {
		arguments.callee.ccount = ++arguments.callee.ccount || 1
		console.log("Called " + arguments.callee.ccount + " times");
		
		//console.log(parameters.title + " : " + parameters.left);
		//console.log(parameters);

		//fancyProductDesigner.currentViewInstance.setElementParameters({top:fabric_object.custom_top, left:fabric_object.custom_left, autoCenter: false, originX:'left', originY:'top',autoSelect: false}, fabric_object, false);
	}
});*/

}//Over UploadLayerImages()

    });


	});//radykalConfirm over

});