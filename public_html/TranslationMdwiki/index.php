<!DOCTYPE html>

<html lang="ar" dir="rtl">

<?php
function get_host()
{
	// $hoste = get_host();
	//---
	static $cached_host = null;
	//---
	if ($cached_host !== null) {
		return $cached_host; // استخدم القيمة المحفوظة
	}
	//---
	$hoste = ($_SERVER["SERVER_NAME"] == "localhost")
		? "https://cdnjs.cloudflare.com"
		: "https://tools-static.wmflabs.org/cdnjs";
	//---
	if ($hoste == "https://tools-static.wmflabs.org/cdnjs") {
		$url = "https://tools-static.wmflabs.org";
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true); // لا نريد تحميل الجسم
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // لمنع الطباعة

		curl_setopt($ch, CURLOPT_TIMEOUT, 3); // المهلة القصوى للاتصال
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CDN-Checker)');
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

		$result = curl_exec($ch);
		$curlError = curl_error($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);

		// إذا فشل الاتصال أو لم تكن الاستجابة ضمن 200–399، نستخدم cdnjs
		if ($result === false || !empty($curlError) || $httpCode < 200 || $httpCode >= 400) {
			$hoste = "https://cdnjs.cloudflare.com";
		}
	}

	$cached_host = $hoste;

	return $hoste;
}

$hoste = get_host();

echo <<<HTML
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>أداة اختبار ترجمة MDWiki</title>

	<link rel='stylesheet' href='$hoste/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css'>
	<link rel='stylesheet' href='$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/css/bootstrap-select.css'>
	<script src='$hoste/ajax/libs/jquery/3.7.0/jquery.min.js'></script>
	<script src='$hoste/ajax/libs/popper.js/2.11.8/umd/popper.min.js'></script>
	<script src='$hoste/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js'></script>
	<script src='$hoste/ajax/libs/bootstrap-select/1.14.0-beta3/js/bootstrap-select.min.js'></script>

	<script>
		const mw = {
			cx: {}
		};
	</script>
HTML;
// ---
$js = $_GET['js'] ?? "local";
// ---
$js_list = [
	"local" => "mw.cx.TranslationMdwiki.js",
	"mdwikicx" => "https://mdwikicx.toolforge.org/w/extensions/ContentTranslation/modules/mw.cx.TranslationMdwiki.js",
	"medwiki" => "https://medwiki.toolforge.org/w/extensions/ContentTranslation/modules/mw.cx.TranslationMdwiki.js",
];
// ---
// list of js files in the same directory using glob
foreach (glob("*.js") as $file) {
	if ($file == "mw.cx.TranslationMdwiki.js") continue;
	$js_list["local: $file"] = $file;
}
// ---
if (isset($js_list[$js])) {
	echo "<script src='$js_list[$js]'></script>";
}
?>

</head>

