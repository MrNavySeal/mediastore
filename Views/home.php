<?php
    headerPage($data);
    
    $slider = $data['slider'];
    $categories1 = $data['category1'];
    $categories2 = $data['category2'];
    $products = $data['products'];
    $popProducts = $data['popProducts'];
    $recPosts = $data['recPosts'];
    //dep($slider);exit;
?>
    <div id="modalItem"></div>
    <div id="modalPoup"></div>
    <main id="<?=$data['page_name']?>">
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
        <section>
            <div id="carouselExampleControls" class="carousel slide main-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                <?php
                    for ($i=0; $i < count($slider) ; $i++) { 
                        $active="";
                        if($i == 0){
                            $active="active";
                        }
                ?>
                <div class="carousel-item <?=$active?>">
                    <img src="<?=media()."/images/uploads/".$slider[$i]['picture']?>" alt="<?=$slider[$i]['name']?>">
                    <div class="carousel-info">
                        <span>Nueva colección</span>
                        <h2><?=$slider[$i]['name']?></h2>
                        <h3><?=$slider[$i]['description']?></h3>
                        <a href="<?=base_url()."/shop/category/".$slider[$i]['route']?>" class="btn btnc-primary fs-5">Comprar ahora</a>
                    </div>
                </div>
                <?php  }?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>
        <section>
            <div class="container mt-5 mb-5">
                <h3 class="t-p"><strong>CATEGORÍAS</strong></h3>
                <div class="row">
                    <?php 
                        for ($i=0; $i < count($categories1); $i++) { 
                            $route = base_url()."/shop/category/".$categories1[$i]['route'];
                            $url = media()."/images/uploads/".$categories1[$i]['picture'];
                        
                    ?>
                    <div class="col-md-4">
                        <a href="<?=$route?>" class="text-decoration-none">
                            <div class="category">
                                <img src="<?=$url?>" alt="<?=$categories1[$i]['name']?>">
                                <div class="category-info">
                                    <h3><strong><?=$categories1[$i]['name']?></strong></h3>
                                </div>
                                <a href="<?=$route?>" class="category-btn"><strong>Comprar ahora</strong></a>
                            </div>
                        </a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </section>
        <section>
            <div class="container">
                <h3 class="t-p"><strong>NUEVOS PRODUCTOS</strong></h3>
                <div class="row mt-5">
                <?php
                    for ($i=0; $i < count($products) ; $i++) { 
                        $idProduct = openssl_encrypt($products[$i]['idproduct'],METHOD,KEY);
                        $favorite = '';
                        $routeP = base_url()."/shop/product/".$products[$i]['route'];
                        $routeC = base_url()."/shop/category/".$products[$i]['routec'];
                        $price ='<p class="m-0 fs-5 product-price"><strong>'.formatNum($products[$i]['price']).'</strong></p>';
                        $btnAdd ='<button type="button" class="btn btn-primary" onclick="addProduct(this)" data-id="'.$idProduct.'">Agregar</button>';
                        $discount="";
                        $rate="";
                        if($products[$i]['favorite']== 0){
                            $favorite = '<button type="button" onclick="addWishList(this)" data-id="'.$idProduct.'" class="btn pe-2 ps-2 "><i class="far fa-heart " data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar a favoritos"></i></button>';
                        }else{
                            $favorite = '<button type="button" onclick="addWishList(this)" data-id="'.$idProduct.'" class="btn pe-2 ps-2 active"><i class="fas fa-heart text-danger " data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar a favoritos"></i></button>';
                        }
                        if($products[$i]['status'] == 1 && $products[$i]['stock']>0){
                            if($products[$i]['discount']>0){
                                $price = '<p class="m-0 fs-5 product-price"><strong>'.formatNum($products[$i]['priceDiscount']).'</strong><span>'.formatNum($products[$i]['price']).'</span></p>';
                                $discount ='<p class="product-discount">-'.$products[$i]['discount'].'%</p>';
                            }
                        }else if($products[$i]['status'] == 1 && $products[$i]['stock']==0){
                            $btnAdd="";
                            $price='<p class="m-0 fs-5 product-price text-danger">Agotado</p>';
                        }else{
                            $btnAdd ="";
                            $price="";
                        }
                        for ($j=0; $j < 5; $j++) { 
                            if($products[$i]['rate']!=null && $j >= intval($products[$i]['rate'])){
                                $rate.='<i class="far me-1 fa-star"></i>';
                            }else if($products[$i]['rate']==null){
                                $rate.='<i class="far me-1 fa-star"></i>';
                            }else{
                                $rate.='<i class="fas me-1 fa-star"></i>';
                            }
                        }
                ?> 
                <div class="col-md-3 mb-2" data-id="<?=$idProduct?>">
                    <div class="product-card">
                        <?=$discount?>
                        <div class="product-img">
                            <img src="<?=$products[$i]['url']?>" alt="<?=$products[$i]['name']?>">
                            <?=$btnAdd?>
                        </div>
                        <div class="product-info">
                            <a class="m-0 product-category fw-bold" href="<?=$routeC?>"><?=$products[$i]['category']?></a>
                            <a href="<?=$routeP?>">
                                <h3 class="product-title fw-bold"><?=$products[$i]['name']?></h3>
                                <?=$price?>
                            </a>
                        </div>
                        <div class="product-rate">
                            <?=$rate?>
                        </div>
                        <div class="product-btns">
                            <?=$favorite?>
                            <button type="button" class="btn pe-2 ps-2" onclick="quickModal(this)" data-id="<?=$idProduct?>"><i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Vista rápida"></i></button>
                        </div>
                    </div>
                </div>
                <?php  }?>
                </div>
            </div>
        </section>
        <section>
            <div class="container mt-4 mb-4">
                <div class="row">
                    <?php 
                        for ($i=0; $i < count($categories2) ; $i++) { 
                            $route = base_url()."/shop/category/".$categories2[$i]['route'];
                            $url = media()."/images/uploads/".$categories2[$i]['picture'];
                            
                    ?>
                    <div class="col-md-4 mb-3">
                        <div class="more-category">
                            <img src="<?=$url?>" alt="<?=$categories2[$i]['name']?>">
                            <div class="more-category-info">
                                <a href="<?=$route?>"><h3><strong><?=$categories2[$i]['name']?></strong></h3></a>
                                <p>Navegue por todas nuestras categorías</p>
                                <a href="<?=$route?>" class="btn">Comprar por <?=$categories2[$i]['name']?></a>
                                <div></div>
                            </div>
                        </div>
                    </div>
                    <?php }?>
                </div>
            </div>
        </section>
        <section>
            <?php if(!empty($popProducts)){?>
            <div class="container">
                <h3 class="t-p"><strong>DESTACADOS</strong></h3>
                <div class="row mt-5">
                <?php
                    for ($i=0; $i < count($popProducts) ; $i++) { 
                        $idProduct = openssl_encrypt($popProducts[$i]['idproduct'],METHOD,KEY);
                        $favorite = '';
                        $routeP = base_url()."/shop/product/".$popProducts[$i]['route'];
                        $routeC = base_url()."/shop/category/".$popProducts[$i]['routec'];
                        $price ='<p class="m-0 fs-5 product-price"><strong>'.formatNum($popProducts[$i]['price']).'</strong></p>';
                        $btnAdd ='<button type="button" class="btn btn-primary" onclick="addProduct(this)" data-id="'.$idProduct.'">Agregar</button>';
                        $discount="";
                        $rate="";
                        if($products[$i]['favorite']== 0){
                            $favorite = '<button type="button" onclick="addWishList(this)" data-id="'.$idProduct.'" class="btn pe-2 ps-2 "><i class="far fa-heart " data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar a favoritos"></i></button>';
                        }else{
                            $favorite = '<button type="button" onclick="addWishList(this)" data-id="'.$idProduct.'" class="btn pe-2 ps-2 active"><i class="fas fa-heart text-danger " data-bs-toggle="tooltip" data-bs-placement="top" title="Agregar a favoritos"></i></button>';
                        }
                        if($popProducts[$i]['status'] == 1 && $popProducts[$i]['stock']>0){
                            if($popProducts[$i]['discount']>0){
                                $price = '<p class="m-0 fs-5 product-price"><strong>'.formatNum($popProducts[$i]['priceDiscount']).'</strong><span>'.formatNum($popProducts[$i]['price']).'</span></p>';
                                $discount ='<p class="product-discount">-'.$popProducts[$i]['discount'].'%</p>';
                            }
                        }else if($popProducts[$i]['status'] == 1 && $popProducts[$i]['stock']==0){
                            $btnAdd="";
                            $price='<p class="m-0 fs-5 product-price text-danger">Agotado</p>';
                        }else{
                            $btnAdd ="";
                            $price="";
                        }
                        for ($j=0; $j < 5; $j++) { 
                            if($j >= intval($popProducts[$i]['rate'])){
                                
                                $rate.='<i class="far me-1 fa-star"></i>';
                            }else{
                                $rate.='<i class="fas me-1 fa-star"></i>';
                            }
                        }
                        
                ?> 
                <div class="col-md-3 mb-2" data-id="<?=$idProduct?>">
                    <div class="product-card">
                        <?=$discount?>
                        <div class="product-img">
                            <img src="<?=$popProducts[$i]['url']?>" alt="<?=$popProducts[$i]['name']?>">
                            <?=$btnAdd?>
                        </div>
                        <div class="product-info">
                            <a class="m-0 product-category fw-bold" href="<?=$routeC?>"><?=$popProducts[$i]['category']?></a>
                            <a href="<?=$routeP?>">
                                <h3 class="product-title fw-bold"><?=$popProducts[$i]['name']?></h3>
                                <?=$price?>
                            </a>
                        </div>
                        <div class="product-rate">
                            <?=$rate?>
                        </div>
                        <div class="product-btns">
                            <?=$favorite?>
                            <button type="button" class="btn quickView pe-2 ps-2" onclick="quickModal(this)" data-id="<?=$idProduct?>"><i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Vista rápida"></i></button>
                        </div>
                    </div>
                </div>
                <?php  }?>
                </div>
            </div>
            <?php }?>
        </section>
        <section class="container mb-5">
            <?php if(!empty($recPosts)){?>
                <div class="row mt-5">
                    <h3 class="t-p mb-3"><strong>NUESTRO BLOG</strong></h3>
                    <?php 
                    for ($i=0; $i < count($recPosts) ; $i++) { 
                        $routePosts = base_url()."/blog/article/".$recPosts[$i]['route'];
                        $imgPost =media()."/images/uploads/category.jpg";
                        if($recPosts[$i]['picture'] !=""){
                            $imgPost = media()."/images/uploads/".$recPosts[$i]['picture'];
                        }
                    ?>
                    <div class="col-lg-4 col-md-6 mb-3 product-item">
                        <div class="card card-post">
                            <img src="<?=$imgPost?>" alt="<?=$recPosts[$i]['name']?>">
                            <div class="card-body">
                                <a href="<?=$routePosts?>" class="text-decoration-none text-dark "><h2 class="card-title fs-5 overflow-hidden"><?=$recPosts[$i]['name']?></h2></a>
                                <div class="card-text overflow-hidden">
                                    <?=$recPosts[$i]['description']?>
                                </div>
                                <a href="<?=$routePosts?>" class="btn btnc-primary mt-1">Leer más</a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
        </section>
    </main>
<?php
    footerPage($data);
?>
    