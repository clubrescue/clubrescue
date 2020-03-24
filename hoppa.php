<?php

	/*** PREVENT THE PAGE FROM BEING CACHED BY THE WEB BROWSER ***/
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Wed, 9 Apr 1986 21:00:00 GMT");
	require_once("wp-authenticate.php");
	/*** REQUIRE USER AUTHENTICATION ***/
	login();
	/*** RETRIEVE LOGGED IN USER INFORMATION ***/
	$user = wp_get_current_user();


	// Include database class
	include 'util/utility.class.php';	
	include 'util/database.class.php';


	$CFupdate = 'UPDATE `cr_options` SET `CFAdmEntryNo` = CFAdmEntryNo + 1 WHERE `cr_options`.`ID` = 1';

	$database = new Database();
	$database->query($CFupdate);
	$database->execute();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!-- saved from url=(0052)https://trb.nu/clubforms/confirm.php?id=15127&done=1 -->
<html lang="en" xml:lang="en" xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Administratie formulier</title>
<style>
html{
	background: url('../../../images/form_resources/grey-mild.png') repeat scroll 0 0 #ececec;
}

#main_body
{
	font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif;
	font-size:small;
	margin:20px 0 50px;
	text-align:center;
}

#form_container
{
	background:#fff;
	border: none;
	border-radius: 10px;
	margin:0 auto;
	text-align:left;
	width:640px;
	box-shadow: 0 0 3px rgba(0, 0, 0, 0.4);
}

#top
{
	display:block;
	height:10px;
	margin:10px auto 0;
	width:650px;
}

#footer
{
	clear:both;
	color:#999999;
	text-align:center;
	width:640px;
	padding-bottom: 15px;
	font-size: 85%;
}

#footer a{
	color:#999999;
	text-decoration: none;
	border-bottom: 1px dotted #999999;
}

#bottom
{
	display:block;
	height:10px;
	margin:0 auto;
	width:650px;
}

form.appnitro
{
	margin:20px 20px 0;
	padding:0 0 20px;
}

/**** Logo Section  *****/
#main_body h1
{

	margin:0;
	padding:0;
	text-decoration:none;
	text-indent:-8000px;
	color: #fff;
	border-radius: 8px 8px 0 0;
	background-color: #525252;
	background-image: -webkit-gradient(linear, left center, right center, from(rgb(82, 82, 82)), to(rgb(136, 136, 136)));
	background-image: -webkit-linear-gradient(left, rgb(82, 82, 82), rgb(136, 136, 136));
	background-image: -moz-linear-gradient(left, rgb(82, 82, 82), rgb(136, 136, 136));
	background-image: -o-linear-gradient(left, rgb(82, 82, 82), rgb(136, 136, 136));
	background-image: -ms-linear-gradient(left, rgb(82, 82, 82), rgb(136, 136, 136));
	background-image: linear-gradient(left, rgb(82, 82, 82), rgb(136, 136, 136));
}

#main_body h1 a
{
	display:block;
	height:40px;
	overflow:hidden;
	background-image: url('../../../images/machform.png'); 
	background-repeat: no-repeat; 
}


/**** Form Section ****/
.appnitro
{
	font-family: "Lucida Grande", Tahoma, Arial, Verdana, sans-serif;
	font-size:small;
}

.appnitro li
{
	width:61%;
}

#main_body  form ul
{
	font-size:100%;
	list-style-type:none;
	margin:0;
	padding:0;
	width:100%;
}

#main_body form li
{
	display:block;
	margin:0;
	padding:4px 5px 2px 9px;
	position:relative;
	clear: both;
}

#main_body form li:after
{
	clear:both;
	content:".";
	display:block;
	height:0;
	visibility:hidden;
}

#main_body .buttons:after
{
	clear:both;
	content:".";
	display:block;
	height:0;
	visibility:hidden;
}

#main_body .buttons
{
	clear:both;
	display:block;
	margin-top:10px;
}

#main_body html form li div
{
	display:inline-block;
}

#main_body form li:not(.media_video) div
{
	color:#444;
	margin:0 4px 0 0;
	padding:0 0 8px;
}

#main_body form li:not(.media_video) span
{
	color:#444;
	float:left;
	margin:0 4px 0 0;
	padding:0 0 8px;
}

#main_body form li div.left
{
	display:inline;
	float:left;
	width:48%;
}

#main_body form li div.right
{
	display:inline;
	float:right;
	width:48%;
}

#main_body form li div.left .medium
{
	width:100%;
}

#main_body form li div.right .medium
{
	width:98%;
}

#main_body .clear
{
	clear:both;
}

#main_body form li div label,
#main_body form li div span.label
{
	clear:both;
	color:#444;
	display:block;
	font-size:85%;
	line-height:15px;
	margin:0;
	padding-top:3px;
}

#main_body form li div span.label var
{
	font-style: normal;
	font-weight: bold;
}

#main_body form li span label
{
	clear:both;
	color:#444;
	display:block;
	font-size:85%;
	line-height:15px;
	margin:0;
	padding-top:3px;
	margin-left: 3px;
}

#main_body em.currently_entered
{
	white-space: nowrap;
}

#main_body form li .datepicker
{
	cursor:pointer !important;
	float:left;
	height:16px;
	margin:.1em 5px 0 0;
	padding:0;
	width:16px;
}

#main_body .form_description
{
	border-bottom:1px dotted #ccc;
	clear:both;
	display:inline-block;
	margin:0 0 1em;
	color: #444;
}

#main_body .form_description[class]
{
	display:block;
}

#main_body .form_description h2
{
	clear:left;
	font-size:160%;
	font-weight:400;
	margin:0 0 3px;
}

#main_body .form_description p
{
	font-size:95%;
	line-height:130%;
	margin:0 0 12px;
}

#main_body form hr
{
	display:none;
}

#main_body form li.total_payment
{
	padding-bottom: 0;
    padding-top: 0;
    width: 97%;
}
#main_body form li.total_payment.mf_review{
	width: 97%;
	margin-top: 10px;
}
#main_body form li.total_payment span
{
	float: right; 
	text-align: center;
	padding-bottom: 0;
}

#main_body form li.total_payment h3{
	margin: 0px;
	font-size: 150%;
}

#main_body form li.total_payment var{
	font-style: normal;
}

#main_body form li.total_payment h5{
	font-size: 70%;
    font-weight: 100;
    margin: 0;
    text-transform: uppercase;
}

#main_body form li.total_payment span.total_extra{
	clear: both; 
	margin-top: 10px; 
	text-align: right
}
#main_body form li.total_payment span.total_extra h5{
	font-weight: 700;
}

#main_body form li.section_break
{
	border-top:1px dotted #ccc;
	margin-top:9px;
	padding-bottom:0;
	padding-left:9px;
	padding-top:13px;
	width:97% !important;
}

#main_body form ul li.first
{
	border-top:none !important;
	margin-top:0 !important;
	padding-top:0 !important;
}

#main_body form .section_break h3,
#main_body form .media h3
{
	font-size:110%;
	font-weight:400;
	line-height:130%;
	margin:0 0 2px;
}
#main_body .media_image{
	max-width: 100%;
	height: auto;
}
#main_body form .section_break p,
#main_body form .media p
{
	font-size:85%;
	margin:0 0 10px;
}
#main_body form li div.media_image_container{
	margin: 0;
	padding: 0 0 10px 0;
}
#main_body form li div.media_video_container{
	width: 99%;
	height: auto;
	background-color: #000;
	margin-bottom: 10px;
}
#main_body form li div.media_video_container.small{
	width: 40%;
}
#main_body form li div.media_video_container.medium{
	width: 65%;
}
#main_body form li div.media_video_container.large{
	width: 99%;
}
#main_body form li div.media_image_left{
	text-align: left;
}
#main_body form li div.media_image_center{
	text-align: center;
}
#main_body form li div.media_image_right{
	text-align: right;
}
#main_body form li:not(.media_video) div span {
	display:block;
	float:left;
	margin:0;
	padding: 0;
	width:100%;
}

#main_body form li div span.state_list{
	height: 54px;
}

.namewm_ext{
	width: 8%;
}
.namewm_first,.namewm_middle{
	width: 23%;
}
.namewm_last{
	width: 30%;
}

/**** Choices Columns ****/
#main_body form li.two_columns div span {
  margin:0 5px 0 0;
  width:48%;
}

#main_body form li.three_columns div span {
  margin:0 5px 0 0;
  width:30%;
}

#main_body form li.inline_columns div span {
  margin:0 6px 0 0;
  width:auto;
}

