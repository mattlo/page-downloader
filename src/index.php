<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Download Utility</title>
		<link type="text/css" href="public/css/bootstrap.min.css" rel="stylesheet" />
		<link type="text/css" href="public/css/bootstrap-theme.min.css" rel="stylesheet" />
		<link type="text/css" href="public/css/main.css" rel="stylesheet" />
	</head>
	<body>
		<div class="wrapper">
			<h1>Page Downloader</h1>

			<div class="input-form">
				<section>
					<h2>Configuration</h2>
					<p>
						This tool expects that all URLs live in the same domain. Your-mile-may-vary for multi-domain support in a single process.
					</p>
					<div class="input-group input-url-list">
						<span class="input-group-addon"><strong>URL(s)</strong><br />Delimited by line breaks</span>
						<textarea class="form-control"></textarea>
					</div>
					
					<h4>Advanced Options</h4>
					<div class="input-group input-asset-prefix">
						<span class="input-group-addon">Asset Prefix</span>
						<input type="text" class="form-control" />
					</div>
					<div class="input-group input-html-output">
						<span class="input-group-addon">Output as .HTML files</span>
						<span class="form-control">
							<input type="checkbox" value="/output" />
						</span>
					</div>
					<div class="input-group pull-right">
						<a href="" class="restart-btn">Restart</a> <button type="submit" class="btn btn-primary review-btn">Review</button>
					</div>
				</section>
				<section>
					<h2>Confirm</h2>
					<div class="input-group confirm-url-count">
						<span class="input-group-addon">Amount of URLs</span>
						<span class="form-control"></span>
					</div>
					<div class="input-group confirm-asset-prefix">
						<span class="input-group-addon">Asset Prefix</span>
						<span class="form-control"></span>
					</div>
					<div class="input-group confirm-output-html">
						<span class="input-group-addon">Output as HTML</span>
						<span class="form-control"></span>
					</div>					
					<div class="input-group pull-right">
						<a href="" class="restart-btn">Restart</a> <button type="submit" class="btn btn-primary run-btn">Run</button>
					</div>
				</section>
			</div>

			<div class="console">
				
			</div>
		</div>

		<script type="text/javascript" src="public/js/jquery-2.0.3.min.js"></script>
		<script type="text/javascript" src="public/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="public/js/main.js"></script>
	</body>
</html>