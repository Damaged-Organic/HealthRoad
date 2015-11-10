"use strict";

import $ from "jquery";
import mask from "jquery-mask-plugin";
import validate from "jquery-validation";
import ProjectMovie from "../controllers/movieController";
import {directLogin as login} from "../lib/directLogin";
import ToggleMenu from "../controllers/toggleMenu";

window.$ = $;

$(function(){
	new ProjectMovie();
	new ToggleMenu();
	login();
});