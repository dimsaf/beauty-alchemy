<?php

/**
 * @author saf
 * @copyright 2016
 * корзина клиента
 */
    global $basket_products;
    global $user;
    //print_r($basket_products);
    $mybasket="";
    if ($basket_products['quantaty']=="") $basket_products['quantaty']=0;
    if ($basket_products['amount']=="") $basket_products['amount']=0;
    foreach($basket_products as $key=>$item){
        if (!($key=='quantaty')&&!($key=='amount')){
            $mybasket.='<tr>';
    		$mybasket.='<td id="name'.$basket_products[$key]["product_id"].'"><a class="basket_hyper" href="?page=showproduct&id='
                .$basket_products[$key]["product_id"].'">'.$basket_products[$key]["product_name"].'</a></td>';
    		$mybasket.='<td id="descr'.$basket_products[$key]["product_id"].'">'.$basket_products[$key]["description"].'</td>';
    		$mybasket.='<td id="price'.$basket_products[$key]["product_id"].'">'.$basket_products[$key]["price"].'</td>';
    		$mybasket.='<td id="count'.$basket_products[$key]["product_id"].'">'.$basket_products[$key]["pcount"].'</td>';
            $mybasket.='<td class="del'.$basket_products[$key]["product_id"].'">
            <button class="delete"  title="Удалить из корзины" onclick=del_from_basket('.$basket_products[$key]["product_id"].')><strong>X</strong></button></td>';
            $mybasket.='</tr>';
        }
    }
    $mybasket.='</tbody></table>';
    $mybasket.='<h3 style="color:red">Всего товаров: '.$basket_products['quantaty'].', на сумму: '.$basket_products['amount'].' руб.</h3>';
    $mybasket.='<h2>Для отправки заказа просим Вас заполнить или подтвердить Ваши контактные данные:</h2>
        <img id="product_img" class="imground" src="design/img/basket.jpg"/>
        <h4>Телефон для связи:</h4>
        <input id="phone"  value="'.$user[0]['phone'].'"/>
        <h4>Адрес доставки:</h4>
        <textarea id="adress">'.$user[0]['adress'].'</textarea><br />
        <button class="btn btn-primary" onclick="basket_confirm();">Подтвердить заказ!</button>
        <img src="design/img/basket-empty.png"/>
        <br /><br />';
    $mybasket = Array('mybasket'=>$mybasket);
    $content="";
    $content=get_tpl("pages/mybasket.tpl",$mybasket);
    echo make_my_page($content);