/**** Buttons ****/
#main_body input.button_text
{
	overflow:visible;
	width:auto;

	outline: none;
	padding: 6px 9px;
	font: 300 1em 'Helvetica Neue', Arial, 'Lucida Grande', sans-serif;
	color: #333;
	text-shadow: 0 1px 0 #f0f0f0;
	background: #ebebeb;
	background: -webkit-gradient(linear, left top, left bottom, from(#fefefe), to(#dddddd));
	background: -moz-linear-gradient(top, #fefefe, #dddddd);
	border-width: 1px;
	border-style: solid;
	border-color: #bbb #bbb #999;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	border-radius: 4px;
	-webkit-box-shadow: 0 1px 0 #f8f8f8;
	-moz-box-shadow: 0 1px 0 #f8f8f8;
	box-shadow: 0 1px 0 #f8f8f8;
}
#main_body input.button_text:hover, #main_body input.button_text:focus{
	color: #333;
	text-shadow: 0 1px 0 #f0f0f0;
	background: #e5e5e5;
	background: -webkit-gradient(linear, left top, left bottom, from(#f0f0f0), to(#dddddd));
	background: -moz-linear-gradient(top, #f0f0f0, #dddddd);
	border-color: #999 #999 #666;
}
#main_body input.button_text:active{
	color: #333;
	text-shadow: none;
	background: #ddd;
	border-color: #999 #999 #666;	
}

#main_body .buttons input
{
	font-size:120%;
	margin-right:5px;
}

#main_body input.btn_secondary{
	background: none;
	border: none;
	color: blue;
	text-decoration: underline;
	cursor: pointer;
	font-size: 100%;
	padding: 0;
}
/**** Inputs and Labels ****/
#main_body form li fieldset{
	margin: 0;
	padding:0;
	border: none;
}

#main_body form li label.description,
#main_body form li span.description
{
	border:none;
	color:#444;
	display:block;
	font-size:95%;
	font-weight:700;
	line-height:150%;
	padding:0 0 1px;
	float: none;
}

#main_body span.symbol
{
	font-size:115%;
	line-height:130%;
}

#main_body input.text
{
	border: 4px solid #EFEFEF;
	border-radius: 8px;
	box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2) inset;
	outline: medium none;
	background: none repeat scroll 0 0 #FBFBFB;
	padding:6px 0 6px 6px;
	color:#666666;
	
	font-size:100%;
	margin:0;
}

#main_body input.other
{
	margin: 0 0 9px 25px;
	background: none repeat scroll 0 0 #FBFBFB;
}

#main_body input.file
{
	color:#333;
	font-size:100%;
	margin:0;
	padding:2px 0;
}

#main_body textarea.textarea
{
	border: 4px solid #EFEFEF;
	border-radius: 8px;
	box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2) inset;
	outline: medium none;
	background: none repeat scroll 0 0 #FBFBFB;
	padding:6px 0 6px 6px;
	color:#666666;
	
	font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif;
	font-size:100%;
	margin:0;
	width:98%;
}

#main_body select.select
{

	border: 4px solid #EFEFEF;
	border-radius: 8px;
	box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2) inset;
	outline: medium none;
	background: none repeat scroll 0 0 #FBFBFB;
	padding:6px 0 6px 6px;
	color:#666666;
	height: 36px;
	font-size:100%;
	margin:1px 0;
}


#main_body input.currency
{
	text-align:right;
	padding-right:3px;
}

#main_body input.checkbox
{
	display:block;
	height:13px;
	line-height:1.4em;
	margin:6px 0 0 3px;
	width:13px;
}

#main_body input.radio
{
	display:block;
	height:13px;
	line-height:1.4em;
	margin:6px 0 0 3px;
	width:13px;
}

#main_body label.choice
{
	color:#444;
	display:block;
	font-size:100%;
	line-height:1.4em;
	margin:-1.55em 0 0 25px;
	padding:4px 0 5px;
	width:90%;
}

#main_body select.select[class]
{
	margin:0;
	padding:6px 0 6px 6px;
}

*:first-child+html select.select[class]
{
	margin:1px 0;
}

#main_body .safari select.select
{
	font-size:120% !important;
	margin-bottom:1px;
}

#main_body input.small
{
	width:25%;
}

#main_body select.small
{
	width:25%;
}

#main_body input.medium
{
	width:50%;
}

#main_body select.medium
{
	width:50%;
}

#main_body input.large
{
	width:98%;
}

#main_body select.large
{
	width:100%;
}

#main_body textarea.small
{
	height:5.5em;
}

#main_body textarea.medium
{
	height:10em;
}

#main_body textarea.large
{
	height:20em;
}

/**** Errors ****/
#error_message
{
	background:#fff;
	border:1px dotted red;
	margin-bottom:1em;
	padding-left:0;
	padding-right:0;
	padding-top:4px;
	text-align:center;
	width:97%;
}

#error_message_title
{
	color:#DF0000;
	font-size:125%;
	margin:7px 0 5px !important;
	padding:0 !important;
}

#error_message_desc
{
	color:#000;
	font-size:100%;
	margin:0 0 .8em !important;
}

#error_message_desc strong
{
	background-color:#FFDFDF;
	color:#c10000;
	padding:2px 3px;
}

#main_body form li.error
{
	background-color:#FFDFDF !important;
	border-bottom:1px solid #EACBCC;
	border-right:1px solid #EACBCC;
	margin:3px 0;
}

#main_body form li.error label,
#main_body form li.error span.description
{
	color:#c10000 !important;
}

#main_body form p.error
{
	clear:both;
	color:#c10000;
	font-size:80%;
	font-weight:700;
	margin:0 0 5px !important;
}

#main_body form .required,#main_body .matrix span.required
{
	color:#c10000 !important;
	float:none !important;
	font-weight:700;
}

/**** Guidelines and Error Highlight ****/
#main_body form li.highlighted
{
	background-color:#fff7c0;
}

#main_body form .guidelines
{
	background:#f5f5f5;
	border:1px solid #e6e6e6;
	color:#444;
	font-size:105%;
	left:100%;
	line-height:100%;
	margin:0 0 0 8px !important;
	padding:8px 10px 9px;
	position:absolute;
	top:0;
	visibility:hidden;
	width:42%;
	z-index:1000;
}

#main_body form .guidelines small
{
	font-size:80%;
}

#main_body form li.highlighted .guidelines
{
	visibility:visible;
}


#main_body form li:hover .guidelines
{
	visibility:visible;
}

.no_guidelines .guidelines
{
	display:none !important;
}

.no_guidelines form li
{
	width:97%;
}

.no_guidelines li.section
{
	padding-left:9px;
}

/*** Success Message ****/
.form_success 
{
	clear: both;
	margin: 0;
	padding: 90px 0pt 100px;
	text-align: center;
}

.form_success h2 {
    clear:left;
    font-size:160%;
    font-weight:normal;
    margin:0pt 0pt 3px;
}

.form_success h3 {
    font-weight:normal;
}

/*** Password ****/
#main_body ul.password{
    margin-top:60px;
    margin-bottom: 60px;
    text-align: center;
}
.password h2{
    color:#DF0000;
    font-weight:bold;
    margin:0pt auto 10px;
}

.password input.text {
   font-size:170% !important;
   width:380px;
   text-align: center;
}
.password label{
   display:block;
   font-size:120% !important;
   padding-top:10px;
   font-weight:bold;
}

#li_captcha{
   padding-left: 5px;
   padding-bottom: 10px !important;
}


#li_captcha span{
	float:none !important;
	padding: 0px !important;
}

#li_captcha span.text_captcha{
	padding-bottom: 5px !important;
}

#li_captcha div{
   padding: 0px !important;
}

#captcha_image{
	padding-top: 5px;
	padding-bottom: 10px;
}
#captcha_response_field{
	margin-bottom: 10px;
}
#dummy_captcha_internal{
	height: 8px;
}

/** Matrix Table **/
#main_body form li.matrix{
	width: 97% !important;
}
#main_body .matrix table
{
	margin: 0 0 5px;
	width: 100%;
	border-collapse: collapse;
	text-align: left;
}
#main_body .matrix th
{
	font-size: 95%;
	text-align: center;
	padding: 5px 0px;
	border-bottom: 1px solid #888;
	font-weight: normal;
}
#main_body .matrix td
{
	border-bottom: 1px solid #ccc;
	padding: 6px 8px;
	text-align: center;
}
#main_body .matrix tbody tr:hover td
{
	background-color: #fff7c0;
}

#main_body .matrix td.first_col{
	text-align: left;
	font-weight: 700;
	font-size: 95%;
	color:#444;
}

#main_body .matrix tr.alt{
	background-color: #F5F5F5;
}

#main_body .matrix caption{
	text-align: left;
	font-size: 95%;
    font-weight: 700;
    color:#444;
}



/** Label Alignment **/
#main_body form.left_label li, #main_body form.right_label li
{
	padding-top: 12px;
	width: 76%;
}
.no_guidelines form.left_label li, .no_guidelines form.right_label li
{
	width: 97% !important;
}
#main_body form.left_label label.description,
#main_body form.left_label span.description{
	float: left;
	margin: 0 15px 0 0;
	width: 29%;
}

#main_body form.right_label label.description,
#main_body form.right_label span.description{
	float: left;
	margin: 0 15px 0 0;
	width: 29%;
	text-align: right;
}
.no_guidelines form.left_label label.description,
.no_guidelines form.right_label label.description,
.no_guidelines form.left_label span.description,
.no_guidelines form.right_label span.description{
	width: 30% !important;
}

#main_body form.left_label li div, #main_body form.right_label li div
{
	float: left;
	width: 65%;
}

#main_body ul.password > li > div{
	width: 100%;
}

#main_body form li div span.left{
	width: 48%;
	float: left;
}
#main_body form li div span.right{
	width: 48%;
	float: right;
	margin-right: 3px;
}
#main_body li.address input.large{
	width: 98%;
}
#main_body li.address .right.state_list input.large{
	width: 96%;
}
#main_body li.address select.large{
	width: 101%;
}
#main_body form li.address div span{
	padding-bottom: 8px;
}

