<div class="wrap wordfence">
	<div class="wf-container-fluid">
		<?php $pageTitle = "Wordfence Firewall"; $options = array(array('t' => 'Web Application Firewall', 'a' => 'waf'), array('t' => 'Country Blocking', 'a' => 'countryblocking'), array('t' => 'Blocked IPs', 'a' => 'blockedips'), array('t' => 'Advanced Blocking', 'a' => 'advancedblocking'), array('t' => 'Brute Force Protection', 'a' => 'bruteforce'), array('t' => 'Rate Limiting', 'a' => 'ratelimiting')); $wantsLiveActivity = true; include('pageTitle.php'); ?>
		<div class="wf-row">
			<?php
			$rightRail = new wfView('marketing/rightrail');
			echo $rightRail;
			?>
			<div class="<?php echo wfStyle::contentClasses(); ?>">
				<div id="waf" class="wordfenceTopTab" data-title="Wordfence Web Application Firewall">
					<?php
					$helpLink = "http://docs.wordfence.com/en/WAF";
					$helpLabel = "Learn more about the Wordfence Web Application Firewall";
					require('menu_firewall_waf.php');
					?>
				</div> <!-- end waf block -->
				<div id="countryblocking" class="wordfenceTopTab" data-title="Block Selected Countries from Accessing your Site">
					<?php
					$helpLink = "http://docs.wordfence.com/en/Country_blocking";
					$helpLabel = "Learn more about Country Blocking";
					require('menu_firewall_countryBlocking.php');
					?>
				</div> <!-- end countryblocking block -->
				<div id="blockedips" class="wordfenceTopTab" data-title="Wordfence Blocked IPs">
					<?php
					$helpLink = "http://docs.wordfence.com/en/Blocked_IPs";
					$helpLabel = "Learn more about Blocked IPs";
					require('menu_firewall_blockedIPs.php'); 
					?>
				</div> <!-- end blockedips block -->
				<div id="advancedblocking" class="wordfenceTopTab" data-title="Advanced Blocking">
					<?php
					$helpLink = "http://docs.wordfence.com/en/Advanced_Blocking";
					$helpLabel = "Learn more about Advanced Blocking";
					require('menu_firewall_advancedBlocking.php');
					?>
				</div> <!-- end advancedblocking block -->
				<div id="bruteforce" class="wordfenceTopTab" data-title="Brute Force Login Security Options">
					<?php
					$helpLink = "http://docs.wordfence.com/en/Wordfence_options#Login_Security_Options";
					$helpLabel = "Learn more about Brute Force Login Security Options";
					require('menu_firewall_bruteForce.php');
					?>
				</div> <!-- end bruteforce block -->
				<div id="ratelimiting" class="wordfenceTopTab" data-title="Rate Limiting Options">
					<?php
					$helpLink = "http://docs.wordfence.com/en/Wordfence_options#Rate_Limiting_Rules";
					$helpLabel = "Learn more about Rate Limiting Options";
					require('menu_firewall_rateLimiting.php');
					?>
				</div> <!-- end ratelimiting block -->
			</div> <!-- end content block -->
		</div> <!-- end row -->
	</div> <!-- end container -->
</div>
