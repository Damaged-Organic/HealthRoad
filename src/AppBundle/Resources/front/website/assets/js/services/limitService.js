"use strict";

class LimitService{

	updateLimit(formUrl, formData){
		return $.ajax({
			url: formUrl,
			type: "POST",
			data: formData
		});	
	}
}

export default new LimitService;