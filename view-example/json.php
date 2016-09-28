<?php

/**
 * @author admin
 * @copyright 2016
 */
if (empty($_POST['action'])) {
    echo json_encode(array('state' => false, 'message' => "ANY ACTION IS ABSENT!"));
    return;
}
switch ($_POST['action']) {
    case 'registr':
        if (load_model('user')) {
            $res = user_registr($_POST['login'], $_POST['password'], $_POST['email']);
            echo json_encode($res);
        } else
            echo json_encode(array('state' => false, 'message' =>
                    "Ошибка! Модели user.php не существует!"));
        break;
    case 'entrance':
        if (load_model('user')) {
            $res = user_entrance($_POST['login'], $_POST['password']);
            echo json_encode($res);
        } else
            echo json_encode(array('state' => false, 'message' =>
                    "Ошибка! Модели user.php не существует!"));
        break;
    case 'exit':
        if (load_model('user')) {
            $res = online_delete($_SESSION['user_id']);
            $main=false;
            if($_SESSION['role_id']==1)$main=true;
            foreach ($_SESSION as $key => $item) {
                $_SESSION[$key] = '';
            }
            echo json_encode(array('state' => true, 'main'=>$main));
        } else
            echo json_encode(array('state' => false, 'message' =>
                    "Ошибка! Модели user.php не существует!"));
        break;
    case 'catalog':
        load_model('product');
        $res = product_get($_POST['line'],$_POST['category'],$_POST['skin'],$_POST['kind']);
        echo json_encode(array('state' => true, 'catalog' => $res));
        break;
    case 'search':
        load_model('product');
        $res = product_get_by_search($_POST);
        echo json_encode(array('state' => true, 'catalog' => $res));
        break;
    case 'table':
        load_model('product');
        $res = products_get_all();
        $links['line']=lines_get_all();
        $links['category']=categories_get_all();
        $links['kind']=kinds_get_all();
        $links['skin']=skins_get_all();
        echo json_encode(array('state' => true, 'res' => $res, 'links'=>$links));
        break;
    case 'edit':
        load_model('product');
        $tmp=product_get_by_id1($_POST['id']);
        if($tmp) $res=product_update($_POST['id'], $_POST['field'], $_POST['new_val']); 
        else $res=product_add($_POST['id'], $_POST['field'], $_POST['new_val']);
        echo json_encode(array('state' => $res));
        break;
    case 'image_add':
              $download = "design/img/".time().".jpg";
              if (move_uploaded_file($_FILES['img']['tmp_name'], $download)) {
                  query("UPDATE product SET product_img = '".$download."' WHERE product_id = ".$_POST['id']);
                  header('location: index.php?page=admin_products&prod_id='.$_POST['id']);
              } else {
                  echo "ошибка загрузки фото";
              }
              return "Фото успешно загружено в базу!";
              break;
    case 'product_delete':
        load_model('product');
        $res = product_delete($_POST['product_id']);
        echo json_encode(array('state' => $res, 'message' =>"Продукт удален из базы!"));
        break;
    case 'line':
        global $ties;
        echo json_encode(array('state' => true, 'line' => $ties['lines']));
        break;
    case 'admin_lines':
        load_model('product');
        $res = lines_get_all();
        echo json_encode(array('state' => true, 'res' => $res));
        break;
    case 'edit_line':
        load_model('product');
        $tmp=line_get_by_id($_POST['id']);
        if($tmp) $res = line_update($_POST['id'], $_POST['field'], $_POST['new_val']);
        else $res=line_add($_POST['id'], $_POST['field'], $_POST['new_val']);
        echo json_encode(array('state' => $res));
        break;
    case 'image_add_line':
              $download = "design/img/lines/".time().".jpg";
              if (move_uploaded_file($_FILES['img']['tmp_name'], $download)) {
                  query("UPDATE line SET img = '".$download."' WHERE line_id = ".$_POST['id']);
                  header('location: index.php?page=admin_lines');
              } else {
                  echo "ошибка загрузки фото";
              }
              echo "Фото успешно загружено в базу!";
              break;
    case 'line_delete':
        load_model('product');
        $res = line_delete($_POST['line_id']);
        echo json_encode(array('state' => $res, 'message' =>"Линия удалена из базы!"));
        break;
    case 'category':
        global $ties;
        echo json_encode(array('state' => true, 'category' => $ties['linecat']));
        break;
    case 'admin_cats':
        load_model('product');
        $res = categories_get_all();
        echo json_encode(array('state' => true, 'res' => $res));
        break;
    case 'edit_category':
        load_model('product');
        $tmp=category_get_by_id($_POST['id']);
        if($tmp) $res = category_update($_POST['id'], $_POST['field'], $_POST['new_val']);
        else $res=category_add($_POST['id'], $_POST['field'], $_POST['new_val']);
        echo json_encode(array('state' => $res));
        break;
    case 'image_add_category':
              $download = "design/img/categories/".time().".jpg";
              if (move_uploaded_file($_FILES['img']['tmp_name'], $download)) {
                  query("UPDATE category SET img = '".$download."' WHERE category_id = ".$_POST['id']);
                  header('location: index.php?page=admin_categories');
              } else {
                  echo "ошибка загрузки фото";
              }
              return "Фото успешно загружено в базу!";
              break;
    case 'category_delete':
        load_model('product');
        $res = category_delete($_POST['category_id']);
        echo json_encode(array('state' => $res, 'message' =>"Категория удалена из базы!"));
        break;
    case 'kind':
        global $ties;
        echo json_encode(array('state' => true, 'kind' => $ties['linecatkind']));
        break;
    case 'skinkind':
        global $ties;
        echo json_encode(array('state' => true, 'kind' => $ties['lineskinkind']));
        break;
    case 'navigator':
        global $ties;
        echo json_encode(array('state' => true, 'res' => $ties));
        break;
    case 'admin_kinds':
        load_model('product');
        $res = kinds_get_all();
        echo json_encode(array('state' => true, 'res' => $res));
        break;
    case 'edit_kind':
        load_model('product');
        $tmp=kind_get_by_id($_POST['id']);
        if($tmp) $res = kind_update($_POST['id'], $_POST['field'], $_POST['new_val']);
        else $res=kind_add($_POST['id'], $_POST['field'], $_POST['new_val']);
        echo json_encode(array('state' => $res));
        break;
    case 'image_add_kind':
              $download = "design/img/kinds/".time().".jpg";
              if (move_uploaded_file($_FILES['img']['tmp_name'], $download)) {
                  query("UPDATE kind SET img = '".$download."' WHERE kind_id = ".$_POST['id']);
                  header('location: index.php?page=admin_kinds');
              } else {
                  echo "ошибка загрузки фото";
              }
              echo "Фото успешно загружено в базу!";
              break;
    case 'kind_delete':
        load_model('product');
        $res = kind_delete($_POST['kind_id']);
        echo json_encode(array('state' => $res, 'message' =>"Вид товара удален из базы!"));
        break;
    case 'skin':
        global $ties;
        echo json_encode(array('state' => true, 'skin' => $ties['lineskin']));
        break;
    case 'admin_skins':
        load_model('product');
        $res = skins_get_all();
        echo json_encode(array('state' => true, 'res' => $res));
        break;
    case 'edit_skin':
        load_model('product');
        $tmp=skin_get_by_id($_POST['id']);
        if($tmp) $res = skin_update($_POST['id'], $_POST['field'], $_POST['new_val']);
        else $res=skin_add($_POST['id'], $_POST['field'], $_POST['new_val']);
        echo json_encode(array('state' => $res));
        break;
    case 'image_add_skin':
              $download = "design/img/skins/".time().".jpg";
              if (move_uploaded_file($_FILES['img']['tmp_name'], $download)) {
                  query("UPDATE skin SET img = '".$download."' WHERE skin_id = ".$_POST['id']);
                  header('location: index.php?page=admin_skins');
              } else {
                  echo "ошибка загрузки фото";
              }
              echo "Фото успешно загружено в базу!";
              break;
    case 'skin_delete':
        load_model('product');
        $res = skin_delete($_POST['skin_id']);
        echo json_encode(array('state' => $res, 'message' =>"Тип кожи удален из базы!"));
        break;
        
    // ДЕЙСТВИЯ С КОРЗИНАМИ КЛИЕНТОВ
    case 'addtobasket':
        if (!empty($_SESSION['user_id'])){
            load_model('basket');
            basket_add_product($_POST['product_id']);
            $res=true;
        }else $res=false;
        echo json_encode(array('state' => $res));
        break;
    case 'delfrombasket':
        load_model('basket');
        $res = basket_del_product($_POST['product_id']);
        echo json_encode(array('state' => $res));
        break;
    case 'getbasket':
        load_model('basket');
        global $basket;
        echo json_encode(array('state' => true, 'basket' => basket_get_product($basket)));
        break;
    case 'basketconfirm';
        load_model('user');
        user_set_contacts($_POST['phone'],$_POST['adress']);
        load_model('basket');
        echo json_encode(array('state' => true, 'basket' => basket_set_status()));
        break;
    case 'basket_change_status':
        load_model('basket');
        echo json_encode(array('state' => true, 'basket' => basket_change_status($_POST['basket_id'], $_POST['status'])));
        break;
        
    // КОММЕНТАРИИ ПОЛЬЗОВАТЕЛЕЙ
    case 'sendcomment':
        load_model('comment');
        echo json_encode(array('state' => comment_add($_POST['text'],$_POST['product_id'])));
        break;
    case 'getcomment':
        load_model('comment');
        echo json_encode(array('state' => true, 'comments' => comment_get_last_by_product_id($_POST['product_id'],$_POST['comment_id'])));
        break;
    case 'delcomment':
        load_model('comment');
        echo json_encode(array('state' => comment_delete($_POST['comment_id']), 'message'=>'Комментарий удален из базы!'));
        break;
        
    // КНИГА ОТЗЫВОВ
    case 'send_review':
        load_model('comment');
        echo json_encode(array('state' => review_add($_POST['text'])));
        break;
    case 'getreview':
        load_model('comment');
        echo json_encode(array('state' => true, 'reviews' => review_get_last($_POST['review_id'])));
        break;
    case 'delreview':
        load_model('comment');
        echo json_encode(array('state' => review_delete($_POST['review_id']), 'message'=>'Отзыв удален из базы!'));
        break;
        
    // ОБРАТНАЯ СЯЗЬ ОТ КЛИЕНТОВ
    case 'send_feedback':
        load_model('comment');
        echo json_encode(array('state' => feedback_add($_POST['text'])));
        break;
    case 'send_admin_feedback':
        load_model('comment');
        echo json_encode(array('state' => admin_feedback_add($_POST['to_id'],$_POST['text'])));
        break;
    case 'getfeedback':
        load_model('comment');
        if (!empty($_POST['time_watch'])) set_time_watch($_POST['user_id'],$_POST['time_watch']);
        echo json_encode(array('state' => true, 'feedbacks' => feedback_get_last_by_user_id($_POST['feedback_id'],$_POST['user_id'])));
        break;
    default:
        echo json_encode(array('state' => false, 'message' =>
                "THIS ACTION IS ABSENT!"));
        break;
}
