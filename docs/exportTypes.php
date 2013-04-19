<?php
$page = "exportTypes";
require_once("templates/header.php");
?>

<div class="container">
	<div class="row">

		<div class="span3 bs-docs-sidebar" id="pagenav">
			<ul class="nav nav-list bs-docs-sidenav" data-spy="affix">
				<li class="active"><a href="#overview"><i class="icon-chevron-right"></i> Overview</a></li>
				<li><a href="#anatomy"><i class="icon-chevron-right"></i> Anatomy of an Export Type</a></li>
				<li><a href="#filesAndFolders"><i class="icon-chevron-right"></i> - Files and Folders</a></li>
				<li><a href="#js"><i class="icon-chevron-right"></i> - JavaScript</a></li>
				<li><a href="#php"><i class="icon-chevron-right"></i> - PHP</a></li>
				<li><a href="#languageFiles"><i class="icon-chevron-right"></i> - Language Files</a></li>
				<li><a href="#phpClass"><i class="icon-chevron-right"></i> <b>The PHP Class</b></a></li>
				<li><a href="#phpClassExample"><i class="icon-chevron-right"></i> - Example: CSV Export Type</a></li>
				<li><a href="#phpClassVars"><i class="icon-chevron-right"></i> - Overridable Class Vars</a></li>
				<li><a href="#phpClassMethods"><i class="icon-chevron-right"></i> - Overridable Class Methods</a></li>
				<li><a href="#phpClassNonOverridableMethods"><i class="icon-chevron-right"></i> - Non-overridable Class Methods</a></li>
				<li><a href="#jsModule"><i class="icon-chevron-right"></i> <b>The JS Module</b></a></li>
				<li><a href="#jsModuleExample"><i class="icon-chevron-right"></i> - Example: CSV</a></li>
				<li><a href="#jsModuleFunctions"><i class="icon-chevron-right"></i> - Registration Functions</a></li>
				<li><a href="#jsModulePubSub"><i class="icon-chevron-right"></i> - Pub/Sub &amp; Event List</a></li>
				<li><a href="#availableResources"><i class="icon-chevron-right"></i> Available JS Resources</a></li>
				<li><a href="#updatingUI"><i class="icon-chevron-right"></i>Adding your Export Type</a></li>
				<li><a href="#contribute"><i class="icon-chevron-right"></i> How to Contribute</a></li>
			</ul>
		</div>
		<div class="span9"> 

			<a id="overview"></a>
			<section>
				<div class="page-header">
					<h1>Export Types</h1>
				</div>
				<p class="lead">
					Provide new ways to display and download the data.
				</p>
			</section>

			<section>
				<h2>Overview</h2>
				<p>
					People need data in different formats. The Core script includes a number of standard formats: CSV, Excel, HTML, XML, JSON, 
					assorted programming language data structures and SQL. However, this list is far from exhaustive. This page explains how 
					to add your own Export Types so you can use the Data Generator to generate the random data in whatever format you require.
				</p>
				<p>
					Export Types appear in the main user interface and may contain whatever additional settings you may need. You can 
					include an optional JS module that ties in with the interface, to do things like perform Export Type-specific validation on the
					Data Type rows selected, or really anything else you may need. On the backend, Export Types do the job of tying together 
					all the data produced by the Data Types and generating whatever strings is needed for the entire data set. We'll step
					through how it all works in a bit, but let's start with dissecting an Export Type to see each part.
				</p>
			</section>

			<section id="anatomy">
				<h2>Anatomy of an Export Type</h2>
				<p>
					Now let's do a high-level view of what goes into a module: the files and folders, the JS + PHP components
					and how the translations / internationalization works. We'll get into the details about the code in the following
					sections.
				</p>
			</section>

			<section id="filesAndFolders">
				<h3>Files and Folders</h3>

				<p>
					All Export Types are found in the <code>/resources/plugins/exportTypes/</code> folder. Each Export Type has its own folder, 
					which acts as the namespace for the JS and PHP code. What I mean is that the exact string you choose for the folder (like 
					<code>XML</code> or <code>CSV</code>) <i>has to be</i> used in your JS module creation and PHP class definition. I'll explain 
					all that below.
				</p>
				<p>
					An Export Type has the following <b>required</b> files. Let's assume the folder name is <code>MyNewExportFormat</code>.
				</p>
				<ul>
					<li><code>/resources/plugins/exportType/MyNewExportFormat.js</code>: this file can actually be called whatever you want, but
						for consistency and for keeping reading the Web Inspector / Firebug net panel, I'd name them like this. You can have 
						as many JS files as you want, but one is almost certainly enough.</li>
					<li><code>/resources/plugins/exportType/MyNewExportFormat.class.php</code>: this contains your <code>ExportType_MyNewExportFormat</code>
						class, which handles all necessary server-side code: the actual markup/export string generation and any markup you want available in the 
						generator webpage. More info about all that below.</li>
					<li><code>/resources/plugins/exportType/lang/en.php</code>: A PHP file containing a single array (hash) that lists all 
						strings used in your module.</li>
				</ul>
				<p>
					You can also include any custom CSS files you want. See the PHP class definition below for more information.
				</p>
			</section>

			<section id="js">
				<h3>JavaScript</h3>
				<p>
					The JS module for your Export Type does the following:
				</p>
				<ul>
					<li>Registers itself with the <code>Manager</code> JS component, to allow it to publish and subscribe to messages; i.e.
						to interact with the Core script and detect when certain user interface events happen.</li>
					<li>Save and load whatever Export Type settings are offered by your Export Type (if any).</li>
					<li>Perform whatever validation is required to ensure the user fills in the Export Type settings properly.</li>
				</ul>
			</section>

			<section id="php">
				<h3>PHP</h3>
				<p>
					The PHP class for your Export Type handles the following functionality:
				</p>
				<ul>
					<li>Creates whatever HTML and fields should be included in the Export Type tab.</li>
					<li>Pieces together the various return values from the Data Types and creates the final output string.</li>
					<li>Handles the different export types: <i>In-page</i>, <i>New window/tab</i>, and <i>Prompt to download</i> and 
						specifies which of them the Export Type supports.</li>
					<li>Specifies which CodeMirror mode(s) that it needs in the UI to syntax highlight the generated data.</li>
					<li>Specifies the download filenames for the <i>Prompt to download</i> option.</li>
				</ul>
			</section>

			<section id="languageFiles">
				<h3>Language Files</h3>
				<p>
					All text strings that appear in your module should be pulled from a language file. It's very simple. Just create a 
					file called <code>en.php</code> in your <code>/resources/plugins/exportTypes/[export type folder]/lang/</code> folder.
					That file should contain a single <code>$L</code> hash, like so:
				</p>
	
