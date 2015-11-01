"use strict";

class BankAccount{
	constructor(){
		this.el = $("#bank-account-widget");
		this.isDisabled = false;
		this._events();
	}
	_events(){
		this.el.on("click", ".change-balance", $.proxy(this.changeBalance, this));
	}
	changeBalance(e){
		this.el.toggleClass("active");
		this.isDisabled = !this.isDisabled;
		this.el.find(".add-money-field").attr("disabled", this.isDisabled);
		return false;
	}
}

export default BankAccount;