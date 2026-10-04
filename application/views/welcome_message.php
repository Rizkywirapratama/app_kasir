<?php
defined('BASEPATH') OR exit('No direct script access allowed');
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
</head>
<body class="bg-slate-50 text-slate-700 font-sans">

<div class="max-w-4xl mx-auto my-10 bg-white/5 border border-neutral-700 shadow-lg rounded-lg overflow-hidden">
	<header class="px-6 py-4 border-b border-neutral-800">
		<h1 class="text-2xl text-slate-900 font-medium">Welcome to CodeIgniter!</h1>
	</header>

	<main class="p-6 space-y-4">
		<p>The page you are looking at is being generated dynamically by CodeIgniter.</p>

		<div>
			<p>If you would like to edit this page you'll find it located at:</p>
			<pre class="bg-white text-slate-900 rounded p-3 mt-2 overflow-x-auto border"><code class="text-sm">application/views/welcome_message.php</code></pre>
		</div>

		<div>
			<p>The corresponding controller for this page is found at:</p>
			<pre class="bg-white text-slate-900 rounded p-3 mt-2 overflow-x-auto border"><code class="text-sm">application/controllers/Welcome.php</code></pre>
		</div>

		<p>If you are exploring CodeIgniter for the very first time, you should start by reading the <a href="userguide3/" class="text-blue-400 hover:text-blue-300">User Guide</a>.</p>
	</main>

	<footer class="px-6 py-3 border-t border-neutral-800 text-right text-sm text-neutral-400">
		Page rendered in <strong class="text-neutral-200">{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
	</footer>
</div>

</body>
</html>