<pre class="prettyprint linenums">
&lt;?php 

$L = array();
$L["EXPORT_TYPE_NAME"] = "XML";
$L["label1"] = "Label 1";
$L["label2"] = "Label 2";

// ...
</pre>

				<p>
					Once you do that, the Data Generator automatically makes that information accessible to your PHP and JS 
					code. I'll explain how that works in the following sections.
				</p>
			</section>

			<hr />

			<section id="phpClass">
				<h2>The PHP Class</h2>
				<p>
					All plugins - <code>Data Types</code>, <code>Export Types</code> and <code>Country</code> plugins have to extend 
					a base, abstract class defined by the core code. Hopefully you know what this means, but if not - time for some 
					Googling! Simply put, abstract classes are a mechanism to help ensure that the class being defined has a proper 
					footprint and contains all the functionality that's expected and required.
				</p>

				<p>
					For Export Types, take a look at this file: <code>/resources/classes/ExportTypePlugin.class.php</code>. That's the 
					class you'll need to extend.
				</p>
			</section>

			<section id="phpClassExample">
				<h3>Example: CSV Export Type</h3>
				<p>
					Now let's look at an actual implementation. If you want to see the complete list of available variables and methods, 
					check out the source code of the Export Type abstract class (<code>/resources/classes/ExportTypePlugin.abstract.class.php</code>). 
					It's well documented.
				</p>
				<p>
					This is the PHP class for the <code>CSV</code> class. It's a simple Export Type that outputs the randomly generated data in
					CSV format. It provides the user with the option to choose the <b>line ending char</b> (Windows / Mac, Unix) and the 
					<b>delimiter char</b>. Pretty straightforward.
				</p>

<pre class="prettyprint linenums">
&lt;?php

/**
 * @package ExportTypes
 */

class CSV extends ExportTypePlugin {
	protected $isEnabled = true;
	protected $exportTypeName = "CSV";
	protected $jsModules = array("CSV.js");
	protected $contentTypeHeader = "application/csv";
	public $L = array();


