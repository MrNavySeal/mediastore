<?php
    headerPage($data);
    $products = $data['products'];
?>
    <div id="modalItem"></div>
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
        <div class="container">
            <?php if(!empty($products)){?>
            <div class="row mt-5 mb-5">
                <h1 class="text-center">Mis favoritos</h1>
                <div class="col-lg-12 mt-5">
                    <table class="table table-borderless text-center table-cart">
                        <thead class="position-relative af-b-line">
                          <tr>
                            <th scope="col">Producto</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                            <?php
                                for ($i=0; $i < count($products) ; $i++) { 
                                    $idProduct = openssl_encrypt($products[$i]['idproduct'],METHOD,KEY);
                                    $price ='<p class="m-0 fs-5 t-p"><strong>'.formatNum($products[$i]['price']).'</strong></p>';
                                    $btnAdd ='<button type="button" class="btn border border-dark bg-p text-white" data-id="'.$idProduct.'" onclick="addProduct(this)"><i class="fas fa-shopping-cart" aria-hidden="true"></i></button>';
                                    $discount="";
                                    $rate="";

                                    if($products[$i]['status'] == 1 && $products[$i]['stock']>0){
                                        if($products[$i]['discount']>0){
                                            $price = '<p class="m-0 fs-5"><strong class="t-p">'.formatNum($products[$i]['priceDiscount']).' </strong><span class="text-decoration-line-through t-s fs-6">'.formatNum($products[$i]['price']).'</span></p>';
                                            $discount ='<div class="position-absolute top-0 end-0 rounded t-p pe-1 ps-1 fw-bold text-white bg-d fs-6">-'.$products[$i]['discount'].'%</div>';
                                        }
                                    }else if($products[$i]['status'] == 1 && $products[$i]['stock']==0){
                                        $btnAdd="";
                                        $price='<p class="m-0 fs-5 text-danger">Agotado</p>';
                                    }else{
                                        $btnAdd ="";
                                        $price="";
                                    }
                            ?>
                          <tr data-id="<?=$idProduct?>">
                            <td>
                                <div class="position-relative">
                                    <img src="<?= $products[$i]['url']?>"  class="p-2" height="100px" width="100px" alt="<?= $products[$i]['name']?>">
                                    <div class="btn-del top-0 start-0">x</div>
                                    <?=$discount?>
                                </div>
                            </td>
                            <td>
                                <a href="<?=base_url()."/shop/product/".$products[$i]['route']?>"><?= $products[$i]['name']?></a>
                            </td>
                            <td><?=$price?></td>
                            <td>
                                <div class="wishlist-actions">
                                    <button type="button" class="btn me-2 quickView border border-dark bg-p text-white" onclick="quickModal(this)" data-id="<?=$idProduct?>"><i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Vista rápida"></i></button>
                                    <?=$btnAdd?>
                                </div>
                            </td>
                          </tr>
                          <?php }?>
                        </tbody>
                      </table>
                </div>
            </div>
            <?php }else{?>
            <div class="mt-5 mb-5 text-center">
                <h1 class="mb-3">¡Uy! No tienes productos favoritos.</h1>
                <a class="btn btnc-primary"href="<?=base_url()?>/shop">Comprar ahora</a>
            </div>
            <?php }?>
        </div>
    </main>
<?php
    footerPage($data);
?>