"use strict";

class PersonalService{

    static change(url, data){
        return $.ajax({
           url: url,
           type: "POST",
           data: data
       });
    }

}
export default PersonalService;
