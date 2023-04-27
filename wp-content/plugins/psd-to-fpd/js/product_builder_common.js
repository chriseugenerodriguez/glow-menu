function b64toBlob(b64Data, contentType, sliceSize) {
  contentType = contentType || '';
  sliceSize = sliceSize || 512;

  var byteCharacters = atob(b64Data);
  var byteArrays = [];

  for (var offset = 0; offset < byteCharacters.length; offset += sliceSize) {
    var slice = byteCharacters.slice(offset, offset + sliceSize);

    var byteNumbers = new Array(slice.length);
    for (var i = 0; i < slice.length; i++) {
      byteNumbers[i] = slice.charCodeAt(i);
    }

    var byteArray = new Uint8Array(byteNumbers);

    byteArrays.push(byteArray);
  }

  var blob = new Blob(byteArrays, {type: contentType});
  return blob;
}
/*function cmykToRGB(c,m,y,k) {

    function padZero(str) {
        return "000000".substr(str.length)+str
    }
    var cyan = (c * 255 * (1-k)) << 16;
    var magenta = (m * 255 * (1-k)) << 8;
    var yellow = (y * 255 * (1-k)) >> 0;
    var black = 255 * (1-k);
    var white = black | black << 8 | black << 16;
    var color = white - (cyan | magenta | yellow );
    return ("#"+padZero(color.toString(16)));
}*/
function rgb2hex(rgb){
 rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);
 return (rgb && rgb.length === 4) ? "#" +
  ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
  ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
}

