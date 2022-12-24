<?php
    headerPage($data);
    $article = $data['article'];
    $categories = $data['categories'];
    $recPosts = $data['recPosts'];
    $relPosts = $data['relPosts'];
    $urlShare = base_url()."/blog/article/".$article['route'];
    //$comments=$data['comments'];
    //dep($data['comments']);exit;
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
<main class="addFilter" id="<?=$data['page_name']?>">
    <div class="container">
        <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>/blog">Blog</a></li>
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()."/blog/category/".$article['routec']?>"><?=$article['category']?></a></li>
                <li class="breadcrumb-item active" aria-current="page"><?=$article['name']?></li>
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
                <article class="post">
                    <?php
                        if($article['picture']!=""){
                    ?>
                    <div class="post-cover">
                        <img src="<?=media()."/images/uploads/".$article['picture']?>" alt="<?=$article['name']?>">
                    </div>
                    <?php }?>
                    <div class="post-content">
                        <h1 class="fw-bold t-p"><?=$article['name']?></h1>
                        <div class="d-flex justify-content-between mb-3">
                            <div>
                                <p class="m-0 text-secondary">Publicado el <?=$article['date']?></p>
                                <p class="m-0 text-secondary">Última actualización <?=$article['dateupdated']?></p>
                            </div>
                            <a class="text-decoration-none t-p" href="#sharePost" id="totalComments">Comentarios (<?=$data['comments']['total']?>)</a>
                        </div>
                        <div>
                            <?=$article['description']?>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between position-relative af-t-line mt-4 mb-4" id="sharePost">
                        <div class="mt-2"><i class="fas fa-share"></i> Compartir este artículo</div>
                        <div class="mt-2">
                            <a href="#" onclick="window.open('http://www.facebook.com/sharer.php?u=<?=$urlShare?>&t=<?=$article['name'];?>','share','toolbar=0,status=0,width=650,height=450')" title="Compartir en facebook" class="me-2 ms-2 text-dark fs-6"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" onclick="window.open('https://twitter.com/intent/tweet?text=<?=$article['name'];?>&url=<?=$urlShare?>&hashtags=<?=SHAREDHASH?>','share','toolbar=0,status=0,width=650,height=450')" title="Compartir en linkedin" class="me-2 ms-2 text-dark fs-6"><i class="fab fa-twitter"></i></a>
                            <a href="#" onclick="window.open('http://www.linkedin.com/shareArticle?url=<?=$urlShare?>','share','toolbar=0,status=0,width=650,height=450')" title="Compartir en twitter" class="me-2 ms-2 text-dark fs-6"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" onclick="window.open('https://api.whatsapp.com/send?text=<?=$urlShare?>','share','toolbar=0,status=0,width=650,height=450')" title="Compartir en whatsapp" class="me-2 ms-2 text-dark fs-6"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    <ul class="comment-list mt-3">
                        <?=$data['comments']['html']?>
                        <button type="button" class="btn t-p d-none" id="showMore">Más comentarios +</button>
                    </ul>
                    <div class="d-flex justify-content-center mb-3">
                        <form action="" id="formComment" class="w-100">
                            <input type="hidden" name="idArticle" id="idArticle" value="<?=$article['idarticle']?>">
                            <input type="hidden" name="idComment" id="idComment" value="">
                            <h3 class="mb-3 text-center">Haz un comentario!</h3>
                            <div class="mb-3">
                                <textarea class="form-control" id="txtDescription" name="txtDescription" rows="5" placeholder="Escribe tu comentario"></textarea>
                            </div>
                            <div class="alert alert-danger d-none" id="alertComment" role="alert"></div>
                            <button type="submit" class="btn btnc-primary" id="addComment">Comentar</button>
                        </form>
                    </div>
                    <?php if(!empty($relPosts)){?>
                    <div class="row mt-5 mb-5">
                        <h3 class="t-p mb-3"><strong>ARTÍCULOS RELACIONADOS</strong></h3>
                        <?php 
                        for ($i=0; $i < count($relPosts) ; $i++) { 
                            $routePosts = base_url()."/blog/article/".$relPosts[$i]['route'];
                            $imgPost =media()."/images/uploads/category.jpg";
                            if($relPosts[$i]['picture'] !=""){
                                $imgPost = media()."/images/uploads/".$relPosts[$i]['picture'];
                            }
                        ?>
                        <article class="col-lg-4 col-md-6 mb-3 product-item">
                            <div class="card card-post">
                                <img src="<?=$imgPost?>" alt="<?=$relPosts[$i]['name']?>">
                                <div class="card-body">
                                    <a href="<?=$routePosts?>" class="text-decoration-none text-dark "><h2 class="card-title fs-5 overflow-hidden" ><?=$relPosts[$i]['name']?></h2></a>
                                    <div class="card-text overflow-hidden">
                                        <?=$relPosts[$i]['description']?>
                                    </div>
                                    <a href="<?=$routePosts?>" class="btn btnc-primary mt-1">Leer más</a>
                                </div>
                            </div>
                        </article>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </article>
            </div>
        </div>
    </div>
</main>
<?php
    footerPage($data);
?>