<style type="text/css">
	.pb_backupbuddy_refresh_stats {
		cursor: pointer;
	}
</style>
<script>
jQuery(document).ready(function() {
	function bb_isNumber( n ) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	};
	
	jQuery('.pb_backupbuddy_testErrorLog').click(function(e) {
		jQuery( '.pb_backupbuddy_loading' ).show();
		jQuery.post( jQuery(this).attr( 'rel' ), { function: 'testErrorLog' }, 
			function(data) {
				jQuery( '.pb_backupbuddy_loading' ).hide();
				alert( data );
			}
		);
		return false;
	});
	
	jQuery( '.pb_backupbuddy_testPHPRuntime' ).click( function(e){
		loading = jQuery(this).children( '.pb_backupbuddy_loading' );
		serializedForm = jQuery(this).closest( 'form' ).serialize();
		
		testPHPRuntimeInterval = setInterval( function(){
			loading.show();
			jQuery.post(
				'<?php echo pb_backupbuddy::ajax_url( 'run_php_runtime_test_results' ); ?>',
				serializedForm, 
				function(data) {
					loading.hide();
					if ( bb_isNumber( data ) ) { // Finished
						result_obj.html( data + ' <?php _e( "secs", "it-l10n-backupbuddy" ); ?>' );
						clearInterval( testPHPRuntimeInterval );
					} else { // In progress.
						result_obj.html( data );
					}
				}
			);
		}, 5000 );
	});
	
	jQuery( '.pb_backupbuddy_testPHPMemory' ).click( function(e){
		loading = jQuery(this).children( '.pb_backupbuddy_loading' );
		serializedForm = jQuery(this).closest( 'form' ).serialize();
		
		testPHPMemoryInterval = setInterval( function(){
			loading.show();
			jQuery.post(
				'<?php echo pb_backupbuddy::ajax_url( 'run_php_memory_test_results' ); ?>',
				serializedForm, 
				function(data) {
					loading.hide();
					if ( bb_isNumber( data ) ) { // Finished
						result_obj.html( data + ' <?php _e( "MB", "it-l10n-backupbuddy" ); ?>' );
						clearInterval( testPHPMemoryInterval );
					} else { // In progress.
						result_obj.html( data );
					}
				}
			);
		}, 5000 );
	});
	
	jQuery('.pb_backupbuddy_refresh_stats').click(function(e) {
		loading = jQuery(this).children( '.pb_backupbuddy_loading' );
		loading.show();
		
		result_obj = jQuery( '#pb_stats_' + jQuery(this).attr( 'rel' ) );
		
		jQuery.post( jQuery(this).attr( 'alt' ), jQuery(this).closest( 'form' ).serialize(), 
			function(data) {
				//alert(data);
				loading.hide();
				result_obj.html( data );
			}
		); //,"json");
		
		return false;
	});
});
</script>
<?php


include( '_server_tests.php' );
	
	
	
?>



