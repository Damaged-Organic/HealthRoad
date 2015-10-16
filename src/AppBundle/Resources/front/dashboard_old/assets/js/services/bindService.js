/*
"use strict";

class BindService{
	
	_send(...args){
		var data = {
				objectClass: args[0],
				objectId: args[1],
				targetId: args[2],
				isBinded: args[3]
			},
			url = args[4];

		return $.ajax({
			url: url,
			type: "POST",
			data: {data: JSON.stringify(data)}
		});
	}
}

export default new BindService();*/
