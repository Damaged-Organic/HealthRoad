var app = app || {};

(function(app, $, root){

	"use strict";

	function MovieController(){
		this.el = $("#movie-holder");
		this.initialize.apply(this, arguments);
	}
	MovieController.prototype = {
		button: [],
		isStarted: false,
		initialize: initialize,
		_events: _events,
		handleMovie: handleMovie,
		closeMovie: closeMovie
	}

	function initialize(){
		this.button = $("#watch-movie");
		this._events();
	}
	function _events(){
		this.button.on("click", $.proxy(this.handleMovie, this));
		this.el.on("click", $.proxy(this.closeMovie, this));
	}
	function handleMovie(e){
		if(this.isStarted) return;

		var self = this;
		this.el.find(".inner").html('<iframe src="https://www.youtube.com/embed/b_eY9fmbUAM?autoplay=1" width="100%" height="100%" allowfullscreen="1" frameborder="0"></iframe>');

		root.setTimeout(function(){
			self.isStarted = true;
			self.el.addClass("active");
		}, 100);
		return false;
	}
	function closeMovie(e){
		var target = $(e.target);

		if(target.closest(".inner").length <= 0 || target.hasClass("close")){
			this.el.removeClass("active").find(".inner").empty();
		}
		this.isStarted = false;

		return false;
	}

	app.MovieController = MovieController;

})(app, jQuery, window);