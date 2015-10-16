(function($, root){

	"use strict";

	var pluginName = "selectify",
		_defaults = {};

	function Plugin(el, options){
		this.el = $(el);
		this._options = $.extend({}, _defaults, options);
		
		this.initialize.apply(this, arguments);
	}
	Plugin.prototype = {
		initialize,
		_events,
		handleOption,
		openList,
		closeList
	}
	function initialize(){
		this._events();
	}
	function _events(){
		this.el
			.on("click", ".btn-select", $.proxy(this.openList, this))
			.on("click", ".select-list", $.proxy(this.closeList, this))
			.on("click", ".option-item", $.proxy(this.handleOption, this));
	}
	function handleOption(e){
		var	btnSelect = this.el.find(".btn-select"),
			hdnInput = this.el.find("input[type=hidden]"),
			target = $(e.target).closest(".option-item"),
			optionID = target.data("option"),
			optionName = target.find(".option-name").text();

		if(isNaN(optionID)) return;
		hdnInput.val(optionID);
		btnSelect.find(".text").text(optionName);
		target.addClass("active").siblings(".option-item").removeClass("active");
		this.closeList();

		return false;
	}
	function openList(e){
		this.el.find(".select-list").addClass("active");
		return false;
	}
	function closeList(e){
		this.el.find(".select-list").removeClass("active");
	}

	$.fn[pluginName] = function(options){
		return this.each(function(){
			if(!$.data(this, "plugin-selectify")){
				$.data(this, "plugin-selectify", new Plugin(this, options));
			}
		});
	}

})($, window);