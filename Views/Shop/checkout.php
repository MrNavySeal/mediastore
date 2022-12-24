<?php
    headerPage($data);
    $total = $data['total']['total'];
    $subtotal = $data['total']['subtotalCoupon'] >0 ? $data['total']['subtotalCoupon'] : $data['total']['subtotal'];
    $subtotalCoupon = $data['total']['subtotal'];
    $arrShipping = $data['arrShipping'];
?>
<script src="https://www.paypal.com/sdk/js?client-id=<?=$data['credentials']['client']?>&currency=<?=$data['company']['currency']['code']?>"></script>
    <!-- Set up a container element for the button -->
    <script>
      paypal.Buttons({
        // Sets up the transaction when a payment button is clicked
        createOrder: (data, actions) => {
          return actions.order.create({
            purchase_units: [{
              amount: {
                value: '<?=$total?>' // Can also reference a variable or function
              }
            }]
          });
        },
        // Finalize the transaction after payer approval
        onApprove: (data, actions) => {
          return actions.order.capture().then(function(orderData) {
            let formData = new FormData();
            formData.append("data",JSON.stringify(orderData, null, 2));
            loading.classList.remove("d-none");
            request(base_url+"/shop/setOrder",formData,"post").then(function(objData){
                if(objData.status){
                    loading.classList.add("d-none");
                    window.location.href=base_url+"/shop/confirm";
                }
            });
            //console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
            //const transaction = orderData.purchase_units[0].payments.captures[0];
            //alert(`Transaction ${transaction.status}: ${transaction.id}\n\nSee console for all available details`);
            // When ready to go live, remove the alert and show a success message within this page. For example:
            // const element = document.getElementById('paypal-button-container');
            // element.innerHTML = '<h3>Thank you for your payment!</h3>';
            // Or go to another URL:  actions.redirect('thank_you.html');
          });
        }
      }).render('#paypal-button-container');
    </script>

<main id="<?=$data['page_name']?>">
    <div class="container">
        <nav class="mt-2 mb-2" style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>">Inicio</a></li>
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>/shop">Tienda</a></li>
                <li class="breadcrumb-item"><a class="text-decoration-none" href="<?=base_url()?>/shop/cart">Mi carrito</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pagar</li>
            </ol>
        </nav>
        <div class="row">
           <div class="col-lg-7 order-lg-1 order-md-5 order-sm-5">
                <form id="formOrder" name="formOrder" class="p-4">
                    <h2>Detalles de facturación</h2>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="txtNameOrder" class="form-label">Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="txtNameOrder" name="txtNameOrder" value="<?=$_SESSION['userData']['firstname']?>" required="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="txtLastNameOrder" class="form-label">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="txtLastNameOrder" name="txtLastNameOrder" value="<?=$_SESSION['userData']['lastname']?>" required="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="txtEmailOrder" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="txtEmailOrder" name="txtEmailOrder" value="<?=$_SESSION['userData']['email']?>" required="">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="txtPhoneOrder" class="form-label">Teléfono <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="txtPhoneOrder" name="txtPhoneOrder" required="">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="txtAddressOrder" class="form-label"> Dirección<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="txtAddressOrder" name="txtAddressOrder" required="">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="listCountry" class="form-label">País <span class="text-danger">*</span></label>
                                <select class="form-select" id="listCountry" name="listCountry" aria-label="Default select example" required="">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="listState" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-select" id="listState" name="listState" aria-label="Default select example" required="">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="listCity" class="form-label">Ciudad <span class="text-danger">*</span></label>
                                <select class="form-select" id="listCity" name="listCity" aria-label="Default select example" required="">
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="txtPostCodeOrder" class="form-label"> Código postal</label>
                                <input type="text" class="form-control" id="txtPostCodeOrder" name="txtPostCodeOrder">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="txtNote" class="form-label">Notas</label>
                        <textarea class="form-control" id="txtNote" name="txtNote" rows="5"></textarea>
                    </div>
                </form>
            </div>
            <div class="col-lg-5 order-lg-5 order-md-5 order-sm-1 mb-4">
                <div class="p-4 mb-4">
                    <h2>Resumen</h2>
                    <?php 
                        $arrProducts = $_SESSION['arrCart'];
                        for ($i=0; $i < count($arrProducts) ; $i++) { 
                            $price=0;
                            if($arrProducts[$i]['discount']>0){
                                $price = $arrProducts[$i]['qty']*($arrProducts[$i]['price']-($arrProducts[$i]['price']*($arrProducts[$i]['discount']*0.01)));
                            }else{
                                $price=$arrProducts[$i]['qty']*$arrProducts[$i]['price'];
                            }
                        
                    ?>
                    <div class="d-flex justify-content-between">
                        <p><?=$arrProducts[$i]['name']." x ".$arrProducts[$i]['qty']?></p>
                        <p><?=formatNum($price)?></p>
                    </div>
                    <?php }?>
                    <?php if(isset($_SESSION['couponInfo'])){?>
                    <div class="d-flex justify-content-between mb-3">
                        <p class="m-0 fw-bold">Subtotal:</p>
                        <p class="m-0" id="subtotalCoupon"><?=formatNum($subtotal)?></p>
                    </div>
                    <div class="mb-3">
                        <p class="m-0 fw-bold">Cupón:</p>
                        <div class="d-flex justify-content-between ">
                            <p class="m-0"><?=$_SESSION['couponInfo']['code']?></p>
                            <p class="m-0">-<?=$_SESSION['couponInfo']['discount']?>%</p>
                        </div>
                        <?php if(!$_SESSION['couponInfo']['status']){?>
                            <p class="m-0 text-danger">Ya has usado este cupón.</p>
                        <?php }?>
                    </div>
                    <div class="d-flex justify-content-between mb-3 position-relative af-b-line">
                        <p class="m-0 fw-bold">Subtotal</p>
                        <p class="m-0"><?=formatNum($subtotalCoupon)?></p>
                    </div>
                    <?php }else{?>
                    <div class="d-flex justify-content-between mb-3 position-relative af-b-line">
                        <p class="m-0 fw-bold">Subtotal</p>
                        <p class="m-0"><?=formatNum($subtotal)?></p>
                    </div>
                    <?php }?>
                    <p class="m-0 fw-bold">Envio:</p>
                    <?php if($arrShipping['id']!=3){?>
                    <div class="d-flex justify-content-between mb-3">
                        <p class="m-0"><?=$arrShipping['name']?></p>
                        <p class="m-0"><?=formatNum($arrShipping['value'])?></p>
                    </div>
                    <?php }else{?>
                    <div class="d-flex justify-content-between mb-3">
                        <p class="m-0"><?=$arrShipping['name']." - ".$arrShipping['city']['city']?></p>
                        <p class="m-0"><?=formatNum($arrShipping['city']['value'])?></p>
                    </div>
                    <?php }?>
                    <div class="d-flex justify-content-between mb-3 position-relative af-b-line">
                        <p class="m-0 fw-bold fs-5">Total</p>
                        <p class="m-0 fw-bold fs-5" id="totalResume"><?=formatNum($total)?></p>
                    </div>
                    <div id="alertCheckData" class="alert alert-danger d-none" role="alert"></div>
                    <div id="paypal-button-container" class="d-none"></div>
                    <button type="button" class="mb-3 w-100 btn btnc-primary" id="checkData">Pagar</button>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
    footerPage($data);
?>