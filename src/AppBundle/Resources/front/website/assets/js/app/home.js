var app = app || {};

$(function(){

	$(":input").inputmask();
	new app.MovieController();
	
});