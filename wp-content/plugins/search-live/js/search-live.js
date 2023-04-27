/*!
 * search-live.js
 *
 * Copyright (c) "kento" Karim Rahimpur www.itthinx.com
 *
 * This code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 *
 * This code is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This header and all notices must be kept intact.
 *
 * @author itthinx
 * @package search-live
 * @since 1.0.0
 */

/**
 * Parameter object.
 * doPost is used to flag if the query can be posted. It is false
 * if we are going somewhere else using document.location.
 */
var ixsl = {
	doPost : true, 
	blinkerTimeouts : [],
	blinkerTimeout : 5000
};

(function($) {

/**
 * Inhibit form submission when enter is pressed on the input field.
 */
ixsl.inhibitEnter = function( fieldId ) {
	$("#"+fieldId).keydown(function(e){
		if ( e.keyCode == 13 ) { // enter
			e.preventDefault();
			return false;
		}
	});
};

/**
 * Show/hide search results when the input field gains/loses focus.
 */
ixsl.dynamicFocus = function ( fieldId, resultsId ) {
	var $field = $('#'+fieldId),
		$results = $('#'+resultsId);
	$field.focusout(function(e){
		// Give any child element the chance to gain focus
		// before we decide to hide the results.
		var $elem = $(this);
		setTimeout(
			function() {
				var hasFocus = ($elem.find(':focus').length > 0);
				if ( !hasFocus ) {
					$results.hide();
				}
			},
			162
		);
	});
	$field.focusin(function(e){
		// When focus is gained, show the results and
		// empty them if the search field is empty.
		var $elem = $(this),
			$searchField = $elem.find('input.search-live-field');
		if ( $searchField.length > 0 ) {
			if ( $($searchField[0]).val().length == 0 ) {
				// Empty the results for an empty query [1]
				$results.html('');
			}
		}
		// Focus has been gained, so show the results whether empty or not.
		$results.show();
	});
};

/**
 * Cursor key result navigation and Escape key handling.
 */
ixsl.navigate = function( fieldId, resultsId ) {
	$("#"+fieldId).keydown(function(e){
		var i = 0, navigate = false, escape = false;
		switch ( e.keyCode ) {
			case 37 : // left
				break;
			case 39 : // right
				break;
			case 38 : // up
				i = -1;
				break;
			case 40 : // down
				i = 1;
				break;
			case 13 : // enter
				navigate = true;
				break;
			case 27 : // esc
				escape = true;
				break;
		}
		if ( i != 0 ) {
			var entries = $("#"+resultsId).find('.entry'),
				active = $("#"+resultsId+" .entry.active").index();
			if ( entries.length > 0 ) {
				if ( active >= 0 ) {
					$(entries[active]).removeClass("active");
				}
				active += i;
				if ( active < 0 ) {
					active = entries.length - 1;
				} else if ( active >= entries.length ) {
					active = 0;
				}
				$(entries[active]).addClass("active");
			}
			e.preventDefault();
			return false;
		}
		if ( navigate ) {
			var entries = $("#"+resultsId).find('.entry'),
				active = $("#"+resultsId+" .entry.active").index();
			if ( ( active >= 0 ) && ( active < entries.length ) ) {
				var link = $(entries[active]).find('a').get(0);
				if ( typeof link !== 'undefined' ) {
					var url = $(link).attr('href');
					if ( typeof url !== 'undefined' ) {
						e.preventDefault();
						ixsl.doPost = false; // disable posting the query
						document.location = url;
						return false;
					}
				}
			}
		}
		if ( escape ) {
			var entries = $("#"+resultsId).find('.entry'),
			active = $("#"+resultsId+" .entry.active").index();
			if ( entries.length > 0 ) {
				if ( active >= 0 ) {
					$(entries[active]).removeClass("active");
				}
			}
			e.preventDefault();
			return false;
		}
	});
};

/**
 * Adjusts the width of the results to the width of the search field.
 */
ixsl.autoAdjust = function( fieldId, resultsId ) {
	var $field = $('#'+fieldId),
		$results = $('#'+resultsId);
	$results.on('adjustWidth',function(e){
		e.stopPropagation();
		// Field width minus own border.
		var w = $field.outerWidth() - ( $results.outerWidth() - $results.innerWidth() );
		$results.width(w);
	});
	$(window).on('resize',function(e){
		$results.trigger('adjustWidth');
	});
};

/**
 * POST the search query and display the results.
 * 
 * The args parameter object allows to indicate:
 * - blinkerTimeout : to modify the default blinker timeout in milliseconds or 0 to disable it
 * - lang : language code
 * - no_results : alternative text to show when no results are obtained
 * - show_description : whether to render descriptions
 * - thumbnails : whether to render thumbnails
 * 
 * @param fieldId
 * @param containerId
 * @param resultsId
 * @param url
 * @param query
 * @param object args
 */
ixsl.searchLive = function( fieldId, containerId, resultsId, url, query, args ) {

	if (!ixsl.doPost) {
		return;
	}

	if ( typeof args === "undefined" ) {
		args = {};
	}

	var $results = $( "#"+resultsId ),
		$blinker = $( "#"+fieldId ),
		blinkerTimeout = ixsl.blinkerTimeout;

	if ( typeof args.blinkerTimeout !== "undefined" ) {
		blinkerTimeout = args.blinkerTimeout;
	}
	query = $.trim(query);
	if ( query != "" ) {
		$blinker.addClass('blinker');
		if ( blinkerTimeout > 0 ) {
			ixsl.blinkerTimeouts["#"+fieldId] = setTimeout(function(){$blinker.removeClass('blinker');}, blinkerTimeout);
		}
		var params = {
			"action" : "search_live",
			"search-live": 1,
			"search-live-query": query
		};
		if ( typeof args.lang !== "undefined" ) {
			params.lang = args.lang;
		}
		$.post(
			url,
			params,
			function ( data ) {
				var results = '';
				if ( ( data !== null ) && ( data.length > 0 ) ) {
					var result_type = null,
						current_type = null,
						thumbnails = true,
						show_description = false;
					if ( typeof args.thumbnails !== "undefined" ) {
						thumbnails = args.thumbnails;
					}
					if ( typeof args.show_description !== "undefined" ) {
						show_description = args.show_description;
					}
					// Search results table start.
					results += '<table class="search-results">';
					for( var key in data ) {
						var first = '';
						if ( current_type != data[key].type ) {
							current_type = data[key].type;
							first = 'first';
						}
						if ( result_type != data[key].result_type ) {
							result_type = data[key].result_type;
						}

						results += '<tr class="entry ' + data[key].result_type + ' ' + data[key].type + ' ' + first + '">';
						if ( thumbnails ) {
							results += '<td class="result-image">';
							results += '<a href="' + data[key].url + '" title="' + data[key].title + '">';
							if ( typeof data[key].thumbnail !== "undefined" ) {
								var width = '', height = '', alt='';
								if ( typeof data[key].thumbnail_alt !== "undefined" ) {
									alt = ' alt="' + data[key].thumbnail_alt + '" ';
								}
								if ( typeof data[key].thumbnail_width !== "undefined" ) {
									width = ' width="' + data[key].thumbnail_width + '" ';
								}
								if ( typeof data[key].thumbnail_height !== "undefined" ) {
									height = ' height="' + data[key].thumbnail_height + '" ';
								}
								results += '<img class="thumbnail" src="' + data[key].thumbnail + '" ' + alt + width + height + '/>';
							}
							results += '</a>';
							results += '</td>';
						}

						results += '<td class="result-info">';
						results += '<a href="' + data[key].url + '" title="' + data[key].title + '">';
						results += '<span class="title">' + data[key].title + '</span>';
						if ( show_description ) {
							if ( typeof data[key].description !== "undefined" ) {
								results += '<span class="description">' + data[key].description + '</span>';
							}
						}
						results += '</a>';
						results += '</td>';
						results += '</tr>';
					}
					results += '</table>';
					// Search results table end.
				} else {
					if ( typeof args.no_results !== "undefined" ) {
						if ( args.no_results.length > 0 ) {
							results += '<div class="no-results">';
							results += args.no_results;
							results += '</div>';
						}
					}
				}
				$results.show().html( results );
				ixsl.clickable( resultsId );
				$results.trigger('adjustWidth');
				$blinker.removeClass('blinker');
				if ( blinkerTimeout > 0 ) {
					clearTimeout(ixsl.blinkerTimeouts["#"+fieldId]);
				}
			},
			"json"
		);
	} else {
		// Hide and empty the results for an empty query.
		// If we don't get here (minimum characters not input), [1] will empty it.
		$results.hide().html('');
	}
};

/**
 * Clickable table rows.
 */
ixsl.clickable = function( resultsId ) {
	$('#' + resultsId + ' table.search-results tr').click(function(){
		var url = $(this).find('a').attr('href');
		if ( url ) {
			window.location = url;
		}
	});
	$('#' + resultsId + ' table.search-results tr').css('cursor','pointer');
};

})(jQuery);
