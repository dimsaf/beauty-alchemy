<?php
// модель для работы с корзиной клиента 

function basket_get_by_user_id($user_id){
    $b= query('SELECT * FROM basket WHERE user_id='.$user_id.' AND state_id=1');
    if (empty($b)){
        $b=query('INSERT INTO basket(user_id, date, state_id) VALUES ('.$user_id.','.time().',1)');
    }
    return $b;
}
function basket_get_by_user_id_state2($user_id){
    return query('SELECT * FROM basket WHERE user_id='.$user_id.' AND state_id > 1');
}
function basket_add_product($product_id){
    global $basket;
    $tmp = query('SELECT * FROM product_basket WHERE product_id='.$product_id.
    ' AND basket_id='.$basket[0]['basket_id']);
    if(!empty($tmp)){
        query('UPDATE product_basket SET pcount=pcount+1 WHERE basket_id='.$basket[0]['basket_id'].
        ' AND product_id='.$product_id);
    }else {
        query('INSERT into product_basket (product_id, basket_id, pcount) VALUES ('.$product_id.','
        .$basket[0]['basket_id'].',1)');
    }
}  
function basket_del_product($product_id){
    global $basket;
    $tmp = query('SELECT * FROM product_basket WHERE product_id='.$product_id.
    ' AND basket_id='.$basket[0]['basket_id']);
    //print_r($tmp);
    if($tmp[0]['pcount']>1){
        query('UPDATE product_basket SET pcount=pcount-1 WHERE basket_id='.$basket[0]['basket_id'].
        ' AND product_id='.$product_id);
    }else {
        query('DELETE FROM product_basket WHERE product_id = '.$product_id.' AND basket_id='.$basket[0]['basket_id']);
    }
    return true;
}   

function basket_get_product($basket){
    //global $basket;
    $quantaty=0;
    $amount=0;
    $basket_products=array();
    $bp= query('SELECT product.*, pcount FROM 
    product_basket INNER JOIN product ON product.product_id=
    product_basket.product_id WHERE product_basket.basket_id='.$basket[0]['basket_id']);
    foreach ($bp as $key=>$item){
        $basket_products['key'.$key]=$item;
        $quantaty+=$bp[$key]['pcount'];
        $amount+=$bp[$key]['price']*$bp[$key]['pcount'];
    }
    $basket_products['quantaty']=$quantaty;
    $basket_products['amount']=$amount;
    return $basket_products;
}
function basket_set_status(){
    global $basket;
    query('UPDATE basket SET state_id=2 WHERE basket_id='.$basket[0]['basket_id']);
    return true;
}
function baskets_status2(){
    return query('SELECT * FROM basket WHERE state_id>1');
}
function basket_change_status($basket_id, $status){
    query('UPDATE basket SET state_id='.$status.' WHERE basket_id='.$basket_id);
    return true;
}
function states_get_all(){
    $tmp = query ('SELECT * FROM basket_state');
	foreach($tmp as $key=>$item){
	   $res[$tmp[$key]['state_id']] = $item;
	}
    return $res;
}
function basket_draw($bas){
        $content='';
        $products=array();
        $content.='<table class="zebra row content">
	                   <thead>
                        <h3>Корзина клиента:</h3>
                		<tr>
                			<th>Название</th>
                            <th>Цена</th>
                            <th>Количество</th>
                		</tr>
                	</thead>
                	<tbody>';
        $bas=Array('0'=>$bas);
        $products=basket_get_product($bas);
        foreach($products as $key=>$item){
            if (!($key=='quantaty')&&!($key=='amount')){
                $content.='<tr>';
        		$content.='<td id="name'.$products[$key]["product_id"].'"><a class="basket_hyper" href="?page=showproduct&id='
                    .$products[$key]["product_id"].'">'.$products[$key]["product_name"].'</a></td>';
        		$content.='<td id="price'.$products[$key]["product_id"].'" style="text-align: center">'.$products[$key]["price"].'</td>';
        		$content.='<td id="count'.$products[$key]["product_id"].'" style="text-align: center">'.$products[$key]["pcount"].'</td>';
                $content.='</tr>';
            }
        }
        $content.='</tbody></table>';
        $content.='<h4 style="color:red">Всего товаров: '.$products['quantaty'].', на сумму: '
                    .$products['amount'].' руб.  Статус корзины (изменить): ';
        $content.= '<select id="status'.$bas[0]['basket_id'].'" onchange="basket_set_status('.$bas[0]['basket_id'].');">';
        $states=states_get_all();
        foreach ($states as $state){
            $selected='';
            if ($bas[0]['state_id']==$state['state_id']) $selected='selected';
            $content.= '<option '.$selected.' value="'.$state['state_id'].'">'.$state['name'].'</option>';
        }
        $content.= '"</select></h4></br>';
        return $content;
}
