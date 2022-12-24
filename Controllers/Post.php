<?php
    class Post extends Controllers{

        public function __construct(){
            session_start();
            if(empty($_SESSION['login'])){
                header("location: ".base_url());
                die();
            }
            parent::__construct();
            getPermits(7);
        }
        
        public function articles(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Articulos";
                $data['page_title'] = "Articulos";
                $data['page_name'] = "articles";
                $data['data'] = $this->getArticles();
                $data['app'] = "blog.js";
                $this->views->getView($this,"articles",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function category(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Categoría";
                $data['page_title'] = "Blog Categorias";
                $data['page_name'] = "blogcategory";
                $data['app'] = "blogcategory.js";
                $data['data'] = $this->getCategories();
                $this->views->getView($this,"category",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        public function subcategory(){
            if($_SESSION['permitsModule']['r']){
                $data['page_tag'] = "Subcategoria";
                $data['page_title'] = "Blog Subcategorias";
                $data['page_name'] = "blogsubcategory";
                $data['app'] = "blogsubcategory.js";
                $data['data'] = $this->getSubCategories();
                $this->views->getView($this,"subcategory",$data);
            }else{
                header("location: ".base_url());
                die();
            }
        }
        /*************************Article methods*******************************/
        public function getArticles($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->search($params);
                }else if($option == 2){
                    $request = $this->model->sort($params);
                }else{
                    $request = $this->model->selectArticles();
                }
                if(count($request)>0){
                    for ($i=0; $i < count($request); $i++) { 

                        $status="";
                        $btnView = '<button class="btn btn-info m-1 text-white" type="button" title="Watch" data-id="'.$request[$i]['idarticle'].'" name="btnView"><i class="fas fa-eye"></i></button>';
                        $btnGlobe = '<a href="'.base_url().'/blog/article/'.$request[$i]['route'].'" target="_blank" class="btn btn-primary m-1 text-white" title="Watch on website"><i class="fas fa-globe"></i></a>';
                        $btnEdit="";
                        $btnDelete="";
                        if($_SESSION['permitsModule']['u']){
                            $btnEdit = '<button class="btn btn-success m-1 text-white" type="button" title="Edit" data-id="'.$request[$i]['idarticle'].'" name="btnEdit"><i class="fas fa-pencil-alt"></i></button>';
                        }
                        if($_SESSION['permitsModule']['d']){
                            $btnDelete = '<button class="btn btn-danger m-1 text-white" type="button" title="Delete" data-id="'.$request[$i]['idarticle'].'" name="btnDelete"><i class="fas fa-trash-alt"></i></button>';
                        }
                        if($request[$i]['status']==1){
                            $status='<span class="badge me-1 bg-success">Activo</span>';
                        }else{
                            $status='<span class="badge me-1 bg-danger">Inactivo</span>';
                        }
                        $html.='
                            <tr class="item">
                                <td>'.$request[$i]['name'].'</td>
                                <td>'.$request[$i]['category'].'</td>
                                <td>'.$request[$i]['subcategory'].'</td>
                                <td>'.$status.'</td>
                                <td>'.$request[$i]['date'].'</td>
                                <td>'.$request[$i]['dateupdated'].'</td>
                                <td class="item-btn">'.$btnGlobe.$btnView.$btnEdit.$btnDelete.'</td>
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
        public function getArticle(){
            if($_SESSION['permitsModule']['r']){

                if($_POST){
                    if(empty($_POST)){
                        $arrResponse = array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $idArticle = intval($_POST['idArticle']);
                        $request = $this->model->selectArticle($idArticle);
                        if(!empty($request)){
                            if($request['picture']!=""){
                                $request['picture'] = media()."/images/uploads/".$request['picture'];
                            }
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
        public function setArticle(){
            if($_SESSION['permitsModule']['r']){
                if($_POST){
                    if(empty($_POST['txtName']) || empty($_POST['subcategoryList']) || empty($_POST['categoryList']) || empty($_POST['txtDescription'] ) || empty($_POST['statusList'])){
                        $arrResponse = array("status" => false, "msg" => 'Error de datos');
                    }else{ 
                        $idArticle = intval($_POST['idArticle']);
                        $strName = strClean($_POST['txtName']);
                        $strDescription = strClean($_POST['txtDescription']);
                        $intCategory = intval($_POST['categoryList']);
                        $intSubCategory = intval($_POST['subcategoryList']);
                        $intStatus = intval($_POST['statusList']);
                        $request_article = "";
                        $photo = "";
                        $photoPost="";

                        $route = str_replace(" ","-",$strName);
                        $route = str_replace("?","",$route);
                        $route = strtolower(str_replace("¿","",$route));
                        $route = clear_cadena($route);
    
                        if($idArticle == 0){
                            if($_SESSION['permitsModule']['w']){

                                $option = 1;
                                if($_FILES['txtImg']['name'] != ""){
                                    $photo = $_FILES['txtImg'];
                                    $photoPost = 'article_'.bin2hex(random_bytes(6)).'.png';
                                }
                                $request_article = $this->model->insertArticle($intCategory,$intSubCategory,$photoPost,$strName,$strDescription,$intStatus,$route);
                            }
                        }else{
                            if($_SESSION['permitsModule']['u']){

                                $option = 2;
                                $request = $this->model->selectArticle($idArticle);

                                if($_FILES['txtImg']['name'] == ""){
                                    $photoPost = $request['picture'];
                                }else{
                                    if($request['picture'] != ""){
                                        deleteFile($request['picture']);
                                    }
                                    $photo = $_FILES['txtImg'];
                                    $photoPost = 'article_'.bin2hex(random_bytes(6)).'.png';
                                }
                                
                                $request_article = $this->model->updateArticle($idArticle,$intCategory,$intSubCategory,$photoPost,$strName,$strDescription,$intStatus,$route);
                            }
                        }
    
                        if($request_article > 0 ){
                            if($photo!=""){
                                uploadImage($photo,$photoPost);
                            }
                            if($option == 1){
                                $arrResponse = $this->getArticles();
                                $arrResponse['msg'] = 'Datos guardados.';
                            }else{
                                $arrResponse = $this->getArticles();
                                $arrResponse['msg'] = 'Datos actualizados.';
                            }
                        }else if($request_article == 'exist'){
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! el título ya está registrado, pruebe con otro.');		
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
        public function delArticle(){
            if($_SESSION['permitsModule']['d']){
                if($_POST){
                    if(empty($_POST['idArticle'])){
                        $arrResponse=array("status"=>false,"msg"=>"Error de datos");
                    }else{
                        $id = intval($_POST['idArticle']);
                        $request = $this->model->selectArticle($id);
                        if($request['picture']!=""){
                            deleteFile($request['picture']);
                        }
                        $request = $this->model->deleteArticle($id);
                        if($request=="ok"){
                            $arrResponse = $this->getArticles();
                            $arrResponse['msg'] = "Se ha eliminado";
                        }else{
                            $arrResponse = array("status"=>false,"msg"=>"No se ha podido eliminar, intenta de nuevo.");
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
        public function search($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getArticles(1,$search);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sort($params){
            if($_SESSION['permitsModule']['r']){
                $sort = intval($params);
                $arrResponse = $this->getArticles(2,$sort);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        /*************************Category methods*******************************/
        public function getCategories($option=null,$params=null){
            if($_SESSION['permitsModule']['r']){
                $html="";
                $request="";
                if($option == 1){
                    $request = $this->model->searchc($params);
                }else if($option == 2){
                    $request = $this->model->sortc($params);
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
                        $route = str_replace(" ","-",$strName);
                        $route = str_replace("?","",$route);
                        $route = strtolower(str_replace("¿","",$route));
                        $route = clear_cadena($route);
                        $photo = "";
                        $photoCategory="";

                        if($idCategory == 0){
                            if($_SESSION['permitsModule']['w']){
                                $option = 1;
                                $request= $this->model->insertCategory($strName,$route);
                            }
                        }else{
                            if($_SESSION['permitsModule']['u']){
                                $option = 2;
                                $request = $this->model->updateCategory($idCategory,$strName,$route);  
                            }
                        }
                        if($request > 0 ){
                            if($option == 1){
                                $arrResponse = $this->getCategories();
                                $arrResponse['msg'] = 'Datos guardados.';
                            }else{
                                $arrResponse = $this->getCategories();
                                $arrResponse['msg'] = 'Datos actualizados.';
                            }
                        }else if($request == 'exist'){
                            $arrResponse = array('status' => false, 'msg' => '¡Atención! La categoría ya existe, pruebe con otro nombre.');		
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
                        $request = $this->model->deleteCategory($id);

                        if($request=="ok"){
                            $arrResponse = $this->getCategories();
                            $arrResponse['msg'] = "Se ha eliminado"; 
                        }else if($request=="exist"){
                            $arrResponse = array("status"=>false,"msg"=>"La categoría tiene al menos una subcategoria asignada, no puede ser eliminado.");
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
        public function searchc($params){
            if($_SESSION['permitsModule']['r']){
                $search = strClean($params);
                $arrResponse = $this->getCategories(1,$search);
                echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
            }
            die();
        }
        public function sortc($params){
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
                                $request= $this->model->insertSubCategory($idCategory,$strName,$route);
                            }
                        }else{
                            if($_SESSION['permitsModule']['u']){
                                $option = 2;
                                $request = $this->model->updateSubCategory($idSubCategory, $idCategory,$strName, $route);
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