	function generate($generator) {
		$exportTarget = $generator->getExportTarget();
		$postData     = $generator->getPostData();
		$data         = $generator->generateExportData();

		$csvDelimiter = ($postData["etCSV_delimiter"] == '\t') ? "\t" : $postData["etCSV_delimiter"];
		$csvLineEndings = $postData["etCSV_lineEndings"];

		switch ($csvLineEndings) {
			case "Windows":
				$newline = "\r\n";
				break;
			case "Unix":
				$newline = "\n";
				break;
			case "Mac":
			default:
				$newline = "\r";
				break;
		}

		$content = "";
		if ($data["isFirstBatch"]) {
			$content .= implode($csvDelimiter, $data["colData"]);
		}
		foreach ($data["rowData"] as $row) {
			$content .= $newline . implode($csvDelimiter, $row);
		}

		return array(
			"success" => true,
			"content" => $content
		);
	}

	/**
	 * Used for constructing the filename of the filename when downloading.
	 * @see ExportTypePlugin::getDownloadFilename()
	 * @param Generator $generator
	 * @return string
	 */
	function getDownloadFilename($generator) {
		$time = date("M-j-Y");
		return "data{$time}.csv";
	}

	function getAdditionalSettingsHTML() {
		$LANG = Core::$language->getCurrentLanguageStrings();

		$html =&lt;&lt;&lt; END
&lt;table cellspacing="0" cellpadding="0" width="100%"&gt;
&lt;tr&gt;
	&lt;td width="50%"&gt;
		&lt;table cellspacing="2" cellpadding="0" width="100%"&gt;
		&lt;tr&gt;
			&lt;td width="160"&gt;{$this-&gt;L["delimiter_chars"]}&lt;/td&gt;
			&lt;td&gt;
				&lt;input type="text" size="2" name="etCSV_delimiter" id="etCSV_delimiter" value="|" /&gt;
			&lt;/td&gt;
		&lt;/tr&gt;
		&lt;/table&gt;
	&lt;/td&gt;
	&lt;td width="50%"&gt;
		&lt;table cellspacing="0" cellpadding="0" width="100%"&gt;
		&lt;tr&gt;
			&lt;td width="160"&gt;{$this-&gt;L["eol_char"]}&lt;/td&gt;
			&lt;td&gt;
				&lt;select name="etCSV_lineEndings" id="etCSV_lineEndings"&gt;
					&lt;option value="Windows"&gt;Windows&lt;/option&gt;
					&lt;option value="Unix"&gt;Unix&lt;/option&gt;
					&lt;option value="Mac"&gt;Mac&lt;/option&gt;
				&lt;/select&gt;
			&lt;/td&gt;
		&lt;/tr&gt;
		&lt;/table&gt;
	&lt;/td&gt;
&lt;/tr&gt;
&lt;/table&gt;
END;

		return $html;
	}
}