#main_body form.left_label .guidelines,#main_body form.right_label .guidelines{
	width:  20%;
}
#main_body form.left_label li div.mf_sig_wrapper, #main_body form.right_label li div.mf_sig_wrapper
{
	float: left;
	width: 309px;
}
#main_body form.left_label li .mf_sigpad_clear, #main_body form.right_label li .mf_sigpad_clear
{
	float: left;
}

/** Embedded Form **/
html.embed{
	background: none repeat scroll 0 0 transparent;
}
.embed #main_body{
	margin: 0px;
}
.embed #top, .embed #bottom, .embed h1{
	display: none;
}

.embed #form_container{
    border: none;
	width: 100%;
	background: none;
	box-shadow: none;
}

.embed #footer{
	text-align: left;
	padding-left: 10px;
	width: 99%;
}

.embed #footer.success{
	text-align: center;
}

.embed form.appnitro
{
	margin:0px 0px 0;
	
}

/** Integrated Form **/
#main_body.integrated{
	margin: 0px;
}
.integrated *{
	font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif;
	color: #000; 
}

.integrated #top, .integrated #bottom, .integrated h1{
	display: none;
}

.integrated #form_container{
    border: none;
	width: 99%;
	background: none;
	box-shadow: none;
}

.integrated #footer{
	text-align: left;
	padding-left: 10px;
	width: 99%;
}

.integrated #footer.success{
	text-align: center;
}

.integrated form.appnitro
{
	margin:0px 0px 0;
	
}

.integrated form .section_break h3
{
	border: none !important;
}

.integrated #error_message h3
{
	border: none !important;
	
}


/** Form Review **/
#machform_review_table tbody tr:hover
{
	background-color: #FFF7C0;
}
.alt{
	background: #efefef;
}
#machform_review_table td
{
	text-align: left;
	border-bottom:1px solid #DEDEDE;
	padding:5px 10px;
}
#machform_review_table td.mf_review_label{
	font-weight: 700;
}

/** Payment Page **/
#main_body ul.payment_summary{
	overflow: hidden;
}
#main_body form li.payment_summary_list{
	border-right: 1px dashed #ccc;
	padding-right: 10px;
	width: 70%;
	float: right;
	clear: none;
	text-align: right;
}
#main_body form li.payment_summary_amount{
	width: auto;
	float: right;
	clear: none;
}
#main_body form ul.payment_list_items li{
	width: 98%;
	font-size: 95%;
	padding-top: 0px;
	padding-bottom: 5px;
}
#main_body form ul.payment_list_items li span{
	margin: 0px;
	float: right;
	display: block;
	font-weight: bold;
	padding: 0px;
	padding-left: 10px;
	color: inherit;
}
#main_body form li.payment_summary_term{
	text-align: right;
	font-size: 90%;
	padding: 15px 0;
}
#main_body form li#li_accepted_cards{
	margin-bottom: 10px;
}
#li_accepted_cards img{
	height: 27px;
}
#main_body form ul.payment_detail_form{
	margin-top: 20px
}
#main_body form li.credit_card div span{
	padding-bottom: 8px;
}
#main_body form li.credit_card div span#li_cc_span_3{
	width: 75%;
}
#main_body form li.credit_card div span#li_cc_span_4{
	width: 21%;
}
#cc_secure_icon{
	float: left;
	margin-top:5px;
}
#cc_expiry_month{
	width: 23%;
}
#main_body select#cc_expiry_year{
	width: 13%;
	margin-right: -3px;
}
#li_billing_address span.state_list,
#li_shipping_address span.state_list{
	padding-bottom: 12px !important;
}
#li_shipping_address div.shipping_address_detail{
	content: "";
    display: table;
  	clear: both;
}
#li_credit_card{
	padding-bottom: 5px !important;
	margin-bottom: 20px !important;
}
#main_body input#cc_cvv.large{
	width: 95%;
}

/** Calendar **/
.day_disabled 
{ 
	text-indent: -9999px; 
	background: #eee url('../../../images/bullet_red.png') no-repeat center; 
}

/** Pagination **/
#main_body form li.li_pagination{
	width: 97%;
	border-bottom: 1px dotted #CCCCCC;
	margin: 0px;
	padding-top: 0px;
	padding-bottom: 10px;
}
#main_body form li.li_pagination span{
	font-family: Arial, Verdana, Helvetica;
	font-size: 90%;
	float: none;
	display: block;
	padding: 0px;
}
.ap_tp_num{
	background-color: #6d6d6d;
	color: white !important;
	font-weight: bold;
	width: 23px !important; 
	height: 23px !important; 
	line-height: 23px !important;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
}
.ap_tp_text{
	clear: both;
	padding-top: 3px !important;
}
.ap_tp_arrow{
	font-size: 90%; 
	color: #bbb;
	font-family: Arial, Verdana, Helvetica;
}
.ap_tp_text_active{
	font-weight: bold;
}
.ap_tp_num_active{
	background-color: #558126;
}

.img_secondary{
	margin-left: 5px;
}

div.mf_progress_container {
  border: 1px solid #ccc !important; 
  width: 98% !important; 
  margin: 2px 5px 2px 0 !important; 
  padding: 1px !important; 
  float: left; 
  background: white;
  -webkit-border-radius: 8px; 
  -moz-border-radius: 8px; 
  border-radius: 8px
}

div.mf_progress_value {
  background-color: #558126; 
  height: 12px;
  text-align: right;
  -webkit-border-radius: 8px; 
  -moz-border-radius: 8px; 
  border-radius: 8px
}

div.mf_progress_value span{
	line-height: 20px; 
	font-weight: bold; 
	color: #fff !important; 
	padding-right: 5px !important; 
	float: right !important;
}
.li_pagination h3{
	font-size: 95%;
	padding-bottom: 2px;
	font-weight: normal;
	margin: 0px;
}

/** File Upload **/
.uploadifyQueueItem {
	background-color: #F5F5F5 !important;
	border: 2px solid #E5E5E5 !important;
	font: 85% Verdana, Geneva, sans-serif;
	margin-top: 5px !important;
	padding: 10px !important;
	width: 350px;
}

.uploadifyQueueItem .cancel {
	float: right;
}
.uploadifyQueueItem .file_attached{
	margin-right: 5px !important;	
}
.uploadifyQueueItem .fileName{
	width: auto !important;
	padding: 0px !important;
}
.uploadifyQueueItem .percentage{
	width: auto !important;
	padding: 0 0 0 5px !important;
}
.uploadifyQueue .completed {
	background-color: #E5E5E5 !important;
	padding-bottom: 25px !important;
}

.uploadifyQueue .uploadifyError {
	background-color: #FDE5DD !important;
	border: 2px solid #FBCBBC !important;
	padding-bottom: 25px !important;
}

.uploadifyProgress {
	background-color: #E5E5E5;
	margin-top: 10px;
	width: 100%;
	clear: both;
	padding: 0px !important;
	margin: 0 !important;
}
.uploadifyProgressBar {
	background-color: #0099FF;
	height: 3px;
	width: 1px;
	padding: 0px !important;
}

