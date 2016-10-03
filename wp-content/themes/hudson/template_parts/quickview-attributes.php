<?php
global $woocommerce;
$attributes = $product->get_attributes();
?>
<ul class="quick_view_attributes">
    <?php
    foreach ($attributes as $attribute) :

        if (empty($attribute['is_visible']) || ( $attribute['is_taxonomy'] && !taxonomy_exists($attribute['name']) ))
            continue;
        ?>

        <li>
            <span class="label label-info"><?php echo wc_attribute_label($attribute['name']); ?></span>
            <span class="attributes_all">
                <?php
                if ($attribute['is_taxonomy']) {

                    $values = woocommerce_get_product_terms($product->id, $attribute['name'], 'names');
                    echo apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values);
                } else {

                    // Convert pipes to commas and display values
                    $values = array_map('trim', explode('|', $attribute['value']));
                    echo apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values);
                }
                ?>
            </span>
        </li>

        <?php
    endforeach;
    ?>
</ul>