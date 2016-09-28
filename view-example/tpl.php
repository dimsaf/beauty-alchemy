<?php
/**
 * @author admin
 * @copyright 2016
 * view для отображения любой страницы
 * на Главной странице рисует динамическое меню $dropdown, пункты которого меняются в зависимости от наличия товаров в базе данных
 */
    // функция "собери мою страницу" - собираем страницу из tpl -ек
    function make_my_page($content){
        // открываем видимость массива переменных $config из config.php
        global $config;
        global $page;
        global $basket_products;
        global $ties;
        // формируем массив с метками, чтобы знать что менять
        $header=array();
        $top=array();
        $header['title']=$config['config']['title'];
        $header['css']=$config['css'];
        $header['js']=$config['js'];
        // окошки для входа пользователя
        if ($_SESSION['user']==""){               
            $top['log_in']='log_in';
            $top['log_out']='log_out_hide';
            $top['basket']='basket_hide';
            $top['quantaty']=0;
            $top['amount']=0;
        }else{
            $top['log_in']='log_in_hide';
            $top['log_out']='log_out';
            $top['user']="Добро пожаловать, ".$_SESSION['user']." !";
            $top['basket']='basket';
            $top['quantaty']=$basket_products['quantaty'];
            $top['amount']=$basket_products['amount'];
        }
        // кнопка и доступ для админа
        if ($_SESSION['role_id']=="1"){
            $top['admin']='admin';
            $top['basket']='basket_hide';
        }else{
            $top['admin']='admin_hide';
        }
        for ($i=1; $i<9; $i++){
            $top['activebtn'.$i]="";
        }
        $top['activebtn'.$config['pages'][$page]]='activebtn';
        // рисуем выпадающие списки в Главном меню
        $dropdown='';
        foreach($ties['lines'] as $key=>$item){
            $vars=$key.",'','',''";
            $dropdown.='<li><a onclick="product_get('.$vars.');">'.$item['line_name'].'</a><ul>';
            foreach($ties['linecat'] as $key1=>$item1){
                if($item1['line']==$key) {
                    $vars=$key.",".$item1['category'].",'',''";
                    $dropdown.='<li><a onclick="product_get('.$vars.');">'.$item1['cat_name'].'</a>';
                    if(strpos($item1['cat_name'], 'лиц')){
                        $dropdown.='<ul>';
                        foreach($ties['lineskin'] as $key2=>$item2){
                            if($item2['line']==$key) {
                                $vars=$key.",".$item1['category'].",".$item2['skin'].",''";
                                $dropdown.='<li><a onclick="product_get('.$vars.');">'.$item2['skin_name'].'</a>';
                                $dropdown.='<ul>';
                                foreach($ties['lineskinkind'] as $key3=>$item3){
                                    if(($item3['line']==$key) && ($item3['skin']==$item2['skin'])) {
                                        $vars=$key.",".$item1['category'].",".$item2['skin'].",".$item3['kind'];
                                        $dropdown.='<li><a onclick="product_get('.$vars.');">'.$item3['kind_name'].'</a></li>';
                                    }
                                }
                                $dropdown.='</ul></li>';
                            }
                        }
                        $dropdown.='</ul>';
                    }else{
                        $dropdown.='<ul>';
                        foreach($ties['linecatkind'] as $key2=>$item2){
                            if(($item2['line']==$key) && ($item2['category']==$item1['category'])) {
                                $vars=$key.",".$item1['category'].",'',".$item2['kind'];
                                $dropdown.='<li><a onclick="product_get('.$vars.');">'.$item2['kind_name'].'</a></li>';
                            }
                        }
                        $dropdown.='</ul>';
                    }
                    $dropdown.='</li>';
                }
            }
            $dropdown.='</ul></li>';
        }
        $vars="'','','',''";
        $dropdown.='<li><a onclick="product_get('.$vars.');">ПОКАЗАТЬ ВСЕ ТОВАРЫ</a></li>';
        $top['dropdown']=$dropdown;
        // рисуем вертикальное меню
        $verticalmenu='';
        foreach($ties['catdescr'] as $key=>$item){
            $verticalmenu.='<div class="vertsub"><button id="vertical'.$item['category'].'" class="btn btn-success vertical" >'.
                            $item['descr'].'</button>';
            foreach($ties['catkind'] as $key1=>$item1){
                if ($item['category']==$item1['category']){
                    $vars="'',".$item['category'].",'',".$item1['kind'];
                    $verticalmenu.='<a id="vertslide'.$item['category'].'kind'.$item1['kind'].'" class="vertslide" onclick="product_get('.
                                    $vars.');" >'.$item1['kind_name'].'</a>';
                }
            }
            $vars="'',".$item['category'].",'',''";
            $verticalmenu.='<a class="vertslide" id="vertslide'.$item['category'].'kind" onclick="product_get('.$vars.');">ПОКАЗАТЬ ВСЕ ТОВАРЫ</a>';
            $verticalmenu.='</div>';
        }
        $top['verticalmenu']=$verticalmenu;
        // открыть-скрыть вертикальное меню
        if($config['access'][$page]==1) {
            $top['verticalid']='verticalmenu_hide';
            $top['container']='container col-sm-12';
        }else {
            $top['verticalid']='verticalmenu';
            $top['container']='container col-sm-9';
        }

        $result="";
        // в переменную $result собираем весь код html
        $result.=get_tpl("head.tpl",$header);
        $result.=get_tpl("top.tpl",$top);       
        $result.=$content; // его мы получаем из 'page'.php
        $result.=get_tpl("footer.tpl",array());
        return $result;
    }
    // функция принимает название tpl-файла и массив со значениями меток и возвращает строку с разметкой страницы
    function get_tpl($tpl_name, $vars){
        $result="";
        $result=file_get_contents("scripts/view/".$tpl_name);
        // заменяем метки на их значения из конфига (т.е. из файлов ini) или на любые другие
        foreach($vars as $key=>$item){
            // эта функция замены принимает: что меняем, на что меняем, где меняем
            $result=str_replace("[#".$key."#]", $item, $result);
        }
        return $result;
    }
