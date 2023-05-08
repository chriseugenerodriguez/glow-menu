(function( $ ) {
  'use strict';
  /**
   * This enables you to define handlers, for when the DOM is ready:
   * $(function() { });
   * When the window is loaded:
   * $( window ).load(function() { }); 
   */  
})( jQuery );
class TVC_GTM_Enhanced {
  constructor(options = {}){
    this.options = {
      tracking_option: 'UA'
    };
    if(options){
      Object.assign(this.options, options);
    } 
    //console.log(this.options);
    //this.addEventBindings();  
  }
  singleProductaddToCartEventBindings(variations_data, product_detail_addtocart_selector){
    var single_btn = "";
    if(product_detail_addtocart_selector != ""){
      single_btn = document.querySelectorAll("button[class*='btn-buy-shop'],button[class*='single_add_to_cart_button'], button[class*='add_to_cart']"+product_detail_addtocart_selector);
    }else{
      single_btn = document.querySelectorAll("button[class*='btn-buy-shop'],button[class*='single_add_to_cart_button'], button[class*='add_to_cart']");
    }
    if(single_btn.length > 0){
      single_btn.forEach((aCartBut) => {
        aCartBut.addEventListener("click", () => this.add_to_cart_click(variations_data, "Product Pages"));
      });
    }    
  }
  ListProductaddToCartEventBindings(){
    var elements = "";    
    elements = document.querySelectorAll("a[href*=add-to-cart]");
    if(elements.length > 0){
      for (var i = 0; i < elements.length; i++) {
        if(elements[i]){
          elements[i].addEventListener("click", () => this.list_add_to_cart_click());
        }
      }
    }    
  }
  ListProductSelectItemEventBindings(){
    var elements = "";    
    elements = document.querySelectorAll("li.product a:not([href*=add-to-cart],.product_type_variable, .product_type_grouped");
    if(elements.length > 0){
      for (var i = 0; i < elements.length; i++) {
        if(elements[i]){
          elements[i].addEventListener("click", () => this.list_select_item_click());
        }
      }
    }    
  }
  RemoveItemCartEventBindings(){
    var elements = "";    
    elements = document.querySelectorAll("a[href*=\"?remove_item\"]");
    if(elements.length > 0){
      for(var i = 0; i < elements.length; i++){
        if(elements[i]){
          elements[i].addEventListener("click", () => this.remove_item_click());
        }
      }
    }
  }
  /*
   * check remarketing option 
   */
  is_add_remarketing_tags(){
    if(this.options.is_admin == false && this.options.ads_tracking_id != "" && ( this.options.remarketing_tags == 1 || this.options.dynamic_remarketing_tags == 1)){
      return true;
    }else{
      return false;
    }
  }
  
