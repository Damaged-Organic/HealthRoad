"use strict";

const isFileReader = window.File && window.FileReader && window.FileList && window.Blob;

let filePreview = $(".file-preview");

class FilePreview{

    constructor(){
        if(!isFileReader) throw new Error("your browser don't support file reader api");
        this._events();
    }
    _events(){
        filePreview.on("change", "input[type=file]", (e) => { this.handleFile(e) });
    }
    handleFile(e){
        let file = e.target.files[0];

        let name = file.name;
        let date = this.getHumanDate(file.lastModified);
        let size = this.convertToMB(file.size);

        this.showFileInfo(name, date, size);

        return false;
    }
    getHumanDate(date){
        date = new Date(date);
        return {
            dd: ("0" + date.getDate()).slice(-2),
            mm: ("0" + (date.getMonth() + 1)).slice(-2),
            yyyy: date.getFullYear()
        };
    }
    convertToMB(bytes = 0){
        return Math.max(bytes / 1024 / 1024, 0.1).toFixed(2) + " MB";
    }
    showFileInfo(name, date, size){
        filePreview.find(".file-preview-holder").html(`
            <p>${name}</p>
            <time>${date.dd}/${date.mm}/${date.yyyy}</time>
            <span class="size">${size}</span>
        `);
    }
}

export default FilePreview;