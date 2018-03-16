<?php
/*
Plugin Name: YAWIK Gastro24
Description: YAWIK bridge
Author: Carsten Bleek <bleek@cross-solution.de>
*/


// Loading text domain
add_action('plugins_loaded', 'yawik_gastro24_load_plugin_textdomain');

add_shortcode("rpt", "gastro24_rpt_sc");

function gastro24_rpt_sc($atts)
{

    global $post;

    /* Gets table slug (post name). */
    $all_attr = shortcode_atts(array("name" => ''), $atts);
    $name     = $all_attr['name'];

    /* Returned variable. */
    $table_view = '';

    /* Gets the pricing table. */
    $args         = array('post_type' => 'rpt_pricing_table', 'name' => $name);
    $custom_posts = get_posts($args);

    foreach ($custom_posts as $post) : setup_postdata($post);

        /* Gets the plans. */
        $plans = get_post_meta($post->ID, '_rpt_plan_group', true);

        /* Counts the plans. */
        $nb_plans = count($plans);

        /* Gets 'force font' setting. */
        $original_font = get_post_meta($post->ID, '_rpt_original_font', true);
        if ($original_font == 'no' || empty($original_font)) {
            $ori_f = '';
        } else {
            $ori_f = 'rpt_plan_ori';
        }

        /* Gets title align settings. */
        $title_align = get_post_meta($post->ID, '_rpt_title_alignment', true);
        if ($title_align == 'center') {
            $title_align_style = 'center';
        } else {
            $title_align_style = 'left';
        }

        /* Gets font size settings. */
        $title_fontsize = get_post_meta($post->ID, '_rpt_title_fontsize', true);
        if ($title_fontsize == 'small') {
            $title_fs_class = ' rpt_sm_title';
        } else if ($title_fontsize == 'tiny') {
            $title_fs_class = ' rpt_xsm_title';
        } else {
            $title_fs_class = '';
        }

        $subtitle_fontsize = get_post_meta($post->ID, '_rpt_subtitle_fontsize', true);
        if ($subtitle_fontsize == 'small') {
            $subtitle_fs_class = ' rpt_sm_subtitle';
        } else if ($subtitle_fontsize == 'tiny') {
            $subtitle_fs_class = ' rpt_xsm_subtitle';
        } else {
            $subtitle_fs_class = '';
        }

        $description_fontsize = get_post_meta($post->ID, '_rpt_description_fontsize', true);
        if ($description_fontsize == 'small') {
            $description_fs_class = ' rpt_sm_description';
        } else {
            $description_fs_class = '';
        }

        $price_fontsize = get_post_meta($post->ID, '_rpt_price_fontsize', true);
        if ($price_fontsize == 'small') {
            $price_fs_class = ' rpt_sm_price';
        } else if ($price_fontsize == 'tiny') {
            $price_fs_class = ' rpt_xsm_price';
        } else if ($price_fontsize == 'supertiny') {
            $price_fs_class = ' rpt_xxsm_price';
        } else {
            $price_fs_class = '';
        }

        $recurrence_fontsize = get_post_meta($post->ID, '_rpt_recurrence_fontsize', true);
        if ($recurrence_fontsize == 'small') {
            $recurrence_fs_class = ' rpt_sm_recurrence';
        } else {
            $recurrence_fs_class = '';
        }

        $features_fontsize = get_post_meta($post->ID, '_rpt_features_fontsize', true);
        if ($features_fontsize == 'small') {
            $features_fs_class = ' rpt_sm_features';
        } else {
            $features_fs_class = '';
        }

        $button_fontsize = get_post_meta($post->ID, '_rpt_button_fontsize', true);
        if ($button_fontsize == 'small') {
            $button_fs_class = ' rpt_sm_button';
        } else {
            $button_fs_class = '';
        }

        /* START pricing table. */
        $table_view .= '<div id="rpt_pricr" class="rpt_plans rpt_' . $nb_plans . '_plans rpt_style_basic">';

        /* START inner. */
        $table_view .= '<div class="row row-eq-height ' . $title_fs_class . $subtitle_fs_class . $description_fs_class . $price_fs_class
                       . $recurrence_fs_class . $features_fs_class . $button_fs_class . '">';

        if (is_array($plans) || is_object($plans)) {

            /* For each plan. */
            foreach ($plans as $key => $plan) {

                /* If recommended plan. */
                if (!empty($plan['_rpt_recommended'])) {
                    $is_reco = $plan['_rpt_recommended'];
                    if ($is_reco == 'no' || empty($is_reco)) {
                        $reco       = '';
                        $reco_class = '';
                    } else {
                        $reco       =
                            '<img class="rpt_recommended" src="' . plugins_url('img/rpt_recommended.png', __FILE__)
                            . '"/>';
                        $reco_class = 'rpt_recommended_plan';
                    }
                    /* If NOT recommended plan. */
                } else {
                    $reco       = '';
                    $reco_class = '';
                }

                if (empty($plan['_rpt_custom_classes'])) {
                    $plan['_rpt_custom_classes'] = ''; 
                }

                /* START plan. */
                $table_view .= '<div class="col-md-3">';
                $table_view .= '<div class="eq_height panel panel-primary rpt_plan  ' . $ori_f . ' rpt_plan_' . $key . ' ' . $reco_class . ' '
                               . $plan['_rpt_custom_classes'] . '">';

                /* Title. */
                $title_style = 'style="text-align:' . $title_align_style . ';"';

                if (!empty($plan['_rpt_title'])) {
                    $table_view .= '<div ' . $title_style . ' class="panel-heading rpt_title_' . $key . '">';

                    if (!empty($plan['_rpt_icon'])) {
                        $table_view .= '<img src="' . $plan['_rpt_icon'] . '" class="rpt_icon rpt_icon_' . $key
                                       . '"/> ';
                    }

                    $table_view .= $plan['_rpt_title'];
                    $table_view .= $reco . '</div>';
                }

                /* START plan head (price). */
                $table_view .= '<div class="panel-body rpt_head rpt_head_' . $key . '">';

                /* Recurrence. */
                if (!empty($plan['_rpt_recurrence'])) {
                    $table_view .= '<div class="rpt_recurrence rpt_recurrence_' . $key . '">' . $plan['_rpt_recurrence']
                                   . '</div>';
                }

                /* Price. */
                if (!empty($plan['_rpt_price']) || $plan['_rpt_price'] == 0) {

                    $table_view .= '<div class="rpt_price rpt_price_' . $key . '">';

                    if (!empty($plan['_rpt_free']) && $plan['_rpt_free'] != 'no') {
                        $table_view .= '<span class="rpt_currency"></span>' . $plan['_rpt_price'];
                    } else {

                        $currency = get_post_meta($post->ID, '_rpt_currency', true);

                        if (!empty($currency)) {
                            $table_view .= '<span class="rpt_currency">';
                            $table_view .= $currency;
                            $table_view .= '</span>';
                        }

                        $table_view .= $plan['_rpt_price'];

                    }

                    $table_view .= '</div>';

                }

                /* Subtitle. */
                if (!empty($plan['_rpt_subtitle'])) {
                    $table_view .=
                        '<div class="rpt_subtitle rpt_subtitle_' . $key
                        . '">' . $plan['_rpt_subtitle'] . '</div>';
                }

                /* Description. */
                if (!empty($plan['_rpt_description'])) {
                    $table_view .=
                        '<div class="rpt_description rpt_description_' . $key . '">' . $plan['_rpt_description']
                        . '</div>';
                }

                /* END plan head. */
                $table_view .= '</div>';

                /* START UL for panel list group. */
                $table_view .= '<ul class="list-group">';


                /* Gets button data. */
                if (!empty($plan['_rpt_btn_text'])) {
                    $btn_text = $plan['_rpt_btn_text'];
                    if (!empty($plan['_rpt_btn_link'])) {
                        $btn_link = $plan['_rpt_btn_link'];
                    } else {
                        $btn_link = '#';
                    }
                } else {
                    $btn_text = '';
                    $btn_link = '#';
                }

                /* Gets button behavior data. */
                $newcurrentwindow = get_post_meta($post->ID, '_rpt_open_newwindow', true);
                if ($newcurrentwindow == 'newwindow') {
                    $link_behavior = 'target="_blank"';
                } else {
                    $link_behavior = 'target="_self"';
                }

                /* If custom button. */
                if (!empty($plan['_rpt_btn_custom_btn'])) {

                    $table_view .= '<li class="list-group-item">';
                    $table_view .= '<div class="rpt_custom_btn">';
                    $table_view .= do_shortcode($plan['_rpt_btn_custom_btn']);
                    $table_view .= '</div>';
                    $table_view .= '</li>';

                    /* If NOT custom button. */
                } else {

                    /* START default button. */
                    
                    /* list group Item. */
                    $table_view .= '<li class="list-group-item">';
                    if (!empty($plan['_rpt_btn_text'])) {
                        $table_view .=
                            '<a ' . $link_behavior . ' href="' . do_shortcode($btn_link) . '" class="btn btn-primary rpt_foot_' . $key . '">';
                    } else {
                        $table_view .= '<a ' . $link_behavior . ' class="btn btn-primary rpt_foot_' . $key . '">';
                    }

                    $table_view .= do_shortcode($btn_text);

                    /* END default button. */
                    $table_view .= '</a>';
                    $table_view .= '</li>';

                }

                /* Features. */
                if (!empty($plan['_rpt_features'])) {

                    $table_view .= '<div class="rpt_features rpt_features_' . $key . '">'; // open

                    $features = array();

                    $string   = $plan['_rpt_features'];
                    $stringAr = explode("\n", $string);
                    $stringAr = array_filter($stringAr, 'trim');

                    foreach ($stringAr as $feature) {
                        $features[] = strip_tags($feature, '<strong></strong><br><br/></br><img><a>');
                    }

                    foreach ($features as $small_key => $feature) {
                        if (!empty($feature)) {
                            
                            /* 6th January,2017
                              $rpt_feature_text_class is used to define color class for feature text.
                              If there is -n prefix before feature text then it will indicate as disabled label. Otherwise an enabled label.
                              Class for Disabled label is 'rpt_yk_inactive' with light gray color.
                              Class for Enabled label is 'rpt_yk_active' with light black color.
                              I found only two color so I had used only two class rpt_yk_inactive & rpt_yk_active. And I have not used rpt_yk_
feature_section class.
                             */
                            /*
                              NOTE : I have replaced color name/code with class name.
                              I do not found any hook / filter for YAWIK Gastro24 so I had added color class in this file directly.
                             */

                            $check = substr($feature, 0, 2);
                            if ($check == '-n') {
                                $feature     = substr($feature, 2);
                                $rpt_feature_text_class = 'rpt_yk_inactive';
                            } else {
                                $rpt_feature_text_class = 'rpt_yk_active';
                            }

                            /* list group Item. */
                            $table_view .= '<li class="list-group-item">';
                            $table_view .= '<div class="rpt_feature rpt_feature_' . $key . '-' . $small_key . ' '. $rpt_feature_text_class . '">';
                            $table_view .= $feature;
                            $table_view .= '</div>';
                            $table_view .= '</li>';

                        }
                    }

                    $table_view .= '</div>'; // close
                }

                /* END UL for panel list group. */
                $table_view .= '</ul>';
                /* END plan. */
                $table_view .= '</div>';
                /* END col-md-3 */
                $table_view .= '</div>';

            }

        }

        /* END inner. */
        $table_view .= '</div>';

        /* END pricing table. */
        $table_view .= '</div>';

        $table_view .= '<div style="clear:both;"></div>';

    endforeach;
    wp_reset_postdata();

    return $table_view;

}

?><?php

function caf_add_api_fields() {
  register_rest_field('post', 'meta-fields', array('get_callback' => 'caf_get_post_meta_fields'));  
  register_rest_field('page', 'meta-fields', array('get_callback' => 'caf_get_post_meta_fields'));  
}

add_action('rest_api_init', 'caf_add_api_fields');

function caf_get_post_meta_fields($object)
{
  $desc = get_post_meta($object['id'], 'meta-description');
  $desc = $desc ? $desc[0] : '';
  $tags = get_post_meta($object['id'], 'meta-keywords');
  $tags = $tags ? $tags[0] : '';
  $title = get_post_meta($object['id'], 'meta-title');
  $title = $title ? $title[0] : '';
  
  
  return array(
    'title' => $title,
    'description' => $desc,
    'keywords' => $tags,
  );
}


