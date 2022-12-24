let formContact = document.querySelector("#formContact");
formContact.addEventListener("submit",function(e){
    e.preventDefault();
    
    let strName = document.querySelector("#txtContactName").value;
    let strEmail = document.querySelector("#txtContactEmail").value;
    let strMessage = document.querySelector("#txtContactMessage").value;
    let alert = document.querySelector("#alertContact");
    let btn = document.querySelector("#btnMessage");

    if( strName =="" || strEmail =="" || strMessage == ""){
        alert.classList.remove("d-none");
        alert.innerHTML="Por favor, completa los campos.";
        return false;
    }
    if(!fntEmailValidate(strEmail)){
        alert.classList.remove("d-none");
        alert.innerHTML = "Your email is incorrect";
        return false;
    }

    btn.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;    
    btn.setAttribute("disabled","");
    let formData = new FormData(formContact);
    request(base_url+"/contact/setContact",formData,"post").then(function(objData){
        btn.innerHTML="Enviar";    
        btn.removeAttribute("disabled");
        if(objData.status){
            alert.classList.remove("d-none");
            alert.classList.replace("alert-danger","alert-success");
            alert.innerHTML =objData.msg;
            formContact.reset();
        }else{
            alert.classList.remove("d-none");
            alert.classList.replace("alert-success","alert-danger");
            alert.innerHTML =objData.msg;
        }
    });

});