<?php
http_response_code(404);

// Include header
// include_once __DIR__ . '/header.php';

ob_start();
include_once __DIR__ . '/db404.php';
ob_end_clean();

echo <<<HTML
	<div class="card-header aligncenter" style="font-weight:bold;">
		<h3>404 Error.</h3>
	</div>
	<div class="card-body">
		<div class="wrapper">
			<div class="header">
				<p>The page you requested was not found.</p>
			</div>
		</div>
	</div>
HTML;

// Include footer
// include_once __DIR__ . '/footer.php';
