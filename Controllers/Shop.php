<?php
    
    require_once("Models/ProductTrait.php");
    require_once("Models/CategoryTrait.php");
    require_once("Models/CustomerTrait.php");
    require_once("Models/LoginModel.php");
    require_once("Models/ReviewTrait.php");
    class Shop extends Controllers{
        use ProductTrait, CategoryTrait, CustomerTrait, ReviewTrait;
        private $login;
        public function __construct(){
            session_start();
            parent::__construct();
            $this->login = new LoginModel();
        }

        /******************************Views************************************/
        public function shop(){
            $company=getCompanyInfo();
            $data['page_tag'] = $company['name'];
            $data['page_title'] = "Tienda | ".$company['name'];
            $data['page_name'] = "shop";
            $data['categories'] = $this->getCategoriesT();
            $data['products'] = $this->getProductsT("");
            $data['popProducts'] = $this->getPopularProductsT(9);
            $data['app'] = "shop.js";
            $this->views->getView($this,"shop",$data);
        }
        public function category($params){
            $arrParams = explode(",",$params);
            $category="";
            $subcategory="";

            if(count($arrParams)>1){
                $category = strClean($arrParams[0]);
                $subcategory = strClean($arrParams[1]);
            }else{
                $category = strClean($arrParams[0]);
            }
            $company=getCompanyInfo();
            $data['page_tag'] = $company['name'];
            $data['page_title'] = "Tienda | ".$company['name'];
            $data['page_name'] = "category";
            $data['categories'] = $this->getCategoriesT();
            $data['routec'] = $category;
            $data['routes'] = $subcategory;
            $data['total'] = $this->getTotalProductsT($category,$subcategory);
            $data['products'] = $this->getProductsCategoryT($category,$subcategory,1);
            $data['popProducts'] = $this->getPopularProductsT(9);
            $data['app'] = "shop.js";
            $this->views->getView($this,"category",$data);
        }
        public function product($params){
            if($params!= ""){
                $params = strClean($params);
                $data['product'] = $this->getProductPageT($params);
                if(!empty($data['product'])){
                    $company=getCompanyInfo();
                    $data['page_tag'] = $company['name'];
                    $data['page_name'] = "product";
                    $data['review'] = $this->getRate($data['product']['idproduct']);
                    $data['reviews'] = $this->getReviews($data['product']['idproduct']);
                    $data['products'] = $this->getProductsRandT(4);
                    $data['page_title'] =$data['product']['name']." | ".$company['name'];
                    $data['app'] = "product.js";
                    $this->views->getView($this,"product",$data); 
                }else{
                    header("location: ".base_url()."/error");
                    die();
                }
               
            }else{
                header("location: ".base_url()."/error");
                die();
            }
        }
        public function cart(){
            $company=getCompanyInfo();
            $data['page_tag'] = $company['name'];
            $data['page_title'] ="Mi carrito | ".$company['name'];
            $data['page_name'] = "cart";
            $data['shipping'] = $this->selectShippingMode();
            $data['app'] = "cart.js";
            if(isset($_SESSION['couponInfo']) && $_SESSION['couponInfo']['status'] == false){
                unset($_SESSION['couponInfo']);
            }
            if(isset($_SESSION['arrCart']) && !empty($_SESSION['arrCart'])){
                $_SESSION['arrShipping'] = $data['shipping'];
                if(!empty($_SESSION['arrShipping']['city'])){
                    $data['total'] = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping'],$_SESSION['arrShipping']['city']['id']);
                }else{
                    $data['total'] = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping']);
                }
            }
            $this->views->getView($this,"cart",$data); 
        }
        public function checkout(){
            if(isset($_SESSION['login']) && isset($_SESSION['arrCart']) && !empty($_SESSION['arrCart'])){
                $company=getCompanyInfo();
                $data['page_tag'] = $company['name'];
                $data['page_title'] ="Checkout | ".$company['name'];
                $data['page_name'] = "checkout";
                $data['credentials'] = getCredentials();
                $data['company'] = getCompanyInfo();
                $data['app'] = "checkout.js";
                if(isset($_SESSION['arrShipping']) && $_SESSION['arrShipping']['id'] == 3 && empty($_SESSION['arrShipping']['city'])){
                    header("location: ".base_url()."/shop/cart");
                    die(); 
                }else if(!isset($_SESSION['arrShipping'])){
                    $_SESSION['arrShipping'] = $this->selectShippingMode();
                    if($_SESSION['arrShipping']['id'] == 3 && empty($_SESSION['arrShipping']['city'])){
                        header("location: ".base_url()."/shop/cart");
                        die(); 
                    }
                }
                if(isset($_SESSION['couponInfo'])){
                    if(!$this->checkCoupon($_SESSION['idUser'],$_SESSION['couponInfo']['id'])){
                        $_SESSION['couponInfo']['status'] = false;
                    }
                }
                $data['arrShipping'] = $_SESSION['arrShipping'];

                if(!empty($_SESSION['arrShipping']['city'])){
                    $data['total'] = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping'],$_SESSION['arrShipping']['city']['id']);
                }else{
                    $data['total'] = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping']);
                }
                $this->views->getView($this,"checkout",$data); 
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function confirm(){
            if(isset($_SESSION['orderData'])){
                $company=getCompanyInfo();
                $data['page_tag'] = $company['name'];
                $data['page_title'] ="Confirmar pedido | ".$company['name'];
                $data['page_name'] = "confirm";
                $data['orderData'] = $_SESSION['orderData'];
                unset($_SESSION['orderData']);
                $this->views->getView($this,"confirm",$data); 
            }else{
                header("location: ".base_url());
                die();
            }
        }
        /******************************Cart methods************************************/
        public function addCart(){
            if($_POST){ 
                $id = intval(openssl_decrypt($_POST['idProduct'],METHOD,KEY));
                $qty = intval($_POST['txtQty']);
                $qtyCart = 0;
                $arrCart = array();
                $valiQty =true;
                if(is_numeric($id)){
                    $request = $this->getProductT($id);
                    $data = array("name"=>$request['name'],"image"=>$request['image'][0],"route"=>base_url()."/shop/product/".$request['route']);

                    if(!empty($request)){
                        $arrProduct = array(
                            "idproduct"=>$_POST['idProduct'],
                            "name" => $request['name'],
                            "qty"=>$qty,
                            "image"=>$request['image'][0]['url'],
                            "url"=>base_url()."/shop/product/".$request['route'],
                            "price" =>$request['price'],
                            "discount" => $request['discount'],
                            "stock"=>$request['stock']
                        );
                        if(isset($_SESSION['arrCart'])){
                            $arrCart = $_SESSION['arrCart'];
                            $currentQty = 0;
                            $flag = true;
                            for ($i=0; $i < count($arrCart) ; $i++) { 
                                if($arrCart[$i]['idproduct'] == $arrProduct['idproduct']){
                                    $currentQty = $arrCart[$i]['qty'];
                                    $arrCart[$i]['qty']+= $qty;
                                    if($arrCart[$i]['qty'] > $request['stock']){
                                        $arrCart[$i]['qty'] = $currentQty;
                                        $arrResponse = array("status"=>false,"msg"=>"No hay suficientes unidades","data"=>$data);
                                        $flag = false;
                                        break;
                                    }else{
                                        $_SESSION['arrCart'] = $arrCart;
                                        foreach ($_SESSION['arrCart'] as $quantity) {
                                            $qtyCart += $quantity['qty'];
                                        }
                                        $arrResponse = array("status"=>true,"msg"=>"Ha sido agregado a tu carrito.","qty"=>$qtyCart,"data"=>$data);
                                    }
                                    $flag =false;
                                    break;
                                }
                            }
                            if($flag){
                                if($qty > $request['stock']){
                                    $arrResponse = array("status"=>false,"msg"=>"No hay suficientes unidades","data"=>$data);
                                    $_SESSION['arrCart'] = $arrCart;
                                }else{
                                    array_push($arrCart,$arrProduct);
                                    $_SESSION['arrCart'] = $arrCart;
                                    foreach ($_SESSION['arrCart'] as $quantity) {
                                        $qtyCart += $quantity['qty'];
                                    }
                                    $arrResponse = array("status"=>true,"msg"=>"Ha sido agregado a tu carrito.","qty"=>$qtyCart,"data"=>$data);
                                }
                            }
                        }else{
                            if($qty > $request['stock']){
                                $arrResponse = array("status"=>false,"msg"=>"No hay suficientes unidades","data"=>$data);
                            }else{
                                array_push($arrCart,$arrProduct);
                                $_SESSION['arrCart'] = $arrCart;
                                foreach ($_SESSION['arrCart'] as $quantity) {
                                    $qtyCart += $quantity['qty'];
                                }
                                $arrResponse = array("status"=>true,"msg"=>"Ha sido agregado a tu carrito.","qty"=>$qtyCart,"data"=>$data);
                            } 
                        }
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"El producto no existe");
                    }
                    
                }else{
                    $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function delCart(){
            if($_POST){
                $id = $_POST['idProduct'];
                $total=0;
                $qtyCart=0;
                $arrCart = $_SESSION['arrCart'];
                for ($i=0; $i < count($arrCart) ; $i++) { 
                    if($arrCart[$i]['idproduct'] == $id){
                        unset($arrCart[$i]);
                        break;
                    } 
                }
                sort($arrCart);
                $_SESSION['arrCart'] = $arrCart;
                foreach ($_SESSION['arrCart'] as $quantity) {
                    $qtyCart += $quantity['qty'];
                }
                if(!empty($_SESSION['arrShipping']['city'])){
                    $arrTotal = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping'],$_SESSION['arrShipping']['city']['id']);
                }else if(!empty($_SESSION['arrShipping'])){
                    $arrTotal = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping']);
                }else{
                    $arrTotal = $this->calculateTotal($_SESSION['arrCart']);
                }
                
                $subtotal = $arrTotal['subtotal'];
                $total = $arrTotal['total'];
                if($arrTotal['subtotalCoupon']> 0){
                    $arrResponse = array("status"=>true,"total" =>formatNum($total),"subtotal"=>formatNum($subtotal),"subtotalCoupon"=>formatNum($arrTotal['subtotalCoupon']),"qty"=>$qtyCart);
                }else{
                    $arrResponse = array("status"=>true,"total" =>formatNum($total),"subtotal"=>formatNum($subtotal),"qty"=>$qtyCart);
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function currentCart(){
            if(isset($_SESSION['arrCart']) && !empty($_SESSION['arrCart'])){
                $arrCart = $_SESSION['arrCart'];
                $html="";
                for ($i=0; $i < count($arrCart); $i++) { 
                    $price="";
                    if($arrCart[$i]['discount']>0){
                        $price = $arrCart[$i]['price']-($arrCart[$i]['price']*($arrCart[$i]['discount']*0.01));
                        $price = formatNum($price).' <span class="text-decoration-line-through t-p">'.formatNum($arrCart[$i]['price']).'</span>';
                    }else{
                        $price = formatNum($arrCart[$i]['price']);
                    }
                    $html.='
                    <div class="cart-panel-item" data-id="'.$arrCart[$i]['idproduct'].'">
                        <img src="'.$arrCart[$i]['image'].'" alt="'.$arrCart[$i]['name'].'">
                        <div class="btn-del">X</div>
                        <h3><a href="'.$arrCart[$i]['url'].'"><strong>'.$arrCart[$i]['name'].'</strong></a></h3>
                        <p>'.$arrCart[$i]['qty'].' x '.$price.' </p>
                    </div>
                    ';
                }
                $total =0;
                foreach ($arrCart as $product) {
                    if($product['discount']>0){
                        $total += $product['qty']*($product['price']-($product['price']*($product['discount']*0.01)));
                    }else{
                        $total+=$product['qty']*$product['price'];
                    }
                }
                $status=false;
                if(isset($_SESSION['login']) && !empty($_SESSION['arrCart'])){
                    $status=true;
                }
                $arrResponse = array("status"=>$status,"items"=>$html,"total"=>formatNum($total));
            }else{
                $arrResponse = array("items"=>"","total"=>formatNum(0));
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function updateCart(){
            if($_POST){
                $id = $_POST['idProduct'];
                $total =0;
                $totalPrice = 0;
                $subtotal = 0;
                $arrTotal = array();
                $qty = intval($_POST['qty']);
                
                if($qty > 0){
                    
                    $arrCart = $_SESSION['arrCart'];
                    for ($i=0; $i < count($arrCart) ; $i++) { 
                        if($arrCart[$i]['idproduct'] == $id){
                            $arrCart[$i]['qty'] = $qty;
                            if($arrCart[$i]['discount']>0){
                                $totalPrice = $arrCart[$i]['qty']*($arrCart[$i]['price']-($arrCart[$i]['price']*($arrCart[$i]['discount']*0.01)));
                            }else{
                                $totalPrice =$arrCart[$i]['qty']*$arrCart[$i]['price'];
                            }
                            break;
                        }
                    }
                    $_SESSION['arrCart'] = $arrCart;
                    if(!empty($_SESSION['arrShipping']['city'])){
                        $arrTotal = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping'],$_SESSION['arrShipping']['city']['id']);
                    }else{
                        $arrTotal = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping']);
                    }

                    $subtotal = $arrTotal['subtotal'];
                    $total = $arrTotal['total'];
                    if($arrTotal['subtotalCoupon']> 0){
                        $arrResponse = array("status"=>true,"total" =>formatNum($total),"subtotal"=>formatNum($subtotal),"totalPrice"=>formatNum($totalPrice),"subtotalCoupon"=>formatNum($arrTotal['subtotalCoupon']));
                    }else{
                        $arrResponse = array("status"=>true,"total" =>formatNum($total),"subtotal"=>formatNum($subtotal),"totalPrice"=>formatNum($totalPrice));
                    }

                }else{
                    $arrResponse = array("status"=>false,"msg" =>"Error de datos.");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function calculateShippingCity($id){
            $id = intval($id);
            $info = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping'],$id);
            $arrResponse = array("total"=>formatNum($info['total']));
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function checkShippingCity($id){
            $id = intval($id);
            $request = $this->selectShippingCity($id);
            if(!empty($request)){
                $arrResponse = array("status"=>true);
            }else{
                $arrResponse = array("status"=>false,"msg"=>"Por favor, seleccione una ciudad.");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function setCouponCode(){
            if($_POST){
                if(empty($_POST['txtCoupon'])){
                    $arrResponse = array("status"=>false,"msg"=>"Error de datos"); 
                }else{
                    $strCoupon = strClean(strtoupper($_POST['txtCoupon']));
                    $request = $this->selectCouponCode($strCoupon);
                    if(!empty($request)){
                        $_SESSION['couponInfo'] = $request;
                        $_SESSION['couponInfo']['status'] = true;
                        $arrResponse = array("status"=>true); 
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"El cupón no existe o está inactivo."); 
                    }
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function delCouponCode(){
            unset($_SESSION['couponInfo']);
        }
        /******************************wishlist methods************************************/
        public function addWishList(){
            if($_POST){
                if(isset($_SESSION['login'])){
                    $idProduct = openssl_decrypt($_POST['idProduct'],METHOD,KEY);
                    if(is_numeric($idProduct)){
                        $request = $this->addWishListT($idProduct,$_SESSION['idUser']);
                        if($request>0){
                            $arrResponse = array("status"=>true);
                        }else if("exists"){
                            $arrResponse = array("status"=>true);
                        }else{
                            $arrResponse = array("status"=>false);
                        }
                    }
                }else{
                    $arrResponse = array("status"=>false);
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function delWishList(){
            if($_POST){
                if(isset($_SESSION['login'])){
                    $idProduct = openssl_decrypt($_POST['idProduct'],METHOD,KEY);
                    if(is_numeric($idProduct)){
                        $request = $this->delWishListT($idProduct,$_SESSION['idUser']);
                        if($request>0){
                            $arrResponse = array("status"=>true);
                        }else{
                            $arrResponse = array("status"=>false);
                        }
                    }
                }else{
                    $arrResponse = array("status"=>false);
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /******************************Customer methods************************************/
        public function validCustomer(){
            if($_POST){
				if(empty($_POST['txtSignName']) || empty($_POST['txtSignEmail']) || empty($_POST['txtSignPassword'])){
                    $arrResponse=array("status" => false, "msg" => "Error de datos");
                }else{
                    $strName = ucwords(strClean($_POST['txtSignName']));
                    $strEmail = strtolower(strClean($_POST['txtSignEmail']));
                    $company = getCompanyInfo();
                    $code = code(); 
                    $dataUsuario = array('nombreUsuario'=> $strName, 
                                        'email_remitente' => $company['email'], 
                                        'email_usuario'=>$strEmail, 
                                        'company' =>$company,
                                        'asunto' =>'Código de verificación - '.$company['name'],
                                        'codigo' => $code);
                    $_SESSION['code'] = $code;
                    $sendEmail = sendEmail($dataUsuario,'email_validData');
                    if($sendEmail){
                        $arrResponse = array("status"=>true,"msg"=>"Se ha enviado un código a su correo electrónico para validar sus datos.");
                        
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo.");
                    }
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

			}
			die();
        }
		public function setCustomer(){
			if($_POST){
				if(empty($_POST['txtSignName']) || empty($_POST['txtSignEmail']) || empty($_POST['txtSignPassword']) || empty($_POST['txtCode'])){
                    $arrResponse=array("status" => false, "msg" => "Error de datos");
                }else{
                    if($_POST['txtCode'] == $_SESSION['code']){
                        unset($_SESSION['code']);
                        $strName = ucwords(strClean($_POST['txtSignName']));
                        $strEmail = strtolower(strClean($_POST['txtSignEmail']));
                        $strPassword = hash("SHA256",$_POST['txtSignPassword']);
                        $strPicture = "user.jpg";
                        $rolid = 2;

                        $request = $this->setCustomerT($strName,$strPicture,$strEmail,$strPassword,$rolid);
                        
                        if($request > 0){
                            $_SESSION['idUser'] = $request;
                            $_SESSION['login'] = true;
                            
                            $arrData = $this->login->sessionLogin($_SESSION['idUser']);
                            sessionUser($_SESSION['idUser']);
    
                            $arrResponse = array("status" => true,"msg"=>"Se ha registrado con éxito.");
                        }else if($request =="exist"){
                            $arrResponse = array("status" => false,"msg"=>"El usuario ya existe, por favor inicie sesión.");
                        }else{
                            $arrResponse = array("status" => false,"msg"=>"No se pueden almacenar los datos, inténtelo más tarde.");
    
                        }
                    }else{
                        $arrResponse = array("status" => false,"msg"=>"Código incorrecto, inténtelo de nuevo.");
                    }

                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);

			}
			die();
		}
        public function setSuscriber(){
            if($_POST){
                if(empty($_POST['txtEmailSuscribe'])){
                    $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                }else{
                    $strEmail = strClean(strtolower($_POST['txtEmailSuscribe']));
                    $request = $this->setSuscriberT($strEmail);
                    $company = getCompanyInfo();
                    if($request>0){
                        $request = $this->statusCouponSuscriberT();
                        $dataEmail = array('email_remitente' => $company['email'], 
                                                'email_usuario'=>$strEmail,
                                                'asunto' =>'Te has suscrito a '.$company['name'],
                                                "code"=>$request['code'],
                                                'company'=>$company,
                                                "discount"=>$request['discount']);
                        sendEmail($dataEmail,'email_suscriber');
                        $arrResponse = array("status"=>true,"msg"=>"Suscrito");
                    }else if($request=="exists"){
                        $arrResponse = array("status"=>false,"msg"=>"Ya se ha suscrito antes.");
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo.");
                    }
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function statusCouponSuscriber(){
            $request = $this->statusCouponSuscriberT();
            if(!empty($request)){
                $arrResponse = array("status"=>true,"discount"=>$request['discount']);
            }else{
                $arrResponse = array("status"=>false,"msg"=>"El cupón no existe o está inactivo");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        /******************************Reviews methods************************************/
        public function getReviews($idProduct,$sort=null,$search=null){
            if(is_numeric($idProduct)){
                $idProduct = intval($idProduct);
            }else{
                $idProduct= intval(openssl_decrypt($idProduct,METHOD,KEY));
            }
            $reviews="";
            if($sort != null){
                $reviews = $this->getReviewsT($idProduct,$sort);
                //dep($reviews);exit;
            }else if($search!=null){
                $reviews = $this->getSearchReviewsT($idProduct,$search);
            }else{
                $reviews = $this->getReviewsT($idProduct);
            }
            
            $rate = $this->getRate($idProduct);
            $html="";
            for ($i=0; $i < count($reviews); $i++) { 
                $image = media()."/images/uploads/".$reviews[$i]['image'];
                $name = $reviews[$i]['firstname']." ".$reviews[$i]['lastname'];
                $rateComment ="";
                $options="";
                if(isset($_SESSION['login'])){
                    if($_SESSION['idUser'] == $reviews[$i]['personid']){
                        $options.='<a href="#formReview" class="p-0 me-2 t-p btn editComment" data-id="'.$reviews[$i]['id'].'" onclick="editReview('.$reviews[$i]['id'].')" title="edit" >Edit</a>';
                    }
                    if($_SESSION['idUser'] == $reviews[$i]['personid'] || $_SESSION['userData']['roleid']==1){
                        $options.='<button type="button" class="btn t-p p-0" onclick="deleteReview('.$reviews[$i]['id'].')" title="delete">Delete</button>';
                    }
                }
                for ($j = 0; $j < 5; $j++) {
                    if($j >= intval($reviews[$i]['rate'])){
                        $rateComment.='<i class="far fa-star"></i>';
                    }else{
                        $rateComment.='<i class="fas fa-star"></i>';
                    }
                }

                $html.='
                <li class="comment-block">
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="comment-info d-flex justify-content-between">
                                <div class="d-flex justify-content-start">
                                    <div class="comment-img me-1">
                                        <img src="'.$image.'" alt="'.$name.'">
                                    </div>
                                    <div>
                                        <p class="m-0">'.$name.'</p>
                                        <p class="m-0 text-secondary">'.$reviews[$i]['date'].'</p>
                                    </div>
                                </div>
                                <div class="product-rate text-end m-0">
                                    '.$rateComment.'
                                    <p class="m-0 text-secondary">'.$reviews[$i]['dateupdated'].'</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-1">
                            <p class="m-0">'.$reviews[$i]['description'].'</p>
                            <div class="t-p text-end">'.$options.'</div>
                        </div>
                        
                    </div>
                </li>
                ';
            }
            $arrResponse = array("html"=>$html,"rate"=>$rate);
            return $arrResponse;
        }
        public function setReview(){
            //dep($_POST);exit;
            if($_POST){
                if(isset($_SESSION['login'])){
                    if(empty($_POST['intRate']) || empty($_POST['txtReview']) || empty($_POST['idProduct'])){
                        $arrResponse = array("status"=>false,"msg"=>"Por favor, califíque y escriba su reseña.");
                        echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                    }else{
                        $idUser = $_SESSION['idUser'];
                        $idReview = intval($_POST['idReview']);
                        $idProduct = intval(openssl_decrypt($_POST['idProduct'],METHOD,KEY));
                        $intRate = intval($_POST['intRate']);
                        $strReview = strClean($_POST['txtReview']);
                        $option=0;
                        $request="";
                        

                        if($idReview==0){
                            $option = 1;
                            $request = $this->setReviewT($idProduct,$idUser,$strReview,$intRate);
                            
                        }else{
                            $option = 2;
                            $request = $this->updateReviewT($idReview,$idProduct,$idUser,$strReview,$intRate);
                        }
                        $reviews = $this->getReviews($idProduct);
                        if($option ==1){
                            if(!is_array($request) && $request>0){
                                $arrResponse = array("status"=>true,"msg"=>"Su reseña ha sido compartida.","html"=>$reviews['html'],"rate"=>$reviews['rate']);
                            }else if(is_array($request)){
                                $arrResponse = array("status"=>false,"msg"=>"Ya has compartido tu reseña antes. Edítala si quieres.","id"=>$request['id']);
                            }else{
                                $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo.");
                            }
                        }else{
                            $arrResponse = array("status"=>true,"msg"=>"Su reseña se ha actualizado.","html"=>$reviews['html'],"rate"=>$reviews['rate']);
                        }
                    }
                }else{
                    $arrResponse = array("login"=>false,"msg"=>"Inicie sesión para compartir su reseña.");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function delReview(){
            if($_POST){
                $id = intval($_POST['idReview']);
                $idProduct = intval(openssl_decrypt($_POST['idProduct'],METHOD,KEY));
                $request = $this->deleteReviewT($id); 
                $reviews = $this->getReviews($idProduct);
                $arrResponse = array("status"=>true,"msg"=>"La reseña ha sido eliminada.","html"=>$reviews['html'],"rate"=>$reviews['rate']);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function getReview(){
            if($_POST){
                $idReview = intval($_POST['idReview']);
                $request = $this->getReviewT($idReview);
                echo json_encode($request,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sortReviews(){
            if($_POST){
                $idProduct = intval(openssl_decrypt($_POST['idProduct'],METHOD,KEY));
                $option = intval($_POST['intSort']);
                $reviews = $this->getReviews($idProduct,$option);
                $arrResponse = array("status"=>true,"html"=>$reviews['html'],"rate"=>$reviews['rate']);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();  
        }
        public function searchReviews(){
            if($_POST){
                $idProduct = intval(openssl_decrypt($_POST['idProduct'],METHOD,KEY));
                $search = strClean($_POST['strSearch']);
                $reviews = $this->getReviews($idProduct,null,$search);
                $arrResponse = array("status"=>true,"html"=>$reviews['html'],"rate"=>$reviews['rate']);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();  
        }
        /******************************Checkout methods************************************/
        public function checkData(){
            if($_POST){
                if(empty($_POST['txtNameOrder']) || empty($_POST['txtLastNameOrder']) || empty($_POST['txtEmailOrder']) || 
                empty($_POST['txtPhoneOrder']) || empty($_POST['txtAddressOrder']) || empty($_POST['country'])
                || empty($_POST['state']) || empty($_POST['city'])){
                    $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                }else{
                    $arrData = array(
                        "firstname"=>strClean(ucwords($_POST['txtNameOrder'])),
                        "lastname"=>strClean(ucwords($_POST['txtLastNameOrder'])),
                        "email"=>strClean(strtolower($_POST['txtEmailOrder'])),
                        "phone"=>strClean($_POST['txtPhoneOrder']),
                        "address"=>strClean($_POST['txtAddressOrder']),
                        "country"=>strClean($_POST['country']),
                        "state"=>strClean($_POST['state']),
                        "city"=>strClean($_POST['city']),
                        "postalcode" =>strClean($_POST['txtPostCodeOrder']),
                        "note"=>strClean($_POST['txtNote'])
                    );
                    $_SESSION['checkData'] = $arrData;
                    $arrResponse = array("status"=>true,"msg"=>"Datos correctos");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function setOrder(){
            if($_POST){
                if(empty($_POST['data']) || empty($_SESSION['checkData'])){
                    $arrResponse = array("status"=>false,"msg"=>"Data error");
                }else{
                    $total = 0;
                    $arrTotal = array();
                    $idUser = $_SESSION['idUser'];
                    $objPaypal = json_decode($_POST['data']);
                    $arrInfo = $_SESSION['checkData'];
                    $amountData=array();
                    unset($_SESSION['checkData']);

                    if(!empty($_SESSION['arrCart'])){
                        if(!empty($_SESSION['arrShipping']['city'])){
                            $arrTotal = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping'],$_SESSION['arrShipping']['city']['id']);
                        }else{
                            $arrTotal = $this->calculateTotal($_SESSION['arrCart'],$_SESSION['arrShipping']);
                        }

                        
                        if(is_object($objPaypal)){
                            
                            $dataPaypal = $_POST['data'];
                            $idTransaction = $objPaypal->purchase_units[0]->payments->captures[0]->id;
                            $status = $objPaypal->purchase_units[0]->payments->captures[0]->status;
                            $firstname = $arrInfo['firstname'];
                            $lastname = $arrInfo['lastname'];
                            $email = $arrInfo['email'];
                            $phone = $arrInfo['phone'];
                            $postalCode = $arrInfo['postalcode'];  
                            $country = $arrInfo['country'];
                            $state = $arrInfo['state'];
                            $city = $arrInfo['city'];
                            $address = $arrInfo['address'];
                            $note = $arrInfo['note'];
                            $total = $arrTotal['total'];

                            if(isset($_SESSION['couponInfo'])){
                                $amountData['couponInfo'] = $_SESSION['couponInfo'];
                                if($amountData['couponInfo']['status'] == true){
                                    $this->setCoupon($amountData['couponInfo']['id'],$idUser,$amountData['couponInfo']['code']);
                                }
                                unset($_SESSION['couponInfo']);
                            }
                            $amountData['totalInfo'] = array("total"=>$arrTotal,"shipping"=>$_SESSION['arrShipping']);
                            $objAmount = json_encode($amountData,true);

                            unset($_SESSION['arrShipping']);

                            $requestOrder = $this->insertOrder($idUser,$idTransaction,$dataPaypal,$objAmount,$firstname,$lastname,$email,$phone,$country,$state,$city,$address,
                            $postalCode,$note,$total,$status);

                            if($requestOrder>0){

                                $arrOrder = array("idorder"=>$requestOrder,"iduser"=>$_SESSION['idUser'],"products"=>$_SESSION['arrCart']);
                                $request = $this->insertOrderDetail($arrOrder);
                                $orderInfo = $this->getOrder($requestOrder);
                                $orderInfo['amountData'] = $amountData;
                                $company = getCompanyInfo();
                                $dataEmailOrden = array(
                                    'asunto' => "Se ha generado un pedido",
                                    'email_usuario' => $_SESSION['userData']['email'], 
                                    'email_remitente'=>$company['email'],
                                    'company'=>$company,
                                    'email_copia' => $company['secondary_email'],
                                    'order' => $orderInfo );

								sendEmail($dataEmailOrden,"email_order");
                                $idOrder = openssl_encrypt($requestOrder,METHOD,KEY);
                                $idTransaction = openssl_encrypt($orderInfo['order']['idtransaction'],METHOD,KEY);
                                $arrResponse = array("status"=>true,"order"=>$idOrder,"transaction"=>$idTransaction,"msg"=>"Pedido realizado");
                                $_SESSION['orderData'] = $arrResponse;

                                unset($_SESSION['arrCart']);
                                
                                
                            }else{
                                $arrResponse = array("status"=>false,"msg"=>"No se ha podido realizar el pedido.");
                            }
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"Se ha producido un error en la transacción.");
                        }
                    }else{
                        $arrResponse = array("status"=>false,"msg"=>"Se ha producido un error en la transacción.");
                    }
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function getCountries(){
            $request = $this->selectCountries();
            $html='<option value="0" selected>Select</option>';
            for ($i=0; $i < count($request) ; $i++) { 
                $html.='<option value="'.$request[$i]['id'].'">'.$request[$i]['name'].'</option>';
            }

            echo json_encode($html,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function getSelectCountry($id){
            $request = $this->selectStates($id);
            $html='<option value="0" selected>Select</option>';
            for ($i=0; $i < count($request) ; $i++) { 
                $html.='<option value="'.$request[$i]['id'].'">'.$request[$i]['name'].'</option>';
            }
            echo json_encode($html,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function getSelectState($id){
            $request = $this->selectCities($id);
            $html='<option value="0" selected>Select</option>';
            for ($i=0; $i < count($request) ; $i++) { 
                $html.='<option value="'.$request[$i]['id'].'">'.$request[$i]['name'].'</option>';
            }
            echo json_encode($html,JSON_UNESCAPED_UNICODE);
            die();
        }
        /******************************General shop methods************************************/
        public function search(){
            if($_POST){
                $strSearch = strClean(strtolower($_POST['txtSearch']));
                $request = $this->getProductsSearchT($strSearch);
                if(!empty($request)){
                    $html="";
                    for ($i=0; $i < count($request) ; $i++) { 
                        $idProduct = openssl_encrypt($request[$i]['idproduct'],METHOD,KEY);
                        $price ='<p class="m-0 text-dark">'.formatNum($request[$i]['price']).'</p>';
                        $btnAdd ='<button type="button" class="btn border border-dark" data-id="'.$idProduct.'" onclick="addProduct(this)"><i class="fas fa-shopping-cart" aria-hidden="true"></i></button>';
                        $discount="";
                        $rate="";
                        $route = base_url()."/shop/product/".$request[$i]['route'];
                        if($request[$i]['status'] == 1 && $request[$i]['stock']>0){
                            if($request[$i]['discount']>0){
                                $price = '<p class="m-0 text-dark">'.formatNum($request[$i]['priceDiscount']).' <span class="text-decoration-line-through t-p">'.formatNum($request[$i]['price']).'</span></p>';
                                $discount ='<div>-'.$request[$i]['discount'].'%</div>';
                            }
                        }else if($request[$i]['status'] == 1 && $request[$i]['stock']==0){
                            $btnAdd="";
                            $price='<p class="m-0 text-danger">Agotado</p>';
                        }else{
                            $btnAdd ="";
                            $price="";
                        }
                        for ($j=0; $j < 5; $j++) { 
                            if($request[$i]['rate']!=null && $j >= intval($request[$i]['rate'])){
                                $rate.='<i class="far me-1 fa-star"></i>';
                            }else if($request[$i]['rate']==null){
                                $rate.='<i class="far me-1 fa-star"></i>';
                            }else{
                                $rate.='<i class="fas me-1 fa-star"></i>';
                            }
                        }
                        $html .='
                        <div class="search-item">
                            <div class="search-item-info">
                                <div class="search-item-img">
                                    <img src="'.$request[$i]['url'].'" alt="'.$request[$i]['name'].'">
                                    '.$discount.'
                                </div>
                                <div class="search-item-data">
                                    <h2><a href="'.$route.'" class="text-decoration-none text-dark">'.$request[$i]['name'].'</a></h2>
                                    <div class="product-rate">
                                        '.$rate.'
                                    </div>
                                    <div>'.$price.'</div>
                                </div>
                            </div>
                            <div class="search-item-actions">
                                <button type="button" class="btn me-2 quickView border border-dark" onclick="quickModal(this)" data-id="'.$idProduct.'"><i class="fas fa-eye" data-bs-toggle="tooltip" data-bs-placement="top" title="Quick view"></i></button>
                                '.$btnAdd.'
                            </div>
                        </div>
                        ';
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $arrResponse = array("status"=>false,"msg"=>"Sin resultados");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function getProduct(){
            if($_POST){
                $idProduct = openssl_decrypt($_POST['idProduct'],METHOD,KEY);
                if(is_numeric($idProduct)){
                    $request = $this->getProductT($idProduct);
                    $request['idproduct'] = $_POST['idProduct']; 
                    $request['priceDiscount']=formatNum($request['price']-($request['price']*($request['discount']*0.01)));
                    $request['price'] = formatNum($request['price']);
                    $arrResponse= array("status"=>true,"data"=>$request);
                }else{
                    $arrResponse= array("status"=>false);
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
            
        }
        public function calculateTotal($arrProducts,$arrShipping=null,$idCity =null){
            $subtotal = 0;
            $total=0;
            $subtotalCoupon=0;
            foreach ($arrProducts as $product) {
                if($product['discount']>0){
                    $subtotal += $product['qty']*($product['price']-($product['price']*($product['discount']*0.01)));
                }else{
                    $subtotal+=$product['qty']*$product['price'];
                }
            }
            if(isset($_SESSION['couponInfo']) && $_SESSION['couponInfo']['status'] == true){
                $subtotalCoupon = $subtotal;
                $subtotal = $subtotal - ($subtotal * ($_SESSION['couponInfo']['discount']/100));
            }
            
            if($idCity != null){
                for ($i=0; $i < count($arrShipping['cities']) ; $i++) { 
                    if($arrShipping['cities'][$i]['id'] == $idCity){
                        $total = $subtotal + $arrShipping['cities'][$i]['value'];
                        $arrShipping['city'] = $arrShipping['cities'][$i];
                        $_SESSION['arrShipping'] = $arrShipping;
                        break;
                    }
                }
            }else if($arrShipping!=null){
                $total = $subtotal+$arrShipping['value'];
            }else{
                $total = $subtotal;
            }
            
            $arrTotal = array("subtotal"=>$subtotal,"total"=>$total,"subtotalCoupon" =>$subtotalCoupon);
            return $arrTotal;
        }
    }
?>