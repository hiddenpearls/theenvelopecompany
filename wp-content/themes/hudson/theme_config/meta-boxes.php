<?php

return array(
    'metaboxes' => array(
        array(
            'id' => 'partner_meta_Box_1', // meta box id, unique per meta box
            'title' => 'Partner details', // meta box title
            'post_type' => array('offer'), // post types, accept custom post types as well, default is array('post'); optional
            'priority' => 'low',
            'context' => 'side',
            'input_fields' => array(// list of meta fields 
                'offer_url' => array(
                    'name' => 'Offer Link',
                    'desc' => 'Provide a URL to where you would like this offer to link.',
                    'type' => 'text'
                ),
                'offer_type' => array(
                    'name' => 'Offer Type',
                    'type' => 'select',
                    'desc' => 'Select offer type. You will need to provide this in the shortcode to properly display it.',
                    'values' => array(
                        'offer_strip' => 'Offer Strip Bar',
                        'offer_hot' => 'Hot Offer',
                        'offer_service' => 'Service Offer',
                        'offer_about_service' => 'About us Service Offer',
                        'offer_generic' => 'Generic Offer'
                    ),
                    'std' => 'offer_generic'  //default value selected
                )
            )
        ),
        array(
            'id' => 'post_meta_Box_1', // meta box id, unique per meta box
            'title' => 'Post Options', // meta box title
            'post_type' => array('post'), // post types, accept custom post types as well, default is array('post'); optional
            'priority' => 'low',
            'context' => 'side',
            'input_fields' => array(// list of meta fields 
                'embed_code' => array(
                    'name' => 'Video Embed Code',
                    'desc' => 'Paste your video embed code.',
                    'type' => 'textarea'
                ),
                'post_head_type' => array(
                    'name' => 'Post Header Type',
                    'type' => 'select',
                    'desc' => 'Select post header type. You can choose to show a featured image, embed code (youtube, vimeo, etc), or nothing at all.',
                    'values' => array(
                        'image' => 'Featured Image',
                        'embed_code' => 'Embeded Code',
                        'none' => 'None'
                    ),
                    'std' => 'image'  //default value selected
                )
            )
        ),
        array(
            'id' => 'partner_meta_box_2', // meta box id, unique per meta box
            'title' => 'Team Member Details', // meta box title
            'post_type' => array('team'), // post types, accept custom post types as well, default is array('post'); optional
            'priority' => 'low',
            'context' => 'side',
            'input_fields' => array(// list of meta fields 
                'job_title' => array(
                    'name' => 'Job Title',
                    'desc' => 'What job position does this team member have?',
                    'type' => 'text'
                ),
                'facebook_url' => array(
                    'name' => 'Facebook profile link',
                    'desc' => 'Provide the profile link on Facebook.',
                    'type' => 'text'
                ),
                'twitter_url' => array(
                    'name' => 'Twitter profile link',
                    'desc' => 'Provide the profile link on Twitter.',
                    'type' => 'text'
                ),
            )
        )
    )
);