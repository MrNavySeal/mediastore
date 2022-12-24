btnSearch.classList.add("d-none");
document.querySelector(".nav-icons-qty").classList.add("d-none");
let intCountry = document.querySelector("#listCountry");
let intState = document.querySelector("#listState");
let intCity = document.querySelector("#listCity");
let formOrder = document.querySelector("#formOrder");
let checkData = document.querySelector("#checkData");

request(base_url+"/shop/getCountries","","get").then(function(objData){
    intCountry.innerHTML = objData;
});

intCountry.addEventListener("change",function(){
    request(base_url+"/shop/getSelectCountry/"+intCountry.value,"","get").then(function(objData){
        intState.innerHTML = objData;
    });
    intCity.innerHTML = "";
});
intState.addEventListener("change",function(){
    request(base_url+"/shop/getSelectState/"+intState.value,"","get").then(function(objData){
        intCity.innerHTML = objData;
    });
});

checkData.addEventListener("click",function(){
    let strName = document.querySelector("#txtNameOrder").value;
    let strLastName = document.querySelector("#txtLastNameOrder").value;
    let strEmail = document.querySelector("#txtEmailOrder").value;
    let strPhone = document.querySelector("#txtPhoneOrder").value;
    let strAddress = document.querySelector("#txtAddressOrder").value;
    let strPostalCode = document.querySelector("#txtPostCodeOrder").value;
    let strNote = document.querySelector("#txtNote");

    const countryList = document.querySelector("#listCountry");
    const stateList = document.querySelector("#listState");
    const cityList = document.querySelector("#listCity"); 
    const alertOrder = document.querySelector("#alertCheckData");
    const btnPaypal = document.querySelector("#paypal-button-container");
    
    
    if(strName=="" || strLastName=="" || strEmail =="" || strPhone =="" || strAddress=="" || countryList.value==0 || stateList.value ==0 || cityList.value==0){
        
        alertOrder.classList.remove("d-none");
        btnPaypal.classList.add("d-none");
        alertOrder.innerHTML =`Por favor, completa los campos con (<span class="text-danger">*</span>)`;

        return false;
    }
    if(!fntEmailValidate(strEmail)){
        alertOrder.innerHTML = "El correo es invalido";
        alertOrder.classList.remove("d-none");
        btnPaypal.classList.add("d-none");
        return false;
    }
    if(strPhone.length < 9){
        alertOrder.innerHTML = "El número de teléfono debe tener al menos 9 dígitos ";
        alertOrder.classList.remove("d-none");
        btnPaypal.classList.add("d-none");
        return false;
    }
    
    
    let formData = new FormData(formOrder);
    formData.append("country",countryList.options[countryList.selectedIndex].text);
    formData.append("state",stateList.options[stateList.selectedIndex].text);
    formData.append("city",cityList.options[cityList.selectedIndex].text);

    checkData.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
    
    
    checkData.setAttribute("disabled","");
    request(base_url+"/shop/checkData",formData,"post").then(function(objData){
        checkData.innerHTML = "Continuar";
        checkData.removeAttribute("disabled","");
        if(objData.status){
            alertOrder.classList.add("d-none");
            btnPaypal.classList.remove("d-none");
            checkData.classList.add("d-none");
        }else{
            alertOrder.classList.remove("d-none");
            checkData.classList.remove("d-none");
            btnPaypal.classList.add("d-none");
            alertOrder.innerHTML = objData.msg;
        }
    });
});