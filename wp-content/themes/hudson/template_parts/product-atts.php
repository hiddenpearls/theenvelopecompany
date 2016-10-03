<?php
global $woocommerce;
$alt = 1;
$attributes = $product->get_attributes();
foreach ($attributes as $attribute) :

    if (empty($attribute['is_visible']) || ( $attribute['is_taxonomy'] && !taxonomy_exists($attribute['name']) ))
        continue;
    ?>

    <tr class="<?php if (( $alt = $alt * -1 ) == 1) echo 'alt'; ?>">
        <th><?php echo $woocommerce->attribute_label($attribute['name']); ?></th>
        <td><?php
    if ($attribute['is_taxonomy']) {

        $values = woocommerce_get_product_terms($product->id, $attribute['name'], 'names');
        echo apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values);
    } else {

        // Convert pipes to commas and display values
        $values = array_map('trim', explode('|', $attribute['value']));
        echo apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values);
    }
    ?></td>
    </tr>

<?php endforeach; ?>