<table class="widefat">
	<thead>
		<tr class="thead">
			<th style="width: 15px;">&nbsp;</th>
			<?php
				echo '<th>', __('Server Configuration', 'it-l10n-backupbuddy' ), '</th>',
					 '<th>', __('Suggestion', 'it-l10n-backupbuddy' ), '</th>',
					 '<th>', __('Value', 'it-l10n-backupbuddy' ), '</th>',
					 //'<th>', __('Result', 'it-l10n-backupbuddy' ), '</th>',
					 '<th style="width: 60px;">', __('Status', 'it-l10n-backupbuddy' ), '</th>';
			?>
		</tr>
	</thead>
	<tfoot>
		<tr class="thead">
			<th style="width: 15px;">&nbsp;</th>
			<?php
				echo '<th>', __('Server Configuration', 'it-l10n-backupbuddy' ), '</th>',
					 '<th>', __('Suggestion', 'it-l10n-backupbuddy' ), '</th>',
					 '<th>', __('Value', 'it-l10n-backupbuddy' ), '</th>',
					 //'<th>', __('Result', 'it-l10n-backupbuddy' ), '</th>',
					 '<th style="width: 15px;">', __('Status', 'it-l10n-backupbuddy' ), '</th>';
			?>
		</tr>
	</tfoot>
	<tbody>
		<?php
		foreach( $tests as $parent_class_test ) {
			echo '<tr class="entry-row alternate">';
			echo '	<td>' . pb_backupbuddy::tip( $parent_class_test['tip'], '', false ) . '</td>';
			echo '	<td>' . $parent_class_test['title'] . '</td>';
			echo '	<td>' . $parent_class_test['suggestion'] . '</td>';
			echo '	<td>' . $parent_class_test['value'] . '</td>';
			//echo '	<td>' . $parent_class_test['status'] . '</td>';
			echo '	<td>';
			if ( $parent_class_test['status'] == __('OK', 'it-l10n-backupbuddy' ) ) {
				echo '<span class="pb_label pb_label-success">Pass</span>';
				//echo '<div style="background-color: #22EE5B; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';
			} elseif ( $parent_class_test['status'] == __('FAIL', 'it-l10n-backupbuddy' ) ) {
				echo '<span class="pb_label pb_label-important">Fail</span>';
				//echo '<div style="background-color: #CF3333; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';
			} elseif ( $parent_class_test['status'] == __('WARNING', 'it-l10n-backupbuddy' ) ) {
				echo '<span class="pb_label pb_label-warning">Warning</span>';
				//echo '<div style="background-color: #FEFF7F; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';
			}
			echo '	</td>';
			echo '</tr>';
		}
		?>
	</tbody>
</table>
<?php
if ( isset( $_GET['phpinfo'] ) && $_GET['phpinfo'] == 'true' ) {
	if ( defined( 'PB_DEMO_MODE' ) ) {
		pb_backupbuddy::alert( 'Access denied in demo mode.', true );
	} else {
		echo '<br><h3>phpinfo() ', __('Response', 'it-l10n-backupbuddy' ), ':</h3>';
		
		echo '<div style="width: 100%; height: 600px; padding-top: 10px; padding-bottom: 10px; overflow: scroll; ">';
		ob_start();
		
		phpinfo();
		
		$info = ob_get_contents();
		ob_end_clean();
		$info = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $info);
		echo $info;
		unset( $info );
		
		echo '</div>';
	}
} else {
	echo '<br>';
	echo '<center>';
	
	if ( !defined( 'PB_IMPORTBUDDY' ) ) {
		echo '<a href="#TB_inline?width=640&#038;height=600&#038;inlineId=pb_serverinfotext_modal" class="button button-secondary button-tertiary thickbox" title="Server Information Results">Display Server Configuration in Text Format</a> &nbsp;&nbsp;&nbsp; ';
		echo '<a href="' . pb_backupbuddy::ajax_url( 'phpinfo' ) . '&#038;TB_iframe=1&#038;width=640&#038;height=600" class="thickbox button secondary-button" title="' . __('Display Extended PHP Settings via phpinfo()', 'it-l10n-backupbuddy' ) . '">' . __('Display Extended PHP Settings via phpinfo()', 'it-l10n-backupbuddy' ) . '</a>';
	} else {
		echo '<a id="serverinfotext" class="button button-secondary button-tertiary button-primary thickbox toggle" title="Server Information Results">Display Results in Text Format</a> &nbsp;&nbsp;&nbsp; ';
	}
	echo '</center>';
	
	/*
	echo '<pre>';
	print_r( ini_get_all() );
	echo '</pre>';
	*/
}
?><br>



<div
<?php
if ( !defined( 'PB_IMPORTBUDDY' ) ) {
	echo 'id="pb_serverinfotext_modal"';
} else {
	echo 'id="toggle-serverinfotext"';
}
?> style="display: none;">
		<?php
		if ( !defined( 'PB_IMPORTBUDDY' ) ) {
			echo '<h3>' . __( 'Server Information Results', 'it-l10n-backupbuddy' ) . '</h3>';
			echo '<textarea style="width: 100%; height: 300px;" wrap="off">';
		} else {
			echo '<textarea style="width: 95%; height: 300px;" wrap="off">';
		}
		foreach( $tests as $test ) {
			echo '[' . $test['status'] . ']     ' . $test['title'] . '   =   ' . strip_tags( $test['value'] ) . "\n"; 
		}
		?></textarea>
</div>
