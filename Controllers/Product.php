<?php
    class Product extends Controllers{
        public function __construct(){
            session_start();
            if(empty($_SESSION['login'])){
                header("location: ".base_url());
                die();
            }
            parent::__construct();
            getPermits(4);
            
        }
        public function product(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Product";
                $data['page_title'] = "Products";
                $data['page_name'] = "product";
                $data['products'] = $this->getProducts();
                $data['app'] = "product.js";
                $this->views->getView($this,"product",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        /*************************Product methods*******************************/
        public function getProducts($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->search($params);
                }else if($option == 2){
                    $request = $this->model->sort($params);
                }else{
                    $request = $this->model->selectProducts();
                }
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 

                        $status="";
                        $btnGlobe = '<a href="'.base_url().'/shop/product/'.$request[$i]['route'].'" target="_blank" class="btn btn-primary m-1 text-white" title="Watch on website"><i class="fas fa-globe"></i></a>';
                        $btnView = '<button class="btn btn-info m-1" type="button" title="Watch" data-id="'.$request[$i]['idproduct'].'" name="btnView"><i class="fas fa-eye"></i></button>';
                        $btnEdit="";
                        $btnDelete="";
                        $price = formatNum($request[$i]['price']);
                        if($request[$i]['discount']>0){
                            $discount = '<span class="text-success">'.$request[$i]['discount'].'% OFF</span>';
                        }else{
                            $discount = '<span class="text-danger">0%</span>';
                        }
                        if($_SESSION['permitsModule']['u']){
                            $btnEdit = '<button class="btn btn-success m-1" type="button" title="Edit" data-id="'.$request[$i]['idproduct'].'" name="btnEdit"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if($_SESSION['permitsModule']['d']){
                            $btnDelete = '<button class="btn btn-danger m-1" type="button" title="Delete" data-id="'.$request[$i]['idproduct'].'" name="btnDelete"><i class="fas fa-trash-alt"></i></button>';
                        }
                        if($request[$i]['status']==1 && $request[$i]['stock']>0){
                            $status='<span class="badge me-1 bg-success">Activo</span>';
                        }else if($request[$i]['status']==2){
                            $status='<span class="badge me-1 bg-danger">Inactivo</span>';
                        }else{
                            $status='<span class="badge me-1 bg-warning">Agotado</span>';
                        }
                        $html.='
                            <tr class="item">
                                <td>
                                    <img src="'.$request[$i]['image'].'" class="rounded">
                                </td>
                                <td>'.$request[$i]['reference'].'</td>
                                <td>'.$request[$i]['name'].'</td>
                                <td>'.$request[$i]['category'].'</td>
                                <td>'.$request[$i]['subcategory'].'</td>
                                <td>'.$price.'</td>
                                <td>'.$discount.'</td>
                                <td>'.$request[$i]['stock'].'</td>
                                <td>'.$request[$i]['date'].'</td>
                                <td>'.$status.'</td>
                                <td class="item-btn">'.$btnGlobe.$btnView.$btnEdit.$btnDelete.'</td>
                            </tr>
                        ';
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $html = '<tr><td colspan="11">No hay datos</td></tr>';
                    $arrResponse = array("status"=>false,"data"=>$html);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            
            return $arrResponse;
        }
        public function getProduct(){
            if($_SESSION['permitsModule']['r']){
                if($_POST){
                    unset($_SESSION['filesInfo']);
                    if(empty($_POST)){
                        $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $id = intval($_POST['idProduct']);
                        $request = $this->model->selectProduct($id);
                        //dep($request);exit;
                        if(!empty($request)){
                            $this->model->deleteTmpImage();
                            $request['priceFormat'] = formatNum($request['price']);
                            $arrImages = $this->model->selectImages($id);
                            for ($i=0; $i < count($arrImages) ; $i++) { 
                                $this->model->insertTmpImage($arrImages[$i]['name'],$arrImages[$i]['rename']);
                            }
                            $arrResponse = array("status"=>true,"data"=>$request);
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"No hay datos"); 
                        }
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            die();
        }
        public function setProduct(){
            //dep($_POST);exit;
            if($_SESSION['permitsModule']['r']){
                if($_POST){
                    if(empty($_POST['txtName']) || empty($_POST['statusList']) || empty($_POST['categoryList'])
                    || empty($_POST['subcategoryList']) || empty($_POST['txtPrice']) || empty($_POST['txtStock']) || empty($_POST['txtShortDescription'])){
                        $arrResponse = array("status" => false, "msg" => 'Error de datos');
                    }else{ 
                        $idProduct = intval($_POST['idProduct']);
                        $strReference = strtoupper(strClean($_POST['txtReference']));
                        $strName = ucwords(strClean($_POST['txtName']));
                        $strShortDescription = strClean($_POST['txtShortDescription']);
                        $idCategory = intval($_POST['categoryList']);
                        $idSubcategory = intval($_POST['subcategoryList']);
                        $intPrice = intval($_POST['txtPrice']);
                        $intDiscount = intval($_POST['txtDiscount']);
                        $intStock =  intval($_POST['txtStock']);
                        $intStatus = intval($_POST['statusList']);
                        $strDescription = strClean($_POST['txtDescription']);
                        
                        $route = str_replace(" ","-",$strName);
                        $route = str_replace("?","",$route);
                        $route = strtolower(str_replace("¿","",$route));
                        $route = clear_cadena($route);

                        $photos = $this->model->selectTmpImages();
                        //dep($photos);
                        if($idProduct == 0){
                            if($_SESSION['permitsModule']['w']){
                                $option = 1;
                                $request= $this->model->insertProduct($idCategory,$idSubcategory,$strReference,$strName,$strShortDescription,$strDescription,$intPrice,$intDiscount,$intStock,$intStatus,$route,$photos);
                            }
                        }else{
                            if($_SESSION['permitsModule']['u']){
                                $option = 2;
                                $request= $this->model->updateProduct($idProduct,$idCategory,$idSubcategory,$strReference,$strName,$strShortDescription,$strDescription,$intPrice,$intDiscount,$intStock,$intStatus,$route,$photos);
                            }
                        }
                        if($request > 0 ){
                            $this->model->deleteTmpImage();
                            if($option == 1){
                                $arrResponse = $this->getProducts();
                                $arrResponse['msg'] = 'Datos guardados.';
                            }else{
                                $arrResponse = $this->getProducts();
                                $arrResponse['msg'] = 'Datos actualizados';
                            }
                        }else if($request == 'exist'){
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! El producto ya existe, pruebe con otro nombre y referencia.');		
                        }else{
                            $arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
                        }
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }else{
                header("location: ".base_url());
                die();
            }
			die();
		}
        public function delProduct(){
            if($_SESSION['permitsModule']['d']){
                if($_POST){
                    if(empty($_POST['idProduct'])){
                        $arrResponse=array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $id = intval($_POST['idProduct']);
                        $request = $this->model->deleteProduct($id);
                        if($request=="ok"){
                            $this->model->deleteTmpImage();
                            $arrResponse = $this->getProducts();
                            $arrResponse['msg'] = 'Se ha eliminado.';
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"No se ha podido eliminatar, inténta de nuevo.");
                        }
                    }
                    echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
                }
            }else{
                header("location: ".base_url());
                die();
            }
            die();
        }
        public function getSelectCategories(){
            $html='<option value="0" selected>Select</option>';
            $request = $this->model->selectCategories();
            if(count($request)>0){
                for ($i=0; $i < count($request); $i++) { 
                    $html.='<option value="'.$request[$i]['idcategory'].'">'.$request[$i]['name'].'</option>';
                }
                $arrResponse = array("data"=>$html);
            }else{
                $arrResponse = array("data"=>"");
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function getSelectSubcategories(){
            if($_POST){
                $idCategory = intval(strClean($_POST['idCategory']));
                $html='<option value="0" selected>Select</option>';
                $request = $this->model->selectSubcategories($idCategory);
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 
                        $html.='<option value="'.$request[$i]['idsubcategory'].'">'.$request[$i]['name'].'</option>';
                    }
                    $arrResponse = array("data"=>$html);
                }else{
                    $arrResponse = array("data"=>"");
                }
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function setImg(){ 
            $arrImages = orderFiles($_FILES['txtImg']);
            for ($i=0; $i < count($arrImages) ; $i++) { 
                $request = $this->model->insertTmpImage($arrImages[$i]['name'],$arrImages[$i]['rename']);
            }
            $arrResponse = array("msg"=>"Uploaded");
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function delImg(){
            $images = $this->model->selectTmpImages();
            $image = $_POST['image'];
            for ($i=0; $i < count($images) ; $i++) { 
                if($image == $images[$i]['name']){
                    deleteFile($images[$i]['rename']);
                    $this->model->deleteTmpImage($images[$i]['rename']);
                    break;
                }
            }
            $arrResponse = array("msg"=>"Deleted");
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function search($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getProducts(1,$params);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sort($params){
            if($_SESSION['permitsModule']['r']){
                $params = intval($params);
                $arrResponse = $this->getProducts(2,$params);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }

?>