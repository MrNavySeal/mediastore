let decrement = document.querySelector(".decrement");
let increment = document.querySelector(".increment");
let cant = document.querySelector(".cant");
let cartProduct = document.querySelector("#addProduct");
let productImages = document.querySelectorAll(".product-image-item");
let btnPrev = document.querySelector(".slider-btn-left");
let btnNext = document.querySelector(".slider-btn-right");
let inner = document.querySelector(".product-image-inner");
let btn = document.querySelector(".product-addwishlist");
let formReview = document.querySelector("#formReview");
let search = document.querySelector("#searchReview");
let sortReview = document.querySelector("#sortReviews");

showMore(document.querySelectorAll(".comment-block"),4,document.querySelector("#showMore"));
/***************************Product Page Events****************************** */

search.addEventListener('input',function() {
    let idProduct = document.querySelector("#idProduct").value;
    let strSearch = search.value;
    let formData = new FormData();

    formData.append("idProduct",idProduct);
    formData.append("strSearch",strSearch);
    request(base_url+"/shop/searchReviews",formData,"post").then(function(objData){
        let rate = objData.rate;
        let rateStars ="";

        for (let i = 0; i < 5; i++) {
            if(i >= parseInt(rate.rate)){
                rateStars+='<i class="far fa-star"></i>';
            }else{
                rateStars+='<i class="fas fa-star"></i>';
            }
        }

        document.querySelector(".comment-list").innerHTML= objData.html;
        document.querySelectorAll(".product-rate")[0].innerHTML= rateStars+` (${rate.total} reseñas)`;
        document.querySelectorAll(".product-rate")[1].innerHTML= rateStars;
    });
});

sortReview.addEventListener("change",function(){
    let idProduct = document.querySelector("#idProduct").value;
    let intSort = sortReview.value;
    let formData = new FormData();

    formData.append("idProduct",idProduct);
    formData.append("intSort",intSort);
    request(base_url+"/shop/sortReviews",formData,"post").then(function(objData){
        let rate = objData.rate;
        let rateStars ="";

        for (let i = 0; i < 5; i++) {
            if(i >= parseInt(rate.rate)){
                rateStars+='<i class="far fa-star"></i>';
            }else{
                rateStars+='<i class="fas fa-star"></i>';
            }
        }

        document.querySelector(".comment-list").innerHTML= objData.html;
        document.querySelectorAll(".product-rate")[0].innerHTML= rateStars+` (${rate.total} reseñas)`;
        document.querySelectorAll(".product-rate")[1].innerHTML= rateStars;
    });
});

rateProduct();
//Select image
for (let i = 0; i < productImages.length; i++) {
    let productImage = productImages[i];
    productImage.addEventListener("click",function(e){
        for (let j = 0; j < productImages.length; j++) {
            productImages[j].classList.remove("active");
            
        }
        productImage.classList.add("active");
        let image = productImage.children[0].src;
        document.querySelector(".product-image img").src = image;
    })
}

//Scroll image buttons 
btnPrev.addEventListener("click",function(){
    inner.scrollBy(-100,0);
});
btnNext.addEventListener("click",function(){
    inner.scrollBy(100,0);
});

