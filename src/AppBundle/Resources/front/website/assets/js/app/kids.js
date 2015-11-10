"use strict";

import $  from "jquery";
import mask from "jquery-mask-plugin";
import validate from "jquery-validation";
import LimitController from "../controllers/limitController";

window.$ = $;

$(function(){

	new LimitController();
	$("form").validate();
});