</pre>

				<p>
					Let's look at each line in turn.
				</p>

				<ul>
					<li><code>class CSV extends ExportTypePlugin</code>: our class definition. All Export Type class names
						must be the same as the folder.</li>
					<li><code>$isEnabled</code>: this var explicitly enables/disables the module. In case you're tinkering around with 
						a new Export Type, sometimes you may not want it to show up in the UI - so you'd just set this to 
						<code>false</code>.</li>
					<li><code>$exportTypeName</code>: this is the human-readable name of your module. It can be in whatever
						language you want, but we prefer English as the default language string. The value you enter in this variable
						is <i>automatically overridden</i> if the current selected language has the following key in the language file array:
						<code>$L["EXPORT_TYPE_NAME"] = "New Name";</code> This provides a simple mechanism to provide alternative translations
						of your Export Type names.</li>
					<li><code>$jsModules</code>: an array containing whatever JS files you want to include. Note: these must be in
						AMD format, for compatibility with requireJS.</li>
					<li><code>$contentTypeHeader</code>: this is used for the <i>Prompt to download</i> option. It lets your Export Type
						send additional headers that let the user's operating system know how to handle the data format.</li>
					<li><code>$L</code>: this variable is automatically populated with the appropriate language strings when your class
						is instantiated.</li>
				</ul>

				<p>
					Now onto the methods.
				</p>

				<ul>
					<li><code>generate</code>: this is the main generation function of your class. It does the job of actually generating the 
						final output data. One important thing to know about Export Type generation is that is happens in different contexts.
						For the <i>in-page</i> export type, the Export Type has to generate it in chunks - each chunk being (say) 100 rows at 
						a time. That information is sent back by the Core script via an Ajax call. As such, if the Export Type supports 
						that export type, the <code>generate</code> function needs to be able to handle both scenarios: generating the final 
						result piece-meal, or in one big chunk. You can see how it works in the code above. It checks the <code>$data["isFirstBatch"]</code>
						boolean to figure out whether to create the first row or not. Similarly, there's a <code>$data["isLastBatch"]</code>
						key available as well (not used here).
					</li>
					<li><code>getDownloadFilename</code>: returns the filename for the downloadable content, used in the <i>Prompt to Download</i>
						export format.</li>
					<li><code>getAdditionalSettingsHTML</code>: this method returns whatever HTML you want to appear in the Export Type
						tab. This is handy if you want to allow for a little fine-tuning of exactly what your Export Type generates. Look
						at the other Export Types to get an idea of how this method can be used.</li>
				</ul>
			</section>


			<section id="phpClassVars">
				<h2>Class Variable List</h2>
				<p>
					Alright! Here's the full list of class vars that have special meaning.
				</p>

				<table class="table table-striped">
					<thead>
						<tr>
							<th>Var</th>
							<th>Req/Opt</th>
							<th>Type</th>
							<th>Explanation</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>$exportTypeName</td>
							<td><span class="label label-success">required</span></td>
							<td>string</td>
							<td>The name of the export type "HTML", "XML" etc. This is always in English; even in different languages,
								"JSON" is still "JSON", so having no translation is acceptable here. But if you do need it to be overridden
								for different languages, add a <code>$L["EXPORT_TYPE_NAME"]</code> key to your lang files. That will override 
								this default value.
							</td>
						</tr>
						<tr>
							<td>$isEnabled</td>
							<td><span class="label label-info">optional</span></td>
							<td>boolean</td>
							<td>Used during development. Only Export Types that have $isEnabled == true will get listed in the Data Generator for use.</td>
						</tr>
						<tr>
							<td>$jsModules</td>
							<td><span class="label label-info">optional</span></td>
							<td>array</td>
							<td>An array of JS modules that need to be included for this module. They should be requireJS-friendly modules.</td>
						</tr>
						<tr>
							<td>$cssFiles</td>
							<td><span class="label label-info">optional</span></td>
							<td>array</td>
							<td>A single CSS file for any additional CSS needed for the module. It's up to the developer to properly name their CSS classes/IDs to prevent namespace collisions.</td>
						</tr>
						<tr>
							<td>$codeMirrorModes</td>
							<td><span class="label label-info">optional</span></td>
							<td>array</td>
							<td>An array of whatever CodeMirror modes (the syntax highlighter) this Export Type needs. This ensures they're all loaded at runtime for use in the generator.</td>
						</tr>
						<tr>
							<td>$contentTypeHeader</td>
							<td><span class="label label-info">optional</span></td>
							<td>string</td>
							<td>Needed for the "prompt for download" export option. This should contain the standard Content-Type header value (like "text/html") of the generated content, so the browser knows what to do with the downloaded file.</td>
						</tr>
						<tr>
							<td>$compatibleExportTypes</td>
							<td><span class="label label-info">optional</span></td>
							<td>array</td>
							<td>
								Export Types *should* be able to handle all three Export Targets available in the page (in-page, new window/tab, prompt for download), but if they 
								can't, they should specify this var. The system will automatically grey out those options that aren't selectable as soon as the user selects the 
								Export Type. Possible values in array: <code>inPage</code>, <code>newTab</code>, <code>promptDownload</code> (all strings).
							</td>
						</tr>
						<tr>
							<td>$compatibleExportTypes</td>
							<td><span class="label label-info">optional</span></td>
							<td>array</td>
							<td>
								Export Types *should* be able to handle all three Export Targets available in the page (in-page, new window/tab, prompt for download), but if they 
								can't, they should specify this var. The system will automatically grey out those options that aren't selectable as soon as the user selects the 
								Export Type. Possible values in array: <code>inPage</code>, <code>newTab</code>, <code>promptDownload</code> (all strings).
							</td>
						</tr>
						<tr>
							<td>$L</td>
							<td><span class="label label-important">auto-generated</span></td>
							<td>array</td>
							<td>Do NOT define this variable. When your Export Type is instantiated, this variable
								is auto-generated and populated with the appropriate language file.</td>
						</tr>
					</tbody>
				</table>
			</section>

			<section id="phpClassMethods">
				<h2>Class Method List</h2>


				<h3>generate()</h3>

				<table class="table">
					<tbody>
						<tr>
							<th>Req/Opt</th>
							<td><span class="label label-success">required</span></td>
						</tr>
						<tr>
							<th>Params</th>
							<td>
								<ol>
									<li>
										<b>$generator</b>: the <code>Generator</code> object, through which your Export Type can call the various available 
										public methods. See <code>/resources/classes/Generator.class.php</code>.
									</li>
								</ol>
							</td>
						</tr>
						<tr>
							<th>Explanation</th>
							<td>
								As mentioned above, this is the main generation function of your Export Type class. It's responsible for generating the 
								final outputted data. The <code>generate</code> function is required to handle all supported export types - i.e. 
								<i>in-page</i>, <i>new window / tab</i> and <i>prompt for download</i>. The difference is that for the <i>in-page</i> 
								export type, the Export Type has to generate it in chunks - each chunk being (say) 100 rows at a time. That 
								information is sent back by the Core script via an Ajax call. As such, if the Export Type supports that export type, 
								the <code>generate</code> function needs to be able to handle both scenarios: generating the final result piece-meal, 
								or in one big chunk. You can see how it works in the code above. It checks the <code>$data["isFirstBatch"]</code> boolean 
								to figure out whether to create the first row or not. Similarly, there's a <code>$data["isLastBatch"]</code> key available 
								as well (not used here).
							</td>
						</tr>
					</tbody>
				</table>


				<h3>getDownloadFilename()</h3>

				<table class="table">
					<tbody>
						<tr>
							<th>Req/Opt</th>
							<td><span class="label label-success">required</span></td>
						</tr>
						<tr>
							<th>Params</th>
							<td>
								<ol>
									<li>
										<b>$generator</b>: the <code>Generator</code> object, through which your Export Type can call the various available 
										public methods. See <code>/resources/classes/Generator.class.php</code>.
									</li>
								</ol>
							</td>
						</tr>
						<tr>
							<th>Explanation</th>
							<td>
							</td>
						</tr>
					</tbody>
				</table>


				<h3>__construct()</h3>

				<table class="table">
					<tbody>
						<tr>
							<th>Req/Opt</th>
							<td><span class="label label-info">optional</span></td>
						</tr>
						<tr>
							<th>Params</th>
							<td>
								<b>$runtimeContext</b>: Export Types classes are instantiated at different times in the code. This parameter
								is a string that describes the context in which it's being instantiated: <code>ui</code> / <code>generation</code>
							</td>
						</tr>
						<tr>
							<th>Explanation</th>
							<td>
								An optional constructor. Note: this should always call <code>parent::__construct($runtimeContext);</code>.
							</td>
						</tr>
					</tbody>
				</table>


				<h3>getAdditionalSettingsHTML()</h3>

				<table class="table">
					<tbody>
						<tr>
							<th>Req/Opt</th>
							<td><span class="label label-info">optional</span></td>
						</tr>
						<tr>
							<th>Params</th>
							<td>
							</td>
						</tr>
						<tr>
							<th>Explanation</th>
							<td>
							</td>
						</tr>
					</tbody>
				</table>
			</section>


			<section id="phpClassNonOverridableMethods">
				<h2>Non-overridable Methods</h2>
				<p>
					The following methods are defined on the Export Plugin abstract class, which you can use when developing your Export Type.
				</p>

				<table class="table table-striped">
					<thead>
						<tr>
							<th>Function</th>
							<th>Explanation</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>getName()</td>
							<td>returns the Data Type name.</td>
						</tr>
						<tr>
							<td>getJSModules()</td>
							<td>returns the array of JS modules.</td>
						</tr>
						<tr>
							<td>getCSSFiles()</td>
							<td>returns the array of CSS files for the Data Type.</td>
						</tr>
						<tr>
							<td>getFolder()</td>
							<td>returns the Export Type folder.</td>
						</tr>
						<tr>
							<td>getPath()</td>
							<td>returns the path to the Export Type folder.</td>
						</tr>
						<tr>
							<td>getContentTypeHeader()</td>
							<td>returns the content type header specified in the <code>$contentTypeHeader</code> member variable.</td>
						</tr>
						<tr>
							<td>getCodeMirrorModes()</td>
							<td>returns the <code>$codeMirrorModes</code> member variable value.</td>
						</tr>
						<tr>
							<td>isEnabled()</td>
							<td>returns whether or not the Data Type is enabled or not.</td>
						</tr>
					</tbody>
				</table>
			</section>

			<section id="jsModule">
				<h2>The JS Module</h2>
				<p>
					Each Data Type may choose to have an optional JS component: a javascript module that performs certain functionality 
					like saving/loading the data type data, running client-side validation on the user inputs (if required) and 
					triggering whatever additional JS code is necessary.
				</p>

				<h4>Optional or required?</h4>

				<p>
					The JS module is optional. The Core script handles saving and loading the Column Title and Data Type for all Data 
					Types, so if you don't need anything in the Example or Options columns, you don't need to include a JS module.
				</p>

				<p>
					Explaining how the JS module works can be a little abstract, so let's start with an example. 
				</p>
			</section>

			<section id="jsModuleExample">
				<h3>Example: CSV Export Type</h3>

				<p>
					The following is the JS module for the <code>Alphanumeric</code> Data Type. Give it a look over, then we'll
					pull it apart and explain each bit below.
				</p>