/** File Upload - HTML5 **/
.uploadifive-button {
	background-color: #505050;
	background-image: linear-gradient(bottom, #505050 0%, #707070 100%);
	background-image: -o-linear-gradient(bottom, #505050 0%, #707070 100%);
	background-image: -moz-linear-gradient(bottom, #505050 0%, #707070 100%);
	background-image: -webkit-linear-gradient(bottom, #505050 0%, #707070 100%);
	background-image: -ms-linear-gradient(bottom, #505050 0%, #707070 100%);
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0, #505050),
		color-stop(1, #707070)
	);
	background-position: center top;
	background-repeat: no-repeat;
	-webkit-border-radius: 30px;
	-moz-border-radius: 30px;
	border-radius: 30px;
	border: 2px solid #808080;
	color: #FFF !important;
	font: bold 12px Arial, Helvetica, sans-serif;
	text-align: center;
	text-shadow: 0 -1px 0 rgba(0,0,0,0.25);
	text-transform: uppercase;
	width: 100%;
	margin: 0 !important;
	padding: 0 !important;
}
.uploadifive-button:hover {
	background-color: #606060;
	background-image: linear-gradient(top, #606060 0%, #808080 100%);
	background-image: -o-linear-gradient(top, #606060 0%, #808080 100%);
	background-image: -moz-linear-gradient(top, #606060 0%, #808080 100%);
	background-image: -webkit-linear-gradient(top, #606060 0%, #808080 100%);
	background-image: -ms-linear-gradient(top, #606060 0%, #808080 100%);
	background-image: -webkit-gradient(
		linear,
		left bottom,
		left top,
		color-stop(0, #606060),
		color-stop(1, #808080)
	);
	background-position: center bottom;
}
.uploadifive-queue-item {
	background-color: #F5F5F5 !important;
	border: 2px solid #E5E5E5 !important;
	font: 85% Verdana, Geneva, sans-serif;
	margin-top: 5px !important;
	padding: 10px !important;
	width: 350px;
}
.uploadifive-queue-item.error {
	background-color: #FDE5DD !important;
	border: 2px solid #FBCBBC !important;
}
.uploadifive-queue-item .close {
	background: url('../../../images/icons/delete.png') 0 0 no-repeat;
	display: block;
	float: right;
	height: 16px;
	text-indent: -9999px;
	width: 16px;
}
.uploadifive-queue-item .progress {
	border: 1px solid #D0D0D0;
	height: 3px;
	margin: 8px 0 0 0 !important;
	width: 100%;
	padding: 0 !important;
	clear: both;
}
.uploadifive-queue-item .progress-bar {
	background-color: #0099FF;
	height: 3px;
	width: 0;
	padding: 0 !important;
}
.uploadifive-queue-item div{
	overflow: auto;
	padding-bottom: 0 !important;
}
.uploadifive-queue-item span{
	width: auto !important;
}
.uploadifive-queue-item span.fileinfo{
	margin-left: 5px !important;
}

.right_label object,.left_label object{
	float: left;
}

#main_body form.right_label li div.uploadifyQueue,
#main_body form.left_label li div.uploadifyQueue{
	clear: both;
	width: 98%;
}

#main_body form.right_label li div.uploadifyQueueItem,
#main_body form.left_label li div.uploadifyQueueItem{
	width: 98%;
	padding-bottom: 8px !important;
}

#main_body form.right_label li div.cancel,
#main_body form.left_label li div.cancel{
	width: auto;
	margin: 0px;
	padding: 0px;
	float: right;
}

#main_body form.right_label li div.uploadifyProgress,
#main_body form.left_label li div.uploadifyProgress{
	margin-top: 10px !important;
}

#main_body form.right_label li div.uploadifyProgressBar,
#main_body form.left_label li div.uploadifyProgressBar
{
	width: 1%;
}

#li_resume_email{
	width: 61%;
}
#guide_resume_email{
	visibility: visible !important;
	display: block !important;
}

.section_scroll_small{
	height: 5em;
	overflow-y: scroll;
}
.section_scroll_medium{
	height: 10em;
	overflow-y: scroll;
}
.section_scroll_large{
	height: 20em;
	overflow-y: scroll;
}
#machform_review_table td.mf_review_section_break{
	padding: 10px 5px;
}
.mf_canvas_pad{
	border-radius: 10px;
	cursor: url("../../../js/signaturepad/pen.cur"), crosshair;
  	cursor: url("../../../js/signaturepad/pen.cur") 16 16, crosshair;
}
#machform_review_table .mf_canvas_pad{
  cursor: auto;
}
.mf_sig_wrapper {
	border-radius: 10px;
	border: 1px solid #ccc;
	width: 309px;
	padding-bottom: 0px !important;
	padding: 3px !important;
}
.mf_sigpad_clear{
	margin-left: 280px;
	margin-top: 5px;
	display: block;
}

/** Built-in Class **/
#main_body form li.column_2{
  width: 47%;
  float: left;
  clear: none !important;
}
#main_body form li.column_3{
	width: 31%;
	float: left;
	clear: none !important;
}
#main_body form li.column_4{
	width: 22%;
  	float: left;
	clear: none !important;
}
#main_body form li.column_5{
	width: 17%;
	float: left;
	clear: none !important;
}
#main_body form li.column_6{
	width: 14%;
	float: left;
	clear: none !important;
}
#main_body form li.new_row{
	clear: left !important;
}
#main_body form li.hidden{
	display: none;
}
#main_body form li.guidelines_bottom .guidelines
{
	background: none !important;
	border: none !important;
	font-size:105%;
	line-height:100%;
	margin: 0 !important;
    padding: 0 0 5px;
	visibility:visible;
	width:100%;
	position: static;
	clear: both;
}

/** Adjustments for built-in class **/
#main_body .column_2 input.large{
	width: 96%;
}
#main_body .column_3 input.large{
	width: 95%;
}
#main_body .column_4 input.large{
	width: 94%;
}
#main_body .column_5 input.large{
	width: 91%;
}
#main_body .column_6 input.large{
	width: 89%;
}
#main_body .column_2 textarea.textarea{
	width: 96%;
}
#main_body .column_3 textarea.textarea{
	width: 94%;
}
#main_body .column_4 textarea.textarea{
	width: 91%;
}
#main_body .namewm_ext input.large{
	width: 75%;
}
#main_body .namewm_first input.large,
#main_body .namewm_middle input.large{
	width: 90%;
}
#main_body .namewm_last input.large{
	width: 92%;
}
/*************************************** SHADOW STYLES ****************************************/


/******************************************************/
/****** WARP SHADOW ******/

.WarpShadow {
    position: relative;
	-moz-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
	-webkit-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
	box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
}

.WarpShadow:before, .WarpShadow:after {
	content: '';
	position: absolute;
	z-index: -1;
	bottom: 15px;
	-moz-box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);
	box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);	
}

.WarpShadow:before {
	  right: 10px;
	  -moz-transform: rotate(4deg) skew(4deg);
	  -webkit-transform: rotate(4deg) skew(4deg);
	  -o-transform: rotate(4deg) skew(4deg);
	  transform: rotate(4deg) skew(4deg);	  			  
}

/**  'smallBox' class for boxes with width between 150px - 350px  **/
.smallBox.WarpShadow:before {
	  -moz-transform: rotate(8deg) skew(4deg);
	  -webkit-transform: rotate(8deg) skew(4deg);
	  -o-transform: rotate(8deg) skew(4deg);
	  transform: rotate(8deg) skew(4deg);	  			  
}
	
.WarpShadow:after {
	  left: 10px;
	  -moz-transform: rotate(-4deg) skew(-4deg);
	  -webkit-transform: rotate(-4deg) skew(-4deg);
	  -o-transform: rotate(-4deg) skew(-4deg);
	  transform: rotate(-4deg) skew(-4deg);
}

.smallBox.WarpShadow:after {
	  -moz-transform: rotate(-8deg) skew(-4deg);
	  -webkit-transform: rotate(-8deg) skew(-4deg);
	  -o-transform: rotate(-8deg) skew(-4deg);
	  transform: rotate(-8deg) skew(-4deg);
}


/*** SHADOW PROJECTION LENGTH ***/

.WSmall:before, .WSmall:after {
	width: 150px;
}

.smallBox.WSmall:before, .smallBox.WSmall:after {
	width: 30px;
}

.WMedium:before, .WMedium:after {
	width: 250px;
}

.smallBox.WMedium:before, .smallBox.WMedium:after {
	width: 80px;
}

.WLarge:before, .WLarge:after {
	width: 350px;
}

.smallBox.WLarge:before, .smallBox.WLarge:after {
	width: 130px;
}


/*** SHADOW INTENSITY ***/

.WLight:before, .WLight:after {
	height: 5px;
}

.WNormal:before, .WNormal:after {
	height: 10px;
}

.WDark:before, .WDark:after {
	height: 15px;
}






/******************************************************/
/****** RIGHT SIDE WARP SHADOW ******/

.RightWarpShadow {
	position: relative;
	-moz-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
	-webkit-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
	box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
}

.RightWarpShadow:before, .RightWarpShadow:after {
	content: '';
	position: absolute;
	z-index: -1;
	-moz-box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);
	box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);	
}

.RightWarpShadow:before {
	  right: 10px;
	  bottom: 15px;
	  -moz-transform: rotate(4deg) skew(4deg);
	  -webkit-transform: rotate(4deg) skew(4deg);
	  -o-transform: rotate(4deg) skew(4deg);
	  transform: rotate(4deg) skew(4deg);	  			  
}

/**  'smallBox' class for boxes with width between 150px - 350px  **/
.smallBox.RightWarpShadow:before {
	  -moz-transform: rotate(8deg) skew(4deg);
	  -webkit-transform: rotate(8deg) skew(4deg);
	  -o-transform: rotate(8deg) skew(4deg);
	  transform: rotate(8deg) skew(4deg);	  			  
}

.RightWarpShadow:after {
	  left: 10px;
	  bottom: 20px;
	  -moz-transform: rotate(-4deg) skew(-4deg);
	  -webkit-transform: rotate(-4deg) skew(-4deg);
	  -o-transform: rotate(-4deg) skew(-4deg);
	  transform: rotate(-4deg) skew(-4deg);
}

.smallBox.RightWarpShadow:after {
	  -moz-transform: rotate(-8deg) skew(-4deg);
	  -webkit-transform: rotate(-8deg) skew(-4deg);
	  -o-transform: rotate(-8deg) skew(-4deg);
	  transform: rotate(-8deg) skew(-4deg);
}



/*** SHADOW PROJECTION LENGTH ***/

.RWSmall:before, .RWSmall:after {
	width: 150px;
}

.smallBox.RWSmall:before, .smallBox.RWSmall:after {
	width: 30px;
}

.RWMedium:before, .RWMedium:after {
	width: 250px;
}

.smallBox.RWMedium:before, .smallBox.RWMedium:after {
	width: 80px;
}

.RWLarge:before, .RWLarge:after {
	width: 350px;
}

.smallBox.RWLarge:before, .smallBox.RWLarge:after {
	width: 130px;
}


/*** SHADOW INTENSITY ***/

.RWLight:before, .RWLight:after {
	height: 5px;
}

.RWNormal:before, .RWNormal:after {
	height: 10px;
}

.RWDark:before, .RWDark:after {
	height: 15px;
}






/******************************************************/
/****** LEFT SIDE WARP SHADOW ******/

.LeftWarpShadow {
	position: relative;
	-moz-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
	-webkit-box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
	box-shadow: 0 14px 10px -12px rgba(0,0,0,0.7);
}

.LeftWarpShadow:before, .LeftWarpShadow:after {
	content: '';
	position: absolute;
	z-index: -1;
	-moz-box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);
	box-shadow: 0px 15px 15px rgba(0, 0, 0, 0.7);	
}

