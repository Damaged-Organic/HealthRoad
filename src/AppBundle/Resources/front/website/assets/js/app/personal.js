"use strict";

import $ from "jquery";
import validate from "jquery-validation";
import PasswordController from "../controllers/passwordController";

$(function(){

    $("form").validate();
    new PasswordController();

});
