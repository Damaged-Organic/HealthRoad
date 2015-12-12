"use strict";

class PasswordService{

    static change(url, data){
        return $.ajax({
           url: url,
           type: "POST",
           data: data
       });
    }

}
export default PasswordService;