<?php
    headerPage($data);
    $categories = $data['categories'];
    $recPosts = $data['recPosts'];
    $posts = $data['posts'];
    //$posts = array();
    //dep($recPosts);exit;
    
?>
<div id="modalItem"></div>
<div class="popup">
    <div class="popup-close">X</div>
    <div class="popup-info">
        <img src="" alt="">
        <div class="h-100">
            <a href="product.html">Product 1</a>
            <p>Ha sido agregado a tu carrito</p>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center text-center mt-3">
        <a href="<?=base_url()?>/shop/cart" class="btnc w-50 p-1 btnc-primary me-4">Mi carrito</a>
        <div class="btnc w-50 h-100 p-1 btnc-primary c-p" id="btnCheckOutPopup">Pagar</div>
    </div>
</div>
<main class="addFilter">
    <div class="container">
        <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Blog</li>
            </ol>
        </nav>
        <?php if(!empty($posts)){?>
        <div class="row">
            <div class="col-lg-3">
                <aside class="filter-options p-2">
                <div class="accordion accordion-flush" id="accordionFlushCategories">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="flush-categories">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseCategories" aria-expanded="false" aria-controls="flush-collapseCategories">
                            <strong class="fs-5">Categorias</strong>
                        </button>
                        </h2>
                        <div id="flush-collapseCategories" class="accordion-collapse collapse show" aria-labelledby="flush-categories" data-bs-parent="#accordionFlushCategories">
                        <div class="accordion-body">
                            <div class="accordion accordion-flush" id="accordionFlushCategorie">
                                <?php
                                    for ($i=0; $i < count($categories) ; $i++) { 
                                        $routeC = base_url()."/blog/category/".$categories[$i]['route'];
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="flush-categorie<?=$i?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseCategorie<?=$i?>" aria-expanded="false" aria-controls="flush-collapseCategorie<?=$i?>">
                                    </button>
                                    <a href="<?=$routeC?>" class="text-decoration-none"><?=$categories[$i]['name']?></a>
                                    </h2>
                                    <div id="flush-collapseCategorie<?=$i?>" class="accordion-collapse collapse show" aria-labelledby="flush-categorie<?=$i?>" data-bs-parent="#accordionFlushCategorie<?=$i?>">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <?php
                                                for ($j=0; $j < count($categories[$i]['subcategories']) ; $j++) { 
                                                    $subcategories = $categories[$i]['subcategories'][$j];
                                                    $routeS = base_url()."/blog/category/".$categories[$i]['route']."/".$subcategories['route'];
                                            ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <a href="<?=$routeS?>"><?=$subcategories['name']?></a>
                                                <span class="badge bg-p rounded-pill"><?=$subcategories['total']?></span>
                                            </li>
                                            <?php }?>
                                        </ul>
                                    </div>
                                    </div>
                                </div>
                                <?php }?>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
                <?php if(!empty($recPosts)>0){?>
                    <div class="featured">
                        <div class="featured-info">
                            <h2 class="fs-5"><strong>Más reciente</strong></h2>
                            <div class="featured-btns">
                                <div class="p-2 featured-btn-left c-p"><i class="fas fa-angle-left"></i></div>
                                <div class="p-2 featured-btn-right c-p"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                        <div class="featured-container-items">
                            <?php 
                            $index = 0;
                            
                            $column=round(count($recPosts)/3);
                            
                            if($column ==0){
                                $column = 1;
                            }
                            
                            for ($i=0; $i < $column ; $i++) { 
                            ?>  
                            <div class="featured-items">
                                <?php for ($j = 0 ;$j < 3 ; $j++) {
                                    if(count($recPosts) > $index){
                                        $routeP = base_url()."/blog/article/".$recPosts[$index]['route'];
                                ?>
                                <div class="featured-item">
                                    <article class="row">
                                        <?php
                                            $routeImg = media()."/images/uploads/category.jpg";
                                            if($recPosts[$index]['picture'] !=""){
                                                $routeImg = media()."/images/uploads/".$recPosts[$index]['picture']; 
                                            }
                                        ?>
                                        <div class="col-4">
                                            <img src="<?=$routeImg?>" alt="<?=$recPosts[$index]['name']?>">
                                        </div>
                                        <div class="col-8">
                                            <div style="height:48px" class="overflow-hidden">
                                                <h3><a href="<?=$routeP?>"><?=$recPosts[$index]['name']?></a></h3>
                                            </div>
                                            <p><?=$recPosts[$index]['date']?></p>
                                        </div>
                                    </article>
                                </div>

                                <?php $index++; }else{ break;} }?>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                    <?php }?>
                </aside>
                <div class="filter-options-overlay"></div>
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="d-flex align-items-center justify-content-between shop-options">
                    <div class="me-2 c-p" id="filter"><i class="fas fa-filter"></i>Filtro</div>
                </div>
                <div class="row mt-5" id="productItems">
                    <?php 
                        for ($i=0; $i < count($posts) ; $i++) { 
                            $routePosts = base_url()."/blog/article/".$posts[$i]['route'];
                            $imgPost =media()."/images/uploads/category.jpg";
                            if($posts[$i]['picture'] !=""){
                                $imgPost = media()."/images/uploads/".$posts[$i]['picture'];
                            }
                    ?>
                    <article class="col-lg-4 col-md-6 mb-3 product-item">
                        <div class="card card-post">
                            <img src="<?=$imgPost?>" alt="<?=$posts[$i]['name']?>">
                            <div class="card-body">
                                <a href="<?=$routePosts?>" class="text-decoration-none text-dark "><h2 class="card-title fs-5 overflow-hidden"><?=$posts[$i]['name']?></h2></a>
                                <div class="card-text overflow-hidden">
                                    <?=$posts[$i]['description']?>
                                </div>
                                <a href="<?=$routePosts?>" class="btn btnc-primary mt-1">Leer más</a>
                            </div>
                        </div>
                        </article>
                    <?php }?>
                </div>
                <div class="pagination">
                    <a href="#" class="pagination-btn pagination-start"><i class="fas fa-angle-double-left"></i></a>
                    <a href="#" class="pagination-btn pagination-prev"><i class="fas fa-angle-left"></i></a>
                    <div class="pagination-pag">
                        <ul>
                            
                        </ul>
                    </div>
                    <a href="#" class="pagination-btn pagination-next"><i class="fas fa-angle-right"></i></a>
                    <a href="#" class="pagination-btn pagination-end"><i class="fas fa-angle-double-right"></i></a>
                </div>
            </div>
        </div>
        <?php }else{?>
        <div class="p-5 m-5 text-center">
            <h1>Aún no tenemos articulos :(</h1>
        </div>
        <?php }?>
    </div>
</main>
<?php
    footerPage($data);
?>