"use strict";

export default class Confirm{
	constructor(){
		this.el = $("#confirm-holder");
		this.buttons = $(".btn-delete");
		this.url = "";

		this.initialize.apply(this, arguments);
	}
	initialize(){
		this._events();
	}
	_events(){
		this.buttons.on("click", $.proxy(this.handleButton, this));
		this.el
			.on("click", $.proxy(this.handleClose, this))
			.on("click", ".confirm-button", $.proxy(this.handleDelete, this));
	}
	handleButton(e){
		this.url = $(e.target).attr("href");
		this.el.addClass("active");

		return false;
	}
	handleDelete(e){
		let target = $(e.target).closest(".confirm-button");

		target.data("confirm") ? window.location.href = this.url : this.el.removeClass("active");
		this.url = "";

		return false;
	}
	handleClose(e){
		if($(e.target).closest(".inner").length <= 0) this.el.removeClass("active");

		return false;
	}
}