<pre class="prettyprint linenums">
/*global $:false*/
define([	
	"manager",
	"constants",
	"lang",
	"generator"
], function(manager, C, L, generator) {

	"use strict";

	/**
	 * @name CSV
	 * @see ExportType
	 * @description Client-side code for the CSV Export Type.
	 * @namespace
	 */

	var MODULE_ID = "export-type-CSV";
	var LANG = L.exportTypePlugins.CSV;


	var _loadSettings = function(settings) {
		$("#etCSV_delimiter").val(settings.delimiter);
		$("#etCSV_lineEndings").val(settings.eol);
	};

	var _saveSettings = function() {
		return {
			"delimiter": $("#etCSV_delimiter").val(),
			"eol":       $("#etCSV_lineEndings").val()
		};
	};

	var _resetSettings = function() {
		$("#etCSV_delimiter").val("|");
		$("#etCSV_lineEndings").val("Windows");
	};

	var _validate = function() {
		var delimiterField = $("#etCSV_delimiter");
		var errors = [];

		// note we don't trim it. I figure whitespace could, technically be used as a delimiter
		if (delimiterField.val() === "") {
			errors.push({
				els: delimiterField,
				error: LANG.validation_no_delimiter
			});
		}

		return errors;
	};

	manager.registerExportType(MODULE_ID, {
		validate: _validate,
		loadSettings: _loadSettings,
		saveSettings: _saveSettings,
		resetSettings: _resetSettings
	});
});
</pre>

				<p>
					Now let's go line by line.
				</p>

				<ul>
					<li><code>/*global $:false*/</code> this first line is for jshint/jslint. In my local environment, I use jshint with strict mode
						to catch problems. This line just tells the interpreter to ignore the jQuery dollar sign global.</li>
					<li>
