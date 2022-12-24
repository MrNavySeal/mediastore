'use strict';

if(document.querySelector("#quickSale")){
    const moneyReceived = document.querySelector("#moneyReceived");
    const btnAddPos = document.querySelector("#btnAddPos");
    let searchProducts = document.querySelector("#searchProducts");
    let searchCustomers = document.querySelector("#searchCustomers");
    moneyReceived.addEventListener("input",function(){
        let total = document.querySelector("#total").getAttribute("data-total");
        let result = 0;
        result = moneyReceived.value - total ;
        if(result < 0){
            result = 0;
        }
    
        document.querySelector("#moneyBack").innerHTML = "Money back: "+MS+formatNum(result,".")+" "+MD;
    });
    btnAddPos.addEventListener("click",function(){
        let id = document.querySelector("#idCustomer").value;
        if(id <= 0){
            Swal.fire("Error","Por favor, añada un cliente para establecer el pedido","error");
            return false;
        }else{
            let products = document.querySelectorAll(".product");
            let arrProducts = [];
            for (let i = 0; i < products.length; i++) {
                let product = {
                    "id":products[i].children[0].getAttribute("data-id"),
                    "qty":products[i].children[1].children[0].children[0].children[1].children[1].getAttribute("data-value")
                };
                arrProducts.push(product);
            }
            let formData = new FormData();
            formData.append("id",id);
            formData.append("products",JSON.stringify(arrProducts));
            btnAddPos.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
            btnAddPos.setAttribute("disabled","");
            request(base_url+"/orders/setOrder",formData,"post").then(function(objData){
                btnAddPos.removeAttribute("disabled");
                btnAddPos.innerHTML="Guardar";
                if(objData.status){
                    location.reload();
                }else{
                    Swal.fire("Error",objData.msg,"error");
                }
            });
        }
    });
    searchProducts.addEventListener('input',function() {
        request(base_url+"/orders/searchProducts/"+searchProducts.value,"","get").then(function(objData){
            if(objData.status){
                document.querySelector("#listProducts").innerHTML = objData.data;
            }else{
                document.querySelector("#listProducts").innerHTML = objData.data;
            }
        });
    });
    searchCustomers.addEventListener('input',function() {
        if(searchCustomers.value !=""){
            request(base_url+"/orders/searchCustomers/"+searchCustomers.value,"","get").then(function(objData){
                if(objData.status){
                    document.querySelector("#customers").innerHTML = objData.data;
                }else{
                    document.querySelector("#customers").innerHTML = objData.data;
                }
            });
        }else{
            document.querySelector("#customers").innerHTML = "";
        }
    });
}
if(document.querySelector("#orders")){
    let search = document.querySelector("#search");
    
    let sort = document.querySelector("#sortBy");
    let element = document.querySelector("#listItem");
    

    search.addEventListener('input',function() {
        request(base_url+"/orders/search/"+search.value,"","get").then(function(objData){
            if(objData.status){
                element.innerHTML = objData.data;
            }else{
                element.innerHTML = objData.msg;
            }
        });
    });
    

    sort.addEventListener("change",function(){
        request(base_url+"/orders/sort/"+sort.value,"","get").then(function(objData){
            if(objData.status){
                element.innerHTML = objData.data;
            }else{
                element.innerHTML = objData.msg;
            }
        });
    });

    element.addEventListener("click",function(e) {
        let element = e.target;
        let id = element.getAttribute("data-id");
        if(element.name == "btnDelete"){
            deleteItem(id);
        }
    });

    function deleteItem(id){
        Swal.fire({
            title:"¿Estás seguro de eliminarlo?",
            text:"Se eliminará para siempre...",
            icon: 'warning',
            showCancelButton:true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText:"Sí, eliminar",
            cancelButtonText:"No, cancelar"
        }).then(function(result){
            if(result.isConfirmed){
                let url = base_url+"/Orders/delOrder"
                let formData = new FormData();
                formData.append("idOrder",id);
                request(url,formData,"post").then(function(objData){
                    if(objData.status){
                        Swal.fire("Eliminado",objData.msg,"success");
                        element.innerHTML = objData.data;
                    }else{
                        Swal.fire("Error",objData.msg,"error");
                    }
                });
            }
        });
    }
    
}
if(document.querySelector("#btnRefund")){
    let btn = document.querySelector("#btnRefund");
    btn.addEventListener("click",function(){
        refund(btn.getAttribute("data-id"));
    });
    function refund(id){
        let btn = document.querySelector("#btnRefund");
        btn.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btn.setAttribute("disabled","");
    
        request(base_url+"/Orders/getTransaction/"+id,"","get").then(function(objData){
            btn.removeAttribute("disabled");
            btn.innerHTML=`<i class="fas fa-undo"></i> Reembolsar`;
    
            if(objData.status){
                
                let transaction = objData.data;
                let idTransaction = transaction.purchase_units[0].payments.captures[0].id;
                let payer = transaction.purchase_units[0].shipping.name.full_name+'<br>'+transaction.payer.email_address;
                let grossAmount = transaction.purchase_units[0].payments.captures[0].seller_receivable_breakdown.gross_amount.value;
                let feeAmount = transaction.purchase_units[0].payments.captures[0].seller_receivable_breakdown.paypal_fee.value;
                let netAmount = transaction.purchase_units[0].payments.captures[0].seller_receivable_breakdown.net_amount.value;
                let modalItem = document.querySelector("#modalItem");
                let modal= `
                <div class="modal fade" id="modalElement">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="staticBackdropLabel">Reembolsar</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="formItem" name="formItem" class="mb-4">
                                    <input type="hidden" id="idTransaction" name="idTransaction" value="${idTransaction}">
                                    <table class="table align-middle text-break">
                                        <tbody id="listItem">
                                            <tr>
                                                <td>Transaccion: </td>
                                                <td>${idTransaction}</td>
                                            </tr>
                                            <tr>
                                                <td>Pagador: </td>
                                                <td>${payer}</td>
                                            </tr>
                                            <tr>
                                                <td>Reembolso bruto: </td>
                                                <td>${grossAmount+" "+MD}</td>
                                            </tr>
                                            <tr>
                                                <td>Comisión de paypal: </td>
                                                <td>${feeAmount+" "+MD}</td>
                                            </tr>
                                            <tr>
                                                <td>Reembolso neto: </td>
                                                <td>${netAmount+" "+MD}</td>
                                            </tr>
                                            <tr>
                                                <td>Observación: </td>
                                                <td><textarea name="txtDescription" id="txtDescription" rows="3" class="w-100 form-control"></textarea></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-success text-white" id="btnRefundConfirm"><i class="fas fa-undo"></i> Reembolsar</a>
                                        <button type="button" class="btn btn-secondary text-white" data-bs-dismiss="modal">Cerrar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                `;
    
                modalItem.innerHTML = modal;
                let modalView = new bootstrap.Modal(document.querySelector("#modalElement"));
                modalView.show();
    
                let form = document.querySelector("#formItem");
                form.addEventListener("submit",function(e){
                    e.preventDefault();
                    
                    let strDescription = document.querySelector("#txtDescription").value;
                    let idTransaction = document.querySelector("#idTransaction").value;
                    let btnRefundConfirm = document.querySelector("#btnRefundConfirm");
                    
                    if(idTransaction == "" || strDescription == ""){
                        Swal.fire("Error","Por favor, rellene los campos ","error");
                        return false;
                    }
                    btnRefundConfirm.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
                    btnRefundConfirm.setAttribute("disabled","");
                    Swal.fire({
                        title:"Está seguro de hacer el reembolso?",
                        icon: 'warning',
                        showCancelButton:true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText:"Sí, reembolsar",
                        cancelButtonText:"No, cancelar"
                    }).then(function(result){
                        
                        let formData = new FormData(form);
                        if(result.isConfirmed){
                            request(base_url+"/Orders/setRefund",formData,"post").then(function(objData){
                                btnRefundConfirm.innerHTML=`<i class="fas fa-undo"></i> Reembolsar`;
                                btnRefundConfirm.removeAttribute("disabled");
                                if(objData.status){
                                    window.location.reload();
                                }else{
                                    Swal.fire("Error",objData.msg,"error");
                                }
                            });
                        }else{
                            btnRefundConfirm.innerHTML=`<i class="fas fa-undo"></i> Reembolsar`;
                            btnRefundConfirm.removeAttribute("disabled");
                        }
                    });
                });
            }else{
                Swal.fire("Error",objData.msg,"error");
            }
        });
    }
}
if(document.querySelector("#btnPrint")){
    let btn = document.querySelector("#btnPrint");
    btn.addEventListener("click",function(){
        if(document.querySelector("#btnRefund"))document.querySelector("#btnRefund").classList.add("d-none");
        printDiv(document.querySelector("#orderInfo"));
    });
}
function addProduct(id,btn){
    btn.setAttribute("disabled","disabled");
    let formData = new FormData();
    formData.append("idProduct",id);
    request(base_url+"/orders/getProduct",formData,"post").then(function(objData){
        let data = objData.data;
        let div = document.createElement("div");
        let html =`
            <button class="btn text-danger p-0 rounded-circle position-absolute top-0 end-0 fs-5" data-id="${data.idproduct}" onclick="delProduct(this)"><i class="fas fa-times-circle"></i></button>
            <div class="p-1">
                <div class="d-flex justify-content-between">
                    <div class="d-flex">
                        <img src="${data.image}" alt="" class="me-1" height="60px" width="60px">
                        <div class="text-start">
                            <div style="height:25px" class="overflow-hidden"><p class="m-0" >${data.name}</p></div>
                            <p class="m-0 productData" data-value ="1" data-price="${data.price}" id="qty${data.idproduct}"><span id="valQty${data.idproduct}">1</span> x ${data.priceFormat}</p>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <button type="button" class="btn btn-sm btn-secondary p-1 text-white" onclick="decQty(${data.idproduct})"><i class="fas fa-minus"></i></button>
                        <button type="button" class="btn btn-sm btn-success p-1 text-white" onclick="incQty(${data.idproduct},${data.stock})"><i class="fas fa-plus"></i></button>
                    </div>
                    <p class="m-0 mt-1 fw-bold text-end" >$<span id="total${data.idproduct}">${data.price}</span></p>
                </div>
            </div>
        `;
        div.classList.add("w-100" ,"border" ,"border-primary" ,"position-relative" ,"mb-3","product");
        div.setAttribute("style","height:100px");
        div.innerHTML = html;
        document.querySelector("#posProducts").appendChild(div);
        calcTotal();
    });
}
function delProduct(element){
    let id = element.getAttribute("data-id");
    document.querySelector("#btn"+id).removeAttribute("disabled");
    element.parentElement.remove();
    calcTotal();
}
function decQty(id){
    let element = document.querySelector("#qty"+id);
    let qty = element.getAttribute("data-value");
    let price = element.getAttribute("data-price");
    if(qty <= 1){
        qty = 1;
    }else{
        qty--;
    }

    element.setAttribute("data-value",qty);
    document.querySelector("#valQty"+id).innerHTML = qty;
    document.querySelector("#total"+id).innerHTML = `${price*qty}`;
    calcTotal();
}
function incQty(id,stock){
    let element = document.querySelector("#qty"+id);
    let qty = element.getAttribute("data-value");
    let price = element.getAttribute("data-price");
    if(qty >= stock){
        qty = stock;
    }else{
        qty++;
    }

    element.setAttribute("data-value",qty);
    document.querySelector("#valQty"+id).innerHTML = qty;
    document.querySelector("#total"+id).innerHTML = `${price*qty}`;
    calcTotal();
}
function calcTotal(){
    let data = document.querySelectorAll(".productData");
    let total = 0;
    
    for (let i = 0; i < data.length; i++) {
        total+= data[i].getAttribute("data-value")*data[i].getAttribute("data-price");
    }
    if(total > 0){
        document.querySelector("#btnPos").classList.remove("d-none");
        document.querySelector("#btnPos").removeAttribute("disabled");
    }else{
        document.querySelector("#btnPos").classList.add("d-none");
        document.querySelector("#btnPos").setAttribute("disabled","disabled");
    }
    document.querySelector("#total").innerHTML = MS+total+" "+MD;
    document.querySelector("#total").setAttribute("data-total",total);
}
function openModalOrder(){
    let modal = new bootstrap.Modal(document.querySelector("#modalPos"));
    moneyReceived.value = document.querySelector("#total").getAttribute("data-total");
    let total = document.querySelector("#total").getAttribute("data-total");
    document.querySelector("#saleValue").innerHTML = "Valor de venta: "+MS+formatNum(total,".")+" "+MD;
    document.querySelector("#moneyBack").innerHTML = "Dinero a devolver: "+MS+0+" "+MD;
    modal.show();
}
function addCustom(element){
    element.setAttribute("onclick","delCustom(this)");
    element.classList.add("border","border-primary");
    document.querySelector("#selectedCustomer").appendChild(element);
    document.querySelector("#customers").innerHTML = "";
    document.querySelector("#idCustomer").value = element.getAttribute("data-id");
    searchCustomers.parentElement.classList.add("d-none");
}
function delCustom(element){
    searchCustomers.parentElement.classList.remove("d-none");
    document.querySelector("#idCustomer").value = 0;
    element.remove();
}