.LeftWarpShadow:before {
	  right: 10px;
	  bottom: 20px;
	  -moz-transform: rotate(4deg) skew(4deg);
	  -webkit-transform: rotate(4deg) skew(4deg);
	  -o-transform: rotate(4deg) skew(4deg);
	  transform: rotate(4deg) skew(4deg);	  			  
}

/**  'smallBox' class for boxes with width between 150px - 350px  **/
.smallBox.LeftWarpShadow:before {
	  -moz-transform: rotate(8deg) skew(4deg);
	  -webkit-transform: rotate(8deg) skew(4deg);
	  -o-transform: rotate(8deg) skew(4deg);
	  transform: rotate(8deg) skew(4deg);	  			  
}


.LeftWarpShadow:after {
	  left: 10px;
	  bottom: 15px;
	  -moz-transform: rotate(-4deg) skew(-4deg);
	  -webkit-transform: rotate(-4deg) skew(-4deg);
	  -o-transform: rotate(-4deg) skew(-4deg);
	  transform: rotate(-4deg) skew(-4deg);
}

.smallBox.LeftWarpShadow:after {
	  -moz-transform: rotate(-8deg) skew(-4deg);
	  -webkit-transform: rotate(-8deg) skew(-4deg);
	  -o-transform: rotate(-8deg) skew(-4deg);
	  transform: rotate(-8deg) skew(-4deg);
}



/*** SHADOW PROJECTION LENGTH ***/

.LWSmall:before, .LWSmall:after {
	width: 150px;
}

.smallBox.LWSmall:before, .smallBox.LWSmall:after {
	width: 30px;
}

.LWMedium:before, .LWMedium:after {
	width: 250px;
}

.smallBox.LWMedium:before, .smallBox.LWMedium:after {
	width: 80px;
}

.LWLarge:before, .LWLarge:after {
	width: 350px;
}

.smallBox.LWLarge:before, .smallBox.LWLarge:after {
	width: 130px;
}


/*** SHADOW INTENSITY ***/

.LWLight:before, .LWLight:after {
	height: 5px;
}

.LWNormal:before, .LWNormal:after {
	height: 10px;
}

.LWDark:before, .LWDark:after {
	height: 15px;
}






/******************************************************/
/****** LEFT SIDE PERSPECTIVE SHADOW ******/
/*** (FOR BOXES OF SIZE GREATER THAN 150x150 px) ***/

.LeftPerspectiveShadow {
	position: relative;
	-moz-box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
	box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
}

.LeftPerspectiveShadow:before {
	content: '';
	position: absolute;
	z-index: -1;
	bottom: 15px;
	width: 90px;
	-moz-transform: skew(50deg);
	-webkit-transform: skew(50deg);
	-o-transform: skew(50deg);
	transform: skew(50deg);
}


/*** SHADOW PROJECTION LENGTH ***/

.LPSmall:before {
	left: 114px;
	height: 20px;
}

.LPMedium:before {
	left: 102px;
	height: 40px;
}

.LPLarge:before {
	left: 90px;
	height: 60px;
}


/*** SHADOW INTENSITY ***/

.LPLight:before {
	-moz-box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.2);
	box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.2);	
}

.LPNormal:before {
	-moz-box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.35);
	-webkit-box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.35);
	box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.35);
}

.LPDark:before {
	-moz-box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.5);
	box-shadow: -130px 0 8px 14px rgba(0, 0, 0, 0.5);
}







/******************************************************/
/****** RIGHT SIDE PERSPECTIVE SHADOW ******/
/*** (FOR BOXES OF SIZE GREATER THAN 150x150 px) ***/

.RightPerspectiveShadow {
	position: relative;
	-moz-box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
	box-shadow: 1px 2px 4px rgba(0, 0, 0, 0.5);
}

.RightPerspectiveShadow:before {
	content: '';
	position: absolute;
	z-index: -1;
	bottom: 15px;
	width: 90px;
	-moz-transform: skewX(-50deg);
	-webkit-transform: skewX(-50deg);
	-o-transform: skewX(-50deg);
	transform: skewX(-50deg);
}


/*** SHADOW PROJECTION LENGTH ***/

.RPSmall:before {
	right: 114px;
	height: 20px;
}

.RPMedium:before {
	right: 102px;
	height: 40px;
}

.RPLarge:before {
	right: 90px;
	height: 60px;
}


/*** SHADOW INTENSITY ***/

.RPLight:before {
	-moz-box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.2);
	box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.2);
}

.RPNormal:before {
	-moz-box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.35);
	-webkit-box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.35);
	box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.35);
}

.RPDark:before {
	-moz-box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.5);
	box-shadow: 130px 0 8px 14px rgba(0, 0, 0, 0.5);
}







/******************************************************/
/****** BOTTOM PERSPECTIVE SHADOW ******/

.BottomShadow {
	position: relative;	
}

.BottomShadow:before, .BottomShadow:after {
	content: '';
	position: absolute;
	z-index: -1;
	bottom: 0;
	width: 30px;
	height: 50px;
}

.BottomShadow:before {
	-moz-transform: skew(40deg);
	-webkit-transform: skew(40deg);
	-o-transform: skew(40deg);
	transform: skew(40deg);	
}

.BottomShadow:after {
	-moz-transform: skew(-40deg);
	-webkit-transform: skew(-40deg);
	-o-transform: skew(-40deg);
	transform: skew(-40deg);	
}



/*** SMALL SHADOW STYLES ***/

/* LIGHT */
.BSmall.BLight {
	-moz-box-shadow: 0 52px 26px -36px rgba(0,0,0,0.7);
	-webkit-box-shadow: 0 52px 30px -39px rgba(0,0,0,0.7);
	box-shadow: 0 52px 30px -39px rgba(0,0,0,0.7);
}

.BSmall.BLight:before {
	right: 54px;
	-moz-box-shadow: 9px 17px 18px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: 14px 17px 26px rgba(0, 0, 0, 0.2);
	box-shadow: 9px 17px 18px rgba(0, 0, 0, 0.2);
}

.BSmall.BLight:after {
	left: 54px;
	-moz-box-shadow: -9px 17px 18px rgba(0, 0, 0, 0.2);	
	-webkit-box-shadow: -14px 17px 26px rgba(0, 0, 0, 0.2);
	box-shadow: -9px 17px 18px rgba(0, 0, 0, 0.2);
}


/* NORMAL */
.BSmall.BNormal {
	-moz-box-shadow: 0 52px 26px -36px rgba(0,0,0,0.8);
	-webkit-box-shadow: 0 52px 30px -39px rgba(0,0,0,0.8);
	box-shadow: 0 52px 30px -39px rgba(0,0,0,0.8);
}

.BSmall.BNormal:before {
	right: 54px;
	-moz-box-shadow: 9px 17px 18px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 14px 17px 26px rgba(0, 0, 0, 0.3);
	box-shadow: 9px 17px 18px rgba(0, 0, 0, 0.3);
}

.BSmall.BNormal:after {
	left: 54px;
	-moz-box-shadow: -9px 17px 18px rgba(0, 0, 0, 0.3);	
	-webkit-box-shadow: -14px 17px 26px rgba(0, 0, 0, 0.3);
	box-shadow: -9px 17px 18px rgba(0, 0, 0, 0.3);
}


/* DARK */
.BSmall.BDark {
	-moz-box-shadow: 0 52px 26px -36px rgba(0,0,0,0.9);
	-webkit-box-shadow: 0 52px 30px -39px rgba(0,0,0,0.9);
	box-shadow: 0 52px 30px -39px rgba(0,0,0,0.9);
}

.BSmall.BDark:before {
	right: 54px;
	-moz-box-shadow: 9px 17px 18px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 14px 17px 26px rgba(0, 0, 0, 0.4);
	box-shadow: 9px 17px 18px rgba(0, 0, 0, 0.4);
}

.BSmall.BDark:after {
	left: 54px;
	-moz-box-shadow: -9px 17px 18px rgba(0, 0, 0, 0.4);	
	-webkit-box-shadow: -14px 17px 26px rgba(0, 0, 0, 0.4);
	box-shadow: -9px 17px 18px rgba(0, 0, 0, 0.4);
}



/*** MEDIUM SHADOW STYLES ***/

/* LIGHT */
.BMedium.BLight {
	-moz-box-shadow: 0 58px 26px -36px rgba(0,0,0,0.7);
	-webkit-box-shadow: 0 60px 44px -39px rgba(0,0,0,0.7);
	box-shadow: 0 58px 30px -39px rgba(0,0,0,0.7);
}

.BMedium.BLight:before {
	right: 60px;
	-moz-box-shadow: 9px 25px 18px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: 14px 25px 26px rgba(0, 0, 0, 0.2);
	box-shadow: 9px 25px 18px rgba(0, 0, 0, 0.2);
}

.BMedium.BLight:after {
	left: 60px;
	-moz-box-shadow: -9px 25px 18px rgba(0, 0, 0, 0.2);	
	-webkit-box-shadow: -14px 25px 26px rgba(0, 0, 0, 0.2);
	box-shadow: -9px 25px 18px rgba(0, 0, 0, 0.2);
}


/* NORMAL */
.BMedium.BNormal {
	-moz-box-shadow: 0 58px 26px -36px rgba(0,0,0,0.8);
	-webkit-box-shadow: 0 60px 44px -39px rgba(0,0,0,0.8);
	box-shadow: 0 58px 30px -39px rgba(0,0,0,0.8);
}