  get_variation_data_by_id(variations_data, variation_id){
    //console.log(variations_data.available_variations)
    var r_val = "";
    if(variations_data.available_variations.length > 0 ){
      variations_data.available_variations.forEach((element, index) => { 
        if(element.variation_id == variation_id){
          r_val = element;
        }
      });
      return r_val;
      //console.log(variations_data.available_variations)
    }
  }
  get_variation_attribute_name(p_attributes){
    //console.log(p_attributes);
    var p_v_title = "";
    if(Object.keys(p_attributes).length >0 ){
      for(var index in p_attributes) {
        p_v_title+=(p_v_title=="")?p_attributes[index]:' | '+p_attributes[index];        
      }
      return p_v_title;
    }
  }
  get_event_data_layer(event_name){
    if(event_name != ""){
      if(Object.keys(dataLayer).length >0 ){
        for(var dataLayer_item in dataLayer){
          event = dataLayer[dataLayer_item].event;
          if(event_name == event){
            return dataLayer[dataLayer_item];
          }
        }
      }
    }
  }
  get_product_from_product_list(product_id){
    if(product_id != ""){
      if(Object.keys(conProductList).length >0 ){
        for(var dataLayer_item in conProductList[0]){
          if(conProductList[0][dataLayer_item].hasOwnProperty('id')){
            var id = conProductList[0][dataLayer_item].id;            
            if(product_id == id){
              return conProductList[0][dataLayer_item];
            }
          }
        }
      }
    }
  }
  get_product_from_product_list_by_url(prod_obj, key, product_url){
    if(product_url != ""){
      if(Object.keys(prod_obj).length >0 ){
        for(var dataLayer_item in prod_obj[0]){
          if(prod_obj[0][dataLayer_item].hasOwnProperty(key)){
            var map_val = prod_obj[0][dataLayer_item][key]; 
            if(product_url == map_val){
              return prod_obj[0][dataLayer_item];
            }
          }
        }
      }
    }
  }
  list_select_item_click(){
    var this_var =  event.currentTarget;
    var href = this_var.getAttribute( 'href' );
    var item = this.get_product_from_product_list_by_url(conProductList, 'productlink', href);
    var add_to_cart_datalayer = {
      event: "select_item",
      ecommerce: {
        items: [
        {
          affiliation: this.options.affiliation,
          item_id: item.id,
          item_name: item.name,
          currency: this.options.currency,
          item_category: item.category,
          price: item.price,
          quantity: 1
        }]
      }
    };
    dataLayer.push(add_to_cart_datalayer);
  }
  remove_item_click(this_var){
    var href = this_var.getAttribute( 'href' );
    if(href){
      var item = this.get_product_from_product_list_by_url(conCarttList, 'remove_cart_link', href);
      var add_to_cart_datalayer = {
        event: "remove_from_cart",
        ecommerce: {
          items: [
          {
            affiliation:this.options.affiliation,
            item_id:item.id,
            item_name:item.name,
            currency:this.options.currency,
            item_category:item.category,
            price:item.price,
            quantity:item.quantity

          }]
        }
      };
      dataLayer.push(add_to_cart_datalayer);
    }
    
  }
  list_add_to_cart_click(){
    var this_var =  event.currentTarget;
    var href = this_var.getAttribute( 'href' );
    var product_id = this.getParameterByName("add-to-cart",href);
    var item = this.get_product_from_product_list(product_id);
    var add_to_cart_datalayer = {
      event: "add_to_cart",
      ecommerce: {
        items: [
        {
          affiliation:this.options.affiliation,
          item_id:item.id,
          item_name:item.name,
          currency:this.options.currency,
          item_category:item.category,
          price:item.price,
          quantity:1
        }]
      }
    };
    
    if(this.options.fb_pixel_id != undefined && this.options.fb_pixel_id != null && this.options.fb_pixel_id != ""){
      add_to_cart_datalayer.fb_event_id = this.options.fb_event_id+'p'+item.id;    
    }

    dataLayer.push(add_to_cart_datalayer);
    
    if(this.options.fb_conversion_api_token != undefined && this.options.fb_conversion_api_token != null && this.options.fb_conversion_api_token != ""){
      //facebook api
      var data = {
        action: "facebook_converstion_api",
        fb_event:"AddToCart",
        event_id:this.options.fb_event_id+'p'+item.id,
        prodct_data:[item]
      };
      jQuery.ajax({
        type: "POST",
        dataType: "json",
        url:  this.options.tvc_ajax_url,
        data: data,
        beforeSend: function () {        
        },
        success: function (response) {
          console.log(response);
        }
      });
    }
  }
  getParameterByName(name, url = window.location.href){
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, ' '));
  }
  /*
   * below code run while add to cart on product page. 
   * ( Event=>  add_to_cart)
   */
  add_to_cart_click( variations_data, page_type ="Product Pages" ){
    var this_var =  event.currentTarget;
    var item_dataLayer = this.get_event_data_layer("view_item");    
    if(this.options.is_admin == true){
      return;
    }    
    if(Object.keys(item_dataLayer.ecommerce.items[0]).length > 0){
      var item = item_dataLayer.ecommerce.items[0];
      var variation_attribute_name= "";
      var vari_data ="";
      var variation_id = "";
      var variation_id_obj = document.getElementsByClassName("variation_id");
      if (variation_id_obj.length > 0) {
        variation_id = document.getElementsByClassName("variation_id")[0].value;
      }
      var varPrice = item.price;
      if(variation_id != ""){
        vari_data = this.get_variation_data_by_id(variations_data, variation_id);
        var p_attributes = vari_data.attributes;
        if( Object.keys(p_attributes).length > 0){
          variation_attribute_name = this.get_variation_attribute_name(p_attributes);
        }
        if(vari_data.display_price){
          varPrice = vari_data.display_price;
        }else if(vari_data.display_regular_price){
          varPrice = vari_data.display_regular_price;
        }      
      }
      
      var add_to_cart_datalayer = {
        event: "add_to_cart",
        ecommerce: {
          items: [
          {
            affiliation:item.affiliation,
            item_id:item.item_id,
            item_name:item.item_name,
            currency:item.currency,
            item_category:item.item_category,
            price:varPrice,
            quantity:jQuery(this_var).parent().find("input[name=quantity]").val(),
            item_variant:variation_attribute_name

          }]
        }
      };
      if(this.options.fb_pixel_id != undefined && this.options.fb_pixel_id != null && this.options.fb_pixel_id != ""){
        add_to_cart_datalayer.fb_event_id = this.options.fb_event_id+'p'+item.id;    
      }
      dataLayer.push(add_to_cart_datalayer);

      //facebook api
      if(this.options.fb_conversion_api_token != undefined && this.options.fb_conversion_api_token != null && this.options.fb_conversion_api_token != ""){
        var quantity = jQuery(this_var).parent().find("input[name=quantity]").val();
        var data = {
          action: "facebook_converstion_api",
          fb_event:"AddToCart",
          event_id:this.options.fb_event_id+'p'+item.item_id,
          prodct_data:[{id:item.item_id,quantity:quantity, price:varPrice}]
        };
        jQuery.ajax({
          type: "POST",
          dataType: "json",
          url:  this.options.tvc_ajax_url,
          data: data,
          beforeSend: function () {        
          },
          success: function (response) {
            console.log(response);
          }
        });
      }  
    }    
  }
  /*
  *
  */
  checkout_step_2_tracking(){
    var item_dataLayer = this.get_event_data_layer("begin_checkout");
    if(this.options.is_admin == true){
      return;
    }    
    if(Object.keys(item_dataLayer.ecommerce.items[0]).length > 0){    
      var checkout_step_2_datalayer = {
        event: "add_shipping_info",
        ecommerce: {
          currency: this.options.currency,
          value: item_dataLayer.ecommerce.value,
          items: item_dataLayer.ecommerce.items
        }
      };
      dataLayer.push(checkout_step_2_datalayer); 
    }
    console.log(dataLayer);
  }
  checkout_step_3_tracking(){
    var item_dataLayer = this.get_event_data_layer("begin_checkout");
    if(this.options.is_admin == true){
      return;
    }    
    if(Object.keys(item_dataLayer.ecommerce.items[0]).length > 0){      
      var checkout_step_3_datalayer = {
        event: "add_payment_info",
        ecommerce: {
          currency: this.options.currency,
          value: item_dataLayer.ecommerce.value,
          items: item_dataLayer.ecommerce.items
        }
      };
      dataLayer.push(checkout_step_3_datalayer); 
    }
  }
  
  getCurrentTime(){
    if (!Date.now) {
       return new Date().getTime(); 
    }else{
      //Math.floor(Date.now() / 1000)
      return Date.now();
    }
  }
  getClientId() {
    let client_id = this.getCookie("_ga");
    if(client_id != null && client_id != ""){
     return client_id;
    }else{
      return ;
    }     
  }
  setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
  }
  getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(";");
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==" ") c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
  }
  eraseCookie(name) {   
    document.cookie = name +"=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
  }
}