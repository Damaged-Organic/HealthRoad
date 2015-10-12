"use strict";

class BindService{
	
	_send(...args){
		var data = {
				entity: args[0],
				parentID: args[1],
				childID: args[2],
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

export default new BindService();