.BMedium.BNormal:before {
	right: 60px;
	-moz-box-shadow: 9px 25px 18px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 14px 25px 26px rgba(0, 0, 0, 0.3);
	box-shadow: 9px 25px 18px rgba(0, 0, 0, 0.3);
}

.BMedium.BNormal:after {
	left: 60px;
	-moz-box-shadow: -9px 25px 18px rgba(0, 0, 0, 0.3);	
	-webkit-box-shadow: -14px 25px 26px rgba(0, 0, 0, 0.3);
	box-shadow: -9px 25px 18px rgba(0, 0, 0, 0.3);
}


/* DARK */
.BMedium.BDark {
	-moz-box-shadow: 0 58px 26px -36px rgba(0,0,0,0.9);
	-webkit-box-shadow: 0 60px 44px -39px rgba(0,0,0,0.9);
	box-shadow: 0 58px 30px -39px rgba(0,0,0,0.9);
}

.BMedium.BDark:before {
	right: 60px;
	-moz-box-shadow: 9px 25px 18px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 14px 25px 26px rgba(0, 0, 0, 0.4);
	box-shadow: 9px 25px 18px rgba(0, 0, 0, 0.4);
}

.BMedium.BDark:after {
	left: 60px;
	-moz-box-shadow: -9px 25px 18px rgba(0, 0, 0, 0.4);	
	-webkit-box-shadow: -14px 25px 26px rgba(0, 0, 0, 0.4);
	box-shadow: -9px 25px 18px rgba(0, 0, 0, 0.4);
}



/*** LARGE SHADOW STYLES ***/

/* LIGHT */
.BLarge.BLight {
	-moz-box-shadow: 0 64px 26px -36px rgba(0,0,0,0.7);
	-webkit-box-shadow: 0 66px 58px -39px rgba(0,0,0,0.7);
	box-shadow: 0 64px 30px -39px rgba(0,0,0,0.7);
}

.BLarge.BLight:before {
	right: 64px;
	-moz-box-shadow: 9px 30px 18px rgba(0, 0, 0, 0.2);
	-webkit-box-shadow: 10px 32px 26px rgba(0, 0, 0, 0.2);
	box-shadow: 9px 28px 18px rgba(0, 0, 0, 0.2);
}

.BLarge.BLight:after {
	left: 64px;
	-moz-box-shadow: -9px 30px 18px rgba(0, 0, 0, 0.2);	
	-webkit-box-shadow: -10px 32px 26px rgba(0, 0, 0, 0.2);
	box-shadow: -9px 28px 18px rgba(0, 0, 0, 0.2);
}


/* NORMAL */
.BLarge.BNormal {
	-moz-box-shadow: 0 64px 26px -36px rgba(0,0,0,0.8);
	-webkit-box-shadow: 0 66px 58px -39px rgba(0,0,0,0.8);
	box-shadow: 0 64px 30px -39px rgba(0,0,0,0.8);	
}

.BLarge.BNormal:before {
	right: 64px;
	-moz-box-shadow: 9px 30px 18px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 10px 32px 26px rgba(0, 0, 0, 0.3);
	box-shadow: 9px 28px 18px rgba(0, 0, 0, 0.3);	
}

.BLarge.BNormal:after {
	left: 64px;
	-moz-box-shadow: -9px 30px 18px rgba(0, 0, 0, 0.3);	
	-webkit-box-shadow: -10px 32px 26px rgba(0, 0, 0, 0.3);
	box-shadow: -9px 28px 18px rgba(0, 0, 0, 0.3);	
}


/* DARK */
.BLarge.BDark {
	-moz-box-shadow: 0 64px 26px -36px rgba(0,0,0,0.9);
	-webkit-box-shadow: 0 66px 58px -39px rgba(0,0,0,0.9);
	box-shadow: 0 64px 30px -39px rgba(0,0,0,0.9);
}

.BLarge.BDark:before {
	right: 64px;
	-moz-box-shadow: 9px 30px 18px rgba(0, 0, 0, 0.4);
	-webkit-box-shadow: 10px 32px 26px rgba(0, 0, 0, 0.4);
	box-shadow: 9px 28px 18px rgba(0, 0, 0, 0.4);
}

.BLarge.BDark:after {
	left: 64px;
	-moz-box-shadow: -9px 30px 18px rgba(0, 0, 0, 0.4);	
	-webkit-box-shadow: -10px 32px 26px rgba(0, 0, 0, 0.4);
	box-shadow: -9px 28px 18px rgba(0, 0, 0, 0.4);
}





/******************************************************/
/****** HOVER SHADOW ******/

.HoverShadow {
	position: relative;
}

.HoverShadow:after {
	content: '';
	position: absolute;
	z-index: -1;
	bottom: 0;		
	height: 20px;	
}

.HoverShadow.safari:after {
	-webkit-box-shadow: none;
	box-shadow: none;
}

.HoverShadow.safari .after {
	position: absolute;
	z-index: -1;
	bottom: 0;		
	height: 20px;
}


/*** SHADOW PROJECTION LENGTH ***/

.HSmall:after, .HSmall.safari .after {
	width: 80%;
	left: 10%;
}

.HSmall:after {
	-moz-border-radius: 40%/10px;
	-webkit-border-radius: 40%/10px;
	border-radius: 40%/10px;
}

.HMedium:after, .HMedium.safari .after {
	width: 90%;
	left: 5%;	
}

.HMedium:after {
	-moz-border-radius: 45%/10px;
	-webkit-border-radius: 45%/10px;
	border-radius: 45%/10px;
}

.HLarge:after, .HLarge.safari .after {
	width: 100%;
	left: 0;	
}

.HLarge:after {
	-moz-border-radius: 50%/10px;
	-webkit-border-radius: 50%/10px;
	border-radius: 50%/10px;
}


/*** SHADOW INTENSITY ***/

.HLight:after, .HLight.safari .after {
	-moz-box-shadow: 0 50px 15px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 0 50px 15px rgba(0, 0, 0, 0.3);
	box-shadow: 0 50px 15px rgba(0, 0, 0, 0.3);
}

.HNormal:after, .HNormal.safari .after {
	-moz-box-shadow: 0 50px 15px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 0 50px 15px rgba(0, 0, 0, 0.5);
	box-shadow: 0 50px 15px rgba(0, 0, 0, 0.5);
}

.HDark:after, .HDark.safari .after {
	-moz-box-shadow: 0 50px 15px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 0 50px 15px rgba(0, 0, 0, 0.7);
	box-shadow: 0 50px 15px rgba(0, 0, 0, 0.7);
}





/******************************************************/
/****** STAND SHADOW ******/

.StandShadow {
	position: relative;
}

.StandShadow:after {
	content: '';
	position: absolute;
	z-index: -1;
	bottom: 40px;		
	height: 40px;
}

.StandShadow.safari:after {
	display: none;
}

.StandShadow.safari .after {
	position: absolute;
	z-index: -1;
	bottom: 40px;		
	height: 40px;
}


/*** SHADOW PROJECTION LENGTH ***/

.SSmall:after, .SSmall.safari .after {
	width: 105%;
	left: -2.5%;
}

.SSmall:after {
	-moz-border-radius: 52.5%/20px;
	-webkit-border-radius: 52.5%/20px;
	border-radius: 52.5%/20px;
}

.SMedium:after, .SMedium.safari .after {
	width: 110%;
	left: -5%;	
}

.SMedium:after {
	-moz-border-radius: 55%/20px;
	-webkit-border-radius: 55%/20px;
	border-radius: 55%/20px;
}

.SLarge:after, .SLarge.safari .after {
	width: 115%;
	left: -7.5%;	
}

.SLarge:after {
	-moz-border-radius: 57.5%/20px;
	-webkit-border-radius: 57.5%/20px;
	border-radius: 57.5%/20px;
}


/*** SHADOW INTENSITY ***/

.SLight:after, .SLight.safari .after {
	-moz-box-shadow: 0 60px 15px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 0 60px 15px rgba(0, 0, 0, 0.3);
	box-shadow: 0 60px 15px rgba(0, 0, 0, 0.3);
}

.SNormal:after, .SNormal.safari .after {
	-moz-box-shadow: 0 60px 15px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 0 60px 15px rgba(0, 0, 0, 0.5);
	box-shadow: 0 60px 15px rgba(0, 0, 0, 0.5);
}

.SDark:after, .SDark.safari .after {
	-moz-box-shadow: 0 60px 15px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 0 60px 15px rgba(0, 0, 0, 0.7);
	box-shadow: 0 60px 15px rgba(0, 0, 0, 0.7);
}





/******************************************************/
/****** FOLD SHADOW ******/

.FoldShadow {
    position: relative;
}

.FoldShadow:after {
	content: '';
	position: absolute;
	z-index: -2;	
	left: 2%;
	width: 96%;
	height: 60px;
	-moz-border-radius: 48%/30px;
	-webkit-border-radius: 48%/30px;
	border-radius: 48%/30px;
}

.FoldShadow:before {
	content: '';
	position: absolute;
	z-index: -1;
	left: 30%;
	width: 40%;
	height: 60px;
	-moz-border-radius: 15%/30px;
	-webkit-border-radius: 15%/30px;
	border-radius: 15%/30px;	
}

.FoldShadow.safari:before, .FoldShadow.safari:after {
	display: none;
}

