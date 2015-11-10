"use strict";

export default class ProjectMovie{
	constructor(){
		this.el = $("#movie-holder");
		this.initialize.apply(this, arguments);
	}
	initialize(){
		this.isStarted = false;
		this.button = $("#watch-movie");
		this._events();
	}
	_events(){
		this.button.on("click", $.proxy(this.handleMovie, this));
		this.el.on("click", $.proxy(this.closeMovie, this));
	}
	handleMovie(e){
		if(this.isStarted) return;

		var self = this;
		this.el.find(".inner").html('<iframe src="https://www.youtube.com/embed/b_eY9fmbUAM?autoplay=1" width="100%" height="100%" allowfullscreen="1" frameborder="0"></iframe>');

		window.setTimeout(function(){
			self.isStarted = true;
			self.el.addClass("active");
		}, 100);

		return false;
	}
	closeMovie(e){
		var target = $(e.target);

		if(target.closest(".inner").length <= 0 || target.hasClass("close")){
			this.el.removeClass("active").find(".inner").empty();
			this.isStarted = false;
		}
		return false;
	}
}
