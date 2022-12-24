'use strict';
setTinymce("#txtDescription",600);
let formPage = document.querySelector("#formPage");
formPage.addEventListener("submit",function(e){
    e.preventDefault();
    tinymce.triggerSave();
    let strName = document.querySelector("#txtName").value;
    let strDescription = document.querySelector("#txtDescription").value;
    if(strDescription =="" || strName==""){
        Swal.fire("Error","Por favor, rellene los campos","error");
        return false;
    }

    let formData = new FormData(formPage);
    request(base_url+"/store/updatePage",formData,"post").then(function(objData){
        if(objData.status){
            window.location.reload();
        }else{
            Swal.fire("Error",objData.msg,"error");
        }
    });
});

