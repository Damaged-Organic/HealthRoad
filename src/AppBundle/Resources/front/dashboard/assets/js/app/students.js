"use strict";

import $ from "jquery";
import validate from "jquery-validation";
import mask from "jquery-mask-plugin";
import selectify from "../lib/select";
import Menu from "../controllers/menu";
import Confirm from "../controllers/confirm";
import Status from "../controllers/statusPanel";
import BankAccount from "../controllers/bankAccount";

window.$ = $;

$(function(){

    new Menu();
    new Status();
    new Confirm();
    new BankAccount();

    $(".select-holder").selectify();
    $("form").validate();
});