"use strict";

import $ from "jquery";
import ToggleMenu from "../controllers/toggleMenu";

window.$ = $;

$(function(){
	new ToggleMenu();
});