"use strict";

export function vendors(property){

	var style = document.createElement("div").style,
		prefixes = ["ms", "O", "Webkit", "Moz"],
		key;
	
	if(style[property] === "") return property;  	
  	property = property.charAt(0).toUpperCase() + property.slice(1);

  	for(key in prefixes){
  		if(style[prefixes[key] + property] === "") return prefixes[key] + property;
  	}
}