if(document.querySelector("#addProduct")){
    cartProduct.addEventListener("click",function(){
        let formData = new FormData();
        let idProduct = cartProduct.getAttribute("data-id");
        formData.append("idProduct",idProduct);
        formData.append("txtQty",cant.value);
        cartProduct.setAttribute("disabled",true);
        cartProduct.innerHTML = `
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        `;
        request(base_url+"/shop/addCart",formData,"post").then(function(objData){
            
            if(objData.status){
                cartProduct.innerHTML = `<i class="fas fa-check"></i> Agregado`;
                setTimeout(function(){
                    cartProduct.removeAttribute("disabled");
                    cartProduct.innerHTML = `<i class="fas fa-shopping-cart me-2"></i> Agregar`;
                },1000);
                document.querySelector("#alert").classList.add("d-none");
                document.querySelector("#qtyCart").innerHTML=objData.qty;
            }else{
                cartProduct.removeAttribute("disabled");
                cartProduct.innerHTML = `<i class="fas fa-shopping-cart me-2"></i> Agregar`;
                document.querySelector("#alert").classList.remove("d-none");
            }
        });
        
    });

    //Quantity events
    cant.addEventListener("change",function(){
        if(cant.value <= 1){
            cant.value = 1;
        }else if(cant.value >= cant.getAttribute("max")){
            cant.value = product['stock'];
        }
    })
    decrement.addEventListener("click",function(){
        if(cant.value<=1){
            return cant.value=1;
        }
        cant.value--;
    });
    increment.addEventListener("click",function(){
        if(cant.value>=cant.getAttribute("max")){
            return cant.value=cant.getAttribute("max");
        }
        cant.value++;
    });
}

