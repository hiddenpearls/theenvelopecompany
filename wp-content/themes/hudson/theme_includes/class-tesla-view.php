<?php

class Tesla_View {

    static public function render($view_path, $pass_data = array()) {
        if (!empty($pass_data))
            extract($pass_data);
        $valid_view = locate_template($view_path);
        if (!$valid_view) {
            trigger_error('View not found: ' . $view_path, E_USER_WARNING);
            return;
        }
        ob_start();
        include $valid_view;
        return ob_get_clean();
    }

}