"use strict";

import $  from "jquery";
import mask from "jquery-mask-plugin";
import validate from "jquery-validation";
import LimitController from "../controllers/limitController";

$(function(){

	$("form").validate();
	new LimitController();

	$("form").on("submit", function(e){
		if(!$(this).valid()) e.preventDefault();
	});

});
