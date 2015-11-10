"use strict";

import validate from "jquery-validation";

export function directLogin(){
	$("#entrance-form").validate({
		errorPlacement: (error, element) => this,
		highlight: (element, errorClass) => $(element).addClass(errorClass),
		unhighlight: (element, errorClass, validClass) => $(element).removeClass(errorClass)
	});
}