<?php //$d is defined here as a wfDashboard instance ?>
<div class="wf-row">
	<div class="wf-col-xs-12">
		<div class="wf-dashboard-item active">
			<div class="wf-dashboard-item-inner">
				<div class="wf-dashboard-item-content">
					<div class="wf-dashboard-item-title">
						<strong>Login Attempts</strong>
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
									<li class="wf-active"><a href="#" class="wf-dashboard-login-attempts" data-grouping="success">Successful</a></li>
									<li><a href="#" class="wf-dashboard-login-attempts" data-grouping="fail">Failed</a></li>
								</ul>
							</div>
							<div class="wf-recent-logins wf-recent-logins-success">
								<?php if (count($d->loginsSuccess) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No successful logins have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->loginsSuccess, 0, min(10, count($d->loginsSuccess)), true); include(dirname(__FILE__) . '/widget_content_logins.php'); ?>
								<?php endif; ?>
							</div>
							<div class="wf-recent-logins wf-recent-logins-fail wf-hidden">
								<?php if (count($d->loginsFail) == 0): ?>
									<div class="wf-dashboard-item-list-text"><p><em>No failed logins have been recorded.</em></p></div>
								<?php else: ?>
									<?php $data = array_slice($d->loginsFail, 0, min(10, count($d->loginsFail)), true); include(dirname(__FILE__) . '/widget_content_logins.php'); ?>
								<?php endif; ?>
							</div>
							<script type="application/javascript">
								(function($) {
									$('.wf-dashboard-login-attempts').on('click', function(e) {
										e.preventDefault();
										e.stopPropagation();
										
										$(this).closest('ul').find('li').removeClass('wf-active');
										$(this).closest('li').addClass('wf-active'); 
										
										$('.wf-recent-logins').addClass('wf-hidden');
										$('.wf-recent-logins-' + $(this).data('grouping')).removeClass('wf-hidden');
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