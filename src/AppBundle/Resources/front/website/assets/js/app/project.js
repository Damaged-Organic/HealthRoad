"use strict";

import $ from "jquery";
import mask from "jquery-mask-plugin";
import validate from "jquery-validation";
import ToggleMenu from "../controllers/toggleMenu";

window.$ = $;

$(function(){
	new ToggleMenu();
	$("form").validate();
});