function CheckParent(np) {
  if( typeof np !== 'undefined' && ( !np.isRoot() && ( typeof np.visible() !== 'undefined' && np.visible() != 'undefined' && np.visible() === true ) ) ) {
    np = np.parent;
    return CheckParent(np);
  }
  return np;
}
jQuery(document).on('ready', function() {

  if( $('#fpd-preview-wrapper').length > 0 ) {
      $selector = $('#fpd-preview-wrapper');
      fancyProductDesigner = $selector.data('instance');
  }
  jQuery('.fpd-container').on('productCreate', function() {

    /*try {
      fancyProductDesigner.currentViewInstance.addElementCustom = function() {};
    } catch(e) {
      $selector = $('#fpd-preview-wrapper');
      fancyProductDesigner = $selector.data('instance');
    }*/

    fancyProductDesigner.currentViewInstance.addElementCustom = function(type, source, title, params) {

      //edited by Tushar
      var instance = fancyProductDesigner.currentViewInstance,
      initialElementsLoaded = false;

      if(type === undefined || source === undefined || title === undefined) {
        return;
      }

      params = typeof params !== 'undefined' ? params : {};
      if(type === 'text') {
        //strip HTML tags
        source = source.replace(/(<([^>]+)>)/ig,"");
        title = title.replace(/(<([^>]+)>)/ig,"");
      }

      if(typeof params != "object") {
        FPDUtil.showModal("The element "+title+" does not have a valid JSON object as parameters! Please check the syntax, maybe you set quotes wrong.");
        return false;
      }

      //check that fill is a string
      if(typeof params.fill !== 'string' && !$.isArray(params.fill)) {
        params.fill = false;
      }

      //replace depraceted keys
      params = FPDUtil.rekeyDeprecatedKeys(params);

      //merge default options
      if(FPDUtil.getType(type) === 'text') {
        params = $.extend({}, instance.options.elementParameters, instance.options.textParameters, params);
      }
      else {
        params = $.extend({}, instance.options.elementParameters, instance.options.imageParameters, params);
      }

      var pushTargetObject = false,
        targetObject = null;

      //store current color and convert colors in string to array
      if(params.colors && typeof params.colors == 'string') {

        //check if string contains hex color values
        if(params.colors.indexOf('#') == 0) {
          //convert string into array
          var colors = params.colors.replace(/\s+/g, '').split(',');
          params.colors = colors;
        }

      }
      
      params._isInitial = !initialElementsLoaded;

      if(FPDUtil.getType(type) === 'text') {
        var defaultTextColor = params.colors[0] ? params.colors[0] : '#000000';
        params.fill = params.fill ? params.fill : defaultTextColor;
      }

      var fabricParams = {
        source: source,
        title: title,
        id: String(new Date().getTime()),
        cornerColor: instance.options.cornerColor ? instance.options.cornerColor : instance.options.selectedColor,
        cornerIconColor: instance.options.cornerIconColor
      };

      params.__editorMode = instance.options.editorMode;
      if(instance.options.editorMode) {
        fabricParams.selectable = fabricParams.evented = fabricParams.draggable = fabricParams.removable = fabricParams.resizable = fabricParams.rotatable = fabricParams.zChangeable = fabricParams.copyable = fabricParams.lockUniScaling = true;

      }
      else {
        $.extend(fabricParams, {
          selectable: false,
          lockRotation: true,
          hasRotatingPoint: false,
          lockScalingX: true,
          lockScalingY: true,
          lockMovementX: true,
          lockMovementY: true,
          hasControls: false,
          evented: false,
        });
      }

      fabricParams = $.extend({}, params, fabricParams);
      
      if(type == 'image' || type == 'path' || type == 'path-group') {

        if(!FPDUtil.isXML(source)) {
          var splitURLParams = source.split('?'); //remove url parameters
          source = fabricParams.source = splitURLParams[0];
        }

        var _fabricImageLoaded = function(fabricImage, params, vectorImage, originParams) {

          originParams = originParams === undefined ? {} : originParams;

          $.extend(params, {
            crossOrigin: 'anonymous',
            originParams: $.extend({}, params, originParams)
          });

          fabricImage.setOptions(params);
          instance.stage.add(fabricImage);

          //Edited by Tushar
          //instance.setElementParameters(params, fabricImage, false);
          
          fabricImage.originParams.angle = fabricImage.angle;
          fabricImage.originParams.z = instance.getZIndex(fabricImage);

          if(instance.options.improvedResizeQuality && !vectorImage) {

            fabricImage.resizeFilters.push(new fabric.Image.filters.Resize({
                resizeType: 'hermite'
            }));

            fabricImage.fire('scaling');

          }

          if(!fabricImage._isInitial) {
            _setUndoRedo({
              element: fabricImage,
              parameters: params,
              interaction: 'add'
            });
          }

          /**
             * Gets fired as soon as an element has beed added.
             *
             * @event FancyProductDesigner#elementAdd
             * @param {Event} event
             * @param {fabric.Object} object - The fabric object.
             */
          $selector.trigger('elementAdd', [fabricImage]);
          $selector.trigger('elementAddCustom', [fabricImage]);

        };


        if(source === undefined || source.length === 0) {
          FPDUtil.log('No image source set for: '+ title);
          return;
        }

        //add SVG from XML document
        if(FPDUtil.isXML(source)) {

          fabric.loadSVGFromString(source, function(objects, options) {
            var svgGroup = fabric.util.groupSVGElements(objects, options);
            _fabricImageLoaded(svgGroup, fabricParams, true);
          });

        }
        //load svg from url
        else if($.inArray('svg', source.split('.')) != -1) {

          fabric.loadSVGFromURL(source, function(objects, options) {

            var svgGroup = fabric.util.groupSVGElements(objects, options);
            if(!params.fill) {
              params.colors = [];
              for(var i=0; i < objects.length; ++i) {
                var color = tinycolor(objects[i].fill);
                params.colors.push(color.toHexString());
              }
              params.svgFill = params.colors;
            }

            _fabricImageLoaded(svgGroup, fabricParams, true, {svgFill: params.svgFill});

          });

        }
        //load png/jpeg from url
        else {

          new fabric.Image.fromURL(source, function(fabricImg) {
            _fabricImageLoaded(fabricImg, fabricParams, false);
          });

        }

      }
      else if(FPDUtil.getType(type) === 'text') {

        source = source.replace(/\\n/g, '\n');
        params.text = params.text ? params.text : source;

        $.extend(fabricParams, {
          spacing: params.curveSpacing,
          radius: params.curveRadius,
          reverse: params.curveReverse,
          originParams: $.extend({}, params)
        });


        //fix for correct boundary when using custom fonts
        var tempFontSize = fabricParams.fontSize;
        fabricParams._tempFontSize = tempFontSize;
        fabricParams.fontSize = tempFontSize + 0.01;

        //make text curved
        var fabricText;
        if(params.curved) {
          fabricText = new fabric.CurvedText(source, fabricParams);
        }
        //make text box
        else if(params.textBox) {
          fabricParams.lockUniScaling = false;
          fabricText = new fabric.Textbox(source, fabricParams);
          fabricText.setControlVisible('bl', true);
        }
        //just interactive text
        else {
          fabricText = new fabric.IText(source, fabricParams);
        }

        if(fabricParams.textPlaceholder || fabricParams.numberPlaceholder) {

          if(fabricParams.textPlaceholder) {
            instance.textPlaceholder = fabricText;
            fabricParams.removable = false;
          }

          if(fabricParams.numberPlaceholder) {
            instance.numberPlaceholder = fabricText;
            fabricParams.removable = false;
          }

        }

        instance.stage.add(fabricText);

        //Edited by Tushar
        //instance.setElementParameters(fabricParams, fabricText, false);

        fabricText.originParams = $.extend({}, fabricText.toJSON(), fabricText.originParams);
        delete fabricText.originParams['clipTo'];
        fabricText.originParams.z = instance.getZIndex(fabricText);

        if(!fabricText._isInitial) {
          _setUndoRedo({
            element: fabricText,
            parameters: fabricParams,
            interaction: 'add'
          });
        }

        /**
           * Gets fired as soon as an element has beed added.
           *
           * @event FancyProductDesigner#elementAdd
           * @param {Event} event
           * @param {fabric.Object} object - The fabric object.
           */
        $selector.trigger('elementAdd', [fabricText]);
        $selector.trigger('elementAddCustom', [fabricText]);

      }
      else {

        FPDUtil.showModal('Sorry. This type of element is not allowed!');

      }

    }; //end element
  });
});