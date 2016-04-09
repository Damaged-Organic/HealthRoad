"use strict";

import $ from "jquery";
import validate from "jquery-validation";
import PasswordController from "../controllers/passwordController";
import Uncheck from "../lib/uncheck";

$(function(){

    $("form").validate();
    new PasswordController();
    new Uncheck();
});
