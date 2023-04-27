/*
 * @name glowmenu
 * @version 1.0.19
 * @desc Give your menu a glow.
 * @author Chris Rodriguez
 * @license MIT
 */
var glowmenu=glowmenu||{init:function(){try{glowmenu.support()}catch(n){console.debug(n)}},resize:function(){try{}catch(n){console.debug(n)}},support:function(){}},$=jQuery.noConflict();$(function(){glowmenu.init()}),$(window).resize(function(){glowmenu.resize()}),$(window).load(function(){});