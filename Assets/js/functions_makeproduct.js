'use strict';

window.addEventListener("load",function(){
    let categoryList = document.querySelector("#categoryList");
    let subcategoryList = document.querySelector("#subcategoryList");
    let form = document.querySelector("#formItem");
    let formFile = document.querySelector("#formFile");
    let parent = document.querySelector("#upload-multiple");
    let img = document.querySelector("#txtImg");
    let btnAdd = document.querySelector("#btnAdd");
    request(base_url+"/inventario/getSelectCategories","","get").then(function(objData){
        categoryList.innerHTML = objData.data;
    });

    categoryList.addEventListener("change",function(){
        let formData = new FormData();
        formData.append("idCategory",categoryList.value);

        request(base_url+"/inventario/getSelectSubcategories",formData,"post").then(function(objData){
            document.querySelector("#subcategoryList").innerHTML = objData.data;
        });
    });
    setImage(img,parent,1);
    delImage(parent,1);
    setTinymce("#txtDescription");
    addVariant();
    form.addEventListener("submit",function(e){
        e.preventDefault();
        tinymce.triggerSave();
        let strName = document.querySelector("#txtName").value;
        let intDiscount = document.querySelector("#txtDiscount").value;
        let intPrice = document.querySelector("#txtPrice").value;
        let intStatus = document.querySelector("#statusList").value;
        let intStock = document.querySelector("#txtStock").value;
        let strShortDescription = document.querySelector("#txtShortDescription").value;
        let intSubCategory = subcategoryList.value;
        let intCategory = categoryList.value;
        let images = document.querySelectorAll(".upload-image");

        if(strName == "" || intStatus == "" || intCategory == 0 || intSubCategory==0 || intPrice=="" || intStock==""){
            Swal.fire("Error","Todos los campos marcados con (*) son obligatorios","error");
            return false;
        }
        if(strShortDescription.length >140){
            Swal.fire("Error","La descripción corta debe tener un máximo de 140 caracteres","error");
            return false;
        }
        if(images.length < 1){
            Swal.fire("Error","Debe subir al menos una imagen","error");
            return false;
        }
        if(intPrice <= 0){
            Swal.fire("Error","El precio no puede ser menor o igual que 0 ","error");
            return false;
        }
        if(intStock <= 0){
            Swal.fire("Error","La cantidad no puede ser menor o igual a 0 ","error");
            return false;
        }
        if(intDiscount !=""){
            if(intDiscount < 0){
                Swal.fire("Error","El descuento no puede ser inferior a 0","error");
                document.querySelector("#txtDiscount").value="";
                return false;
            }else if(intDiscount > 90){
                Swal.fire("Error","El descuento no puede ser superior al 90%.","error");
                document.querySelector("#txtDiscount").value="";
                return false;
            }
        }
        let arrImg =[];
        for (let i = 0; i < images.length; i++) {
            arrImg.push(images[i].getAttribute("data-rename"));
        }
        
        
        let data = new FormData(form);
        data.append("images",JSON.stringify(arrImg));
        btnAdd.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btnAdd.setAttribute("disabled","");

        request(base_url+"/inventario/setProduct",data,"post").then(function(objData){
            form.reset();
            formFile.reset();
            if(objData.status){
                Swal.fire("Added",objData.msg,"success");
                let divImg = document.querySelectorAll(".upload-image");
                for (let i = 0; i < divImg.length; i++) {
                    divImg[i].remove();
                }
                element.innerHTML = objData.data;
            }else{
                Swal.fire("Error",objData.msg,"error");
            }
        });
        btnAdd.innerHTML=`<i class="fas fa-plus-circle"></i> Agregar`;
        btnAdd.removeAttribute("disabled");
    });
});
let element = document.querySelector("#listItem");
    
