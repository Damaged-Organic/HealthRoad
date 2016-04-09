"use strict";

let el = $(".cancel-all");

class Uncheck{

    constructor(){
        this._events();
    }
    _events(){
        el.on("click", (e) => { this._handleCancel(e) });
    }
    _handleCancel(e){
        $(e.target).closest("form").find("input[type=checkbox]").removeAttr("checked");

        return false;
    }
}

export default Uncheck;
