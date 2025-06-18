<h2>🧩 Captcha</h2>

<p>
To solve the captcha, you have to select <strong>four image parts</strong> that do <em>not match</em> the main image.
</p>

<p>
If you want to insert your logo on the loading page, replace <code>[Base64 code]</code> with the Base64 code of your <code>.png</code> image here:
</p>

<pre>
&lt;img src="data:image/png;base64,[Base64 code]" alt="Forum-Logo" class="splash-logo"&gt;
</pre>

<hr>

<h3>📁 Usage</h3>

<ul>
  <li>Place at least <strong>seven</strong> image parts (as <code>.jpg</code>) in the <code>captchas/false_parts</code> folder.</li>
  <li>Place at least <strong>seven</strong> original full images (as <code>.jpg</code>) in the <code>captchas/originals</code> folder.</li>
  <li>To activate the captcha, insert the following code at the beginning of your protected pages:</li>
</ul>

<pre>
&lt;?php
session_start();
if (!isset($_SESSION['captcha_passed']) || $_SESSION['captcha_passed'] !== true) {
    require_once 'captcha.php';
    exit;
}
?&gt;
</pre>
