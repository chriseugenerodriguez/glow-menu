$=jQuery;
jQuery(function() {
var conversion_funnel_chart = "";
var conversion_bar_chart = "";
var checkout_funnel_chart = "";
var checkout_bar_chart = "";
});
var  chart_ids = {};
var tvc_helper = {
	tvc_alert:function(msg_type=null, msg_subject=null, msg, auto_close=false, tvc_time=7000){
		document.getElementById('tvc_msg_title').innerHTML ="";
		document.getElementById('tvc_msg_content').innerHTML ="";
		document.getElementById('tvc_msg_icon').innerHTML ="";

		if(msg != ""){
			let tvc_popup_box = document.getElementById('tvc_popup_box');
			tvc_popup_box.classList.remove("tvc_popup_box_close");
			tvc_popup_box.classList.add("tvc_popup_box");

	  	//tvc_popup_box.style.display = "block";
	  	document.getElementById('tvc_msg_title').innerHTML =this.tvc_subject_title(msg_type, msg_subject);
			document.getElementById('tvc_msg_content').innerHTML =msg;
			if(msg_type=="success"){
				document.getElementById('tvc_msg_icon').innerHTML ='<i class="fas fa-check-circle fa-3x tvc-success"></i>';
			}else{
				document.getElementById('tvc_msg_icon').innerHTML ='<i class="fas fa-exclamation-circle fa-3x"></i>';
			}
			if(auto_close == true){
				setTimeout(function(){  //tvc_popup_box.style.display = "none";				
					tvc_popup_box.classList.add("tvc_popup_box_close");
					tvc_popup_box.classList.remove("tvc_popup_box");				
				}
				, tvc_time);
			}
		}
	},
	tvc_subject_title:function(msg_type=null, msg_subject=null){
		if(msg_subject == null || msg_subject ==""){
			if(msg_type=="success" ){
				return '<span class="tvc-success">Success!!</span>';
			}else{
				return '<span class="tvc-error">Oops!</span>';
			}
		}else{
			if(msg_type=="success" ){
				return '<span class="tvc-success">'+msg_subject+'</span>';
			}else{
				return '<span>'+msg_subject+'</span>';
			}
		}		
	},
	tvc_close_msg:function(){
		let tvc_popup_box = document.getElementById('tvc_popup_box');
		tvc_popup_box.classList.add("tvc_popup_box_close");
		tvc_popup_box.classList.remove("tvc_popup_box");
		//tvc_popup_box.style.display = "none";
	},
	loaderSection:function(isShow) {
	  if (isShow){
	    jQuery('#feed-spinner').show();
	  }else{
	    jQuery('#feed-spinner').hide();
	  }
	},
	get_google_analytics_reports:function(post_data){
		//console.log(post_data);
		this.cleare_dashboard();
		this.add_loader_for_analytics_reports();
		this.google_analytics_reports_call_api(post_data);		
	},
	google_ads_reports_call_api:function(post_data){
		// Shopping and Google Ads Performance
		post_data['action']='get_google_ads_reports_chart';
		var v_this = this;
		jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: post_data,
      success: function (response) {
      	console.log(response);
      	if(response.error == false){
      		if(Object.keys(response.data).length > 0){
      			v_this.set_google_ads_reports_chart_value(response.data, post_data);
      		}
      	}else{
      		if(response.status == "423" || response.status == "400"){
      			v_this.tvc_alert("error","", "If Google Ads Performance Data is not generated please make sure your Google Ads account should link with our MCC account");
      		}else{
        		v_this.tvc_alert("error", "", response?.errors);
        	}
      	}
        v_this.remove_loader_for_analytics_reports();
      }
    });
		//Compaign Performance
    post_data['action']='get_google_ads_campaign_performance';
		var v_this = this;
		jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: post_data,
      success: function (response) {
      	console.log(response);
      	if(response.error == false){
      		if(Object.keys(response.data).length > 0){
      			v_this.set_google_ads_reports_campaign_performance_value(response.data, post_data);
      		}
      	}else{
      		if(response.errors != ""){
      			//v_this.tvc_alert("error","",response.errors);
      		}
      	}
        //v_this.remove_loader_for_analytics_reports();
      }
    });
	},
	set_google_ads_reports_campaign_performance_value:function(data, post_data){
		//if(data.hasOwnProperty('data')){
			//var p_p_r = data.product_performance_report.products;
			//console.log(p_p_r);
			var table_row = '';
			var table_row_last = '';
			var product_revenue_per = 0;
			var status = "";
			var v_this = this;
			if(data != undefined && Object.keys(data).length > 0){
				var i=0;
				jQuery.each(data, function (propKey, propValue) {
					if(i<5){
						//table_row = ''; table_row_last = ''; 
						status = (propValue['active'] == 1)?'active':'deactivate';
						if(status == 'active'){
							table_row += '<tr><td class="prdnm-cell">'+propValue['compaignName']+'</td>';
							table_row += '<td>'+v_this.numberWithCommas(propValue['dailyBudget'].toFixed(2))+'</td>';
							table_row += '<td>'+status+'</td>';
							table_row += '<td>'+propValue['clicks']+'</td>';
							table_row += '<td>'+v_this.numberWithCommas(propValue['cost'].toFixed(2))+'</td>';
							table_row += '<td>'+v_this.numberWithCommas(propValue['conversions'].toFixed(2))+'</td>';
							table_row += '<td>'+v_this.numberWithCommas(propValue['sales'].toFixed(2))+'</td></tr>';
						}else{
							table_row_last += '<tr><td class="prdnm-cell">'+propValue['compaignName']+'</td>';
							table_row_last += '<td>'+v_this.numberWithCommas(propValue['dailyBudget'].toFixed(2))+'</td>';
							table_row_last += '<td>'+status+'</td>';
							table_row_last += '<td>'+propValue['clicks']+'</td>';
							table_row_last += '<td>'+v_this.numberWithCommas(propValue['cost'].toFixed(2))+'</td>';
							table_row_last += '<td>'+v_this.numberWithCommas(propValue['conversions'].toFixed(2))+'</td>';
							table_row_last += '<td>'+v_this.numberWithCommas(propValue['sales'].toFixed(2))+'</td></tr>';
						}						
						i = i+1;
					}
				});
				jQuery("#campaign_performance_report table tbody").append(table_row);
				jQuery("#campaign_performance_report table tbody").append(table_row_last);
			}else{
				jQuery("#campaign_performance_report table tbody").append("<tr><td colspan='7'>Data not available</td></tr>");
			}
		//}
	},
	set_google_ads_reports_chart_value:function(data, post_data){
		var v_this = this;
		var s_1_div_id ={
			'daily_clicks':{
				'id':'dailyClicks',
				'type':'number',
				'is_chart':true,
				'chart_type':'line',
				'chart_value_field_id':'clicks',
				'chart_title':'Clicks',
				'chart_id':'dailyClicks'
			},'daily_cost':{
				'id':'dailyCost',
				'type':'currency',
				'is_chart':true,
				'chart_type':'line',
				'chart_value_field_id':'costs',
				'chart_title':'Cost',
				'chart_id':'dailyCost'
			},'daily_conversions':{
				'id':'dailyConversions',
				'type':'number',
				'is_chart':true,
				'chart_type':'line',
				'chart_value_field_id':'conversions',
				'chart_title':'Conversions',
				'chart_id':'dailyConversions'
			},'daily_sales':{
				'id':'dailySales',
				'type':'number',
				'is_chart':true,
				'chart_type':'line',
				'chart_value_field_id':'sales',
				'chart_title':'Sales',
				'chart_id':'dailySales'
			}
		};
		if(Object.keys(s_1_div_id).length > 0){
			var labels_key = "";
			if(data.hasOwnProperty('graph_type')){
				labels_key = data['graph_type'];
			} 
			jQuery.each(s_1_div_id, function (propKey, propValue) {
				if(data.hasOwnProperty(propValue['id'])){
					if(propValue['chart_id']!= undefined && propValue['is_chart'] != undefined && propValue['chart_type'] != undefined){
						
						var chart_id = propValue['chart_id'];
						var field_id = propValue['chart_value_field_id'];
						var chart_title = propValue['chart_title'];
						//console.log(propValue['chart_type']+"call"+chart_id);
						if(propValue['chart_type'] == 'line'){
							v_this.drow_google_ads_chart(chart_id, data[propValue['id']], field_id, chart_title, labels_key);
						}
					}
				}
			});
		}

	},
	drow_google_ads_chart:function(chart_id, alldata, field_key,  d_label, labels_key){
		var chart_data = alldata;
		var ctx = document.getElementById(chart_id).getContext('2d');
		var gradientFill = ctx.createLinearGradient(0, 0, 0, 500);
		if(chart_id == 'dailyClicks'){
			gradientFill.addColorStop(0.4, 'rgba(153, 170, 255, 0.9)');
    	gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.7)');
    }else if(chart_id =='dailyCost'){
    	gradientFill.addColorStop(0.4, 'rgba(110, 245, 197, 0.9)');
      gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.7)');
    }else if(chart_id =='dailyConversions'){
    	gradientFill.addColorStop(0.4, 'rgba(255, 229, 139, 0.9)');
      gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.7)');
    }else if(chart_id =='dailySales'){
    	gradientFill.addColorStop(0.4, 'rgba(107, 232, 255, 0.9)');
      gradientFill.addColorStop(0.85, 'rgba(255, 255, 255, 0.75)');
    }
		const labels = [];
		const chart_val = [];
		var t_labels = "";
		
		//var d_backgroundColors = ['#FF6384','#22CFCF','#0ea50b','#FF9F40','#FFCD56']
		jQuery.each(chart_data, function (key, value) {
			if(labels_key != "" && value.hasOwnProperty(labels_key)){
				t_labels =value[labels_key];
			}else{
				t_labels = value['date'];
			}		
		  labels.push(t_labels.toString());
		  //chart_val.push(value[field_key]);
		  chart_val.push(((value[field_key]!=null)?value[field_key]:0));
		});
		//console.log(alldata);
		//console.log(field_key);
		//console.log(chart_val);
		const data = {
		  labels: labels,
		  datasets: [
		    {
		      data: chart_val,
		      borderColor: '#002BFC',
          pointBorderColor: '#002BFC',
          pointBackgroundColor: '#fff',
		      pointBorderWidth: 1,
          pointRadius: 2,
          fill: true,
          backgroundColor: gradientFill,
          borderWidth: 1
		    }
		  ]
		};
		const config = {
		  type: 'line',
		  data: data,
		  options: {
		  	animation: {
            easing: "easeInOutBack"
        },
        plugins:{
            legend:false
        },
		    responsive: true,
		    scales: {
          y:{
            fontColor: "#ffffff",
            fontStyle: "normal",
            beginAtZero: true,
            maxTicksLimit: 5,
            padding: 30,
            grid:{
              borderWidth:0,
            },
            ticks: {
              stepSize: 1000,
              callback: function(value) {
                 var ranges = [
                    { divider: 1e6, suffix: 'M' },
                    { divider: 1e3, suffix: 'k' }
                 ];
                 function formatNumber(n) {
                    for (var i = 0; i < ranges.length; i++) {
                       if (n >= ranges[i].divider) {
                          return (n / ranges[i].divider).toString() + ranges[i].suffix;
                       }
                    }
                    return n;
                 }
                 return '' + formatNumber(value);
              }
           }
	        },
	        x:{
	            padding: 10,
	            fontColor: "#ffffff",
	            fontStyle: "normal",
	            grid: {
	              display:false
	            }
	        }        
    		}
		  }
		};
		chart_ids[chart_id] = new Chart(ctx,config);
	},
	google_analytics_reports_call_api:function(post_data){
		var v_this = this;
		var g_mail = post_data.g_mail;
		jQuery.ajax({
      type: "POST",
      dataType: "json",
      url: tvc_ajax_url,
      data: post_data,
      success: function (response) {
      	console.log(response);
      	if(response.error == false){
      		if(Object.keys(response.data).length > 0){
      			v_this.set_google_analytics_reports_value(response.data, post_data);
      		}
      	}else if(response.error == true && response.errors != undefined){
	        const errors = response.errors;
	        if(response.errors == "access_token_error"){
	        	if(g_mail != ""){	
	        		v_this.tvc_alert("error","","It seems the token to access your Google Analytics account is expired. Sign in with "+g_mail+" again to reactivate the token. <span class='google_connect_url'>Click here..</span>");
	        	}else{
	        		v_this.tvc_alert("error","","It seems the token to access your Google Analytics account is expired. Sign in with the connected email again to reactivate the token. <span class='google_connect_url'>Click here..</span>");
	        	}
	        }else{
	        	v_this.tvc_alert("error","Error",errors);
	        }	        
	      }else{
	      		v_this.tvc_alert("error","Error","Analytics report data not fetched");
	      }
        v_this.remove_loader_for_analytics_reports();
      }
    });
	},
	set_google_analytics_reports_value:function(data, post_data){
		var v_this = this;
		data = JSON.parse(data);
		//console.log(data);
		var ga_swatch = "ga3";
		if(post_data.hasOwnProperty('ga_swatch')){
		 	ga_swatch = post_data.ga_swatch;
		}
		var basic_data = data.dashboard_data_point;
		var currency_code = post_data.ga_currency;
		var plugin_url = post_data.plugin_url;
		var s_1_div_id = {};
		if(ga_swatch == "ga3"){
			s_1_div_id = {
				'conversion_rate':{
					'id':'transactionsPerSession',
					'type':'rate'
				},'revenue':{
					'id':'transactionRevenue',
					'type':'currency'
				},'total_transactions':{
					'id':'transactions',
					'type':'number'
				},'avg_order_value':{
					'id':'revenuePerTransaction',
					'type':'currency'
				},'added_to_cart':{
					'id':'productAddsToCart',
					'type':'number'
				},'sessions':{
					'id':'sessions',
					'type':'number'
				},'users':{
					'id':'users',
					'type':'number'
				},'new_users':{
					'id':'newUsers',
					'type':'number'
				},'product_views':{
					'id':'productDetailViews',
					'type':'number'
				},'removed_from_cart':{
					'id':'productRemovesFromCart',
					'type':'number'
				},'transaction_shipping':{
					'id':'transactionShipping',
					'type':'currency'
				},'transaction_tax':{
					'id':'transactionTax',
					'type':'currency'
				}
			};
		}else{
			currency_code = data.currencyCode;
			s_1_div_id = {
				/*'conversion_rate':{
					'id':'transactionsPerSession',
					'type':'rate'
				},*/'revenue':{
					'id':'totalRevenue',
					'type':'currency'
				},'total_transactions':{
					'id':'transactions',
					'type':'number'
				},'avg_order_value':{
					'id':'averagePurchaseRevenue',
					'type':'currency'
				},'added_to_cart':{
					'id':'addToCarts',
					'type':'number'
				},'sessions':{
					'id':'sessions',
					'type':'number'
				},'users':{
					'id':'totalUsers',
					'type':'number'
				},'new_users':{
					'id':'newUsers',
					'type':'number'
				},'product_views':{
					'id':'itemViews',
					'type':'number'
				}/*,'removed_from_cart':{
					'id':'productRemovesFromCart',
					'type':'number'
				}*/,'transaction_shipping':{
					'id':'transactionShipping',
					'type':'currency'
				},'transaction_tax':{
					'id':'transactionTax',
					'type':'currency'
				}
			};
		}
		var reports_typs = {
			basec_data:{
				is_free:true
			},product_performance_report:{
				is_free:false
			},medium_performance_report:{
				is_free:false
			},conversion_funnel:{
				is_free:false
			},checkout_funnel:{
				is_free:false
			}
		};
		var paln_type = 'free';		
		if(Object.keys(s_1_div_id).length > 0){
			var temp_val =""; var temp_div_id = "";
			jQuery.each(s_1_div_id, function (propKey, propValue) {				
				if(basic_data.hasOwnProperty(propValue['id'])){
					temp_val = basic_data[propValue['id']];
					temp_div_id = "#s1_"+propValue['id']+" > .dash-smry-value";
					v_this.display_field_val(temp_div_id, propValue, temp_val, propValue['type'], currency_code);
				}
				if(basic_data.hasOwnProperty('compare_'+propValue['id'])){
					temp_val = basic_data['compare_'+propValue['id']];
					temp_div_id = "#s1_"+propValue['id']+" > .dash-smry-compare-val";
					v_this.display_field_val(temp_div_id, propValue, temp_val, 'rate', currency_code, plugin_url);

					//jQuery("#s1_"+propValue['id']+" > .dash-smry-value").html(temp_val);
				}
				
			});
		}

		if(data.hasOwnProperty('product_performance_report') && ( reports_typs.product_performance_report.is_free || paln_type == 'paid')){
			var p_p_r = data.product_performance_report.products;
			//console.log(p_p_r);
			var table_row = '';
			var product_revenue_per = 0;
			if(p_p_r != undefined && Object.keys(p_p_r).length > 0){
				jQuery.each(p_p_r, function (propKey, propValue) {
					table_row = '';
					if(ga_swatch == "ga3"){
						product_revenue_per = ((propValue['itemRevenue']*100)/basic_data.transactionRevenue).toFixed(1);
						if(product_revenue_per == 'NaN'){product_revenue_per = 0;}
						product_revenue_per = data
						table_row += '<tr><td class="prdnm-cell">'+propValue['productName']+'</td>';
						table_row += '<td>'+propValue['productDetailViews']+'</td>';
						table_row += '<td>'+propValue['productAddsToCart']+'</td>';
						table_row += '<td>'+propValue['uniquePurchases']+'</td>';
						table_row += '<td>'+propValue['itemQuantity']+'</td>';
						table_row += '<td>'+parseFloat(propValue['itemRevenue']).toFixed(2)+'<span class="tddshpertg"></span></td>';
						table_row += '<td>'+propValue['revenuePerItem']+'</td>';
						table_row += '<td>'+propValue['productRefundAmount']+'</td>';
						table_row += '<td>'+propValue['cartToDetailRate']+'%</td>';
						table_row += '<td>'+propValue['buyToDetailRate']+'%</td></tr>';
					}else{
						jQuery(".ga_currency_symbols").html(v_this.get_currency_symbols(currency_code));
						product_revenue_per = ((propValue['itemRevenue']*100)/basic_data.transactionRevenue).toFixed(1);
						if(product_revenue_per == 'NaN'){product_revenue_per = 0;}
						product_revenue_per = data
						table_row += '<tr><td class="prdnm-cell">'+propValue['itemName']+'</td>';
						table_row += '<td>'+propValue['itemViews']+'</td>';
						table_row += '<td>'+propValue['addToCarts']+'</td>';
						table_row += '<td>'+propValue['transactions']+'</td>';
						table_row += '<td>'+propValue['itemPurchaseQuantity']+'</td>';
						table_row += '<td>'+parseFloat(propValue['itemRevenue']).toFixed(2)+'</td>';
						//table_row += '<td>'+propValue['revenuePerItem']+'</td>';
						//table_row += '<td>'+propValue['productRefundAmount']+'</td>';
						table_row += '<td>'+parseFloat(propValue['cartToViewRate']).toFixed(2)+'%</td>';
						table_row += '<td>'+parseFloat(propValue['purchaseToViewRate']).toFixed(2)+'%</td></tr>';
					}
					jQuery("#product_performance_report table tbody").append(table_row);
				})
			}else{
				jQuery("#product_performance_report table tbody").append("<tr><td colspan='10'>Data not available</td></tr>");
			}
		}

		if(data.hasOwnProperty('medium_performance_report') && ( reports_typs.medium_performance_report.is_free || paln_type == 'paid')){
			var m_p_r = data.medium_performance_report.mediums;
			//console.log(m_p_r);
			var table_row = '';
			
			jQuery.each(m_p_r, function (propKey, propValue) {
				table_row = '';
				if(ga_swatch == "ga3"){	
					table_row += '<tr><td class="prdnm-cell">'+((propValue["medium"]!=undefined)?propValue["medium"]:0)+'</td>';
					table_row += '<td>'+((propValue["transactionsPerSession"]!=undefined)?propValue["transactionsPerSession"]:0)+'</td>';
					table_row += '<td>'+((propValue["transactionRevenue"]!=undefined)?parseFloat(propValue["transactionRevenue"]).toFixed(2):0)+'</td>';
					table_row += '<td>'+((propValue["transactions"]!=undefined)?propValue["transactions"]:0)+'</td>';
					table_row += '<td>'+((propValue["revenuePerTransaction"]!=undefined)?parseFloat(propValue["revenuePerTransaction"]).toFixed(2):0)+'</td>';
					table_row += '<td>'+((propValue["productAddsToCart"]!=undefined)?propValue["productAddsToCart"]:0)+'</td>';
					table_row += '<td>'+((propValue["productRemovesFromCart"]!=undefined)?propValue["productRemovesFromCart"]:0)+'</td>';
					table_row += '<td>'+((propValue["productDetailViews"]!=undefined)?propValue["productDetailViews"]:0)+'</td>';
					table_row += '<td>'+((propValue["users"]!=undefined)?propValue["users"]:0)+'</td>';
					table_row += '<td>'+((propValue["sessions"]!=undefined)?propValue["sessions"]:0)+'</td></tr>';
				}else{
					table_row += '<tr><td class="prdnm-cell">'+((propValue["medium"]!=undefined)?propValue["medium"]:0)+'</td>';
					//table_row += '<td>'+((propValue["transactionsPerSession"]!=undefined)?propValue["transactionsPerSession"]:0)+'</td>';
					table_row += '<td>'+((propValue["totalRevenue"]!=undefined)?parseFloat(propValue["totalRevenue"]).toFixed(2):0)+'</td>';
					table_row += '<td>'+((propValue["transactions"]!=undefined)?propValue["transactions"]:0)+'</td>';
					table_row += '<td>'+((propValue["averagePurchaseRevenue"]!=undefined)?parseFloat(propValue["averagePurchaseRevenue"]).toFixed(2):0)+'</td>';
					table_row += '<td>'+((propValue["addToCarts"]!=undefined)?propValue["addToCarts"]:0)+'</td>';
					//table_row += '<td>'+((propValue["productRemovesFromCart"]!=undefined)?propValue["productRemovesFromCart"]:0)+'</td>';
					table_row += '<td>'+((propValue["itemViews"]!=undefined)?propValue["itemViews"]:0)+'</td>';
					table_row += '<td>'+((propValue["totalUsers"]!=undefined)?propValue["totalUsers"]:0)+'</td>';
					table_row += '<td>'+((propValue["sessions"]!=undefined)?propValue["sessions"]:0)+'</td></tr>';
				}
				jQuery("#medium_performance_report table tbody").append(table_row);
			})
		}
		if(reports_typs.conversion_funnel.is_free || paln_type == 'paid'){
			if(( data.hasOwnProperty('ecommerce_funnel') && Object.keys(data.ecommerce_funnel).length >1) || ga_swatch == "ga4"){
				if(ga_swatch == "ga3"){	
					this.set_ecommerce_conversion_funnel(basic_data, data.ecommerce_funnel.shoppingStage);				
					this.set_ecommerce_checkout_funnel(data.ecommerce_funnel.shoppingStage);
				}else{
					 let shoppingStage = {
					 	ALL_VISITS:basic_data.totalUsers,
					 	PRODUCT_VIEW:basic_data.itemViews,
					 	ADD_TO_CART:basic_data.addToCarts,
					 	CHECKOUT:basic_data.checkouts,
					 	TRANSACTION:basic_data.transactions
					 };
					this.set_ecommerce_conversion_funnel(basic_data,shoppingStage);	
				}
			}else{
				jQuery(".conversion_s1, .conversion_s2, .conversion_s3, .conversion_s4, .checkoutfunn_s1, .checkoutfunn_s2, .checkoutfunn_s3").html("");
			}
		}
	},
	set_ecommerce_conversion_funnel:function(data,shoppingStage){
		/**
      * Ecommerce Conversion Funnel
      **/
    var conversion_s1 = ((shoppingStage.PRODUCT_VIEW*100)/shoppingStage.ALL_VISITS).toFixed(2) || 0;
    if(conversion_s1 == 'NaN'){conversion_s1 = 0;}
    jQuery(".conversion_s1").html(conversion_s1+"%");
    var conversion_s2 = ((shoppingStage.ADD_TO_CART*100)/shoppingStage.PRODUCT_VIEW).toFixed(2) || 0;
    if(conversion_s2 == 'NaN'){conversion_s2 = 0;}
    jQuery(".conversion_s2").html(conversion_s2+"%");
    var conversion_s3 = ((shoppingStage.CHECKOUT*100)/shoppingStage.ADD_TO_CART).toFixed(2) || 0;
    if(conversion_s3 == 'NaN'){conversion_s3 = 0;}
    jQuery(".conversion_s3").html(conversion_s3+"%");
    var conversion_s4 = ((shoppingStage.TRANSACTION*100)/shoppingStage.CHECKOUT).toFixed(2) || 0;
    if(conversion_s4 == 'NaN'){conversion_s4 = 0;}
    jQuery(".conversion_s4").html(conversion_s4+"%");

    conversion_funnel_chart =document.getElementById('ecomfunchart').getContext('2d');
		var conversion_bluechartgradient = conversion_funnel_chart.createLinearGradient(0, 0, 0, 800);
    conversion_bluechartgradient.addColorStop(0, '#002BFC');
    conversion_bluechartgradient.addColorStop(1, '#00CFF6');

    conversion_bar_chart = new Chart(conversion_funnel_chart, {
      type: 'bar',
      scaleSteps : 5,
      data: {
          labels: ["Total Sessions", "Product View", "Add to Cart", "Checkouts", "Order Confirmation"],
          datasets: [{
            labels:false,
            data: [shoppingStage.ALL_VISITS, shoppingStage.PRODUCT_VIEW, shoppingStage.ADD_TO_CART, shoppingStage.CHECKOUT, shoppingStage.TRANSACTION],
            backgroundColor: conversion_bluechartgradient,
            hoverBackgroundColor: conversion_bluechartgradient,
            hoverBorderWidth: 0,
            hoverBorderColor: 'blue',
            datalabels: {
              /*formatter: (value, ctx) => {
                let sum = 0;
                let dataArr = ctx.chart.data.datasets[0].data;
                  dataArr.map(data => {
                      sum += data;
                  });
                  let percentage = (value*300 / sum).toFixed(0)+"%";
                  return percentage;
              },*/
              color: '#515151',
              anchor:'end',
              align:'top',
              offset:'10',
             }
          }]
      },
      plugins: [ChartDataLabels],        
      options: {
          responsive:true,
          plugins:{
              legend:false,
           },
          scales: {
              y:{
                  fontColor: "#ffffff",
                  fontStyle: "normal",
                  beginAtZero: true,
                  maxTicksLimit: 6,
                  padding: 0,
                  grid:{
                    borderWidth:0,
                  },
                  ticks: {
                    stepSize: 1000,
                    callback: function(value) {
                       var ranges = [
                          { divider: 1e6, suffix: 'M' },
                          { divider: 1e3, suffix: 'k' }
                       ];
                       function formatNumber(n) {
                          for (var i = 0; i < ranges.length; i++) {
                             if (n >= ranges[i].divider) {
                                return (n / ranges[i].divider).toString() + ranges[i].suffix;
                             }
                          }
                          return n;
                       }
                       return '' + formatNumber(value);
                    }
                 }
              },
              x:{
                  padding: 0,
                  fontColor: "#ffffff",
                  fontStyle: "normal",
                  grid: {
                    display:false
                  }
              }
          },
          
      }
    });

	},
	set_ecommerce_checkout_funnel:function(data){
		/**
      * Ecommerce Checkout Funnel
      **/
    
    var conversion_s1 = ((data.CHECKOUT_2*100)/data.CHECKOUT_1).toFixed(2) || 0;
    if(conversion_s1 == 'NaN'){conversion_s1 = 0;}
    jQuery(".checkoutfunn_s1").html(conversion_s1+"%");
    var conversion_s2 = ((data.CHECKOUT_3*100)/data.CHECKOUT_2).toFixed(2) || 0;
    if(conversion_s2 == 'NaN'){conversion_s2 = 0;}
    jQuery(".checkoutfunn_s2").html(conversion_s2+"%");
    var conversion_s3 = ((data.TRANSACTION*100)/data.CHECKOUT_3).toFixed(2) || 0;
    if(conversion_s3 == 'NaN'){conversion_s3 = 0;}
    jQuery(".checkoutfunn_s3").html(conversion_s3+"%");

    checkout_funnel_chart = document.getElementById('ecomcheckoutfunchart').getContext('2d');
    var bluechartgradient = checkout_funnel_chart.createLinearGradient(0, 0, 0, 800);
    bluechartgradient.addColorStop(0, '#002BFC');
    bluechartgradient.addColorStop(1, '#00CFF6');

    checkout_bar_chart = new Chart(checkout_funnel_chart, {
      type: 'bar',
      scaleSteps : 6,
      data: {
          labels: [ "Checkout Step 1", "Checkout Step 2","Checkout Step 3", "Purchase"],
          datasets: [{
            labels:false,
            data: [data.CHECKOUT_1,data.CHECKOUT_2,data.CHECKOUT_3,data.TRANSACTION],
            backgroundColor: bluechartgradient,
            hoverBackgroundColor: bluechartgradient,
            hoverBorderWidth: 0,
            hoverBorderColor: 'blue',
            datalabels: {
              /*formatter: (value, ctx) => {
                let sum = 0;
                let dataArr = ctx.chart.data.datasets[0].data;
                  dataArr.map(data => {
                      sum += data;
                  });
                  let percentage = (value*300 / sum).toFixed(0)+"%";
                  return percentage;
              },*/
              color: '#515151',
              anchor:'end',
              align:'top',
              offset:'10',
             }
          }]
      },
      plugins: [ChartDataLabels],        
      options: {
          responsive:true,
          plugins:{
              legend:false,
           },
          scales: {
              y:{
                  fontColor: "#ffffff",
                  fontStyle: "normal",
                  beginAtZero: true,
                  maxTicksLimit: 6,
                  padding: 0,
                  grid:{
                    borderWidth:0,
                  },
                  ticks: {
                    stepSize: 1000,
                    callback: function(value) {
                       var ranges = [
                          { divider: 1e6, suffix: 'M' },
                          { divider: 1e3, suffix: 'k' }
                       ];
                       function formatNumber(n) {
                          for (var i = 0; i < ranges.length; i++) {
                             if (n >= ranges[i].divider) {
                                return (n / ranges[i].divider).toString() + ranges[i].suffix;
                             }
                          }
                          return n;
                       }
                       return '' + formatNumber(value);
                    }
                 }
              },
              x:{
                  padding: 0,
                  fontColor: "#ffffff",
                  fontStyle: "normal",
                  grid: {
                    display:false
                  }
              }
          },
          
      }
    });

	},
	display_field_val:function(div_id, field, field_val, field_type, currency_code, plugin_url){
		if(field_type == "currency"){
			if(Math.floor(field_val) != field_val){
				field_val = parseFloat(field_val.replace(/,/g, '')).toFixed(2);
			}
			var currency = this.get_currency_symbols(currency_code);
			jQuery(div_id).html(currency +''+this.numberWithCommas(field_val));
		}else if(field_type == "rate"){
			if(Math.floor(field_val) != field_val){
				field_val = parseFloat(field_val.replace(/,/g, '')).toFixed(2);
			}
			var img = "";
			if(plugin_url != "" && plugin_url != undefined){
				img = '<img src="'+plugin_url+'/admin/images/red-down.png">';
				if(field_val >0){
					img = '<img src="'+plugin_url+'/admin/images/green-up.png">';
				}
			}
			jQuery(div_id).html(img+field_val+'%');
		}else {
			if(Math.floor(field_val) != field_val){
				field_val = parseFloat(field_val.replace(/,/g, '')).toFixed(2);
			}
			jQuery(div_id).html(this.numberWithCommas(field_val));
		}

	},
	remove_loader_for_analytics_reports:function(){
		var reg_section = this.get_analytics_reports_section();
		if(Object.keys(reg_section).length > 0){
			jQuery.each(reg_section, function (propKey, propValue) {
				if(propValue.hasOwnProperty('main-class') && propValue.hasOwnProperty('loading-type')){
					if(propValue['loading-type'] == 'bgcolor'){
						//jQuery("."+propValue['main-class']).addClass("is_loading");
						if(Object.keys(propValue['ajax_fields']).length > 0){
							jQuery.each(propValue['ajax_fields'], function (propKey, propValue) {
									jQuery("."+propValue['class']).removeClass("loading-bg-effect");
							});
						}
					}else if(propValue['loading-type'] == 'gif'){
						jQuery("."+propValue['main-class']).removeClass("is_loading");
					}

				}
			});
			
		}
	},
	cleare_dashboard:function(){
		var v_this = this;
		jQuery("#product_performance_report table tbody").html("");
		jQuery("#medium_performance_report table tbody").html("");
		jQuery("#campaign_performance_report table tbody").html("");
		var canvas = document.getElementById('ecomfunchart');
		if( canvas != null){
			var is_blank = this.is_canvas_blank(canvas);
	    if(!is_blank){
	    	conversion_bar_chart.destroy();
	    	//const canvas = document.getElementById('ecomfunchart');
	  		//canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
	    }
	  }
	  canvas = document.getElementById('ecomcheckoutfunchart');
	  if(canvas != null){
	    var is_blank = this.is_canvas_blank(canvas);
	    if(!is_blank){
	    	checkout_bar_chart.destroy();
	    }
	  }

	  if(Object.keys(chart_ids).length > 0){
			jQuery.each(chart_ids, function (propKey, propValue) {
				var canvas = document.getElementById(propKey);
				if( canvas != null){
					var is_blank = v_this.is_canvas_blank(canvas);
					//console.log(propValue+"-"+canvas+"-"+is_blank);
			    if(!is_blank){
			    	chart_ids[propKey].destroy();		    	
			    }
			  }
			});			
		}
	},
	add_loader_for_analytics_reports:function(){
		var reg_section = this.get_analytics_reports_section();
		if(Object.keys(reg_section).length > 0){
			jQuery.each(reg_section, function (propKey, propValue) {
				if(propValue.hasOwnProperty('main-class') && propValue.hasOwnProperty('loading-type')){
					if(propValue['loading-type'] == 'bgcolor'){
						//jQuery("."+propValue['main-class']).addClass("is_loading");
						if(Object.keys(propValue['ajax_fields']).length > 0){
							jQuery.each(propValue['ajax_fields'], function (propKey, propValue) {
									jQuery("."+propValue['class']).addClass("loading-bg-effect");
							});
						}
					}else if(propValue['loading-type'] == 'gif'){
						jQuery("."+propValue['main-class']).addClass("is_loading");
					}

				}
			});			
		}
	},
	get_analytics_reports_section:function(){
		return {
			'dashboard_summary':{
				'loading-type':'bgcolor',
				'main-class':'dashsmry-item',
				'sub-clsass':'dashsmrybx',
				'ajax_fields':{
					'field_1':{
						'class':'dash-smry-title'
					},'field_2':{
						'class':'dash-smry-value'
					},'field_3':{
						'class':'dash-smry-compare-val'
					},'field_4':{
						'class':'dshsmryprdtxt'
					}
				}
			},'ecommerce_funnel':{
				'loading-type':'gif',
				'main-class':'ecom-funn-chrt-bx',
			},'checkout_funnel':{
				'loading-type':'gif',
				'main-class':'ecom-checkout-funn-chrt-bx',
			},'product_performance_report':{
				'loading-type':'gif',
				'main-class':'product_performance_report',
			},'medium_performance_report':{
				'loading-type':'gif',
				'main-class':'medium_performance_report',
			},'daily_clicks':{
				'loading-type':'gif',
				'main-class':'daily-clicks-bx',
			},'daily_cost':{
				'loading-type':'gif',
				'main-class':'daily-cost-bx',
			},'daily_sales':{
				'loading-type':'gif',
				'main-class':'daily-sales-bx',
			},'daily_conversions':{
				'loading-type':'gif',
				'main-class':'daily-conversions-bx',
			},'campaign_performance_report':{
				'loading-type':'gif',
				'main-class':'campaign_performance_report',
			}
		};
	},get_currency_symbols:function(code){
		var currency_symbols = {
		    'USD': '$', // US Dollar
		    'EUR': '€', // Euro
		    'CRC': '₡', // Costa Rican Colón
		    'GBP': '£', // British Pound Sterling
		    'ILS': '₪', // Israeli New Sheqel
		    'INR': '₹', // Indian Rupee
		    'JPY': '¥', // Japanese Yen
		    'KRW': '₩', // South Korean Won
		    'NGN': '₦', // Nigerian Naira
		    'PHP': '₱', // Philippine Peso
		    'PLN': 'zł', // Polish Zloty
		    'PYG': '₲', // Paraguayan Guarani
		    'THB': '฿', // Thai Baht
		    'UAH': '₴', // Ukrainian Hryvnia
		    'VND': '₫', // Vietnamese Dong
		};
		if(currency_symbols[code]!==undefined) {
		  return currency_symbols[code];
		}else{
			return code;
		}
	},is_canvas_blank:function (canvas) {
  	const context = canvas.getContext('2d');
	  const pixelBuffer = new Uint32Array(
	    context.getImageData(0, 0, canvas.width, canvas.height).data.buffer
	  );
  	return !pixelBuffer.some(color => color !== 0);
	},numberWithCommas:function(x) {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}
};