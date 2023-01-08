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
        let data = new FormData(form);
        let strName = document.querySelector("#txtName").value;
        let intDiscount = document.querySelector("#txtDiscount").value;
        let intPrice = document.querySelector("#txtPrice").value;
        let intStatus = document.querySelector("#statusList").value;
        let intStock = document.querySelector("#txtStock").value;
        let strShortDescription = document.querySelector("#txtShortDescription").value;
        let intSubCategory = subcategoryList.value;
        let intCategory = categoryList.value;
        let images = document.querySelectorAll(".upload-image");
        let productType = document.querySelector("#simple-tab").className.includes("active") ? true : false;
        let specs = {
            "warrany": document.querySelector("#txtWarranty").value,
            "mark":document.querySelector("#txtMark").value,
            "weight":document.querySelector("#txtWeight").value,
            "length":document.querySelector("#txtLength").value,
            "width":document.querySelector("#txtWidth").value,
            "height":document.querySelector("#txtHeight").value
        };
        if(strName == "" || intStatus == "" || intCategory == 0 || intSubCategory==0 || strShortDescription ==""){
            Swal.fire("Error","Todos los campos marcados con (*) son obligatorios","error");
            return false;
        }
        if(strShortDescription.length >140){
            Swal.fire("Error","La descripción corta debe tener un máximo de 140 caracteres","error");
            return false;
        }
        if(images.length < 1){
            Swal.fire("Error","Debe subir al menos una imagen","error");
        }

        if(productType){
            if(intPrice=="" || intStock==""){
                Swal.fire("Error","El precio y la cantidad son obligatorios","error");
                return false;
            }
            if(intPrice < 0){
                Swal.fire("Error","El precio no puede ser menor que cero ","error");
                return false;
            }
            if(intStock < 0){
                Swal.fire("Error","La cantidad no puede ser menor que cero ","error");
                return false;
            }
        }else{
            let arrData =orderVariantValues(1);
            let variantsValues =[];
            if(arrData.length <=0){
                Swal.fire("Error","Por favor, ingresa al menos una variante","error");
                return false; 
            }
            let variants = document.querySelectorAll(".variantCombination");
            let flag =true;
            for (let i = 0; i < variants.length; i++) {
                let variantName = variants[i].children[0].innerHTML;
                let variantPrice = variants[i].children[1].children[0].children[0].value;
                let variantQty = variants[i].children[2].children[0].children[0].value;
                
                if(variantPrice < 0 || variantQty < 0){
                    Swal.fire("Error","El precio y la cantidad de la variante ("+variantName+") debe ser igual o mayor que cero","error");
                    flag = false;
                    break;
                }
                let obj = {"name":variantName,"price":variantPrice,"qty":variantQty};
                variantsValues.push(obj);
            }
            if(!flag){
                return false;
            }
            data.append("variants",JSON.stringify(arrData));
            data.append("variantsvalues",JSON.stringify(variantsValues));

        }
        if(images.length < 1){
            Swal.fire("Error","Debe subir al menos una imagen","error");
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
        
        
        data.append("specs",JSON.stringify(specs));
        data.append("images",JSON.stringify(arrImg));
        data.append("producttype",productType);
        btnAdd.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btnAdd.setAttribute("disabled","");

        request(base_url+"/inventario/setProduct",data,"post").then(function(objData){
            if(objData.status){
                form.reset();
                formFile.reset();
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
    orderVariantValues();
}
function orderVariantValues(option=null){
    let variantInput = document.querySelectorAll(".variantItem");
    let arrData=[];
    if(variantInput.length>0){
        for (let i = 0; i < variantInput.length; i++) {
            let attrName = variantInput[i].children[0].children[1].children[1].value;
            let variantValues = variantInput[i].children[1].children[2].children[0].children;
            if(i >=3)break;
            if(variantValues.length>0){
                let attrData = [];
                for (let j = 0; j < variantValues.length; j++) {
                    attrData.push(variantValues[j].getAttribute("data-value"));
                }
                let attributes = {"nombre":attrName,"opciones":attrData};
                arrData.push(attributes);
            }
        }
    }
    if(option==null){
        setCombination(arrData);
    }else{
        return arrData;
    }
}
function setCombination(array){
    let variants=[];
    if(array.length > 0){
        let options1 =array[0].opciones;
        let options2=[];
        let options3=[];
        if(array.length > 1){
            options2 = array[1].opciones;
            if(array.length > 2){
                options3 = array[2].opciones;
            }
        }
        for (let i = 0; i < options1.length; i++) {
            if(options2.length>0){
                for (let j = 0; j < options2.length; j++) {
                    if(options3.length > 0){
                        for (let k = 0; k < options3.length; k++) {
                            variants.push(options1[i]+"-"+options2[j]+"-"+options3[k]);
                        }
                    }else{
                        variants.push(options1[i]+"-"+options2[j]);
                    }
                }
            }else{
                variants.push(options1[i]);
            }
        }
    }
    displayVariants(variants);
}
function displayVariants(array){
    let html="";
    let listItem = document.querySelector("#listItem");
    if(array.length>0){
        for (let i = 0; i < array.length; i++) {
            html+=`
            <tr class="variantCombination">
                <td data-label="Variante: ">${array[i]}</td>
                <td data-label="Precio: "><div class="d-flex justify-content-end"><input type="number" value="0" class="form-control w-50 text-center"></div></td>
                <td data-label="Cantidad: "><div class="d-flex justify-content-end"><input type="number" value="0" class="form-control w-50 text-center"></div></td>
            </tr>
            `;
        }
    }
    listItem.innerHTML = html;
}
function delVariant(element){
    element.parentElement.parentElement.parentElement.remove();
    orderVariantValues();
}
function delVariantValue(element){
    element.remove();     
    orderVariantValues();  
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
