//<![CDATA[
var $ =jQuery.noConflict();		
$(window).load(function(){

//get Quantity select index or per 500 onload
	// var mySelected = ($('input.tmcp_select_8').val());
	//alert(mySelected);

// var myProduct = ($('select[name="add-to-cart"]').val());
 	var myQuantity = ($('select[name="tmcp_select_8"]').val());

	 // alert('before case '+ myProduct);
$('select[name="tmcp_select_8"]').change(function(){
	 //	 alert($(this).val());
		
			var numQty = ($(this).val());

                switch(numQty){

					case (numQty = "500_0"):
					var updateQty = 1;
					var numSel = 1;
					break;

					case (numQty = "1,000_1"):
					var updateQty = 2;
					var numSel = 1;
					break;

					case (numQty = "1,500_2"):
					var updateQty = 3;
					var numSel = 1;
					break;

					case (numQty = "2,000_3"):
					var updateQty = 4;
					var numSel = 1;
					break;

					case (numQty = "2,500_4"):
					var updateQty = 5;
					var numSel = 1;
					break;

					case (numQty = "3,000_5"):
					var updateQty = 6;
					var numSel = 1;
					break;

					case (numQty = "3,500_6"):
					var numSel = 1;
					var updateQty = 7;
					break;

					var numSel = 1;
					case (numQty = "4,000_7"):
					var updateQty = 8;
					var numSel = 1;
					break;

					case (numQty = "4,500_8"):
					var updateQty = 9;
					var numSel = 1;
					break;

					case (numQty = "5,000_9"):
					var updateQty = 10;
					var numSel = 1;
					break;

					case (numQty = "5,500_10"):
					var numSel = 1;
					var updateQty = 11;
					break;

					case (numQty = "6,000_11"):
					var updateQty = 12;
					var numSel = 1;
					break;

					case (numQty = "6,500_12"):
					var updateQty = 13;
					var numSel = 1;
					break;

					case (numQty = "7,000_13"):
					var updateQty = 14;
					var numSel = 1;
					break;

					case (numQty = "7,500_14"):
					var updateQty = 15;
					var numSel = 1;
					break;

					case (numQty = "8,000_15"):
					var updateQty = 16;
					var numSel = 1;
					break;

					case (numQty = "8,500_16"):
					var numSel = 1;
					var updateQty = 17;
					break;

					case (numQty = "9,000_17"):
					var updateQty = 18;
					var numSel = 1;
					break;

					case (numQty = "9,500_18"):
					var updateQty = 19;
					var numSel = 1;
					break;

					case (numQty = "10,000_19"):
					var updateQty = 20;
					var numSel = 1;
					break;

					case (numQty = "11,000_20"):
					var updateQty = 22;
					var numSel = 1;
					break;

					case (numQty = "12,000_21"):
					var updateQty = 24;
					var numSel = 1;
					break;

					case (numQty = "13,000_22"):
					var updateQty = 26;
					var numSel = 1;
					break;

					case (numQty = "14,000_23"):
					var updateQty = 28;
					var numSel = 1;
					break;

					case (numQty = "15,000_24"):
					var updateQty = 30;
					var numSel = 1;
					break;

					case (numQty = "20,000_25"):
					var updateQty = 40;
					var numSel = 1;
					break;

					case (numQty = "25,000_26"):
					var updateQty = 50;
					var numSel = 1;
					break;

					case (numQty = "30,000_27"):
					var updateQty = 60;
					var numSel = 1;
					break;

                    default:
                    var updateQty = 2;
                    break
                }

    // alert(updateQty);
		 $("input#quantityGo").val(updateQty);
		 $("input#quantityGo").change();
	});

	$( ":input" ).select(function() {
	  $("div.quantity").text( '<input type="hidden" id="quantityGo" step="1" min="1" name="quantity" value="' + updateQty + '" title="Qty" class="input-text qty text" size="4">' ).show().fadeOut( 100 );
	});

});//]]>

