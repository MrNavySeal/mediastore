<?php
    headerPage($data);
    $review = $data['review'];
    $reviews = $data['reviews'];
    $product = $data['product'];
    $products = $data['products'];
    //dep($product['image']);exit;
    $urlShare = base_url()."/shop/product/".$product['route'];
    $idProductEncrypt= openssl_encrypt($product['idproduct'],METHOD,KEY);
    $status="";
    $rate = "";
    $price ='<p class="fs-3"><strong>'.formatNum($product['price']).'</strong></p>';
    $btns ='
    <div class="product-cant me-3">
        <div class="decrement"><i class="fas fa-minus"></i></div>
        <input class="cant me-2 ms-2" type="number" min="1" max="'.$product['stock'].'" value="1">
        <div class="increment"><i class="fas fa-plus"></i></div>
        <button type="button" class="ms-3" data-id="'.$idProductEncrypt.'" id="addProduct"><i class="fas fa-shopping-cart me-2"></i> Agregar</button>
    </div>
    ';
    $discount="";
    $favorite="";

    if($product['favorite']==1){
        $favorite = '<button type="button" class="c-p btn"><i class="fas fa-heart product-addwishlist me-1 text-danger active"></i> <a href="'.base_url().'/wishlist"class="c-p">Mis favoritos</a></button>';
    }else{
        $favorite = '<button type="button" class="c-p btn"><i class="far fa-heart product-addwishlist me-1"></i> <a class="c-d">Agregar a favoritos</a></button>';
    }
    if($product['status'] == 1 && $product['stock']>0){
        $status ='<p class="text-secondary m-0">Stock: ('.$product['stock'].') unidades</p>';
        if($product['discount']>0){
            $price = '<p class="m-0 fs-5 product-price"><strong>'.formatNum($product['priceDiscount']).'</strong> <span class="t-p text-decoration-line-through fs-6">'.formatNum($product['price']).'</span></p>';
            $discount ='<p class="product-discount">-'.$product['discount'].'%</p>';
        }
    }else if($product['status'] == 1 && $product['stock']==0){
        $btns="";
        $price='<p class="m-0 fs-5 product-price text-danger">Agotado</p>';
    }
    for ($i = 0; $i < 5; $i++) {
        if($product['rate']>0 && $i >= intval($product['rate'])){
            $rate.='<i class="far fa-star"></i>';
        }else if($product['rate'] == 0){
            $rate.='<i class="far fa-star"></i>';
        }else{
            $rate.='<i class="fas fa-star"></i>';
        }
    }
    
