var glowmenu = glowmenu || {
    init: function() {
        try {
            glowmenu.support();
        } catch (e) {
            console.debug(e);
        }
    },
    resize: function() {
        try {} catch (e) {
            console.debug(e);
        }
    },
    support: function() {

    },
};

var $ = jQuery.noConflict();
$(function() {   
    glowmenu.init();
});
$(window).resize(function() {   
    glowmenu.resize();
});
$(window).load(function() {});
