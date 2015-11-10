"use strict";

import $ from "jquery";
import mask from "jquery-mask-plugin";
import validate from "jquery-validation";
import ToggleMenu from "../controllers/toggleMenu";

$(function(){
	new ToggleMenu();
	$("form").validate();
});