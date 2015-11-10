import {vendors as v} from "./vendors";

(function($, root, v){

	"use strict";

	var pluginName = "slidy",
		_defaults = {},

		transform = v("transform"),
		transition = v("transition");

	function Plugin(el, options){
		this.el = $(el);
		this._options = $.extend({}, _defaults, options);

		this.initialize.apply(this, arguments);
	}
	Plugin.prototype = {
		slider: [],
		slides: [],
		slideCount: 0,
		slideWidth: 0,
		current: 0,
		position: 0,
		startX: 0,
		moveX: 0,
		deltaX: 0,
		initialize: initialize,
		_events: _events,
		defineDOM: defineDOM,
		setCSS: setCSS,
		handleArrow: handleArrow,
		handleDot: handleDot,
		touchStart: touchStart,
		touchMove: touchMove,
		touchEnd: touchEnd,
		changeSlide: changeSlide,
		handleResize: handleResize
	}
	function initialize(){
		this._events();
		this.defineDOM();
		this.setCSS();

		if(this.slideCount === 1) this.el.addClass("no-nav");
	}
	function _events(){
		this.el
			.on("click", ".arrow", $.proxy(this.handleArrow, this))
			.on("click", ".dot", $.proxy(this.handleDot, this))
			.on("touchstart", ".carousel", $.proxy(this.touchStart, this))
			.on("touchmove", ".carousel", $.proxy(this.touchMove, this))
			.on("touchend", ".carousel", $.proxy(this.touchEnd, this));

		$(window).on("resize", $.proxy(this.handleResize, this));
	}
	function defineDOM(){
		this.carousel = this.el.find(".carousel");
		this.slides = this.el.find(".slide");
		this.slideCount = this.slides.length;
	}
	function setCSS(){
		this.slideWidth = this.el.outerWidth(true);
		this.slides.css({ width: this.slideWidth });
		this.carousel.css({
			width: this.slideWidth * this.slideCount,
			transform: "translate(0, 0)",
			transform: "translate3d(0, 0, 0)"
		});
	}
	function handleArrow(e){
		var target = $(e.target);

		target.hasClass("left") ? this.current-- : this.current++;

		if(this.current >= this.slideCount - 1){
			this.current = this.slideCount - 1
		} else if(this.current <= 0){
			 this.current = 0;
		}
		this.changeSlide();

		return false;
	}
	function handleDot(e){
		this.current = $(e.target).index();
		this.changeSlide();
		return false;
	}
	function touchStart(e){
		this.startX = e.originalEvent.changedTouches[0].pageX;
		this.isAnimate = true;
		this.carousel.removeClass("animate");
		return false;
	}
	function touchMove(e){
		this.moveX = e.originalEvent.changedTouches[0].pageX;
		this.deltaX = this.startX - this.moveX;

		if(this.deltaX > 0 && this.current >= this.slideCount - 1 || this.deltaX < 0 && this.current <= 0) this.deltaX /= 10;
		this.position = (this.el.outerWidth() * this.current + this.deltaX) * -1;

		this.carousel.css({
			transform: "translate("+ this.position +"px, 0)",
			transform: "translate3d("+ this.position +"px, 0, 0)"
		});

		return false;
	}
	function touchEnd(e){
		if(Math.abs(this.deltaX) > this.slideWidth / 2){
			this.deltaX > 0 ? this.current++ : this.current--;
		}
		
		if(this.deltaX > 0 && this.current >= this.slideCount - 1){
			this.current = this.slideCount - 1;
		} else if(this.deltaX < 0 && this.current <= 0){
			this.current = 0;
		}
		this.changeSlide();

		return false;
	}
	function changeSlide(){
		this.position = this.slideWidth * this.current * -1;
		this.carousel.addClass("animate").css({
			transform: "translate("+ this.position +"px, 0)",
			transform: "translate3d("+ this.position +"px, 0, 0)"
		});
		this.el.find(".dot").removeClass("active").eq(this.current).addClass("active");
		this.slides.removeClass("active").eq(this.current).addClass("active");
		if(this.current >= this.slideCount - 1 || this.current <= 0) this.carousel.removeClass("active");
	}
	function handleResize(e){
		this.setCSS();
		this.current = 0;
		this.el.find(".dot").removeClass("active").eq(0).addClass("active");
	}

	$.fn[pluginName] = function(options){
		return this.each(function(){
			if(!$.data(this, "plugin" + pluginName)) $.data(this, "plugin" + pluginName, new Plugin(this, options));
		});
	}

})($, window, v);