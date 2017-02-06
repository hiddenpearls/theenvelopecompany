<?php //$d is defined here as a wfDashboard instance ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong>Top IPs Blocked</strong>
					</div>
					<div class="wf-dashboard-item-action"><div class="wf-dashboard-item-action-disclosure"></div></div>
				</div>
			</div>
			<div class="wf-dashboard-item-extra">
				<ul class="wf-dashboard-item-list">
					<li>
						<div>
							<div class="wf-dashboard-toggle-btns">
								<ul class="wf-pagination wf-pagination-sm">
									<li class="wf-active"><a href="#" class="wf-dashboard-ips" data-grouping="24h">24 Hours</a></li>
									<li><a href="#" class="wf-dashboard-ips" data-grouping="7d">7 Days</a></li>
									<li><a href="#" class="wf-dashboard-ips" data-grouping="30d">30 Days</a></li>
								</ul>
							</div>
							<div class="wf-ips wf-ips-24h">
								<?php if (count($d->ips24h) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->ips24h, 0, min(10, count($d->ips24h)), true); include(dirname(__FILE__) . '/widget_content_ips.php'); ?>
								<?php endif; ?>
							</div>
							<div class="wf-ips wf-ips-7d wf-hidden">
								<?php if (count($d->ips7d) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->ips7d, 0, min(10, count($d->ips7d)), true); include(dirname(__FILE__) . '/widget_content_ips.php'); ?>
								<?php endif; ?>
							</div>
							<div class="wf-ips wf-ips-30d wf-hidden">
								<?php if (count($d->ips30d) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No blocks have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->ips30d, 0, min(10, count($d->ips30d)), true); include(dirname(__FILE__) . '/widget_content_ips.php'); ?>
								<?php endif; ?>
							</div>
							<script type="application/javascript">
								(function($) {
									$('.wf-dashboard-ips').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();

										$(this).closest('ul').find('li').removeClass('wf-active');
										$(this).closest('li').addClass('wf-active');

										$('.wf-ips').addClass('wf-hidden');
										$('.wf-ips-' + $(this).data('grouping')).removeClass('wf-hidden');
									});
								})(jQuery);
							</script>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>