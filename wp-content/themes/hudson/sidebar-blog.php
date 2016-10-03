<?php
// If we get this far, we have widgets. Let do this.
?>

<div class="sidebar">
    <?php
    if (is_active_sidebar('sidebar-1')) {
        dynamic_sidebar('sidebar-1');
    }
    ?>
</div>