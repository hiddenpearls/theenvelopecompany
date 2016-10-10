<?php

class Tesla_Contact {

    public static function checkNsend($submit_field, $to_email, array $form_fields) {
        if (!empty($_POST[$submit_field]) && !empty($to_email)) {
            return self::send($to_email, $form_fields);
        }
    }

    public static function send($to_email, array $form_fields) {
        $subject = __('Comment form message *', 'hudson');
        $message = __('New message through comment form:', 'hudson') . "\n\n";
        foreach ($form_fields as $_form_field) {
            if (isset($_POST[$_form_field]))
                $message.=ucfirst(strtolower($_form_field)) . ': ' . $_POST[$_form_field] . "\n\n";
        }
        if (wp_mail($to_email, $subject, $message))
            return TRUE;
        return FALSE;
    }

}