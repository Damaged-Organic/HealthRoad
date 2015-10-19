"use strict";

export default class StatusPanel{
	constructor(){
		this.el = $("#status-panel-holder");
		this.initialize.apply(this, arguments);
	}
	initialize(){
		this._events();
	}
	_events(){
		this.el.on("click", $.proxy(this.closePanel, this));
	}
	closePanel(e){
		this.el.removeClass("success-active warning-active error-active info-active");
	}
}