<pre class="prettyprint">define([
	"manager",
	"constants",
	"lang",
	"generator"
], function(manager, C, L, generator) {
	//...
});
</pre>
						<p>
							The outer code that wraps the entire JS module is called within requireJS's <core>define</code> function. This ensures
							the code is defined as an AMD (Asynchronous Module Definition) for consumption by other code. The important thing 
							to understand here is the parameters. The first array params define string labels to other modules: they all map to
							specific JS files - you can find the mapping in <code>/resources/scripts/requireConfig.js</code>. Each of those 
							discrete modules is in turn passed to the Data Type module via functions in the anonymous section param to define(). 
							Whatever public API those modules reveal are now accessible via the four params: <code>manager</code>, <code>constants</code>,
							<code>lang</code>, <code>generator</code>.
						</p>

						<p>
							When defining your own Export Type module JS file, you'll want to include all four of those params. They all contain
							useful functionality and data that you'll need. 
						</p>
					</li>
					<li><code>"use strict";</code> - do it! JS strict mode is never a bad idea. :D</li>
					<li>Here we're going to skip ahead to the very end of the code, to these lines:

<pre class="prettyprint">
	manager.registerExportType(MODULE_ID, {
		validate: _validate,
		loadSettings: _loadSettings,
		saveSettings: _saveSettings,
		resetSettings: _resetSettings
	});
</pre>
						<p>
							This chunk of code is <b>required</b> for your Export Type JS Module. It registers your Export Type with the Core. That 
							allows it to listen to published events, publish its own events for other code to listen to, tie into the validation
							functionality and so on. It's pretty straightforward. The <code>manager.registerExportType()</code> function takes
							two parameters: the unique MODULE_ID constant, (see below) and an object containing certain required
							and optional functions, whose property names have special values. Again, more on that below. Now let's go back to the 
							top of the code again. 
						</p>
					</li>
					<li>

