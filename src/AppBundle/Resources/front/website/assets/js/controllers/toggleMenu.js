"use strict";

export default class ToggleMenu{
	constructor(){
		this.el = $("#toggle-menu");
		this.menu = $("#menu-holder");
		this.bindUIevents();
	}
	bindUIevents(){
		this.el.on("click touchstart", $.proxy(this.handleMenu, this));
	}
	handleMenu(e){
		this.menu.toggleClass("active");
		return false;
	}
}