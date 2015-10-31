"use strict";

import PhotoList from "../view/photoList";

const isFileReader = window.File && window.FileReader && window.FileList && window.Blob;

class PhotoPreview{
	constructor(){
		this.initialize();
	}
	initialize(){
		if(!isFileReader) return;
		this._events();
		this.files = [];
		this.holder = [];
		this.photosHolder = [];
		this.photoList = new PhotoList;
	}
	_events(){
		$("input[type=file]").on("change", $.proxy(this.handleFiles, this));
	}
	handleFiles(e){
		let target = $(e.target),
			files = e.target.files,
			file = {},
			fileSize = 0,
			key;

		this.files = [];

		for(key in files){			
			if(typeof files[key] !== "object") continue;
			file = files[key];
			fileSize = this.convertToMBytes(file.size);
			this.files.push({file, fileSize, fileUrl: ""});
		}
		this.holder = target.closest(".file-holder");
		this.photosHolder = this.holder.find(".photo-list-holder");
		this.photosHolder.empty();
		this.holder.find(".error-holder").empty();
		this.readFiles();

		return false;
	}
	convertToMBytes(bytes, mb){
		if(bytes <= 0) return;
		return Math.max(bytes / 1024 / 1024, 0.1).toFixed(2) + " MB";
	}
	readFiles(){
		let self = this, reader, key;

		for(key in this.files){
			reader = new FileReader();

			reader.onload = (function(file, fileSize){
				return function(e){
					self.photoList.showPhoto(self.photosHolder, e.target.result, fileSize);
				}
			})(this.files[key].file, this.files[key].fileSize);
			reader.readAsDataURL(this.files[key].file);
		}
	}

}

export default PhotoPreview;