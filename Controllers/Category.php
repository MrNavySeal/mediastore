<?php
    class Category extends Controllers{

        public function __construct(){
            session_start();
            if(empty($_SESSION['login'])){
                header("location: ".base_url());
                die();
            }
            parent::__construct();
            getPermits(4);
        }
        
        public function category(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Categoría";
                $data['page_title'] = "Categorias";
                $data['page_name'] = "category";
                $data['categories'] = $this->getCategories();
                $data['app'] = "category.js";
                $this->views->getView($this,"category",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function subcategory(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Subcategoria";
                $data['page_title'] = "Subcategorias";
                $data['page_name'] = "subcategory";
                $data['subcategories'] = $this->getSubCategories();
                $data['app'] = "subcategory.js";
                $this->views->getView($this,"subcategory",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        /*************************Category methods*******************************/
        public function getCategories($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->search($params);
                }else if($option == 2){
                    $request = $this->model->sort($params);
                }else{
                    $request = $this->model->selectCategories();
                }
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 

                        $btnEdit="";
                        $btnDelete="";
                        
                        if($_SESSION['permitsModule']['u']){
                            $btnEdit = '<button class="btn btn-success m-1" type="button" title="Edit" data-id="'.$request[$i]['idcategory'].'" name="btnEdit"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if($_SESSION['permitsModule']['d']){
                            $btnDelete = '<button class="btn btn-danger m-1" type="button" title="Delete" data-id="'.$request[$i]['idcategory'].'" name="btnDelete"><i class="fas fa-trash-alt"></i></button>';
                        }
                        $html.='
                            <tr class="item" data-name="'.$request[$i]['name'].'">
                                <td>'.$request[$i]['name'].'</td>
                                <td class="item-btn">'.$btnEdit.$btnDelete.'</td>
                            </tr>
                        ';
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $arrResponse = array("status"=>false,"data"=>"No hay datos");
                }
            }else{
                header("location: ".base_url());
                die();
            }
            
            return $arrResponse;
        }
        public function getCategory(){
            if($_SESSION['permitsModule']['r']){

                if($_POST){
                    if(empty($_POST)){
                        $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $idCategory = intval($_POST['idCategory']);
                        $request = $this->model->selectCategory($idCategory);
                        if(!empty($request)){
                            $request['picture'] = media()."/images/uploads/".$request['picture'];
                            $arrResponse = array("status"=>true,"data"=>$request);
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo"); 
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
        public function setCategory(){
            if($_SESSION['permitsModule']['r']){
                if($_POST){
                    if(empty($_POST['txtName'])){
                        $arrResponse = array("status" => false, "msg" => 'Error de datos');
                    }else{ 
                        $idCategory = intval($_POST['idCategory']);
                        $strName = ucwords(strClean($_POST['txtName']));
                        $strDescription = strClean($_POST['txtDescription']);
                        $route = str_replace(" ","-",$strName);
                        $route = str_replace("?","",$route);
                        $route = strtolower(str_replace("¿","",$route));
                        $route = clear_cadena($route);
                        $photo = "";
                        $photoCategory="";

                        if($idCategory == 0){
                            if($_SESSION['permitsModule']['w']){
                                $option = 1;

                                if($_FILES['txtImg']['name'] == ""){
                                    $photoCategory = "category.jpg";
                                }else{
                                    $photo = $_FILES['txtImg'];
                                    $photoCategory = 'category_'.bin2hex(random_bytes(6)).'.png';
                                }

                                $request= $this->model->insertCategory(
                                    $photoCategory, 
                                    $strName,
                                    $strDescription,
                                    $route
                                );
                            }
                        }else{
                            if($_SESSION['permitsModule']['u']){
                                $option = 2;
                                $request = $this->model->selectCategory($idCategory);
                                if($_FILES['txtImg']['name'] == ""){
                                    $photoCategory = $request['picture'];
                                }else{
                                    if($request['picture'] != "category.jpg"){
                                        deleteFile($request['picture']);
                                    }
                                    $photo = $_FILES['txtImg'];
                                    $photoCategory = 'category_'.bin2hex(random_bytes(6)).'.png';
                                }
                                $request = $this->model->updateCategory(
                                    $idCategory, 
                                    $photoCategory,
                                    $strName,
                                    $strDescription,
                                    $route
                                );
                            }
                        }
                        if($request > 0 ){
                            if($photo!=""){
                                uploadImage($photo,$photoCategory);
                            }
                            if($option == 1){
                                $arrResponse = $this->getCategories();
                                $arrResponse['msg'] = 'Datos guardados.';
                            }else{
                                $arrResponse = $this->getCategories();
                                $arrResponse['msg'] = 'Datos actualizados.';
                            }
                        }else if($request == 'exist'){
                            $arrResponse = array('status' => false, 'msg' => 'La categoría ya existe, prueba con otro nombre.');		
                        }else{
                            $arrResponse = array("status" => false, "msg" => 'No es posible guardar los datos.');
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
        public function delCategory(){
            if($_SESSION['permitsModule']['d']){

                if($_POST){
                    if(empty($_POST['idCategory'])){
                        $arrResponse=array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $id = intval($_POST['idCategory']);

                        $request = $this->model->selectCategory($id);
                        if($request['picture']!="category.jpg"){
                            deleteFile($request['picture']);
                        }
                        
                        $request = $this->model->deleteCategory($id);

                        if($request=="ok"){
                            $arrResponse = $this->getCategories();
                            $arrResponse['msg'] = "Se ha eliminado";
                        }else if($request =="exist"){
                            $arrResponse = array("status"=>false,"msg"=>"La categoría tiene al menos una subcategoría asignada, no puede ser eliminada.");
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"No es posible eliminar, intenta de nuevo.");
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
        public function search($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getCategories(1,$search);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sort($params){
            if($_SESSION['permitsModule']['r']){
                $sort = intval($params);
                $arrResponse = $this->getCategories(2,$sort);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /*************************SubCategory methods*******************************/
        public function getSubCategories($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->searchs($params);
                }else if($option == 2){
                    $request = $this->model->sorts($params);
                }else{
                    $request = $this->model->selectSubCategories();
                }
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 

                        $btnEdit="";
                        $btnDelete="";
                        
                        if($_SESSION['permitsModule']['u']){
                            $btnEdit = '<button class="btn btn-success m-1" type="button" title="Edit" data-id="'.$request[$i]['idsubcategory'].'" name="btnEdit"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if($_SESSION['permitsModule']['d']){
                            $btnDelete = '<button class="btn btn-danger m-1" type="button" title="Delete" data-id="'.$request[$i]['idsubcategory'].'" name="btnDelete"><i class="fas fa-trash-alt"></i></button>';
                        }
                        $html.='
                            <tr class="item" data-name="'.$request[$i]['name'].'" data-category="'.$request[$i]['category'].'">
                                <td>'.$request[$i]['name'].'</td>
                                <td>'.$request[$i]['category'].'</td>
                                <td class="item-btn">'.$btnEdit.$btnDelete.'</td>
                            </tr>
                        ';
                    }
                    $arrResponse = array("status"=>true,"data"=>$html);
                }else{
                    $arrResponse = array("status"=>false,"data"=>"No hay datos");
                }
            }else{
                header("location: ".base_url());
                die();
            }
            return $arrResponse;
        }
        public function getSubCategory(){
            if($_SESSION['permitsModule']['r']){

                if($_POST){
                    if(empty($_POST)){
                        $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $idCategory = intval($_POST['idSubCategory']);
                        $request = $this->model->selectSubCategory($idCategory);
                        if(!empty($request)){
                            $arrResponse = array("status"=>true,"data"=>$request);
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"Error, intenta de nuevo."); 
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
        public function setSubCategory(){
            if($_SESSION['permitsModule']['r']){
                if($_POST){
                    if(empty($_POST['txtName']) ||empty($_POST['categoryList'])){
                        $arrResponse = array("status" => false, "msg" => 'Error de datos.');
                    }else{ 
                        $idSubCategory = intval($_POST['idSubCategory']);
                        $strName = ucwords(strClean($_POST['txtName']));
                        $idCategory = intval(strClean($_POST['categoryList']));
                        $route = str_replace(" ","-",$strName);
                        $route = str_replace("?","",$route);
                        $route = strtolower(str_replace("¿","",$route));
                        $route = clear_cadena($route);

                        if($idSubCategory == 0){
                            if($_SESSION['permitsModule']['w']){

                                $option = 1;
                                $request= $this->model->insertSubCategory($idCategory,$strName, $route);
                            }
                        }else{
                            if($_SESSION['permitsModule']['u']){
                                $option = 2;
                                $request = $this->model->updateSubCategory($idSubCategory,$idCategory, $strName, $route);
                            }
                        }
                        if($request > 0 ){
                            if($option == 1){
                                $arrResponse = $this->getSubCategories();
                                $arrResponse['msg'] = 'Datos guardados.';
                            }else{
                                $arrResponse = $this->getSubCategories();
                                $arrResponse['msg'] = 'Datos actualizados.';
                            }
                        }else if($request == 'exist'){
                            $arrResponse = array('status' => false, 'msg' => 'La subcategoría ya existe, intenta con otro nombre.');		
                        }else{
                            $arrResponse = array("status" => false, "msg" => 'No es posible guardar los datos.');
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
        public function delSubCategory(){
            if($_SESSION['permitsModule']['d']){

                if($_POST){
                    if(empty($_POST['idSubCategory'])){
                        $arrResponse=array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $id = intval($_POST['idSubCategory']);
                        $request = $this->model->deleteSubCategory($id);
                        if($request=="ok"){
                            $arrResponse = $this->getSubCategories();
                            $arrResponse['msg'] = "Se ha eliminado"; 
                        }else if($request=="exist"){
                            $arrResponse = array("status"=>false,"msg"=>"La subcategoría tiene al menos un producto asignado, no puede ser eliminado.");
                        }
                        else{
                            $arrResponse = array("status"=>false,"msg"=>"No es posible eliminar, intenta de nuevo.");
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
            $html="";
            $request = $this->model->selectCategories();
            if(count($request)>0){
                for ($i=0; $i < count($request); $i++) { 
                    $html.='<option value="'.$request[$i]['idcategory'].'">'.$request[$i]['name'].'</option>';
                }
                $arrResponse = array("data"=>$html);
            }else{
                $arrResponse = array("data"=>$html);
            }
            echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            die();
        }
        public function searchs($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getSubCategories(1,$search);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sorts($params){
            if($_SESSION['permitsModule']['r']){
                $sort = intval($params);
                $arrResponse = $this->getSubCategories(2,$sort);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
    }
?>