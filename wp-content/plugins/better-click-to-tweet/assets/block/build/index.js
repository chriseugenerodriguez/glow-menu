(()=>{var e={184:(e,t)=>{var o;!function(){"use strict";var r={}.hasOwnProperty;function n(){for(var e=[],t=0;t<arguments.length;t++){var o=arguments[t];if(o){var l=typeof o;if("string"===l||"number"===l)e.push(o);else if(Array.isArray(o)){if(o.length){var a=n.apply(null,o);a&&e.push(a)}}else if("object"===l){if(o.toString!==Object.prototype.toString&&!o.toString.toString().includes("[native code]")){e.push(o.toString());continue}for(var i in o)r.call(o,i)&&o[i]&&e.push(i)}}}return e.join(" ")}e.exports?(n.default=n,e.exports=n):void 0===(o=function(){return n}.apply(t,[]))||(e.exports=o)}()}},t={};function o(r){var n=t[r];if(void 0!==n)return n.exports;var l=t[r]={exports:{}};return e[r](l,l.exports,o),l.exports}o.n=e=>{var t=e&&e.__esModule?()=>e.default:()=>e;return o.d(t,{a:t}),t},o.d=(e,t)=>{for(var r in t)o.o(t,r)&&!o.o(e,r)&&Object.defineProperty(e,r,{enumerable:!0,get:t[r]})},o.o=(e,t)=>Object.prototype.hasOwnProperty.call(e,t),(()=>{"use strict";const e=window.wp.i18n,t=window.wp.blocks,r=window.wp.element;var n=o(184),l=o.n(n);const a=window.wp.blockEditor,i=window.wp.components,c=t=>{let{attributes:o,setAttributes:n}=t;const{username:l,via:c,url:s,urlcustom:u,nofollow:p,prompt:m}=o,d=(e,t)=>{n({[e]:t})};return(0,r.createElement)(a.InspectorControls,null,(0,r.createElement)(i.PanelBody,{title:(0,e.__)("General")},(0,r.createElement)(i.TextControl,{label:(0,e.__)("Twitter Username"),value:l,onChange:e=>d("username",e)}),(0,r.createElement)(i.ToggleControl,{label:(0,e.__)("Include the username in Tweet?"),checked:c,onChange:e=>d("via",e)}),(0,r.createElement)(i.TextControl,{label:(0,e.__)("Prompt"),value:m,onChange:e=>d("prompt",e),help:(0,e.__)("Text for action/prompt link")})),(0,r.createElement)(i.PanelBody,{title:(0,e.__)("URL"),initialOpen:!1},(0,r.createElement)(i.ToggleControl,{label:(0,e.__)("Include URL in tweet?"),checked:s,onChange:e=>d("url",e)}),(0,r.createElement)(i.TextControl,{label:(0,e.__)("Custom URL"),value:u,onChange:e=>d("urlcustom",e),help:(0,e.__)("Custom URL to use instead of post")}),(0,r.createElement)(i.ToggleControl,{label:(0,e.__)("Nofollow"),checked:p,onChange:e=>d("nofollow",e),help:(0,e.__)("Make links nofollow")})))};(0,t.registerBlockType)("bctt/clicktotweet",{title:(0,e.__)("Better Click to Tweet"),description:(0,e.__)("Add text for your readers to tweet, calling them to action on your behalf."),category:"widgets",icon:"twitter",keywords:[(0,e.__)("Twitter"),(0,e.__)("Tweet")],supports:{align:!1,alignWide:!1},edit:t=>{const{attributes:o,setAttributes:n,className:i}=t,{tweet:s,prompt:u}=o,p=wp.data.select("core/editor").getEditedPostAttribute("title");let m;return s||n({tweet:p}),(0,r.createElement)(r.Fragment,null,(0,r.createElement)(c,t),(0,r.createElement)("span",{className:l()(i,"bctt-click-to-tweet")},(0,r.createElement)("span",{className:"bctt-ctt-text"},(0,r.createElement)(a.RichText,{format:"string",allowedFormats:[],tagName:"div",placeholder:(0,e.__)("Enter text for readers to Tweet"),onChange:e=>{clearTimeout(m),e?n({tweet:e}):m=setTimeout((()=>{n({tweet:p})}),3e3)},value:s})),(0,r.createElement)("a",{href:"#",onClick:()=>!1,className:"bctt-ctt-btn"},u)))},save:()=>null})})()})();