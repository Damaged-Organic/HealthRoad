"use strict";

export default class PhotoList{

	showPhoto(el, src, size){
		el.append(`
			<figure class='photo-holder'>
				<img src="${ src }" alt="preview">
				<span class="size">${ size }</span>
			</figure>
		`).addClass("active");
	}

}