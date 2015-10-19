"use strict";

import $ from "jquery";
import validate from "jquery-validation";
import mask from "jquery-mask-plugin";
import selectify from "../lib/select";
import Menu from "../controllers/menu";
import Confirm from "../controllers/confirm";
import Bind from "../controllers/bindController";
import Status from "../controllers/statusPanel";
import PhotoPreview from "../controllers/PhotoPreview";

window.$ = $;

$(function(){

	new Menu();
	new Bind();
	new Status();
	new Confirm();
	new PhotoPreview();

	$(".select-holder").selectify();
	$("form").validate();
});