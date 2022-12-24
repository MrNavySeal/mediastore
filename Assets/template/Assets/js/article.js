window.addEventListener("load",function(){
    let featured = document.querySelector(".featured-container-items");
    let left = document.querySelector(".featured-btn-left");
    let right = document.querySelector(".featured-btn-right");
    let filter = document.querySelector("#filter");
    let filterOptions = document.querySelector(".filter-options");
    let filterOverlay = document.querySelector(".filter-options-overlay");

    left.addEventListener("click",function(){
        featured.scrollBy(-featured.offsetWidth,0);
    });
    right.addEventListener("click",function(){
        featured.scrollBy(featured.offsetWidth,0);
    });
    filterOverlay.addEventListener("click",function(){
        filterOverlay.style.display="none";
        filterOptions.classList.remove("active");
    });
    filter.addEventListener("click",function(){
        filterOptions.classList.add("active");
        document.querySelector(".filter-options-overlay").style.display="block";
    });
    
    if(document.querySelector("#formComment")){
        let formComment = document.querySelector("#formComment");
        formComment.addEventListener("submit",function(e){
            e.preventDefault();
            let formData = new FormData(formComment);
            let strComment = document.querySelector("#txtDescription").value;
            let alert = document.querySelector("#alertComment");
            let addComment = document.querySelector("#addComment");
            if(strComment ==""){
                alert.classList.remove("d-none");
                alert.classList.replace("alert-warning","alert-danger");
                alert.innerHTML = "Please write your comment.";
                return false;
            }
            addComment.setAttribute("disabled","disabled");
            addComment.innerHTML = `<span class="spinner-border text-primary spinner-border-sm" role="status" aria-hidden="true"></span>`;
            request(base_url+"/blog/setComment",formData,"post").then(function(objData){
                addComment.removeAttribute("disabled");
                addComment.innerHTML="Post comment";
                if(objData.status){
                    alert.classList.add("d-none");
                    document.querySelector("#txtDescription").value="";
                    document.querySelector(".comment-list").innerHTML= objData.html;
                    document.querySelector("#totalComments").innerHTML =`Comments (${objData.total})`;
                    //showMore(document.querySelectorAll(".comment-block"),4,document.querySelector("#showMore"));
                }else if(objData.login == false){
                    alert.classList.remove("d-none");
                    alert.classList.replace("alert-warning","alert-danger");
                    alert.innerHTML = objData.msg;
                    openLoginModal();
                }else{
                    alert.classList.remove("d-none");
                    alert.classList.replace("alert-warning","alert-danger");
                    alert.innerHTML = objData.msg;
                }
            });
        });
    }
    showMore(document.querySelectorAll(".comment-block"),4,document.querySelector("#showMore"));
})
function editComment(id,element){
    if(document.querySelector("#formReplyComment"))document.querySelector("#formReplyComment").remove();
    let html = `<form id="formReplyComment" class="mt-2 mb-2">
                        <input type="hidden" name="idComment" id="idCommentReply" value="${id}">
                        <textarea class="form-control" id="txtDescriptionReply" name="txtDescription" rows="3" placeholder="Escribe tu comentario"></textarea>
                        <button type="submit" class="btn btnc-primary mt-2" id="editComment">Actualizar</button>
                    </form>`;
    element.innerHTML=html;
    request(base_url+"/blog/getComment/"+id,"","get").then(function(objData){
        let formEditComment = document.querySelector("#formReplyComment");
        document.querySelector("#idCommentReply").value=objData.data.idcomment;
        document.querySelector("#txtDescriptionReply").value=objData.data.description;
        let idArticle = document.querySelector("#idArticle").value;
        formEditComment.addEventListener("submit",function(e){
            e.preventDefault();
            let strDescription = document.querySelector("#txtDescriptionReply").value;
            if(strDescription ==""){
                return false;
            }
            let formData = new FormData(formEditComment);
            formData.append('idArticle',idArticle);
            request(base_url+"/blog/setComment",formData,"post").then(function(objData){
                if(objData.status){
                    document.querySelector(".comment-list").innerHTML = objData.html;
                    document.querySelector("#totalComments").innerHTML =`Comentarios (${objData.total})`;
                }
            });
        });
        
    });
}
function deleteComment(id){
    let idArticle = document.querySelector("#idArticle").value;
    let formData = new FormData();
    formData.append("idArticle",idArticle);
    formData.append("idComment",id);
    request(base_url+"/blog/delComment",formData,"post").then(function(objData){
        if(objData.status){
            document.querySelector(".comment-list").innerHTML= objData.html;
            document.querySelector("#totalComments").innerHTML =`Comentarios (${objData.total})`;
            //showMore(document.querySelectorAll(".comment-block"),4);
        }
    });
}
function replyComment(id,element){
    if(document.querySelector("#formReplyComment"))document.querySelector("#formReplyComment").remove();
    let html = `<form id="formReplyComment" class="mt-2 mb-2">
                        <input type="hidden" name="idComment" id="idCommentReply" value="${id}">
                        <textarea class="form-control" id="txtDescriptionReply" name="txtDescription" rows="3" placeholder="Escribe tu comentario"></textarea>
                        <button type="submit" class="btn btnc-primary mt-2" id="editComment">Comentar</button>
                    </form>`;
    element.innerHTML=html;
    let formReplyComment = document.querySelector("#formReplyComment");
    formReplyComment.addEventListener("submit",function(e){
        e.preventDefault();
        let strDescription = document.querySelector("#txtDescriptionReply").value;
        if(strDescription ==""){
            return false;
        }
        let idArticle = document.querySelector("#idArticle").value;
        let formData = new FormData(formReplyComment);
        formData.append("idArticle",idArticle);
        request(base_url+"/blog/setReply",formData,"post").then(function(objData){
            if(objData.status){
                document.querySelector(".comment-list").innerHTML= objData.html;
                document.querySelector("#totalComments").innerHTML =`Comentarios (${objData.total})`;
            }
        });
    });
}
function showReplies(btn,element){
    if(element.className.includes("d-none")){
        element.classList.remove("d-none");
        btn.innerHTML = "Mostrar menos";
    }else{
        element.classList.add("d-none");
        btn.innerHTML = "Mostrar más";
    }
}
function editReply(id,element){
    if(document.querySelector("#formReplyComment"))document.querySelector("#formReplyComment").remove();
    let html = `<form id="formReplyComment" class="mt-2">
                        <input type="hidden" name="idReply" id="idReply" value="${id}">
                        <textarea class="form-control" id="txtDescriptionReply" name="txtDescription" rows="3" placeholder="Escribe tu comentario"></textarea>
                        <button type="submit" class="btn btnc-primary mt-2" id="editComment">Actualizar</button>
                    </form>`;
    element.innerHTML=html;
    request(base_url+"/blog/getReply/"+id,"","get").then(function(objData){
        
        let formEditComment = document.querySelector("#formReplyComment");
        let idArticle = document.querySelector("#idArticle").value;
        document.querySelector("#txtDescriptionReply").value=objData.data.description;
        
        formEditComment.addEventListener("submit",function(e){
            e.preventDefault();
            let strDescription = document.querySelector("#txtDescriptionReply").value;
            if(strDescription ==""){
                return false;
            }
            let formData = new FormData(formEditComment);
            formData.append('idArticle',idArticle);
            request(base_url+"/blog/setReply",formData,"post").then(function(objData){
                if(objData.status){
                    document.querySelector(".comment-list").innerHTML = objData.html;
                    document.querySelector("#totalComments").innerHTML =`Comentarios (${objData.total})`;
                }
            });
        });
        
    });
}
function deleteReply(id){
    let idArticle = document.querySelector("#idArticle").value;
    let formData = new FormData();
    formData.append("idArticle",idArticle);
    formData.append("idReply",id);
    request(base_url+"/blog/delReply",formData,"post").then(function(objData){
        if(objData.status){
            document.querySelector(".comment-list").innerHTML= objData.html;
            document.querySelector("#totalComments").innerHTML =`Comentarios (${objData.total})`;
            //showMore(document.querySelectorAll(".comment-block"),4);
        }
    });
}