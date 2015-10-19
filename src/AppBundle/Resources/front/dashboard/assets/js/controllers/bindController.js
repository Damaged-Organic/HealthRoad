"use strict";

import BindService from "../services/bindService";

class BindController{
	constructor(){
		this.el = $(".btn-bind");
		this.loader = $("#toggle-menu-loader");
		this.statusPanel = $("#status-panel-holder");

		this.initialize.apply(this, arguments);
	}
	initialize(){
		this._events();
		this.service = BindService;
	}
	_events(){
		this.el.on("click", $.proxy(this.handleBind, this));
	}
	handleBind(e){
		var self = this,
			target = $(e.target).closest(".btn-bind"),
			isBinded = this.checkBind(target),
			entity = target.data("entity"),
			parentID = target.data("parentId"),
			childID = target.data("childId"),
			path = target.data("path");

		this.loader.addClass("loading");
		this.statusPanel.removeClass("success-active warning-active error-active info-active");
		this.service
			._send(entity, parentID, childID, isBinded, path)
			.done(function(response){
				response = JSON.parse(response);
				isBinded ? target.removeClass("binded") : target.addClass("binded");
				self.showNotification("success", response.message);
			})
			.fail(function(error){
				self.showNotification("error", error.responseText);
			})
			.always(function(){
				self.loader.removeClass("loading");
			});

		return false;
	}
	checkBind(target){
		return target.hasClass("binded") ? true : false;
	}
	showNotification(status, msg){
		var panel = this.statusPanel.find(`.inner.${ status }-holder`);
		
		panel.html(`<span>${ msg }</span>`);
		this.statusPanel.removeClass("success-active warning-active error-active info-active").addClass(`${ status }-active`);
	}
}

export default BindController;