<body>
	<div class="container mt-5">
		<div class="card">
			<div class="card-header">
				<span class="h2">🧪 اختبار جلب المحتوى MDWiki</span>
			</div>
			<div class="card-body">
				<form id="testForm">
					<div class="row">
						<div class="col-md-4">
							<div class="mb-3">
								<label for="titleInput" class="form-label">عنوان المقال:</label>
								<select id="titleInput" class="selectpicker w-100" data-live-search="true" title="اختر عنوانًا أو اكتب يدويًا">
									<option value="Hydrocodone/paracetamol" selected>Hydrocodone/paracetamol</option>
									<option value="Trimethoprim/sulfamethoxazole">Trimethoprim/sulfamethoxazole</option>
									<option value="shingles">shingles</option>
									<option value="Shingles">Shingles</option>
									<option value="ALS">ALS</option>
									<option value="Video:Measles">Video:Measles</option>
									<option value="Video:HIV/TB coinfection">Video:HIV/TB coinfection</option>
									<option value="Health policy">Health policy</option>

								</select>
							</div>
						</div>
						<div class="col-md-2">
							<label for="trTypeSelect" class="form-label">نوع الترجمة:</label>
							<div id="trTypeSelect" class="form-check form-control d-flex flex-column">
								<div class="form-check">
									<input class="form-check-input" type="radio" name="trType" id="trTypeLead" checked>
									<label class="form-check-label" for="trTypeLead">
										المقدمة
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="trType" id="trTypeAll">
									<label class="form-check-label" for="trTypeAll">
										كامل
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label for="lang" class="form-label">اللغة:</label>
								<input type="text" id="lang" class="form-control" value="sw">
							</div>
						</div>
						<div class="col-md-2">
							<div class="mb-3">
								<label for="js" class="form-label">ملف js:</label>
								<select id="js" class="form-control form-select">
									<?php
									foreach ($js_list as $is_title => $js_file) {
										$selected = ($js == $is_title) ? "selected" : "";
										echo "<option value='$is_title' $selected>$is_title</option>";
									}
									?>
								</select>
							</div>
						</div>
						<div class="col-md-3">
							<div class="mb-3">
								<label for="funcs" class="form-label">وظيفة:</label>
								<select id="funcs" class="form-control form-select">
									<option value='None'>None</option>
									<option value='get_new_html_2025'>get_new_html_2025</option>
									<option value='get_from_medwiki_or_mdwiki_api'>get_from_medwiki_or_mdwiki_api</option>
									<option value='get_mdtexts_2024'>get_mdtexts_2024</option>
									<option value='get_Segments_from_mdwiki'>get_Segments_from_mdwiki</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="mb-3">
							<label for="lasturl" class="form-label">آخر رابط:</label>
							<textarea type="text" id="lasturl" class="form-control" dir="ltr" style="text-align: left;" readonly></textarea>
						</div>
					</div>
					<div class="mb-3">
						<button type="button" class="btn btn-primary" onclick="runTest()">تشغيل التجربة</button>
						<button type="button" class="btn btn-secondary" onclick="runAllTests()">تشغيل كل
							الحالات</button>

						<button type="button" class="btn btn-success" onclick="copyToClipboard()">نسخ JSON</button>
						<span class="text-success" id="copyStatus" style="display: none;">✅ تم نسخ البيانات إلى
							الحافظة!</span>
					</div>
				</form>

				<div class="mb-3">
					<label for="outputArea" class="form-label">النتيجة:</label>
					<span id="time"></span>
					<textarea id="outputArea" class="form-control" readonly></textarea>
				</div>
			</div>
			<div class="card-body mt-3">
				<div id="htmlOutput" class="form-control" dir="ltr" style="text-align: left;" stylex="height: 100px; overflow-y: auto;">
				</div>
			</div>
		</div>
	</div>
	<script>
		// --- ---
		// when js chenged, reload the page with new js
		$('#js').change(function() {
			// add js to url
			const js = $(this).val();
			const url = new URL(window.location.href);
			url.searchParams.set('js', js);
			window.location.href = url.toString();
		});

		// ---  ---
		async function add_segmented(segmentedContent) {
			let seg_body = new DOMParser().parseFromString(segmentedContent, "text/html").body;
			// Remove all <script> tags
			seg_body.querySelectorAll('script').forEach(script => script.remove());
			let html = seg_body.innerHTML;
			document.getElementById('htmlOutput').innerHTML = html;
		}

		async function runTest() {
			// ---
			var start = new Date().getTime();
			// ---
			document.getElementById('htmlOutput').innerHTML = "";
			// ---
			const funcs = $('#funcs').val();
			// ---
			$('#lasturl').val("");
			$('#lasturl').removeClass('is-valid');
			$('#lasturl').removeClass('is-invalid');
			// ---
			const title = $('#titleInput').val().trim();
			$('#outputArea').val("⏳ جاري التنفيذ...\n");
			// ---
			const trType = $('#trTypeSelect').find('input[name="trType"]:checked').attr('id') === 'trTypeLead' ? 'lead' : 'all';
			// ---
			console.log("trType:(" + trType + ")");
			// ---
			let lang = $('#lang').val().trim();
			// ---
			let output;
			// ---
			if (funcs === 'None') {
				output = await mw.cx.TranslationMdwiki.fetchSourcePageContent_mdwiki(title, lang, trType, funcs);
			} else {
				output = await mw.cx.TranslationMdwiki.fetchSourcePageContent_mdwiki_user_test(title, lang, trType, funcs);
			}
			// ---
			const text = JSON.stringify(output, null, 2);
			// ---
			$('#outputArea').val(text);
			// ---
			if (output) {
				$('#lasturl').addClass('is-valid');
			} else {
				$('#lasturl').removeClass('is-valid');
				$('#lasturl').addClass('is-invalid');
			}
			// ---
			console.log(output);
			if (output?.segmentedContent) {
				await add_segmented(output.segmentedContent);
			}
			// ---
			const lasturl = mw.cx.TranslationMdwiki.get_last_url();
			$('#lasturl').val(lasturl);
			// ---
			var end = new Date().getTime();
			var delta = end - start;
			// delta = Math.round(delta / 1000, 4);
			// ---
			$('#time').text(`⏱️ ${delta} مللي ثانية`);
			// ---
		}

		async function runAllTests() {

			const funcs = $('#funcs').val();

			const testCases = $("#titleInput option").map(function() {
				return {
					title: $(this).val(),
					tr_type: $('#trTypeSelect').val()
				};
			}).get();

			console.log(testCases);

			// Clear the output area
			$('#outputArea').val("🧪 بدء تنفيذ الحالات...\n");

			for (const test of testCases) {
				$('#outputArea').val(prev => prev + `\n--- الحالة: ${test.title} (${test.tr_type}) ---\n`);
				try {
					const res = await mw.cx.TranslationMdwiki.fetchSourcePageContent_mdwiki(test.title, 'sw', test.tr_type, funcs);
					$('#outputArea').val(prev => prev + JSON.stringify(res, null, 2) + "\n");
				} catch (e) {
					$('#outputArea').val(prev => prev + "❌ خطأ: " + e.message + "\n");
				}
			}
		}

		function copyToClipboard() {
			const textArea = document.getElementById("outputArea");
			textArea.select();
			document.execCommand("copy");
			// copyStatus
			$('#copyStatus').show();
			setTimeout(() => {
				$('#copyStatus').hide();
			}, 2000);
		}

		// Initialize selectpicker
		$(document).ready(function() {
			$('.selectpicker').selectpicker();
		});
	</script>
</body>

</html>
