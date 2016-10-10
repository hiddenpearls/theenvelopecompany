<div id="<?php echo esc_attr($input_id);?>" class="tt_map_container">
  <input
    class="map_key"
    type="text"
    placeholder="Google Maps API Key"
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_key]"
    value="<?php if ( ! empty( $options[ $input_id . "_key" ] ) ) echo esc_attr($options[ $input_id . "_key" ]) ;?>">
    <p class="tt_explain">Create an API key as per instructions here: <a href="https://developers.google.com/maps/documentation/javascript/get-api-key">https://developers.google.com/maps/documentation/javascript/get-api-key</a></p>
  <input
    class="map_search"
    type="text">
  <div
    class="tt_map map-canvas<?php if ( ! empty( $input['class' ] ) ) echo esc_attr($input['class']); ?>"
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>]"
  ></div>
  <input
    class="map-coords"
    type="hidden"
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_coords]"
    value="<?php if ( ! empty( $options[ $input_id . "_coords" ] ) ) echo esc_attr($options[ $input_id . "_coords" ]) ;?>">
  <input
    class="marker-coords"
    type="hidden"
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_marker_coords]"
    value="<?php if ( ! empty( $options[ $input_id . "_marker_coords" ] ) ) echo esc_attr($options[ $input_id . "_marker_coords" ]) ;?>">
  <input
    class="map-zoom"
    type="hidden"
    name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_zoom]"
    value="<?php if ( ! empty( $options[ $input_id . "_zoom" ] ) ) echo esc_attr($options[ $input_id . "_zoom" ]);?>">
  <?php if (!empty($input['icons']))
    foreach ($input['icons'] as $icon) : ?>
      <label class="map-icon">
        <input
          type="radio"
          name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_icon]"
          <?php $icon_safe_url = strpos($icon,'theme_config') ? TT_THEME_URI . '/' . strstr($icon,'theme_config')  : TT_FW . '/static/images/mapicons/' . $icon ?>
          value="<?php echo esc_attr($icon_safe_url) ?>"
          <?php if(!empty($options[ $input_id . '_icon']))checked( TT_FW . '/static/images/mapicons/' . $icon , $options[ $input_id . '_icon']); ?>
          ><img src="<?php echo esc_attr($icon_safe_url) ?>" alt="map icon" /></label>
    <?php endforeach;?>
  <?php if(isset($input['mapOptions'])) : ?>
    <p>Additional options in MapOptions object (JS object format) e.g. <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/styling#styling_the_default_map">Map styles</a>: </p>
    <textarea id="editor_<?php echo esc_attr($input_id);?>" class="mapoptions" name="<?php echo THEME_OPTIONS?>[<?php echo esc_attr($input_id);?>_mapOptions]"><?php 
      if ( ! empty( $options[ $input_id . "_mapOptions" ] ) ) 
        echo ( $options[ $input_id . "_mapOptions" ] );
      elseif( !empty( $input['mapOptions'] ) )
        echo ( $input['mapOptions'] );
    ?></textarea>
  <?php endif; ?>
</div>