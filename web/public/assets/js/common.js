// const { default: Axios } = require("axios");

var documentReady = function(f) {
    document.readyState == 'loading' ? document.addEventListener("DOMContentLoaded", f) : f();
};

function getArchiveId(){
    return document.body.dataset.archive;
    // return $("body").data("archive")
}

function getFolderId(){
    return document.body.dataset.folder;
    // return $("body").data("folder")
}

function getBodyParam(keyname, defValue){
    var def = (typeof defValue === "undefined" || defValue == "") ? "" : defValue
    var s = document.body.dataset[keyname]
    return (typeof s === "undefined" || s == "")? def : s
}

var func = {
    exists : function(v){
        if(typeof v === "undefined" || v == null)
            return false;
        else
            return true;
    },
    empty : function(v){
        if(typeof v === "undefined" || v == null || v == "")
            return true;
        else
            return false;
    }
}
