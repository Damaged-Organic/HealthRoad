"use strict";

import PasswordService from "../services/passwordService";

let isLoading = false;

export default class PasswordController{
    constructor(){
        this.el = $("#change-password-form");
        this.loader = this.el.find(".loader");
        this.responseHolder = this.el.find(".response-holder");
        this._UIevents();
    }
    _UIevents(){
        this.el.on("submit", this.handleSubmit.bind(this));
    }
    handleSubmit(e){
        if(isLoading || !this.el.valid()) return;

        let formData = this.el.serializeArray();
        let url = this.el.attr("action");

        this.loader.addClass("active");
        this.responseHolder.removeClass("success error");

        PasswordService
            .change(url, formData)
            .done((response) => {
                response = JSON.parse(response);

                this.responseHolder
                    .addClass("success")
                    .html(`<p>${ response.message }</p>`);

                this.el[0].reset();
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

        return false;
    }
    closeNotify(){
        window.setTimeout(() => {
            this.responseHolder.empty();
        }, 4000);
    }
}