<?php

class Tesla_Shortcodes {

    public function __construct() {
        $this->__init_shortcodes();
    }

    protected function __init_shortcodes() {
        add_shortcode('tesla_social_icons', array($this, 'tesla_social_icons'));
        add_shortcode('tt_rubric', array($this, 'tt_rubric'));
        add_shortcode('tt_row', array($this, 'tt_row'));
        add_shortcode('tt_column', array($this, 'tt_column'));
        add_shortcode('tt_alert', array($this, 'tt_alert'));
        add_shortcode('tt_tabs', array($this, 'tt_tabs'));
        add_shortcode('tt_tabs_nav', array($this, 'tt_tabs_nav'));
        add_shortcode('tt_tabs_nav_li', array($this, 'tt_tabs_nav_li'));
        add_shortcode('tt_tabs_content', array($this, 'tt_tabs_content'));
        add_shortcode('tt_tabs_content_pane', array($this, 'tt_tabs_content_pane'));
        add_shortcode('tt_pricing_table', array($this, 'tt_pricing_table'));
    }

    public function tesla_social_icons($atts) {
        $atts = shortcode_atts(array(
            'services' => 'facebook,twitter,pinterest,instagram,flickr,dribbble,behance,google,linkedin,youtube,rss'
                ), $atts);
        $atts['services'] = array_map('strtolower', array_map('trim', explode(',', $atts['services'])));
        $get_view = Tesla_View::render('template_parts/socials-footer.php', $atts);
        return $get_view;
    }

    public function tt_rubric($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => ''
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_rubric.php', $data);
        return $get_view;
    }

    public function tt_row($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => ''
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_row.php', $data);
        return $get_view;
    }

    public function tt_column($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
            'columns' => 12
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_column.php', $data);
        return $get_view;
    }

    public function tt_alert($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
            'type' => 'info',
            'has_close' => 'true'
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_alert.php', $data);
        return $get_view;
    }

    public function tt_tabs($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => ''
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_tabs.php', $data);
        return $get_view;
    }

    public function tt_tabs_nav($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
            'id' => ''
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_tabs_nav.php', $data);
        return $get_view;
    }

    public function tt_tabs_nav_li($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
            'link_id' => ''
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_tabs_nav_li.php', $data);
        return $get_view;
    }

    public function tt_tabs_content($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_tabs_content.php', $data);
        return $get_view;
    }

    public function tt_tabs_content_pane($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
            'id' => ''
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_tabs_content_pane.php', $data);
        return $get_view;
    }

    public function tt_pricing_table($atts, $content, $tag) {
        $atts = shortcode_atts(array(
            'class' => '',
            'heading' => '',
            'currency' => '$',
            'price' => '',
            'url' => '#',
            'type' => '',
            'buy_text' => 'buy it now'
                ), $atts);
        $data = array('atts' => &$atts, 'content' => do_shortcode($content), 'tag' => &$tag);
        $get_view = Tesla_View::render('template_parts/tesla_shortcode-tt_pricing_table.php', $data);
        return $get_view;
    }

}

$tesla_class_registry['Tesla_Shortcodes'] = new Tesla_Shortcodes;