<pre class="prettyprint">
	/**
	 * @name CSV
	 * @see ExportType
	 * @description Client-side code for the CSV Export Type.
	 * @namespace
	 */

	var MODULE_ID = "export-type-CSV";
	var LANG = L.exportTypePlugins.CSV;
</pre>
						<ul>
							<li>
								The comment is of a particular format for being understood by JSDoc. For more information 
								on that, see the <a href="http://code.google.com/p/jsdoc-toolkit/" target="_blank">JS Doc project</a>.
							</li>
							<li>
								The <code>MODULE_ID</code> variable is special. It must <i>always</i> be of the form <b>data-type-[FOLDER NAME]</b>.
								That acts a unique identifier within the client-side code so the Manager can keep track of who's who.
							</li>
							<li>
								As with the PHP code, the language strings for your Data Type are automatically accessible: you don't have to do 
								any extra work to get access to them. The <code>L</code> function param fed to your Export Type contains all language
								strings in the system - in whatever language is currently selected. To locate the strings for your own module, 
								just reference it by your Export Type folder name, again: <code>L.exportTypePlugins.[FOLDER NAME]</code>
							</li>
						</ul>
					</li>
					<li>
						The following lines all define special functions. Rather than explain the implementation details of each of these for the 
						CSV type, we'll discuss these in a more abstract sense in the next section.
					</li>
				</ul>
			</section>

			<section id="jsModuleFunctions">
				<h3>Registration Functions</h3>

				<p>
					As explained above, the second parameter of the <code>manager.registerExportType()</code> function is an object 
					containing various predefined functions. This explains what are the properties for that object and what they're used 
					for. Note: <i>all properties are optional</i>, but you'll almost certainly need one or more.
				</p>

				<table class="table table-striped">
					<thead>
						<tr>
							<th>Property</th>
							<th width="100">Params</th>
							<th>Returns</th>
							<th>Explanation</th>
						</tr>
					</thead>
					<tbody>

			validate: function() { return []; },
			saveSettings: function() { return {}; },
			loadSettings: null,
			resetSettings: function() { },
			subscriptions: {}

						<tr>
							<td>init</td>
							<td>&#8212;</td>
							<td>&#8212;</td>
							<td>If this is defined for your Export Type, it gets called on page load prior to any events being published. By "event"
								I mean a custom published event, which I'll explain more thoroughly in the <a href="#jsModulePubSub">Pub/Sub</a>
								section below.
							</td>
						</tr>
						<tr>
							<td>run</td>
							<td>&#8212;</td>
							<td>&#8212;</td>
							<td>
								The run() function gets called for all Data Types and Export Types after their init()'s are called. As such, 
								run() can rely on all subscriptions being in place so events published at this juncture will have an 
								audience. 
							</td>
						</tr>
						<tr>
							<td>saveSettings</td>
							<td>&#8212;</th>
							<td><span class="label label-info">object</span></td>
							<td>
								When the user saves a data set, the Data Generator examines all Export Types - even those that aren't 
								selected - and calls their saveSettings() method. This method is responsible for determining what information 
								it wants to save for the row. Generally all it does is examine the DOM and extract whatever values the user 
								entered in custom fields that the Export Type aded. It then returns an object of simple property-value pairs. 
							</td>
						</tr>
						<tr>
							<td>loadSettings</td>
							<td>
								data <span class="label label-info">object</span>
							</td>
							<td>&#8212;</td>
							<td>
								When a user loads a data set, each Export Type has their loadSettings() function called, with whatever
								previous saved information passed as the single parameter.
							</td>
						</tr>
						<tr>
							<td>validate</td>
							<td>&#8212;</td>
							<td>
								<span class="label label-inverse">array</span>
							</td>
							<td>
								<p>
									This function needs to return an array of errors to display - or an empty array if there are no errors. Each 
									array index is an object of the following form: <code> { els: [], error: "error message here" }</code>. <b>els</b>
									is an array of DOM elements that have problems with them; <b>error</b> is the error message that will be displayed.
								</p>
								<p>
									Check out the CSV Data Type's validate() function above for an example of how this function can work.
								</p>
							</td>
						</tr>
					</tbody>
				</table>

			</section>

			<section id="jsModulePubSub">
				<h3>Pub/Sub &amp; Event List</h3>

				<p>
					As mentioned elsewhere, the client-side code revolves around the idea of publish/subscribe - or pub/sub. Different parts of 
					the script can publish arbitrary events with arbitrary information associated with them, and any module can choose to listen
					out for particular events and run code when they occur. This is a very elegant pattern: it allow us to keep our modules 
					loosely coupled and reduce the likelihood of introducing dependencies that can break things. 
				</p>

				<p>
					The core script publishes the following script for certain events that occur in the lifetime of the page. They're all 
					found in <code>/resources/scripts/constants.php</code> (returned as JS). You can refer to them in your code via the 
					<code>C</code> parameter, mapping to the <code>constants</code> module. The names are pretty descriptive so I won't 
					bother explaining them any further.
				</p>

				<ul>
					<li><code>C.EVENT.RESULT_TYPE.CHANGE</code></li>
					<li><code>C.EVENT.COUNTRIES.CHANGE</code></li>
					<li><code>C.EVENT.DATA_TABLE.ONLOAD_READY</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.CHECK_TO_DELETE</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.UNCHECK_TO_DELETE</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.DELETE</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.TYPE_CHANGE</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.EXAMPLE_CHANGE</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.ADD</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.RE_SORT</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.HELP_DIALOG_OPEN</code></li>
					<li><code>C.EVENT.DATA_TABLE.ROW.HELP_DIALOG_CLOSE</code></li>
					<li><code>C.EVENT.DATA_TABLE.CLEAR</code></li>
					<li><code>C.EVENT.GENERATE</code></li>
					<li><code>C.EVENT.IO.SAVE</code></li>
					<li><code>C.EVENT.IO.LOAD</code></li>
					<li><code>C.EVENT.TAB.CHANGE</code></li>
					<li><code>C.EVENT.MODULE.REGISTER</code></li>
					<li><code>C.EVENT.MODULE.UNREGISTER</code></li>
				</ul>

				<h4>How to subscribe to an event</h4>

				<p>
					Generally you'll want to set up your subscriptions in your module's <b>init()</b> function. Here's how it works:
				</p>