.FoldShadow.safari .after {
	position: absolute;
	z-index: -2;	
	left: 2%;
	width: 96%;
	height: 60px;
}

.FoldShadow.safari .before {
	position: absolute;
	z-index: -1;
	left: 30%;
	width: 40%;
	height: 60px;
}


/*** SHADOW PROJECTION LENGTH ***/

.FSmall:after, .FSmall.safari .after {
	bottom: 28px;
}

.FSmall:before, .FSmall.safari .before {
	bottom: 48px;
}

.FMedium:after, .FMedium.safari .after {
	bottom: 24px;
}

.FMedium:before, .FMedium.safari .before {
	bottom: 44px;
}

.FLarge:after, .FLarge.safari .after {	
	bottom: 20px;	
}

.FLarge:before, .FLarge.safari .before {
	bottom: 40px;;
}


/*** SHADOW INTENSITY ***/

.FLight:after, .FLight.safari .after {
	-moz-box-shadow: 0 40px 15px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 0 40px 15px rgba(0, 0, 0, 0.5);
	box-shadow: 0 40px 15px rgba(0, 0, 0, 0.5);
}

.FLight:before, .FLight.safari .before {
	-moz-box-shadow: 0 50px 50px rgba(255, 255, 255, 0.4);
	-webkit-box-shadow: 0 50px 50px rgba(255, 255, 255, 0.4);
	box-shadow: 0 50px 50px rgba(255, 255, 255, 0.4);
}

.FNormal:after, .FNormal.safari .after {
	-moz-box-shadow: 0 40px 15px rgba(0, 0, 0, 0.6);
	-webkit-box-shadow: 0 40px 15px rgba(0, 0, 0, 0.6);
	box-shadow: 0 40px 15px rgba(0, 0, 0, 0.6);
}

.FNormal:before, .FNormal.safari .before {
	-moz-box-shadow: 0 50px 50px rgba(255, 255, 255, 0.5);
	-webkit-box-shadow: 0 50px 50px rgba(255, 255, 255, 0.5);
	box-shadow: 0 50px 50px rgba(255, 255, 255, 0.5);
}

.FDark:after, .FDark.safari .after {
	-moz-box-shadow: 0 40px 15px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 0 40px 15px rgba(0, 0, 0, 0.7);
	box-shadow: 0 40px 15px rgba(0, 0, 0, 0.7);
}

.FDark:before, .FDark.safari .before {
	-moz-box-shadow: 0 50px 50px rgba(255, 255, 255, 0.6);
	-webkit-box-shadow: 0 50px 50px rgba(255, 255, 255, 0.6);
	box-shadow: 0 50px 50px rgba(255, 255, 255, 0.6);
}





/******************************************************/
/****** RIGHT SIDE CURL SHADOW ******/

.RightCurlShadow {
	position: relative;	
}

.RightCurlShadow:before {
	content: '';
	position: absolute;
	z-index: -1;
	top: 55px;
	right: 58px;
	width: 50px;	
	-moz-transform: rotate(4deg) skew(-4deg);
	-webkit-transform: rotate(4deg) skew(-4deg);
	-o-transform: rotate(4deg) skew(-4deg);
	transform: rotate(4deg) skew(-4deg);
}


/*** SHADOW PROJECTION LENGTH ***/

.RCSmall:before {	
	height: 60%;
}

.RCMedium:before {	
	height: 70%;
}

.RCLarge:before {	
	height: 80%;
}


/*** SHADOW INTENSITY ***/

.RCLight:before {
	-moz-box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.3);
	box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.3);	
}

.RCNormal:before {
	-moz-box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.5);
	box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.5);
}

.RCDark:before {
	-moz-box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.7);
	box-shadow: 50px -40px 20px rgba(0, 0, 0, 0.7);
}




/******************************************************/
/****** LEFT SIDE CURL SHADOW ******/

.LeftCurlShadow {
	position: relative;
}

.LeftCurlShadow:before {
	content: '';
	position: absolute;
	z-index: -1;
	top: 55px;
	left: 58px;
	width: 50px;	
	-moz-transform: rotate(-4deg) skew(4deg);
	-webkit-transform: rotate(-4deg) skew(4deg);
	-o-transform: rotate(-4deg) skew(4deg);
	transform: rotate(-4deg) skew(4deg);
}


/*** SHADOW PROJECTION LENGTH ***/

.LCSmall:before {	
	height: 60%;
}

.LCMedium:before {	
	height: 70%;
}

.LCLarge:before {	
	height: 80%;
}


/*** SHADOW INTENSITY ***/

.LCLight:before {
	-moz-box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.3);
	-webkit-box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.3);
	box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.3);	
}

.LCNormal:before {
	-moz-box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.5);
	-webkit-box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.5);
	box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.5);
}

.LCDark:before {
	-moz-box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.7);
	-webkit-box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.7);
	box-shadow: -50px -40px 20px rgba(0, 0, 0, 0.7);
}#main_body form li.hide_cents .sub_currency{
	display: none;
}
#main_body form ul.payment_detail_form li span.description{
	margin-bottom: 5px;
}
#stripe-card-element {
	border: 4px solid #EFEFEF;
	border-radius: 8px;
	box-shadow: 0 0 0 1px rgba(0, 0, 0, 0.2) inset;
	outline: medium none;
	background: none repeat scroll 0 0 #FBFBFB;
	padding: 0 0 0 5px !important;
	color:#666666;
	font-size:100%;
	margin-bottom:15px !important;
	width: 75%;
}
#main_body form li.media{
	width: 97% !important;
}

