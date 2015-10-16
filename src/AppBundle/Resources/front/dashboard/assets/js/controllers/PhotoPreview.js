"use strict";

const isFileReader = window.File && window.FileReader && window.FileList && window.Blob;

class PhotoPreview{
	constructor(){
		this.initialize();
	}
	initialize(){
		if(!isFileReader) return;
		this._events();
		this.holder = [];
	}
	_events(){
		$("input[type=file]").on("change", $.proxy(this.handleFile, this));
	}
	handleFile(e){
		e.stopPropagation();
		e.preventDefault();
		let file = e.target.files[0],
			sizeInMB = 0;

		this.holder = $(e.target).closest(".file-holder");

		if(!file.type || !file.type.match(/image.*/)) return;
		this.holder.find(".error-holder").empty();
		sizeInMB = this.convertToMBytes(file.size);
		this.readFile(file, sizeInMB);
	}
	convertToMBytes(bytes, mb){
		if(bytes <= 0) return;
		return Math.max(bytes / 1024 / 1024, 0.1).toFixed(2) + " MB";
	}
	readFile(file, size){
		let self = this,
			reader = new FileReader();

		reader.onload = function(e){
			self.showFile(e.target.result, size);
		}
		reader.readAsDataURL(file);
	}
	showFile(src, size){
		this.holder.find(".photo-holder").html(`
			<img src="${ src }" alt="preview">
			<span class="size">${ size }</span>
		`).andSelf().addClass("active");
	}
}

export default PhotoPreview;