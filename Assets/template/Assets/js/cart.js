btnSearch.classList.add("d-none");
document.querySelector(".nav-icons-qty").classList.add("d-none");
let decrement = document.querySelectorAll(".decrement");
let increment = document.querySelectorAll(".increment");
let inputs = document.querySelectorAll(".cant");
for (let i = 0; i < inputs.length; i++) {
    let input = inputs[i];
    let minus = decrement[i];
    let plus = increment[i];
    input.addEventListener("change",function(){
        let idProduct = input.getAttribute("data-id");
        let formData = new FormData();
        formData.append("idProduct",idProduct);

        request(base_url+"/shop/getProduct",formData,"post").then(function(objData){
            if(input.value <= 1){
                input.value = 1;
            }else if(input.value >=objData.data.stock){
                input.value = objData.data.stock;
            }
            let qty=input.value;

            formData.append("idProduct",idProduct);
            formData.append("qty",qty);
            request(base_url+"/shop/updateCart",formData,"post").then(function(objData){
                if(objData.status){
                    if("subtotalCoupon" in objData){
                        document.querySelector("#subtotal").innerHTML = objData.subtotalCoupon;
                        document.querySelector("#subtotalCoupon").innerHTML = objData.subtotal;
                    }else{
                        document.querySelector("#subtotal").innerHTML = objData.subtotal;
                    }
                    document.querySelector("#totalProducts").innerHTML = objData.total;
                    document.querySelectorAll(".totalPerProduct")[i].innerHTML = objData.totalPrice;
                }
            });
        });
        
            
        
    })
    minus.addEventListener("click",function(){
        let idProduct = input.getAttribute("data-id");
        let formData = new FormData();
        if(input.value<=1){
            input.value=1;
        }else{
            input.value--;
        }
        let qty=input.value;
        formData.append("idProduct",idProduct);
        formData.append("qty",qty);
        request(base_url+"/shop/updateCart",formData,"post").then(function(objData){
            if(objData.status){
                if("subtotalCoupon" in objData){
                    document.querySelector("#subtotal").innerHTML = objData.subtotalCoupon;
                    document.querySelector("#subtotalCoupon").innerHTML = objData.subtotal;
                }else{
                    document.querySelector("#subtotal").innerHTML = objData.subtotal;
                }
                document.querySelector("#totalProducts").innerHTML = objData.total;
                document.querySelectorAll(".totalPerProduct")[i].innerHTML = objData.totalPrice;
            }
        });
    });
    plus.addEventListener("click",function(){
        let idProduct = input.getAttribute("data-id");
        let formData = new FormData();
        formData.append("idProduct",idProduct);
        request(base_url+"/shop/getProduct",formData,"post").then(function(objData){

            if(input.value >=objData.data.stock){
                input.value=objData.data.stock;
            }else{
                input.value++;
            }

            let qty=input.value;
            formData.append("idProduct",idProduct);
            formData.append("qty",qty);

            request(base_url+"/shop/updateCart",formData,"post").then(function(objData){
                if(objData.status){
                    if("subtotalCoupon" in objData){
                        document.querySelector("#subtotal").innerHTML = objData.subtotalCoupon;
                        document.querySelector("#subtotalCoupon").innerHTML = objData.subtotal;
                    }else{
                        document.querySelector("#subtotal").innerHTML = objData.subtotal;
                    }
                    document.querySelector("#totalProducts").innerHTML = objData.total;
                    document.querySelectorAll(".totalPerProduct")[i].innerHTML = objData.totalPrice;
                }
            });
        });
    })
}

if(document.querySelectorAll(".table-cart .btn-del")){
    let btns = document.querySelectorAll(".table-cart .btn-del");
    for (let i = 0; i < btns.length; i++) {
        let btn = btns[i];
        btn.addEventListener("click",function(){
            let idProduct = inputs[i].getAttribute("data-id");
            let formData = new FormData();
            formData.append("idProduct",idProduct);
            request(base_url+"/shop/delCart",formData,"post").then(function(objData){
                if(objData.status){
                    window.location.reload();
                }
            });
            
        })
    }
}
if(document.querySelector("#selectCity")){
    let select = document.querySelector("#selectCity");
    select.addEventListener("change",function(){
        if(select.value == 0){
            return false;
        }
        request(base_url+"/shop/calculateShippingCity/"+select.value,"","get").then(function(objData){
            document.querySelector("#totalProducts").innerHTML = objData.total;
        });
    });
}
if(document.querySelector("#btnCoupon")){
    let btnCoupon = document.querySelector("#btnCoupon");
    btnCoupon.addEventListener("click",function(){
        let strCoupon = document.querySelector("#txtCoupon").value;
        if(strCoupon ==""){
            alertCoupon.innerHTML="Please put your coupon code.";
            alertCoupon.classList.remove("d-none");
            return false;
        }
        btnCoupon.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btnCoupon.setAttribute("disabled","");

        let formData = new FormData(formCoupon);
        request(base_url+"/shop/setCouponCode",formData,"post").then(function(objData){
            btnCoupon.innerHTML=`+`;
            btnCoupon.removeAttribute("disabled");
            if(objData.status){
                window.location.reload();
            }else{
                alertCoupon.innerHTML=objData.msg;
                alertCoupon.classList.remove("d-none");
            }
        });
    })
}
if(document.querySelector("#removeCoupon")){
    let btn = document.querySelector("#removeCoupon");
    btn.addEventListener("click",function(){
        request(base_url+"/shop/delCouponCode","","get").then(function(objData){
            window.location.reload();
        });
    });
}
if(document.querySelector("#checkCity")){
    let btn = document.querySelector("#checkCity");
    btn.addEventListener("click",function(){
        let id = document.querySelector("#selectCity").value;
        btn.innerHTML=`<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btn.setAttribute("disabled","");
        request(base_url+"/shop/checkShippingCity/"+id,"","get").then(function(objData){
            btn.innerHTML=`Checkout`;
            btn.removeAttribute("disabled");
            if(objData.status){
                window.location.href = base_url+"/shop/checkout";
            }else{
                document.querySelector("#alertCity").classList.remove("d-none");
                document.querySelector("#alertCity").innerHTML = objData.msg;
            }
        });
    });
}