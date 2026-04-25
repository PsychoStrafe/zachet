<?php
/**
 * Этот код нужно добавить в functions.php вашей темы или в файл плагина.
 * Он регистрирует шорткод [boat_form], который можно вставить на любую страницу.
 */

function boat_rental_form_handler() {
    // 1. Регистрация типа записи для заказов (чтобы они сохранялись в админку)
    if (!post_type_exists('boat_order')) {
        register_post_type('boat_order', array(
            'labels' => array('name' => 'Заказы лодок', 'singular_name' => 'Заказ'),
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-clipboard',
            'supports' => array('title', 'editor')
        ));
    }

    // 2. Обработка отправки формы
    if (isset($_POST['boat_submit'])) {
        if (isset($_POST['boat_nonce_field']) && wp_verify_nonce($_POST['boat_nonce_field'], 'boat_form_action')) {
            $user_name = sanitize_text_field($_POST['user_name']);
            $boat_name = sanitize_text_field($_POST['boat_name']);
            $message   = sanitize_textarea_field($_POST['user_message']);

            wp_insert_post(array(
                'post_title'   => "Заказ от: " . $user_name,
                'post_content' => "Лодка: $boat_name \nСообщение: $message",
                'post_status'  => 'publish',
                'post_type'    => 'boat_order',
            ));
            echo '<div style="color: green; margin-bottom: 20px;">Ваш заказ успешно сохранен в админке!</div>';
        }
    }

    // 3. Вывод самой формы
    ob_start(); ?>
    <div class="boat-form-container" style="max-width: 450px; margin: 20px 0; font-family: sans-serif;">
        <form action="" method="post" style="background: #ffffff; padding: 25px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #333;">Бронирование лодки</h3>
            <?php wp_nonce_field('boat_form_action', 'boat_nonce_field'); ?>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Ваше имя:</label>
                <input type="text" name="user_name" required placeholder="Иван Иванов" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Название лодки:</label>
                <input type="text" name="boat_name" required placeholder="Например: Катер Swift" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
            </div>

            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Ваше сообщение:</label>
                <textarea name="user_message" rows="4" placeholder="Укажите дату и время..." style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;"></textarea>
            </div>

            <button type="submit" name="boat_submit" style="width: 100%; background-color: #0073aa; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                Забронировать на сайте
            </button>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('boat_form', 'boat_rental_form_handler');