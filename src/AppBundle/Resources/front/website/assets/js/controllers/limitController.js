"use strict";

import LimitService from "../services/limitService";

class LimitController{
	constructor(){
		this.initialize.apply(this, arguments);
	}
	initialize(){
		this.el = $("#limit-form");
		this.limitInput = $("#limit-input");
		this.limitPreview = this.el.find(".limit-preview");
		this.limitButton = $("#change-limit"); 
		this.loader = this.el.find(".loader");

		this._events();
	}
	_events(){
		this.el.on("submit", $.proxy(this.handleForm, this));
		this.limitButton.on("click", $.proxy(this.changeLimit, this));
	}
	handleForm(e){
		e.preventDefault();
		if(!this.el.valid()) return;

		this.loader.addClass("active");
		var self = this,
			formUrl = this.el.attr("action"),
			formData = {data: this.el.serializeArray()};

		LimitService
			.updateLimit(formUrl, formData)
			.done($.proxy(this.success, this))
			.fail($.proxy(this.fail, this));
	}
	changeLimit(e){
		this.el.addClass("active");
		return false;
	}
	success(result){
		result = JSON.parse(result);
		this.limitPreview.html(`${ result.limit } ${ this.limitPreview.data("currency") }`);
		this.limitInput.val(result.limit);
		this.el.removeClass("active");
		this.loader.removeClass("active");
	}
	fail(error){
		let self = this,
			errorHolder = self.el.find(".error");

		errorHolder.html(error.responseText);
		this.loader.removeClass("active");
		
		window.setTimeout(() => {
			self.el.removeClass("active");
			errorHolder.empty();
		}, 3000);
	}
}

export default LimitController;