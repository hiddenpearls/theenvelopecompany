<?php

return array(
        'logo' => array(
                'dir' => '/images/logo',
                'ext' => '.png'
        ),
        'favico' => array(
                'dir' => '/images/favico.ico'
        ),
        'option_saved_text' => 'Options successfully saved',
        'tabs' => array(
                array(
                        'title' => 'General Options',
                        'icon' => 1,
                        'boxes' => array(
                                'Logo Customization' => array(
                                        'icon' => 'customization',
                                        'size' => '2_3',
                                        'columns' => true,
                                        'description' => 'Here you upload a image as logo or you can write it as text and select the logo color, size, font.',
                                        'input_fields' => array(
                                                'Logo As Image' => array(
                                                        'size' => 'half',
                                                        'id' => 'logo_image',
                                                        'type' => 'image_upload',
                                                        'note' => 'Here you can insert your link to a image logo or upload a new logo image.'
                                                ),
                                                'Logo As Text' => array(
                                                        'size' => 'half_last',
                                                        'id' => 'logo_text',
                                                        'type' => 'text',
                                                        'note' => "Type the logo text here, then select a color, set a size and font",
                                                        'color_changer' => true,
                                                        'font_changer' => true,
                                                        'font_size_changer' => array(8, 80, 'px'),
                                                        'font_preview' => array(true, true)
                                                )
                                        )
                                ),
                                'Favicon' => array(
                                        'icon' => 'customization',
                                        'size' => '1_3_last',
                                        'input_fields' => array(
                                                array(
                                                        'id' => 'favicon',
                                                        'type' => 'image_upload',
                                                        'note' => 'Here you can upload the favicon icon.'
                                                )
                                        )
                                ),
                                'Custom CSS' => array(
                                        'icon' => 'css',
                                        'size' => '1_3_last',
                                        'description' => 'Here you can write your personal CSS for customizing the classes you choose to modify.',
                                        'input_fields' => array(
                                                array(
                                                        'id' => 'custom_css',
                                                        'type' => 'textarea'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Site Color',
                        'icon'=>4,
                        'boxes' => array(
                                'Site Color Customization'=>array(
                                        'icon'=>'background',
                                        'columns'=>true,
                                        'size'=>3,
                                        'input_fields' => array(
                                                'Background Color'=>array(
                                                        'size'=>'1',
                                                        'id'=>'bg_color',
                                                        'type'=>'colorpicker',
                                                        'note'=>'Choose color for your website\'s background. <br>To return to default color , open colorpicker and click the Clear button.'
                                                ),
                                                'Primary Site Color'=>array(
                                                        'size'=>'3',
                                                        'id'=>'site_color',
                                                        'type'=>'colorpicker',
                                                        'note'=>'Choose primary color (Yellow) for your website. This will affect only specific elements.<br>To return to default color , open colorpicker and click the Clear button.'
                                                ),
                                                'Secondary Site Color'=>array(
                                                        'size'=>'3',
                                                        'id'=>'site_color_2',
                                                        'type'=>'colorpicker',
                                                        'note'=>'Choose secondary (green) color for your website. This will affect only specific elements.<br>To return to default color , open colorpicker and click the Clear button.'
                                                ),
                                                'Tertiary Site Color'=>array(
                                                        'size'=>'3_last',
                                                        'id'=>'site_color_3',
                                                        'type'=>'colorpicker',
                                                        'note'=>'Choose tertiary (brown) color for your website. This will affect only specific elements.<br>To return to default color , open colorpicker and click the Clear button.'
                                                ),
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Socials',
                        'icon' => 2,
                        'boxes' => array(
                                'Social Platforms' => array(
                                        'icon' => 'social',
                                        'description' => "Insert the link to the social share page.",
                                        'size' => 'half',
                                        'columns' => true,
                                        'input_fields' => array(
                                                array(
                                                        'id' => 'social_platforms',
                                                        'size' => 'half',
                                                        'type' => 'social_platforms',
                                                        'platforms' => array('facebook', 'twitter', 'google', 'pinterest', 'instagram', 'linkedin', 'dribbble', 'behance', 'youtube', 'flickr', 'rss')
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Additional Options',
                        'icon' => 6,
                        'boxes' => array(
                                'Custom messages' => array(
                                        'icon' => 'customization',
                                        'description' => "Social links used to build icons in template that will lead to your social accounts.",
                                        'size' => '2_3',
                                        'columns' => true,
                                        'input_fields' => array(
                                                'Footer copyright' => array(
                                                        'id' => 'copyright_message',
                                                        'type' => 'textarea',
                                                        'note' => 'This message will appear on footer. To use links insert this HTML tag[<a href="your_copyright_link">Copyright name</a>]',
                                                        'size' => 'half'
                                                )
                                        )
                                ),
                                'Page Settings'=>array(
                                        'icon' => 'customization',
                                        'description'=>"Other settings",
                                        'size'=>'1_3_last',
                                        'columns'=>false,
                                        'input_fields' =>array(
                                                'Shop' => array(
                                                        'id'    => 'show_best_sellers',
                                                        'label' => 'Show best sellers.',
                                                        'type'  => 'checkbox'
                                                ),
                                                array(
                                                        'id'    => 'show_recent',
                                                        'label' => 'Show recent products.',
                                                        'type'  => 'checkbox'
                                                ),
                                                array(
                                                        'id'    => 'show_top_rated',
                                                        'label' => 'Show considered products.',
                                                        'type'  => 'checkbox'
                                                ),
                                                
                                        )
                                )
                                
                        )
                ),
                array(
                        'title' => 'Contact Info',
                        'icon' => 5,
                        'boxes' => array(
                                'Contact info' => array(
                                        'icon' => 'customization',
                                        'description' => "Provide contact information. This information will appear in contact template. For reference read the documentation.",
                                        'size' => '2_3',
                                        'columns' => true,
                                        'input_fields' => array(
                                                'Google Map' => array(
                                                        'id' => 'contact_map',
                                                        'type' => 'map',
                                                        'note' => 'Just navigate to the location you want to be displayed on the google map and if you want a pin over your location , 
                                                                    press the "Drop marker here" button. You can also choose another icon for it.',
                                                        'size' => 'half',
                                                        'icons' => array('google-marker.gif', 'home.png', 'home_1.png', 'home_2.png', 'administration.png', 'office-building.png')
                                                ),
                                                'Contact form' => array(
                                                        'id' => 'contact_form',
                                                        'type' => 'checkbox',
                                                        'label' => 'To use Contact Form , this checkbox must be checked',
                                                        'size' => 'half_last',
                                                        'action' => array('show', array( 'contact_address', 'contact_phone', 'contact_fax'))
                                                ),
                                                array(
                                                        'id' => 'contact_title',
                                                        'type' => 'text',
                                                        'note' => 'Contact form title',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'DROP US A LINE'
                                                ),
                                                array(
                                                        'id' => 'contact_email',
                                                        'type' => 'text',
                                                        'note' => 'Provide an email, used to recive messages from Contact Form (will be displayed) ',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'Contact Form Email'
                                                ),
                                                'Contact address' => array(
                                                        'id' => 'contact_address',
                                                        'type' => 'textarea',
                                                        'note' => 'Provide your address',
                                                        'size' => 'half'
                                                ),
                                                'Contact phones' => array(
                                                        'id' => 'contact_phone',
                                                        'type' => 'text',
                                                        'note' => 'Provide your phone number',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'Phone number'
                                                ),
                                                array(
                                                        'id' => 'contact_fax',
                                                        'type' => 'text',
                                                        'note' => 'Provide your fax number',
                                                        'size' => 'half',
                                                        'placeholder' => 'Fax number'
                                                ),
                                                array(
                                                        'id' => 'address_title',
                                                        'type' => 'text',
                                                        'note' => 'Address headline',
                                                        'size' => 'half_last',
                                                        'placeholder' => 'CONTACT INFO'
                                                ),
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Subscription',
                        'icon' => 5,
                        'boxes' => array(
                                'Subscribers' => array(
                                        'icon' => 'social',
                                        'description' => 'All the subscribers are listed below:',
                                        'size' => 'full',
                                        'input_fields' => array(
                                                array(
                                                        'type' => 'subscription',
                                                        'id' => 'subscription_list'
                                                )
                                        )
                                )
                        )
                ),
                array(
                        'title' => 'Our Themes',
                        'icon'  => 8,
                        'type'=>'iframe',
                        'link'=>'http://teslathemes.com/our-themes/'
                ),
        ),
        'styles' => array(array('wp-color-picker'), 'style', 'select2')
        ,
        'scripts' => array(array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'wp-color-picker'), 'select2.min', 'jquery.cookie', 'tt_options', 'admin_js')
);