@media only screen and (max-width : 480px) {
	html{
		background: none;
	}
	#main_body{
		margin: 0 !important;
	}
	#main_body h1 a{
		background-size: 100% 100%;
		height: auto;
	}
	#form_container,#footer{
		width: 100% !important;
		padding: 0px !important;
		margin: 0 auto !important;
	}
	#form_container{
		box-shadow: none !important;
		border: none !important;
	}
	#form_container:before,#form_container:after{
		display: none !important;
	}
	form.appnitro,.embed form.appnitro{
		margin: 15px 15px 0 15px;
	}
	#top,#bottom{
		display: none;
	}
	#li_resume_email{
		width: 99%;
	}
	#main_body #element_resume_email{
		width: 70%;
	}
	#main_body form li{
		padding: 4px 2px 2px;
	}
	.no_guidelines form li,.appnitro li{
		width: 99%;
	}
	#main_body form li.column_2,
	#main_body form li.column_3,
	#main_body form li.column_4,
	#main_body form li.column_5,
	#main_body form li.column_6{
		width: 99% !important;
		float: none;
	}
	
	#main_body input.text{
		padding: 5px 3px;
	}
	#main_body input.medium{
		width:70%;
	}
	#main_body input.large,#main_body textarea.textarea{
		width: 98%;
	}
	#main_body form li .guidelines{
		background: none !important;
		border: none !important;
		font-size:105%;
		line-height:100%;
		margin: 0 !important;
	    padding: 0 0 5px;
		visibility:visible;
		width:100%;
		position: static;
		clear: both;
	}
	.password input.text {
		width: 90%;
	}
	/** Label Alignment **/
	#main_body form.left_label li, #main_body form.right_label li{
		padding-top: 4px;
		width: 99%;
	}
	.no_guidelines form.left_label li, .no_guidelines form.right_label li{
		width: 99% !important;
	}
	#main_body form.left_label label.description,#main_body form.right_label label.description,
	#main_body form.left_label span.description,#main_body form.right_label span.description{
		float: none;
		margin: 0;
		width: 100%;
		text-align: left;
	}
	.no_guidelines form.left_label label.description,
	.no_guidelines form.right_label label.description,
	.no_guidelines form.left_label span.description,
	.no_guidelines form.right_label span.description,
	#main_body form.left_label .guidelines,
	#main_body form.right_label .guidelines{
		width: 100% !important;
	}

	#main_body form.left_label li div, #main_body form.right_label li div
	{
		float: none;
		width: 100%;
	}

	/** Multiple Choice and Checkboxes **/
	#main_body form li.two_columns div span,
	#main_body form li.three_columns div span,
	#main_body form li.inline_columns div span{
	  	margin:0;
	}
	
	#main_body form li.multiple_choice div span,
	#main_body form li.checkboxes div span{
		border-bottom: 1px solid #ccc;
		border-left: 1px solid #ccc;
		border-right: 1px solid #ccc;
		padding: 5px 2px 5px 10px;
		width: 96%;
	}
	#main_body form li.multiple_choice div fieldset span:first-of-type,
	#main_body form li.checkboxes div span:first-child{
		border: 1px solid #ccc;
		border-top-left-radius: 10px;
		border-top-right-radius: 10px;
	}
	#main_body form li.multiple_choice div fieldset span:last-child,
	#main_body form li.checkboxes div span:last-child{
		border-bottom-left-radius: 10px;
		border-bottom-right-radius: 10px;
	}

	#main_body form li.multiple_choice .guidelines,
	#main_body form li.checkboxes .guidelines{
		clear: both;
		padding-top: 10px;
	}

	#main_body input.radio,#main_body input.checkbox{
    	visibility: hidden;
	}

	#main_body input[type="radio"] + label::before,
	#main_body input[type="checkbox"] + label::before{
	    content: '';
	    display:inline-block;
	    position: absolute;
	    width:1.2em;
	    height:1.2em;
	    margin: 1px 0 0 -23px;
	    cursor:pointer;
	    vertical-align:middle;
	    background-color:#ccc;
	    border-radius: 0.8em;
	    
	}
	#main_body input[type="checkbox"] + label::before{
		border-radius: 0.3em;
	}
	#main_body input[type="radio"]:checked + label::before {
		background-color: #4596CE;
	}
	#main_body input[type="checkbox"]:checked + label::before {
		width:0.5em;
    	height:0.9em;
		background-color: transparent;
    	border-bottom: 0.5em solid #4596CE;
    	border-right: 0.5em solid #4596CE;
    	-webkit-transform: translate(2px,-2px)  rotateZ(45deg);
    	-moz-transform: translate(2px,-2px)  rotateZ(45deg);
    	transform: translate(2px,-2px)  rotateZ(45deg);
	}
	#main_body input.other{
		width: 83%;
	}

	/** Matrix **/
	#main_body li.matrix td{
		padding: 6px 0;
	}
	#main_body li.matrix input[type="radio"]{
		background-color: #ccc;
		border-color: #ccc; 
	}
	#main_body li.matrix input[type="checkbox"]{
		background-color: #ccc;
		border-color: #ccc; 
	}
	#main_body li.matrix input[type="radio"]:checked{
		background-color: #4596CE;
		border-color: #4596CE; 
	}
	
	/** Name fields **/
	#main_body li.simple_name .simple_name_1{
		width: 42%;
	}
	#main_body li.simple_name .simple_name_2{
		width: 56%;
		margin-right: 0px;
	}
	#main_body li.simple_name .simple_name_1 input.text{
		width: 94%;
	}
	#main_body li.simple_name .simple_name_2 input.text{
		width: 96%;
	}
	
	#main_body li.simple_name_wmiddle input.text{
		width: 90%;
	}
	#main_body li.simple_name_wmiddle .simple_name_wmiddle_3 input.text{
		width: 95%;
	}
	#main_body li.simple_name_wmiddle .simple_name_wmiddle_1,
	#main_body li.simple_name_wmiddle .simple_name_wmiddle_2{
		width: 25%;
	}
	#main_body li.simple_name_wmiddle .simple_name_wmiddle_3{
		width: 47%;
		margin-right: 0px;
	}

	#main_body li.fullname .fullname_2 input.text{
		width: 91%;
	}
	#main_body li.fullname .fullname_3 input.text{
		width: 93%;
	}
	#main_body li.fullname .fullname_1 input.text,
	#main_body li.fullname .fullname_4 input.text{
		width: 77%;
	}
	#main_body li.fullname .fullname_1{
		width: 12%;
	}
	#main_body li.fullname .fullname_2{
		width: 30%;
	}
	#main_body li.fullname .fullname_3{
		width: 42%;
	}
	#main_body li.fullname .fullname_4{
		width: 12%;
		margin-right: 0px;
	}

	#main_body li.fullname_wmiddle input.text{
		width: 90%;
	}
	#main_body li.fullname_wmiddle .namewm_ext input.text{
		width: 79%;
	}
	#main_body li.fullname_wmiddle .namewm_last input.text{
		width: 92%;
	}
	#main_body li.fullname_wmiddle .namewm_ext{
		width: 9%;
	}
	#main_body li.fullname_wmiddle .namewm_first{
		width: 25%;
	}
	#main_body li.fullname_wmiddle .namewm_middle{
		width: 25%;
	}
	#main_body li.fullname_wmiddle .namewm_last{
		width: 26%;
	}
	#main_body select.select{
		height: 36px;
	}

	/** Time field **/
	#main_body li.time_field input.text{
		width: 35px;
	}

	/** Address field **/
	#main_body li.address input.large{
		width: 98%;
	}
	#main_body form li div span.state_list{
		height: 46px;
	}
	#main_body li.address select.select{
		width: 105%;
	}

	/** Signature Field **/
	#main_body form li div.mf_sig_wrapper{
		-webkit-transform: scaleX(0.9);
    	-moz-transform: scaleX(0.9);
    	transform: scaleX(0.9);
    	margin-left: -16px;
	}
	.mf_sigpad_clear{
		margin-left: 250px;
	}
	.mf_sigpad_view{
		-webkit-transform: scale(0.65,0.65);
    	-moz-transform: scale(0.65,0.65);
    	transform: scale(0.65,0.65);
    	margin-left: -55px;
    	margin-top: -20px;
	}

	/** Date Field **/
	#main_body li.date_field .date_mm input.text,
	#main_body li.date_field .date_dd input.text,
	#main_body li.europe_date_field .date_mm input.text,
	#main_body li.europe_date_field .date_dd input.text{
		width: 35px;
	}
	#main_body li.date_field .date_yyyy input.text,
	#main_body li.europe_date_field .date_yyyy input.text{
		width: 70px;
	}
	#main_body li img.datepicker{
		-webkit-transform: scale(1.5,1.5);
    	-moz-transform: scale(1.5,1.5);
    	transform: scale(1.5,1.5);
    	margin-left: 5px;
    	margin-top: 5px;
	}
	#main_body form li.date_field label{
		margin-left: 10px;
	}

	/** Phone Field **/
	#main_body li.phone input.text{
		width: 35px;
	}
	#main_body li.phone .phone_3 input.text{
		width: 70px;
	}
	#main_body li.phone .phone_1,
	#main_body li.phone .phone_2{
		width: 20%;
	}
	#main_body li.phone .phone_3{
		width: 30%;
	}
	#main_body form li.phone label{
		margin-left: 10px;
	}

	/** Payment Page **/
	#main_body form li.payment_summary_list{
		width: 60%;
		margin-right: 8px;
	}
	#main_body form li.credit_card div span#li_cc_span_4{
		margin-right: 3px;
	}
	#cc_expiry_month{
		width: 38%;
	}
	#cc_expiry_year{
		width: 22%;
	}
	#stripe-card-element {
		width: 98%;
	}
	/** File Upload **/
	.uploadifive-queue-item{
		width: 95%;
	} 
	/** reCAPTCHA **/
	#rc-imageselect, .g-recaptcha {
	   transform:scale(0.77);
	  -webkit-transform:scale(0.77);
	   transform-origin:0 0;
	  -webkit-transform-origin:0 0;
	}
	/** Media Video **/
	#main_body form li div.media_video_container,
	#main_body form li div.media_video_container.small,
	#main_body form li div.media_video_container.medium,
	#main_body form li div.media_video_container.large{
		width: 99%;
	}
}

/** DO NOT MODIFY THIS FILE. All code here are generated by MachForm Theme Editor **/
#main_body h1 a
{
background-image: url('https://trb.nu/clubredders/logo-clubforms.png');
height: 80px;
}

html
{
background-color: #f0f0f0;
background-image: none;
}

#main_body h1
{
background-color: #ffffff;
background-image: none;
}

#form_container
{
background-color: #ffffff;
border-width: 0px;
border-style: solid;
border-color: #CCCCCC;
}

#main_body form li.highlighted,#main_body .matrix tbody tr:hover td,#machform_review_table tr.alt
{
background-color: #f0f0f0;
}

#main_body form .guidelines
{
background-color: #f0f0f0;
border-width: 1px;
border-style: solid;
border-color: #CCCCCC;
}

#main_body form .guidelines small
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 80%;
color: #444444;
}

#main_body input.text,#main_body input.file,#main_body textarea.textarea,#main_body select.select,#main_body input.checkbox,#main_body input.radio
{
background-color: #ffffff;
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 100%;
color: #666666;
}

#machform_review_table td.mf_review_value
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 100%;
color: #444444;
}

#main_body .form_description h2,#main_body .form_success h2
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 160%;
color: #444444;
}

#main_body .form_description p,#main_body form ul.payment_list_items li
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 95%;
color: #444444;
}

#main_body form li span.ap_tp_text
{
color: #444444;
}

#main_body form li label.description,#main_body form li span.description,#main_body .matrix caption,#main_body .matrix td.first_col,#main_body form li.total_payment span,#machform_review_table td.mf_review_label
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 700;
font-style: normal;
font-size: 95%;
color: #444444;
}

#main_body form li span label,#main_body label.choice,#main_body .matrix th,#main_body form li span.symbol,.mf_sigpad_clear,#main_body form li div label,#main_body form li div span.label
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
color: #444444;
}

#main_body form .section_break h3,#main_body form .media h3,#machform_review_table td .mf_section_title
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 110%;
color: #444444;
}

#main_body form .section_break p,#main_body form .media p,#machform_review_table td .mf_section_content
{
font-family: 'Lucida Grande','Lucida Grande',Tahoma,Arial,sans-serif;
font-weight: 400;
font-style: normal;
font-size: 85%;
color: #444444;
}

#main_body form li.section_break
{
border-top-width: 1px;
border-top-style: dotted;
border-top-color: #CCCCCC;
}
</style>
</head>
<body id="main_body">
	<div id="form_container" class="">
	
		<h1><a>Club.Forms</a></h1>
			
		<div class="form_success">
			<h2>Hoppa!<br><br>Het geld is onderweg,<br>Nog even goedkeuren, betalen en van een extra krabbel voorzien...</h2><br><h3>Klik nu <a href="https://trb.nu/mijn-trb-nu" target="_self">hier</a> om terug te gaan naar Mijn TRB.nu en/of nog een declaratie in te dienen.</h3>
		</div>
		<div id="footer" class="success">
			
		</div>		
	</div>

</body>
</html>