//Add to wishlist
btn.addEventListener("click",function(){
    let idProduct = document.querySelector("#idProduct").getAttribute("data-id");
    let formData = new FormData();
    formData.append("idProduct",idProduct);
    btn.classList.toggle("active");
    if(btn.classList.contains("active")){
        btn.parentElement.children[1].innerHTML= `<span class="spinner-border text-primary spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btn.setAttribute("disabled","disabled");
        request(base_url+"/shop/addWishList",formData,"post").then(function(objData){
            btn.removeAttribute("disabled");
            if(objData.status){
                btn.classList.replace("far","fas");
                btn.classList.add("text-danger");
                btn.parentElement.children[1].classList.replace("c-d","c-p");
                btn.parentElement.children[1].setAttribute("href",base_url+"/wishlist");
                btn.parentElement.children[1].innerHTML="Mis favoritos";
            }else{
                openLoginModal();
                btn.parentElement.children[1].innerHTML="Agregar a favoritos";
                btn.classList.replace("fas","far");
                btn.classList.remove("text-danger");
                btn.parentElement.children[1].classList.replace("c-p","c-d");
                btn.parentElement.children[1].removeAttribute("href");
            }
        });
        
    }else{
        btn.parentElement.children[1].innerHTML= `<span class="spinner-border text-primary spinner-border-sm" role="status" aria-hidden="true"></span>`;
        btn.setAttribute("disabled","disabled");
        request(base_url+"/shop/delWishList",formData,"post").then(function(objData){
            btn.removeAttribute("disabled");
            if(objData.status){
                btn.classList.replace("fas","far");
                btn.classList.remove("text-danger");
                btn.parentElement.children[1].classList.replace("c-p","c-d");
                btn.parentElement.children[1].removeAttribute("href");
                btn.parentElement.children[1].innerHTML="Agregar a favoritos";
            }else{
                openLoginModal();
                btn.parentElement.children[1].innerHTML="Agregar a favoritos";
                btn.classList.replace("fas","far");
                btn.classList.remove("text-danger");
                btn.parentElement.children[1].classList.replace("c-p","c-d");
                btn.parentElement.children[1].removeAttribute("href");
            }
        });
    }
});

/***************************Review Events****************************** */
//Add review
formReview.addEventListener("submit",function(e){
    e.preventDefault();
    let formData = new FormData(formReview);
    let alert = document.querySelector("#alertReview");
    let intRate = document.querySelector("#intRate").value;
    let strReview = document.querySelector("#txtReview").value;
    let addReview = document.querySelector("#addReview");
    if(intRate ==0 || strReview ==""){
        alert.classList.remove("d-none");
        alert.classList.replace("alert-warning","alert-danger");
        alert.innerHTML = "Por favor, califica y escribe tu reseña";
        return false;
    }
    addReview.setAttribute("disabled","disabled");
    addReview.innerHTML = `<span class="spinner-border text-primary spinner-border-sm" role="status" aria-hidden="true"></span>`;
    request(base_url+"/shop/setReview",formData,"post").then(function(objData){
        addReview.removeAttribute("disabled");
        addReview.innerHTML="Publicar reseña";
        if(objData.status){
            let stars = document.querySelectorAll(".starBtn");
            let rate = objData.rate;
            let rateStars ="";
            for (let i = 0; i < stars.length; i++) {
                stars[i].innerHTML =`<i class="far fa-star"></i>`;
            }
            for (let i = 0; i < 5; i++) {
                if(i >= parseInt(rate.rate)){
                    rateStars+='<i class="far fa-star"></i>';
                }else{
                    rateStars+='<i class="fas fa-star"></i>';
                }
            }
            alert.classList.add("d-none");
            document.querySelector("#intRate").value="";
            document.querySelector("#txtReview").value="";
            document.querySelector("#idReview").value="";
            document.querySelector(".comment-list").innerHTML= objData.html;
            document.querySelectorAll(".product-rate")[0].innerHTML= rateStars+` (${rate.total} reseñas)`;
            document.querySelectorAll(".product-rate")[1].innerHTML= rateStars;
            document.querySelector("#pills-reviews-tab").innerHTML=`Reseñas (${rate.total})`;

            document.querySelector("#avgRate").innerHTML = `${parseFloat(rate.rate).toFixed(1)}<span class="fs-6">/ 5</span>`;
            document.querySelectorAll(".progress-bar")[0].style.width=`${(rate.five/rate.total)*100}%`;
            document.querySelectorAll(".progress-bar")[0].ariaValueNow = (rate.five/rate.total)*100;
            document.querySelectorAll(".progress-bar")[1].style.width=`${(rate.four/rate.total)*100}%`;
            document.querySelectorAll(".progress-bar")[1].ariaValueNow = (rate.four/rate.total)*100;
            document.querySelectorAll(".progress-bar")[2].style.width=`${(rate.three/rate.total)*100}%`;
            document.querySelectorAll(".progress-bar")[2].ariaValueNow = (rate.three/rate.total)*100;
            document.querySelectorAll(".progress-bar")[3].style.width=`${(rate.two/rate.total)*100}%`;
            document.querySelectorAll(".progress-bar")[3].ariaValueNow = (rate.two/rate.total)*100;
            document.querySelectorAll(".progress-bar")[4].style.width=`${(rate.one/rate.total)*100}%`;
            document.querySelectorAll(".progress-bar")[4].ariaValueNow = (rate.one/rate.total)*100;
            document.querySelectorAll(".review-stars span")[0].innerHTML =`(${rate.five})`;
            document.querySelectorAll(".review-stars span")[1].innerHTML =`(${rate.four})`;
            document.querySelectorAll(".review-stars span")[2].innerHTML =`(${rate.three})`;
            document.querySelectorAll(".review-stars span")[3].innerHTML =`(${rate.two})`;
            document.querySelectorAll(".review-stars span")[4].innerHTML =`(${rate.one})`;
        }else if(objData.login == false){
            alert.classList.remove("d-none");
            alert.classList.replace("alert-warning","alert-danger");
            alert.innerHTML = objData.msg;
            openLoginModal();
        }else{
            if(typeof objData.id !== 'undefined'){
                alert.classList.remove("d-none");
                alert.classList.replace("alert-danger","alert-warning");
                alert.innerHTML = objData.msg;
                editReview(objData.id);
            }else{
                alert.classList.remove("d-none");
                alert.classList.replace("alert-warning","alert-danger");
                alert.innerHTML = objData.msg;
            }
        }
    });
});

function rateProduct(){
    let stars = document.querySelectorAll(".starBtn");
    for (let i = 0; i < stars.length; i++) {
        let star = stars[i];
        star.addEventListener("click",function(){
            document.querySelector("#intRate").value = i+1;
            for (let j = 0; j < stars.length; j++) {
                if(j>i){
                    stars[j].innerHTML =`<i class="far fa-star"></i>`;
                }else{
                    stars[j].innerHTML =`<i class="fas fa-star"></i>`;
                }
            }
        })
    }
}
function editReview(id){
    let html="";
    let formData = new FormData();
    formData.append("idReview",id);
    request(base_url+"/shop/getReview",formData,"post").then(function(objData){
        for (let i = 0; i < 5; i++) {
            if(i >= parseInt(objData.rate)){
                html+=`<button type="button" class="starBtn"><i class="far fa-star"></i></button>`;
            }else{
                html+='<button type="button" class="starBtn"><i class="fas fa-star"></i></button>';
            }
        }
        document.querySelector(".review-rate").innerHTML=html;
        document.querySelector("#intRate").value = objData.rate;
        document.querySelector("#txtReview").value = objData.description;
        document.querySelector("#idProduct").value = objData.productid;
        document.querySelector("#idReview").value = objData.id;
        rateProduct();
    });
}
function deleteReview(id){
    let idProduct = document.querySelector("#idProduct").value;
    let formData = new FormData();
    formData.append("idProduct",idProduct);
    formData.append("idReview",id);
    request(base_url+"/shop/delReview",formData,"post").then(function(objData){
        if(objData.status){
            let rate = objData.rate;
            let rateStars ="";
            for (let i = 0; i < 5; i++) {
                if(i >= parseInt(rate.rate)){
                    rateStars+='<i class="far fa-star"></i>';
                }else{
                    rateStars+='<i class="fas fa-star"></i>';
                }
            }

            document.querySelector(".comment-list").innerHTML= objData.html;
            document.querySelectorAll(".product-rate")[0].innerHTML= rateStars+` (${rate.total} reseñas)`;
            document.querySelectorAll(".product-rate")[1].innerHTML= rateStars;
            document.querySelector("#pills-reviews-tab").innerHTML=`Reseñas (${rate.total})`;

            document.querySelector("#avgRate").innerHTML = `${parseFloat(rate.rate).toFixed(1)}<span class="fs-6">/ 5</span>`;
            document.querySelectorAll(".progress-bar")[0].style.width=`${(rate.total>0 ? rate.five/rate.total : 0)*100}%`;
            document.querySelectorAll(".progress-bar")[0].ariaValueNow = (rate.total>0 ? rate.five/rate.total : 0)*100;
            document.querySelectorAll(".progress-bar")[1].style.width=`${(rate.total>0 ? rate.four/rate.total : 0)*100}%`;
            document.querySelectorAll(".progress-bar")[1].ariaValueNow = (rate.total>0 ? rate.four/rate.total : 0)*100;
            document.querySelectorAll(".progress-bar")[2].style.width=`${(rate.total>0 ? rate.three/rate.total : 0)*100}%`;
            document.querySelectorAll(".progress-bar")[2].ariaValueNow = (rate.total>0 ? rate.three/rate.total : 0)*100;
            document.querySelectorAll(".progress-bar")[3].style.width=`${(rate.total>0 ? rate.two/rate.total : 0)*100}%`;
            document.querySelectorAll(".progress-bar")[3].ariaValueNow = (rate.total>0 ? rate.two/rate.total : 0)*100;
            document.querySelectorAll(".progress-bar")[4].style.width=`${(rate.total>0 ? rate.one/rate.total : 0)*100}%`;
            document.querySelectorAll(".progress-bar")[4].ariaValueNow = (rate.total>0 ? rate.one/rate.total : 0)*100;
            document.querySelectorAll(".review-stars span")[0].innerHTML =`(${rate.five})`;
            document.querySelectorAll(".review-stars span")[1].innerHTML =`(${rate.four})`;
            document.querySelectorAll(".review-stars span")[2].innerHTML =`(${rate.three})`;
            document.querySelectorAll(".review-stars span")[3].innerHTML =`(${rate.two})`;
            document.querySelectorAll(".review-stars span")[4].innerHTML =`(${rate.one})`;
        }
    });
}