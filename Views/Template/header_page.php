<?php
    $company = getCompanyInfo();
    $qtyCart = 0;
    $total = 0;
    $arrProducts = array();

    $title = $company['name'];
    $urlWeb = base_url();
    $urlImg =media()."/images/uploads/".$company['logo'];
    $description =$company['description'];
    //dep($data['article']);exit;
    if(!empty($data['product'])){
        $urlWeb = base_url()."/shop/product/".$data['product']['route'];
        $urlImg = $data['product']['image'][0];
        $title = $data['product']['name'];
        $description = $data['product']['shortdescription'];
    }else if(!empty($data['article'])){
        $urlWeb = base_url()."/blog/article/".$data['article']['route'];
        $urlImg = $data['article']['picture'];
        $title = $data['article']['name'];
        $description = $data['article']['description'];
    }

    if(isset($_SESSION['arrCart']) && !empty($_SESSION['arrCart'])){
        $arrProducts = $_SESSION['arrCart'];
        foreach ($arrProducts as $product) {
            $qtyCart += $product['qty'];
            if($product['discount']>0){
                $total += $product['qty']*($product['price']-($product['price']*($product['discount']*0.01)));
            }else{
                $total+=$product['qty']*$product['price'];
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?=$company['description']?>">
    <meta name="author" content="<?=$company['name']?>" />
    <meta name="copyright" content="<?=$company['name']?>"/>
    <meta name="robots" content="index,follow"/>
    <meta name="keywords" content="<?=$company['keywords']?>"/>

    <title><?=$data['page_title']?></title>
    <link rel ="shortcut icon" href="<?=media()."/images/uploads/".$company['logo']?>" sizes="114x114" type="image/png">

    <meta property="fb:app_id"          content="1234567890" /> 
    <meta property="og:locale" 		content='es_ES'/>
    <meta property="og:type"        content="article" />
    <meta property="og:site_name"	content="<?= $company['name']; ?>"/>
    <meta property="og:description" content="<?=$description?>"/>
    <meta property="og:title"       content="<?= $title; ?>" />
    <meta property="og:url"         content="<?= $urlWeb; ?>" />
    <meta property="og:image"       content="<?= $urlImg; ?>" />
    <meta name="twitter:card" content="summary"></meta>
    <meta name="twitter:site" content="<?= $urlWeb; ?>"></meta>
    <meta name="twitter:creator" content="<?= $company['name']; ?>"></meta>
    <link rel="canonical" href="<?= $urlWeb?>"/>

    <!------------------------------Frameworks--------------------------------->
    <link rel="stylesheet" href="<?=media();?>/frameworks/bootstrap/bootstrap.min.css">
    <!------------------------------Plugins--------------------------------->
    <link href="<?=media();?>/plugins/fontawesome/font-awesome.min.css">
    <!------------------------------------Styles--------------------------->
    <link rel="stylesheet" href="<?=media()."/template/Assets/css/style.css?v=".rand()?>">

</head>
<body>
    <header>
        <nav class="nav-custom">
            <div class="nav-logo">
                <a href="<?=base_url()?>">
                    <img src="<?=media()."/images/uploads/".$company['logo']?>" alt="<?=$company['name']?>">
                    <strong><?= $company['name']; ?></strong>
                </a>
            </div>
            <div class="nav-search d-none d-flex">
                <div class="w-100">
                    <input type="text" id="txtSearch" name="txtSearch" placeholder="Busca algo...">
                    <div class="search-items d-none"></div>
                </div>
                <div id="btnCloseSearch">X</div>
            </div>
            <div class="nav-main">
                <ul>
                    <li class="navigation"><a href="<?=base_url()?>/home">Inicio</a></li>
                    <li class="navigation"><a href="<?=base_url()?>/shop">Tienda</a></li>
                    <li class="navigation"><a href="<?=base_url()?>/blog">Blog</a></li>
                    <li class="navigation"><a href="<?=base_url()?>/about">Nosotros</a></li>
                    <li class="navigation"><a href="<?=base_url()?>/contact">Contacto</a></li>
                </ul>
            </div>
            <div class="nav-icons">
                <ul class="nav-icons-btns">
                    <li title="Search" class="c-p" id="btnSearch"><i class="fas fa-search"></i></li>
                    <?php
                        if(isset($_SESSION['login'])){
                    ?>
                    <li title="Wishlist"><a href="<?=base_url()?>/wishlist"><i class="fas fa-heart"></i></a></li>
                    <?php  }else{ ?>
                    <li onclick="openLoginModal();" title="Wishlist" class="c-p"><a><i class="fas fa-heart"></i></a></li>
                    <?php }?>
                    <li class="nav-icons-qty" title="My cart" id="btnToggleCart">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="qtyCart" class="text-white"><?=$qtyCart?></span>
                    </li>
                    <?php
                        if(isset($_SESSION['login'])){
                    ?>
                    <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item " href="<?=base_url()?>/user/profile" target="__blank">Perfil</a></li>
                            <li id="logout"><a href="#" class="dropdown-item">Cerrar sesión</a></li>
                        </ul>
                    </div>
                    <?php }else{ ?>
                    <li onclick="openLoginModal();" title="My account" class="c-p" ><i class="fas fa-user"></i></li> 
                    <?php }?>
                    <li id="btnToggleNav"><i class="fas fa-bars"></i></li>
                </ul>
                
            </div>
            <div class="nav-mobile">
                <div class="container nav-mobile-main">
                    <div class="nav-logo d-flex justify-content-between align-items m-0">
                        <strong class="t-w"><?= $company['name'];?></strong>
                        <div id="btnCloseNav">X</div>
                    </div>
                    <ul>
                        <li class="navigation-mobile"><a href="<?=base_url()?>/home">Inicio</a></li>
                        <li class="navigation-mobile"><a href="<?=base_url()?>/shop">Tienda</a></li>
                        <li class="navigation-mobile"><a href="<?=base_url()?>/blog">Blog</a></li>
                        <li class="navigation-mobile"><a href="<?=base_url()?>/about">Nosotros</a></li>
                        <li class="navigation-mobile"><a href="<?=base_url()?>/contact">Contacto</a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="cart-panel">
            <div class="container cart-panel-main">
                <div class="cart-panel-title d-flex justify-content-between align-items m-0">
                    <strong>MI CARRITO</strong>
                    <div id="btnCloseCart">X</div>
                </div>
                <div class="cart-panel-items scroll-y">
                    <?php 
                    if(isset($_SESSION['arrCart']) && !empty($_SESSION['arrCart'])){
                        for ($i=0; $i <count($arrProducts) ; $i++) { 
                            $price="";
                            if($arrProducts[$i]['discount']>0){
                                $price = $arrProducts[$i]['price']-($arrProducts[$i]['price']*($arrProducts[$i]['discount']*0.01));
                                $price = formatNum($price).' <span class="text-decoration-line-through t-p">'.formatNum($arrProducts[$i]['price']).'</span>';
                            }else{
                                $price = formatNum($arrProducts[$i]['price']);
                            }
                    ?>
                    <div class="cart-panel-item" data-id="<?=$arrProducts[$i]['idproduct']?>">
                        <img src="<?=$arrProducts[$i]['image']?>" alt="<?=$arrProducts[$i]['name']?>">
                        <div class="btn-del">X</div>
                        <h3><a href="<?=$arrProducts[$i]['url']?>"><strong><?=$arrProducts[$i]['name']?></strong></a></h3>
                        <p><?=$arrProducts[$i]['qty']?> x <?=$price?> </p>
                    </div>
                    <?php } }?>
                    
                </div>
                <p class="t-p " id="total"><strong>Total: <?=formatNum($total)?></strong></p>
                <div class="d-none" id="btnsCartPanel">
                    <a href="<?=base_url()?>/shop/cart" class="btn w-100 btnc-primary mb-2">Mi carrito</a>
                    <button type="button" class="mb-3 w-100 btn btnc-primary" id="btnCheckoutCart">Pagar</button>
                    <!--<button type="button" onclick="openLoginModal();" class="mb-3 w-100 btn btnc-primary">Checkout</button>-->
                </div>
            </div>
        </div>
    </header>
    <div id="divLoading">
        <div></div>
        <span>Cargando...</span>
    </div>
    <div id="modalLogin"></div>
    <a class="text-decoration-none" href="#" id="scrollTop"><i class="fas fa-angle-up"></i></a>
    