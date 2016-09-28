<?php
/**
 * @author admin
 * @copyright 2016
 */
// модель КОММЕНТАРИИ ПОЛЬЗОВАТЕЛЕЙ
function comment_add($text, $product_id){
    global $user;
    return query("INSERT INTO comment (text, date, user_id, product_id) VALUES ('".
    $text."',".time().",".$user[0]['user_id'].",".$product_id.")");
}

function comment_get_last_by_product_id($product_id, $comment_id){
    return query("SELECT comment.*, user.login as uname from comment INNER JOIN user ON 
    user.user_id = comment.user_id WHERE product_id=".$product_id." AND 
    comment_id>".$comment_id." ORDER BY comment_id");
    
}
function comment_delete($id){
    query("DELETE FROM comment WHERE comment_id = ".$id);
    return true;
}
// КНИГА ОТЗЫВОВ
function review_add($text){
    global $user;
    return query("INSERT INTO review (text, date, user_id) VALUES ('".
    $text."',".time().",".$user[0]['user_id'].")");
}
function review_get_last($review_id){
    return query("SELECT review.*, user.login AS uname FROM review, user WHERE 
    user.user_id = review.user_id AND review_id>".$review_id." ORDER BY review_id");
}

function review_delete($id){
    query("DELETE FROM review WHERE review_id = ".$id);
    return true;
}
// ОБРАТНАЯ СЯЗЬ ОТ КЛИЕНТОВ
function feedback_add($text){
    global $user;
    #!/usr/bin/php -q 
    $address="feedback@beauty-alchemy.ru";
    $subj="уведомление от сайта beauty-alchemy.ru";
    $massage="Светлана! На Ваш сайт http://beauty-alchemy.ru пришло новое письмо от клиента ".$user[0]['login'].
            "!\nСодержание письма:\n***\n".$text."\n***\n\n\nВаш верный автоинформатор :)))";
    $head="Content-type:text/plain; \n\t charset=utf-8;"; 
    mail($address, $subj, $massage, $head, "-f feedback@beauty-alchemy.ru");
    return query("INSERT INTO feedback (text, date, from_id, to_id) VALUES ('".$text."',".time().",".$user[0]['user_id'].",0)");
}
function admin_feedback_add($to_id,$text){
    global $user;
    load_model('user');
    $client=user_get_by_id($to_id);
    #!/usr/bin/php -q 
    $address=$client[0]['email'];
    $subj="ответ от сайта beauty-alchemy.ru";
    $massage="Уважаемый ".$client[0]['login']."! Вам пришло письмо от администрации сайта 'Алхимия красоты' http://beauty-alchemy.ru !\n
              Содержание письма:\n***\n".$text."\n***\n
              Ответить на письмо, а также посмотреть всю переписку Вы можете в вашем личном кабинете: http://beauty-alchemy.ru?page=feedback\n\n
              С уважением, ваша 'Алхимия красоты'.";
    $head="Content-type:text/plain; \n\t charset=utf-8;"; 
    mail($address, $subj, $massage, $head, "-f feedback@beauty-alchemy.ru");
    return query("INSERT INTO feedback (text, date, from_id, to_id) VALUES ('".$text."',".time().",".$user[0]['user_id'].",".$to_id.")");
}
function feedback_get_last_by_user_id($feedback_id, $user_id){
    return query("SELECT feedback.*, user.login AS fromname FROM feedback INNER JOIN user ON 
    feedback.from_id = user.user_id WHERE (feedback.from_id =".$user_id." OR feedback.to_id = ".$user_id.") AND 
    feedback_id>".$feedback_id." ORDER BY feedback_id");
}
function feedback_get_all_incoming(){
    return query("SELECT feedback.feedback_id, feedback.from_id, feedback.date, user.login AS fromname, user.time_watch FROM 
                feedback, user WHERE feedback.from_id=user.user_id AND to_id=0 ORDER BY feedback.feedback_id DESC");
}
function set_time_watch($user_id,$time_watch){
    query("UPDATE user SET time_watch= ".$time_watch." WHERE user_id=".$user_id);
    return true;
}
