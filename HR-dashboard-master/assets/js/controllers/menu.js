"use strict";

export default class Menu{
	constructor(){
		this.el = $("#page");
		this.initialize.apply(this, arguments);
	}
	initialize(){
		this._events();
	}
	_events(){
		this.el.on("click", ".menu-button", $.proxy(this.handleMenu, this));
	}
	handleMenu(e){
		this.el.toggleClass("menu-active");
		return false;
	}
};