function addVariant(){
    let btnAddVariant = document.querySelector("#addVariant");
    let makeVariant = document.querySelector("#makeVariant");
    btnAddVariant.addEventListener("click",function(){
        let div = document.createElement("div");
        div.classList.add("row","variantItem");
        div.innerHTML = `
        <div class="col-md-6 mb-3">
            <p>Nombre de la opción</p>
            <div class="d-flex">
                <button class="btn btn-danger me-1 text-white" type="button" onclick="delVariant(this)" title="Eliminar"><i class="fas fa-trash-alt" aria-hidden="true"></i></button>
                <input type="text" class="form-control" placeholder="Talla"></input>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <p>Valor de la opción</p>
            <div class="d-flex">
                <input type="text" class="form-control me-1" placeholder="Escribe el valor"></input>
                <button class="btn btn-secondary text-white" type="button" title="Agregar" onclick="addVariantValue(this)">Agregar</button>
            </div>
            <div class="row mt-2">
                <div class="col-md-10 variantValues "></div>
            </div>
        </div>
        `;
        makeVariant.appendChild(div);
        if(document.querySelectorAll(".variantItem").length >= 3){
            btnAddVariant.setAttribute("disabled","");
        }else{
            btnAddVariant.removeAttribute("disabled");
        }
    });
}
function addVariantValue(element){
    let optionValue = element.previousElementSibling.value;
    let optionName = element.parentElement.parentElement.previousElementSibling.children[1].children[1].value;
    let variantValues = element.parentElement.nextElementSibling.children[0];
    let variants = variantValues.children;
    
    if(optionValue =="" || optionName ==""){
        Swal.fire("Error","El nombre y el valor de la opción son obligatorios.","error");
        return false;
    }
    if(variants.length > 0){
        for (let i = 0; i < variants.length; i++) {
            let val = variants[i].getAttribute("data-value");
            if(val == optionValue){
                Swal.fire("Error","El valor ya se ha agregado, intente con otro.","error");
                return false;
            }
        }
    }
    let span = document.createElement("span");
    span.classList.add("badge","bg-success","mt-1","mb-1","me-2","p-2");
    span.setAttribute("data-value",optionValue);
    span.setAttribute("onclick","delVariantValue(this)");
    span.innerHTML= optionValue+` <i class="fas fa-times"></i>`;
    variantValues.appendChild(span);
    element.previousElementSibling.value ="";
    setCombination();
}
function setCombination(){
    let variantInput = document.querySelectorAll(".variantItem");
    let html="";
    let listItem = document.querySelector("#listItem");
    for (let i = 0; i < variantInput.length; i++) {
        let variantValues = variantInput[i].children[1].children[2].children[0].children;
        for (let j = 0; j < variantValues.length; j++) {
            let variant = variantValues[j].getAttribute("data-value");
            if(i == 1){
                
            }
            html+=`
            <tr class="variantCombination">
                <td data-label="Variante: ">${variant}</td>
                <td data-label="Precio: "><div class="d-flex justify-content-end"><input type="number" value="1" min="0" class="form-control w-25 text-center"></div></td>
                <td data-label="Cantidad: "><div class="d-flex justify-content-end"><input type="number" value="1" min="0" class="form-control w-25 text-center"></div></td>
            </tr>
            `;
        }
    }
    listItem.innerHTML =html;
}
function delVariant(element){
    element.parentElement.parentElement.parentElement.remove();
}
function delVariantValue(element){
    element.remove();
}
function setImage(element,parent,option){
    let formFile = document.querySelector("#formFile");
    element.addEventListener("change",function(e){
        if(element.value!=""){
            let formImg = new FormData(formFile);
            uploadMultipleImg(element,parent);
            formImg.append("id","");
            if(option == 2){
                let images = document.querySelectorAll(".upload-image").length;
                formImg.append("images",images);
                formImg.append("id",document.querySelector("#idProduct").value);  
            }
            request(base_url+"/UploadImages/uploadMultipleImages",formImg,"post").then(function(objData){
                let divImg = document.querySelectorAll(".upload-image");
                let newImg =[];
                let images = objData.data;
                for (let i = 0; i < divImg.length; i++) {
                    if(!divImg[i].hasAttribute("data-rename")){
                        newImg.push(divImg[i]);
                    }
                }
                if(newImg.length == images.length){
                    for (let i = 0; i < images.length; i++) {
                        newImg[i].setAttribute("data-rename",images[i].rename);
                    }
                }
            });
        }
    });
}
function delImage(parent,option){
    parent.addEventListener("click",function(e){
        if(e.target.className =="deleteImg"){
            let divImg = document.querySelectorAll(".upload-image");
            let deleteItem = e.target.parentElement;
            let nameItem = deleteItem.getAttribute("data-rename");
            let imgDel;
            for (let i = 0; i < divImg.length; i++) {
                if(divImg[i].getAttribute("data-rename")==nameItem){
                    deleteItem.remove();
                    imgDel = document.querySelectorAll(".upload-image");
                }
            }
            let formDel = new FormData();
            formDel.append("image",nameItem);
            request(base_url+"/UploadImages/delImg",formDel,"post").then(function(objData){});
        }
    });
}
