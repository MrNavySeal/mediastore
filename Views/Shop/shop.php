<?php
    headerPage($data);
    $categories = $data['categories'];
    $products = $data['products'];
    $popProducts = $data['popProducts'];
    $arrPrice =array();
    for ($i=0; $i < count($products) ; $i++) { 
        array_push($arrPrice,$products[$i]['price']);
    }
    $maxPrice = max($arrPrice);
    $minPrice = min($arrPrice);
?>
    <div id="modalItem"></div>
    <div id="modalPoup"></div>
    <main class="addFilter">
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
        <div class="container-fluid mt-5 mb-3">
            <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Tienda</li>
                </ol>
            </nav>
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
                                                $routeC = base_url()."/shop/category/".$categories[$i]['route'];
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
                                                            $routeS = base_url()."/shop/category/".$categories[$i]['route']."/".$subcategories['route'];
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
                        <div class="accordion accordion-flush" id="accordionFlushPrice">
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="flush-price">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapsePrice" aria-expanded="false" aria-controls="flush-collapsePrice">
                                  <strong class="fs-5">Precio</strong>
                                </button>
                              </h2>
                              <div id="flush-collapsePrice" class="accordion-collapse collapse show" aria-labelledby="flush-price" data-bs-parent="#accordionFlushPrice">
                                <div class="accordion-body">
                                    <div class="filter-price">
                                        <div class="track"></div>
                                        <input type="range" min="<?=$minPrice?>" value="<?=$minPrice?>" max="<?=$maxPrice?>">
                                        <input type="range" min="<?=$minPrice?>" value="<?=$maxPrice?>" max="<?=$maxPrice?>">
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-3">
                                        <span id="filter-price-info">Precio: <?=formatNum($minPrice)?> - <?=formatNum($maxPrice)?></span>
                                    </div>
                                </div>
                              </div>
                            </div>
                        </div>
                        <?php if(!empty($popProducts)>0){?>
                        <div class="featured">
                            <div class="featured-info">
                                <h2 class="fs-5"><strong>Destacados</strong></h2>
                                <div class="featured-btns">
                                    <div class="p-2 featured-btn-left c-p"><i class="fas fa-angle-left"></i></div>
                                    <div class="p-2 featured-btn-right c-p"><i class="fas fa-angle-right"></i></div>
                                </div>
                            </div>
                            <div class="featured-container-items">
                                <?php 
                                $index = 0;
                                
                                $column=round(count($popProducts)/3);
                                
                                if($column ==0){
                                    $column = 1;
                                }
                                
                                for ($i=0; $i < $column ; $i++) { 
                                ?>  
                                <div class="featured-items">
                                    <?php for ($j = 0 ;$j < 3 ; $j++) {
                                        if(count($popProducts) > $index){
                                            
                                            $price ='<p class="fs-6 t-p"><strong>'.formatNum($popProducts[$index]['price']).'</strong></p>';
                                            $discount="";
                                            $rate="";
                                            $routeP = base_url()."/shop/product/".$popProducts[$index]['route'];
                                            if($popProducts[$index]['status'] == 1 && $popProducts[$index]['stock']>0){
                                                if($popProducts[$index]['discount']>0){
                                                    $price = '<p class="fs-6"><strong class="t-p">'.formatNum($popProducts[$index]['priceDiscount']).'</strong> <span class="text-decoration-line-through t-p">'.formatNum($popProducts[$index]['price']).'</span></p>';
                                                    $discount ='<p class="position-absolute top-0 start-0 rounded t-p pe-1 ps-1 fw-bold text-white bg-d fs-6">-'.$popProducts[$index]['discount'].'%</p>';
                                                }
                                            }else if($popProducts[$index]['status'] == 1 && $popProducts[$index]['stock']==0){
                                                $price='<p class="text-danger">Agotado</p>';
                                            }else{
                                                $price="";
                                            }
                                            for ($k=0; $k < 5; $k++) { 
                                                if($k >= intval($popProducts[$index]['rate'])){
                                                    
                                                    $rate.='<i class="far me-1 fa-star"></i>';
                                                }else{
                                                    $rate.='<i class="fas me-1 fa-star"></i>';
                                                }
                                            }
                                    ?>
                                    <div class="featured-item">
                                        <div class="row position-relative">
                                            <div class="col-4 position-relative h-100">
                                                <?=$discount?>
                                                <img src="<?=$popProducts[$index]['url']?>" alt="<?=$popProducts[$index]['name']?>">
                                            </div>
                                            <div class="col-8 h-100">
                                                <h3 class="fs-6"><a href="<?=$routeP?>"><?=$popProducts[$index]['name']?></a></h3>
                                                <div class="product-rate text-start">
                                                    <?=$rate?>
                                                </div>
                                                <?=$price?>
                                            </div>
                                        </div>
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
                        <div class="d-flex align-items-center justify-content-center">
                            <label for="selectSort" class="form-label m-0 me-4">Ordenar por:</label>
                            <select class="form-select w-50" aria-label="Default select example" id="selectSort">
                                <option selected>Ordenar por</option>
                                <option value="1">Destacados</option>
                                <option value="2">Precio más alto a más bajo</option>
                                <option value="3">Precio más bajo a más alto</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-5" id="productItems">
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
                    <div class="col-md-3 product-item mb-3" data-id="<?=$idProduct?>" data-price="<?=$products[$i]['price']?>" data-rate="<?=$products[$i]['rate'] > 0 ? $products[$i]['rate'] : 0?>">
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
        </div>
    </main>
<?php
    footerPage($data);
?>