<pre class="prettyprint">
...

var _init = function() {
	var subscriptions = {};
	subscriptions[C.EVENT.COUNTRIES.CHANGE] = _onChangeCountries;
	manager.subscribe(subscriptions);
};

var _onChangeCountries = function(msg) {
	console.log(msg);
};

...

manager.registerDataType(MODULE_ID, {
	init: _init
});

...
</pre>

				<p>
					That would subscribe to the <code>C.EVENT.COUNTRIES.CHANGE</code> event (which is where the user adds/removes a country 
					from the Country List section in the UI) and attaches a callback function - <code>_onChangeCountries()</code>. The manager.subscribe()
					function can be called at any time in any of your functions, so you can subscribe to events on the fly.
				</p>
			</section>

			<section id="availableResources">
				<h2>Available Resources</h2>
				<p>
					There are several client-side code libraries already available in the page that can be used in your Data Type:
				</p>
				<ul>
					<li>jQuery ($)</li>
					<li>jQuery UI</li>
					<li><a href="http://momentjs.com/" target="_blank">MomentJS</a>- date/time formatting script</li>
					<li><a href="http://harvesthq.github.io/chosen/" target="_blank">Chosen</a> - dropdown enhancement</li>
				</ul>

				<p>
					You can always include additional libraries should you wish, but do try to namespace them.
				</p>
			</section>

			<section id="updatingUI">
				<h2>Adding your Export Type</h2>
				<p>
					When you add a new Export Type, just creating the new files and folders won't get it to show up in the UI. First,
					you'll need to follow the steps below to make sure your PHP class and (optionally) JS Module have been created properly, 
					and afterwards you'll need to refresh the UI.
				</p>
				<p>
					To update the list of available Export Types in the UI, go to the second <code>Settings</code> tab. There, click the 
					<code>Reset Plugins</code> button. A dialog will appears which resets all the available plugins (don't worry, this 
					won't cause any problems with saved content or anything like that). After refreshing the page, you should see
					your Export Type appear as a tab at the bottom of the page.
				</p>
			</section>

			<section id="contribute">
				<h2>How to Contribute</h2>
				<p>
					If you feel that your Export Type could be of use to other people, send it my way! I'd love to take a look at it,
					and maybe even include it in the core script for others to download. Read the <a href="contribute.php">How to Contribute</a>
					page.
				</p>
			</section>

		</div>
	</div>
</div>

<?php
require_once("templates/footer.php");
?>