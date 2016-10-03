<li class="item ads">
    <ul class="clearfix row-fluid">
        <?php foreach ($instance as $key => $val): ?>
            <?php if (!empty($val)): ?>
                <li class="ad span6">
                    <?php echo $val; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</li>