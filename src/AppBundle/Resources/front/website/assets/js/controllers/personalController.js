"use strict";

import PersonalService from "../services/personalService";

let isLoading = false;

export default class PersonalController{
    constructor(){
        this.el = $("#personal-form");
        this.loader = this.el.find(".loader");
        this.responseHolder = this.el.find(".response-holder");
        this._UIevents();
    }
    _UIevents(){
        this.el.on("submit", this.handleSubmit.bind(this));
    }
    handleSubmit(e){
        e.preventDefault();
        if(isLoading || !this.el.valid()) return;

        let formData = this.el.serializeArray();
        let url = this.el.attr("action");

        this.loader.addClass("active");
        this.responseHolder.removeClass("success error");

        PersonalService
            .change(url, formData)
            .done((response) => {
                response = JSON.parse(response);

                console.log(response);

                this.responseHolder
                    .addClass("success")
                    .html(`<p>${ response.message }</p>`);
            })
            .fail((error) => {
               this.responseHolder
                    .addClass("error")
                    .html(`<p>${ error.responseText }</p>`);
            })
            .always(() => {
                this.loader.removeClass("active");
                this.closeNotify();
            });
    }
    closeNotify(){
        window.setTimeout(() => {
            this.responseHolder.empty();
        }, 4000);
    }
}
