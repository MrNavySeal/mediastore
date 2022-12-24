window.addEventListener("load",function(){
    
    let featured = document.querySelector(".featured-container-items");
    let left = document.querySelector(".featured-btn-left");
    let right = document.querySelector(".featured-btn-right");
    let filter = document.querySelector("#filter");
    let filterOptions = document.querySelector(".filter-options");
    let filterOverlay = document.querySelector(".filter-options-overlay");
    const items = Array.from(document.querySelectorAll(".product-item"));
    const paginationbtns = document.querySelector(".pagination-pag ul");
    const listItems = document.querySelector("#productItems");
    const prev = document.querySelector(".pagination-prev");
    const next = document.querySelector(".pagination-next");
    const start = document.querySelector(".pagination-start");
    const end = document.querySelector(".pagination-end");

    let current = 1;
    let rows = 12;
    let max = 3;

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

    
    paginationbtns.addEventListener("click",function(e){
        
        if(e.target.getAttribute("data-page")!=null){
            let current =e.target.getAttribute("data-page");
            displayList(items,listItems,rows,current,paginationbtns,max);
        }
        filterPrice();
    });
    start.addEventListener("click",function(){
        displayList(items,listItems,rows,1,paginationbtns,max);
        filterPrice();
    });
    end.addEventListener("click",function(){
        let end = Math.ceil(items.length/rows);
        displayList(items,listItems,rows,end,paginationbtns,max);
        filterPrice();
    });
    prev.addEventListener("click",function(){
        let current = document.querySelector(".page.active").getAttribute("data-page");
        if(current == 1){
            current = 1;
        }else{
            current--;
        }
        displayList(items,listItems,rows,current,paginationbtns,max);
        filterPrice();
    });
    next.addEventListener("click",function(){
        let end = Math.ceil(items.length/rows);
        let current = document.querySelector(".page.active").getAttribute("data-page");
    
        if(end == current){
            current = end;
        }else{
            current++;
        }
        displayList(items,listItems,rows,current,paginationbtns,max);
        filterPrice();
    });

    displayList(items,listItems,rows,current,paginationbtns,max);
})
function displayList(items,list,rows,current,paginationBtn,max){
    list.innerHTML="";
    let display ="";
    current--;

    let start = rows*current;
    let end = start+rows;
    let paginated = items.slice(start,end);

    for (let i = 0; i < paginated.length; i++) {
        let div = document.createElement("div");
        div.appendChild(paginated[i]);
        display+=div.innerHTML;
    }
    list.innerHTML = display;
    displayBtns(items,rows,current,paginationBtn,max);
}
function displayBtns(items,rows,current,paginationbtns,max){
    current++;
    let total = Math.ceil(items.length/rows);
    let half = Math.round(max/2);
    if(total < max){
        max = total;
    }
    let to = max;
    let html="";
    
    
    if(current + half > total){
        to = total;
    }else if(current>half){
        to = current+half;
    }
    
    let from = to-max;
    let buttons = Array.from({length:max},(v,i)=>(i+1)+from);
    for (let i = 0; i < buttons.length; i++) {
        if(buttons[i]==current){
            html+=` <a href="#" class="page active" data-page="${buttons[i]}">${buttons[i]}</a>`; 
        }else{
            html+=` <a href="#" class="page" data-page="${buttons[i]}">${buttons[i]}</a>`; 
        }
    }
    paginationbtns.innerHTML = html;
}

