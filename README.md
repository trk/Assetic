Assetic Module For [Ionize CMS](http://ionizecms.com/ "Ionize CMS")
=======
**Author :** [İskender TOTOĞLU](http://altivebir.com.tr "ALTI ve BIR IT.")

**Version :** 1.0

**Ionize Version :** 1.0.6 `Didn't tested for older versions`

Assetic Module for Ionize CMS http://ionizecms.com . Module Help you to manage your assets, compile and minify javascript and stylesheet files.

>Rename downloaded file as **Assetic** !

>IF **ENVIRONMENT** is **production** module will make all give files one file and will compile it.

>IF **ENVIRONMENT** isn't **production** module will make all given files one file, won't compile it.

**Example :**

* Your **ENVIRONMENT** is **development**, your output file will be **development.given.output.name.ext**
* IF you turn **ENVIRONMENT** is **production**, your file will have given output filename : **given.output.name.ext**



### USAGE ###

>Base Tag

	<ion:assetic>
		.......
	</ion:assetic>
	
>Manage CSS Files, Call **EACH FILE**

	<ion:assetic>
		<ion:AssetManager>
		
			<!-- collection is your collection group name -->
			<!-- path is output path for compiled and minified asset file path, without separator for start and end -->
			<!-- filename is output filename with fileextention -->
			<ion:AssetCollection collection="SiteStyle" path="assets/css" filename="site.min.css">
				
				<!-- Call each file by writing filepath with filename and extention -->
				<ion:FileAsset src="assets/css/bootstrap.css" />
				<ion:FileAsset src="assets/css/fontawesome.css" />
				<ion:FileAsset src="assets/css/theme.css" />
				
				<!-- Use cssmin for compress compiled file -->
				<ion:Compressor method="cssmin" />
				
				<!-- Render Result -->
				<ion:Render />
				
				<!-- Call URL :: you can use "url" attribute for call just URL of File, without link tag -->
				<ion:AssetURL />
				
			<ion:AssetCollection>
			
		</ion:AssetManager>
	</ion:assetic>
	
	<!-- RESULT -->
	<link rel="stylesheet" href="http://yoursite.ltd/themes/your-theme-path/assets/css/site.min.css?v=1398805852" />

>Manage CSS Files, Call **ALL FILES**

	<ion:assetic>
		<ion:AssetManager>
		
			<!-- collection is your collection group name -->
			<!-- path is output path for compiled and minified asset file path, without separator for start and end -->
			<!-- filename is output filename with fileextention -->
			<ion:AssetCollection collection="SiteStyle" path="assets/css" filename="site.min.css">
				
				<!-- Call All Files under given path and given extention -->
				<ion:FileAsset src="assets/css/build/*.css" />
				
				<!-- Use cssmin for compress compiled file -->
				<ion:Compressor method="cssmin" />
				
				<!-- Render Result -->
				<ion:Render />
				
				<!-- Call URL :: you can use "url" attribute for call just URL of File, without link tag -->
				<ion:AssetURL url="true" />
				
			<ion:AssetCollection>
			
		</ion:AssetManager>
	</ion:assetic>
	
	<!-- RESULT -->
	http://yoursite.ltd/themes/your-theme-path/assets/css/site.min.css?v=1398805852

>Manage **JS** Files, Call **EACH FILE**

	<ion:assetic>
		<ion:AssetManager>
		
			<!-- collection is your collection group name -->
			<!-- path is output path for compiled and minified asset file path, without separator for start and end -->
			<!-- filename is output filename with fileextention -->
			<ion:AssetCollection collection="SiteJavascript" path="assets/js" filename="site.min.js">
				
				<!-- Call each file by writing filepath with filename and extention -->
				<ion:FileAsset src="assets/css/bootstrap.js" />
				<ion:FileAsset src="assets/css/application.js" />
				
				<!-- Use cssmin for compress compiled file -->
				<ion:Compressor method="jsmin" />
				
				<!-- Render Result -->
				<ion:Render />
				
				<!-- Call URL :: you can use "url" attribute for call just URL of File, without link tag -->
				<ion:AssetURL />
				
			<ion:AssetCollection>
			
		</ion:AssetManager>
	</ion:assetic>
	
	<!-- RESULT -->
	<script type="text/javascript" src="http://yoursite.ltd/themes/your-theme-path/assets/js/site.min.js?v=1398805862"></script>

>Manage **JS** Files, Call **ALL FILES**

	<ion:assetic>
		<ion:AssetManager>
		
			<!-- collection is your collection group name -->
			<!-- path is output path for compiled and minified asset file path, without separator for start and end -->
			<!-- filename is output filename with fileextention -->
			<ion:AssetCollection collection="SiteJavascript" path="assets/js" filename="site.min.js">
				
				<!-- Call All Files under given path and given extention -->
				<ion:FileAsset src="assets/js/build/*.js" />
				
				<!-- Use cssmin for compress compiled file -->
				<ion:Compressor method="jsmin" />
				
				<!-- Render Result -->
				<ion:Render />
				
				<!-- Call URL :: you can use "url" attribute for call just URL of File, without link tag -->
				<ion:AssetURL url="true" />
				
			<ion:AssetCollection>
			
		</ion:AssetManager>
	</ion:assetic>
	
	<!-- RESULT -->
	http://yoursite.ltd/themes/your-theme-path/assets/js/site.min.js?v=1398805862

>Compressor Methods

| Method       	| Type           	| File Requered ?|
| -------------	|:-------------:	|:-------------: |
| yui			| **-**			|[Download Latest YUI Compressor](https://github.com/yui/yuicompressor/releases), Rename downloaded jar file as **yuicompressor.jar** and copy **yuicompressor.jar** file to **(modules/Assetic/libraries/Compressor/)** folder. |
| google		| **jar** or **api**	| For **type** is **api** you don't need **JAR** file for compiling, its using http service. For Local Compile : [Download Latest Closure Compiler](http://dl.google.com/closure-compiler/compiler-latest.zip), Rename downloaded jar file as **compiler.jar** and copy **compiler.jar** file to **(modules/Assetic/libraries/Compressor/)** folder. |
| cssmin		| **-**			| **-** |
| jsmin			| **-**			| **-** |


**NOTE :**

* You need **JAVA** on your server for use **JAR** files.
* You can use **YUI Compressor** for **JS** and **CSS** file compiling.
* You can use **Closure Compiler** only for **JS** file compiling.