?>
    <div id="modalItem"></div>
    <main id="product">
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
        <div class="container mt-4 mb-4">
            <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb">
                  <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                  <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>/shop">Tienda</a></li>
                  <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()."/shop/category/".$product['routec']?>"><?=$product['category']?></a></li>
                  <li class="breadcrumb-item active" aria-current="page"><?=$product['name']?></li>
                </ol>
            </nav>
            <div class="row ps-2 pe-2 pb-4">
                <div class="col-md-6">
                    <div class="product-image">
                        <?=$discount?>
                        <img src="<?=$product['image'][0]['url']?>" alt="<?=$product['name']?>">
                    </div>
                    <div class="product-image-slider">
                        <div class="slider-btn-left"><i class="fas fa-angle-left"></i></div>
                        <div class="product-image-inner">
                            <?php
                                for ($i=0; $i < count($product['image']); $i++) { 
                                    if($i == 0){
                                        $active="active";
                                    }else{
                                        $active="";
                                    }
                            ?>
                            <div class="product-image-item <?=$active?>">
                                <img src="<?=$product['image'][$i]['url']?>" alt="<?=$product['name']?>">
                            </div>
                            <?php }?>
                        </div>
                        <div class="slider-btn-right"><i class="fas fa-angle-right"></i></div>
                    </div>
                </div>
                <div class="col-md-6 product-data">
                    <div id="idProduct" class="d-none" data-id="<?=$idProductEncrypt?>"></div>
                    <h1><strong><?=$product['name']?></strong></h1>
                    <div class="product-rate text-start mt-1">
                        <?=$rate?>
                        (<?=$product['reviews']?> reseñas)
                        
                    </div>
                    <?=$status?>
                    <?=$price?>
                    <p class="mb-3" id="description"><?=$product['shortdescription']?></p>
                    <p class="m-0">SKU: <strong><?=$product['reference']?></strong></p>
                    <a href="<?=base_url()."/shop/category/".$product['routec']?>" class="m-0">Categoría: <strong><?=$product['category']?></strong></a>
                    <div class="mt-4 mb-4 d-flex align-items-center">
                        <?=$btns?>
                    </div>
                    <div class="alert alert-warning d-none" id="alert" role="alert">
                        ¡Ups! No hay suficiente stock, inténtalo con menos o comprueba en tu cesta si has añadido todas nuestras unidades antes.
                    </div>
                    <div class="d-flex align-items-center mt-4">
                        <ul class="product-social">
                            <li title="Share on facebook"><a href="#" onclick="window.open('http://www.facebook.com/sharer.php?u=<?=$urlShare?>&t=<?=$product['name'];?>','share','toolbar=0,status=0,width=650,height=450')"><i class="fab fa-facebook-f"></i></a></li>
                            <li title="Share on twitter"><a href="#" onclick="window.open('https://twitter.com/intent/tweet?text=<?=$product['name'];?>&url=<?=$urlShare?>&hashtags=<?=SHAREDHASH?>','share','toolbar=0,status=0,width=650,height=450')"><i class="fab fa-twitter"></i></a></li>
                            <li title="Share on linkedin"><a href="#" onclick="window.open('http://www.linkedin.com/shareArticle?url=<?=$urlShare?>','share','toolbar=0,status=0,width=650,height=450')"><i class="fab fa-linkedin-in"></i></a></li>
                            <li title="Share on whatsapp"><a href="#" onclick="window.open('https://api.whatsapp.com/send?text=<?=$urlShare?>','share','toolbar=0,status=0,width=650,height=450')"><i class="fab fa-whatsapp"></i></a></li>
                        </ul>
                        <?=$favorite?>
                    </div>
                </div>
            </div>
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item position-relative" role="presentation">
                  <button class="nav-link active" id="pills-description-tab" data-bs-toggle="pill" data-bs-target="#pills-description" type="button" role="tab" aria-controls="pills-description" aria-selected="true">Descripción</button>
                </li>
                <li class="nav-item position-relative" role="presentation">
                  <button class="nav-link" id="pills-reviews-tab" data-bs-toggle="pill" data-bs-target="#pills-reviews" type="button" role="tab" aria-controls="pills-reviews" aria-selected="false">Reseñas (<?=$product['reviews']?>)</button>
                </li>
            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-description" role="tabpanel" aria-labelledby="pills-description-tab">
                    <?=$product['description']?>
                </div>
                <div class="tab-pane fade" id="pills-reviews" role="tabpanel" aria-labelledby="pills-reviews-tab">
                    <div class="review-general">
                        <div class="row mb-3">
                            <div class="col-md-3 text-center">
                                <h2 class="fs-5"><strong>Reseñas de <?=$product['name']?></strong></h2>
                                <p class="fs-1 fw-bold t-p" id="avgRate"><?=$product['rate']?><span class="fs-6">/ 5</span></p>
                                <div class="product-rate mb-3">
                                    <?=$rate?>
                                </div>
                                <p class="fw-bold">Raiting promedio</p>
                            </div>
                            <div class="col-md-9">
                                <div class="row h-75 mb-4 mt-4">
                                    <div class="col-lg-3 col-5 t-p d-flex justify-content-between flex-column align-items-center">
                                        <div class="review-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(<?=$product['reviews'] == 0 ? 0 : $review['five']?>)</span>
                                        </div>
                                        <div class="review-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span>(<?=$product['reviews'] == 0 ? 0 : $review['four']?>)</span>
                                        </div>
                                        <div class="review-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span>(<?=$product['reviews'] == 0 ? 0 : $review['three']?>)</span>
                                        </div>
                                        <div class="review-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span>(<?=$product['reviews'] == 0 ? 0 : $review['two']?>)</span>
                                        </div>
                                        <div class="review-stars">
                                            <i class="fas fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <i class="far fa-star"></i>
                                            <span>(<?=$product['reviews'] == 0 ? 0 : $review['one']?>)</span>
                                        </div>
                                    </div>
                                    <div class="col-lg-9 col-7 d-flex justify-content-between flex-column">
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?= $product['reviews'] == 0 ? 0 : ($review['five']/$product['reviews'])*100 ?>%" aria-valuenow="<?=$product['reviews'] == 0 ? 0 : ($review['five']/$product['reviews'])*100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?=$product['reviews'] == 0 ? 0 : ($review['four']/$product['reviews'])*100 ?>%" aria-valuenow="<?=$product['reviews'] == 0 ? 0 : ($review['four']/$product['reviews'])*100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?=$product['reviews'] == 0 ? 0 : ($review['three']/$product['reviews'])*100 ?>%" aria-valuenow="<?=$product['reviews'] == 0 ? 0 : ($review['three']/$product['reviews'])*100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?=$product['reviews'] == 0 ? 0 : ($review['two']/$product['reviews'])*100 ?>%" aria-valuenow="<?=$product['reviews'] == 0 ? 0 : ($review['two']/$product['reviews'])*100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" style="width: <?=$product['reviews'] == 0 ? 0 : ($review['one']/$product['reviews'])*100 ?>%" aria-valuenow="<?=$product['reviews'] == 0 ? 0 : ($review['one']/$product['reviews'])*100 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <p class="fs-4 fw-bold">Filtros</p>
                        <div class="col-md-6 mb-3">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search" id="searchReview" name="searchReview">        
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3 d-flex align-items-center">
                                    <label for="selectSort" class="form-label m-0 me-4">Ordenar por:</label>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-select" aria-label="Default select example" id="sortReviews">
                                        <option value="1">Más reciente</option>
                                        <option value="2">Lo más relevante</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <ul class="comment-list mt-3">
                        <?=$reviews['html']?>
                        <button type="button" class="btn t-p d-none" id="showMore">Mostrar más</button>
                    </ul>
                    
                    <div class="d-flex justify-content-center mb-3">
                        <form id="formReview" class="w-100">
                            <input type="hidden" name="idReview" id="idReview" value="">
                            <input type="hidden" name="intRate" id="intRate" value="0">
                            <input type="hidden" name="idProduct" id="idProduct" value="<?=openssl_encrypt($product['idproduct'],METHOD,KEY)?>">
                            <h3 class="mb-3 text-center">Comparte tu reseña</h3>
                            <div class="d-flex justify-content-center">
                                <div class="review-rate mb-3">
                                    <button type="button" class="starBtn"><i class="far fa-star"></i></button>
                                    <button type="button" class="starBtn"><i class="far fa-star"></i></button>
                                    <button type="button" class="starBtn"><i class="far fa-star"></i></button>
                                    <button type="button" class="starBtn"><i class="far fa-star"></i></button>
                                    <button type="button" class="starBtn"><i class="far fa-star"></i></button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" id="txtReview" name="txtReview" rows="5" placeholder="Escribe tu reseña"></textarea>
                            </div>
                            <div class="alert alert-danger d-none" id="alertReview" role="alert"></div>
                            <button type="submit" class="btn btnc-primary" id="addReview">compartir</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <section>
            <div class="container mb-3">
                <h3 class="t-p"><strong>MÁS PRODUCTOS</strong></h3>
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
                <div class="col-md-3 mb-3" data-id="<?=$idProduct?>">
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
    </main>
<?php
    footerPage($data);
?>