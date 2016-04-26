"use strict";

import $ from "jquery";
import validate from "jquery-validation";
import PasswordController from "../controllers/passwordController";
import PersonalController from "../controllers/personalController";
import Uncheck from "../lib/uncheck";

$(function(){

    $("form").validate();
    new PersonalController();
    new PasswordController();
    new Uncheck();
});
