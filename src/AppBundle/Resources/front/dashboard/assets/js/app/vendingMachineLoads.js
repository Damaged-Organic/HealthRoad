"use strict";

import $ from "jquery";
import Menu from "../controllers/menu";
import Status from "../controllers/statusPanel";

window.$ = $;

$(function(){

    new Menu();
    new Status();
});