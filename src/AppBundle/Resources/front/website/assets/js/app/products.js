"use strict";

import $ from "jquery";
import slidy from "../lib/slider";
import ToggleMenu from "../controllers/toggleMenu";

window.$ = $;

$(function(){
	new ToggleMenu();
	$(".carousel-holder").slidy();
});